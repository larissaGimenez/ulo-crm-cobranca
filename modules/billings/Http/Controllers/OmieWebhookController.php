<?php

namespace Modules\Billings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Billings\Models\Billing;
use Modules\Billings\Models\WebhookLog;
use Modules\Clients\Contracts\ClientServiceInterface;

class OmieWebhookController extends Controller
{
    public function __construct(
        protected ClientServiceInterface $clientService
    ) {}

    /**
     * Endpoint para receber webhooks da Omie
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1. Registrar o log do webhook recebido
        $log = WebhookLog::create([
            'provider' => 'omie',
            'payload' => $payload,
            'processed' => false,
        ]);

        try {
            // 2. Validar chaves de autenticação do Omie (app_key e app_secret)
            $expectedKey = env('OMIE_APP_KEY');
            $expectedSecret = env('OMIE_APP_SECRET');

            $appKey = $payload['appKey'] ?? $payload['app_key'] ?? null;
            $appSecret = $payload['appSecret'] ?? $payload['app_secret'] ?? null;

            if ($expectedKey && $expectedSecret) {
                if ($appKey !== $expectedKey || $appSecret !== $expectedSecret) {
                    throw new \Exception("Credenciais do Webhook Omie inválidas.");
                }
            }

            // 3. Tratar ping do Omie
            if (isset($payload['ping']) && $payload['ping'] === 'omie') {
                $log->update(['processed' => true]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ping da Omie respondido com sucesso.',
                ], 200);
            }

            // 4. Extrair dados do cliente e da cobrança do payload
            // Damos suporte tanto ao payload oficial do Omie quanto a payloads planos (usados em testes e importações)
            $eventData = $payload['event'] ?? $payload;

            $clientDocument = $eventData['cliente_documento'] ?? $eventData['Cliente (CNPJ/CPF)'] ?? $eventData['cnpj_cpf'] ?? $eventData['cnpj'] ?? $eventData['cpf'] ?? null;
            $clientName = $eventData['cliente_nome'] ?? $eventData['Cliente (Razão Social)'] ?? $eventData['Cliente (Nome Fantasia)'] ?? $eventData['razao_social'] ?? $eventData['nome_fantasia'] ?? 'Cliente Omie';
            $clientTags = $eventData['cliente_tags'] ?? $eventData['Tags do Cliente'] ?? $eventData['tags'] ?? null;
            $clientEmail = $eventData['cliente_email'] ?? $eventData['email'] ?? null;

            if (!$clientDocument) {
                // Se o webhook vier como cadastro de cliente com chaves parciais, pode ser que o documento precise vir de uma consulta posterior.
                // Mas, por ora, buscamos em qualquer nível do payload para evitar erros
                $clientDocument = $payload['cnpj_cpf'] ?? $payload['cnpj'] ?? $payload['cpf'] ?? null;
                
                if (!$clientDocument) {
                    throw new \Exception("Documento do cliente (CNPJ/CPF) não fornecido no payload.");
                }
            }

            // Limpar formatação do documento
            $clientDocument = preg_replace('/\D/', '', $clientDocument);

            // Mapear campos da cobrança
            $documentNumber = $eventData['numero_documento'] ?? $eventData['Número do Documento'] ?? null;
            $installment = $eventData['parcela'] ?? $eventData['Parcela'] ?? '1/1';
            $omieId = $eventData['codigo_lancamento_omie'] ?? $eventData['omie_id'] ?? null;

            // Se for um webhook puramente de Cliente/Fornecedor (ex: ClienteFornecedor.Inserido/Alterado)
            // e não contiver campos de título/documento de cobrança, apenas finalizamos com sucesso 200
            // uma vez que o cliente já foi atualizado/criado corretamente no passo anterior.
            if (!$documentNumber && !$omieId) {
                $log->update(['processed' => true]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Dados de cliente atualizados. Evento não necessita de cobrança vinculada.',
                ], 200);
            }

            // Chamar o contrato de clientes para garantir a existência do registro do cliente
            $clientData = $this->clientService->findOrCreateByDocument($clientDocument, [
                'name' => $clientName,
                'email' => $clientEmail,
                'tags' => $clientTags,
            ]);

            // Formatação de datas
            $dueDate = $this->parseDate($eventData['data_vencimento'] ?? $eventData['Vencimento'] ?? null);
            $expectedPayment = $this->parseDate($eventData['previsao_recebimento'] ?? $eventData['Previsão de Recebimento'] ?? null) ?? $dueDate;
            $lastPayment = $this->parseDate($eventData['ultimo_recebimento'] ?? $eventData['Último Recebimento'] ?? null);
            $issueDate = $this->parseDate($eventData['data_emissao'] ?? $eventData['Data de Emissão'] ?? null) ?? now();
            $registrationDate = $this->parseDate($eventData['data_registro'] ?? $eventData['Data de Registro'] ?? null) ?? now();

            // Valores monetários
            $amount = $this->parseAmount($eventData['valor_documento'] ?? $eventData['Valor da Conta'] ?? 0);
            $netAmount = $this->parseAmount($eventData['valor_liquido'] ?? $eventData['Valor Líquido'] ?? $amount);
            $taxWithheld = $this->parseAmount($eventData['impostos_retidos'] ?? $eventData['Impostos Retidos'] ?? 0);
            $discount = $this->parseAmount($eventData['desconto'] ?? $eventData['Desconto'] ?? 0);
            $interestFine = $this->parseAmount($eventData['juros_multa'] ?? $eventData['Juros e Multa'] ?? 0);
            $amountReceived = $this->parseAmount($eventData['valor_recebido'] ?? $eventData['Valor Recebido'] ?? 0);
            $amountToReceive = $this->parseAmount($eventData['valor_a_receber'] ?? $eventData['Valor a Receber'] ?? ($amount - $amountReceived));

            // Informações adicionais
            $status = $eventData['situacao'] ?? $eventData['Situação'] ?? 'Pendente';
            $invoiceNumber = $eventData['nota_fiscal'] ?? $eventData['Nota Fiscal / Cupom Fiscal'] ?? null;
            $category = $eventData['categoria'] ?? $eventData['Categoria'] ?? null;
            $operation = $eventData['operacao'] ?? $eventData['Operação'] ?? null;
            $salesperson = $eventData['vendedor'] ?? $eventData['Vendedor'] ?? null;
            $project = $eventData['projeto'] ?? $eventData['Projeto'] ?? null;
            $bankAccount = $eventData['conta_corrente'] ?? $eventData['Conta Corrente'] ?? null;
            $boletoNumber = $eventData['numero_boleto'] ?? $eventData['Número do Boleto'] ?? null;
            $documentType = $eventData['tipo_documento'] ?? $eventData['Tipo de Documento'] ?? null;
            $nsu = $eventData['nsu'] ?? $eventData['Número NSU (Cupom Fiscal)'] ?? null;
            $clientOrderNumber = $eventData['numero_pedido'] ?? $eventData['Nº do Pedido do Cliente'] ?? null;
            $contractNumber = $eventData['numero_contrato'] ?? $eventData['Nº do Contrato de Venda'] ?? null;
            $notes = $eventData['observacao'] ?? $eventData['Observação'] ?? null;
            
            // Mapeamento de empresa (ulo01 a ulo05)
            $company = $eventData['empresa'] ?? $eventData['company'] ?? null;
            if (!$company && $appKey) {
                // Tenta associar baseado no appKey se mapeado no .env, ex: OMIE_APP_KEY_ULO01, etc.
                // Mas por padrão extrai/usa o que vier no webhook ou define com fallback se puder identificar
                for ($i = 1; $i <= 5; $i++) {
                    if ($appKey === env("OMIE_APP_KEY_ULO0" . $i)) {
                        $company = "ulo0" . $i;
                        break;
                    }
                }
            }
            // Fallback genérico se nada for encontrado, mas veio no payload do webhook original
            if (!$company) {
                $company = $payload['company'] ?? $payload['empresa'] ?? null;
            }

            $omieCreatedAt = $this->parseDateTime($eventData['inclusao'] ?? $eventData['Inclusão'] ?? null);
            $omieUpdatedAt = $this->parseDateTime($eventData['ultima_alteracao'] ?? $eventData['Última Alteração'] ?? null);
            $omieCreatedBy = $eventData['incluido_por'] ?? $eventData['Incluído por'] ?? null;
            $omieUpdatedBy = $eventData['alterado_por'] ?? $eventData['Alterado por'] ?? null;

            // 4. Salvar ou atualizar a cobrança no banco de dados
            Billing::updateOrCreate(
                [
                    'document_number' => $documentNumber,
                    'installment' => $installment,
                ],
                [
                    'client_id' => $clientData->id,
                    'invoice_number' => $invoiceNumber,
                    'due_date' => $dueDate,
                    'expected_payment_date' => $expectedPayment,
                    'last_payment_date' => $lastPayment,
                    'amount' => $amount,
                    'net_amount' => $netAmount,
                    'tax_withheld' => $taxWithheld,
                    'discount' => $discount,
                    'interest_fine' => $interestFine,
                    'amount_received' => $amountReceived,
                    'amount_to_receive' => $amountToReceive,
                    'category' => $category,
                    'operation' => $operation,
                    'salesperson' => $salesperson,
                    'project' => $project,
                    'bank_account' => $bankAccount,
                    'boleto_number' => $boletoNumber,
                    'document_type' => $documentType,
                    'nsu' => $nsu,
                    'issue_date' => $issueDate,
                    'registration_date' => $registrationDate,
                    'client_order_number' => $clientOrderNumber,
                    'contract_number' => $contractNumber,
                    'status' => $status,
                    'notes' => $notes,
                    'company' => $company,
                    'omie_created_at' => $omieCreatedAt,
                    'omie_updated_at' => $omieUpdatedAt,
                    'omie_created_by' => $omieCreatedBy,
                    'omie_updated_by' => $omieUpdatedBy,
                ]
            );

            // 5. Marcar log como processado
            $log->update(['processed' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processado com sucesso.',
            ], 200);

        } catch (\Exception $e) {
            // Registrar erro no log
            $log->update([
                'processed' => false,
                'error_message' => $e->getMessage() . "\n" . $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar webhook: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Converte datas em formatos variados para o padrão Y-m-d
     */
    private function parseDate($dateStr)
    {
        if (!$dateStr) {
            return null;
        }

        try {
            // Formato 'd/m/Y' (muito comum no Omie e Excel)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateStr)) {
                return Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');
            }
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Converte datetimes em formatos variados
     */
    private function parseDateTime($dateTimeStr)
    {
        if (!$dateTimeStr) {
            return null;
        }

        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}(:\d{2})?$/', $dateTimeStr)) {
                return Carbon::createFromFormat('d/m/Y H:i:s', $dateTimeStr)->format('Y-m-d H:i:s');
            }
            return Carbon::parse($dateTimeStr)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Limpa e converte valores monetários
     */
    private function parseAmount($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Se for string com formato R$ 1.500,00 ou similar
        $clean = preg_replace('/[^\d,.-]/', '', $value);
        if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }
}
