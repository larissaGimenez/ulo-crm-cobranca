# Módulo de Billings (Cobranças)

Este documento descreve as tabelas, rotas e regras do sistema de cobranças integrado com o Omie ERP.

---

## 1. Visão Geral
O módulo `modules/Billings` gerencia os recebíveis e títulos financeiros em aberto importados da Omie.
Toda entrada, edição e alteração de situação das cobranças ocorre através do processamento de Webhooks enviados pelo ERP Omie. A listagem é disponibilizada aos usuários autenticados sob a rota `/billings`.

---

## 2. Estrutura de Tabelas

### Tabela `billings`
Modelada exatamente a partir do mapeamento de colunas do relatório oficial do Omie (`financas_*.xlsx`):
* `client_id`: ID relacional interno (módulo `Clients`).
* `company`: Empresa pertencente (ulo01, ulo02, ulo03, ulo04 ou ulo05) - mapeada a partir do webhook de múltiplos parceiros.
* `document_number`: Número do documento/título.
* `installment`: Código da parcela (ex: '001/012').
* `invoice_number`: Nota fiscal ou cupom fiscal associado.
* `due_date`: Vencimento do título.
* `expected_payment_date`: Data de previsão de recebimento.
* `amount`: Valor total da conta.
* `net_amount`: Valor líquido a receber (já descontados impostos).
* `amount_received` e `amount_to_receive`: Valores relativos ao recebimento.
* `status`: Situação atual (ex: 'Pago', 'Atrasado', 'Pendente').
* `boleto_number`: Número do boleto bancário emitido.
* `notes`: Observações e histórico da tratativa de cobrança.

### Tabela `webhook_logs`
Responsável por salvar todas as tentativas de sincronização do Webhook da Omie para fins de auditoria e debugging:
* `provider`: Nome do integrador ('omie').
* `payload`: JSON completo recebido.
* `processed`: Status de sucesso do processamento.
* `error_message`: Stack trace ou mensagem de erro caso o processamento falhe.

---

## 3. Integração via Webhooks (Omie)
* **Endpoint:** `POST /api/webhooks/omie`
* **Autenticação:** O cabeçalho/payload valida as variáveis de ambiente `OMIE_APP_KEY` e `OMIE_APP_SECRET`.
* **Múltiplas Empresas:** O sistema está preparado para gerenciar 5 empresas ULO simultaneamente (`ulo01` a `ulo05`). A empresa é identificada no payload através da chave `company`, `empresa` ou baseada na chave do appKey enviada, associada a `OMIE_APP_KEY_ULO01` até `OMIE_APP_KEY_ULO05` configuradas no arquivo `.env`.
* **Fluxo de Sincronização:**
  1. O webhook registra a carga na tabela `webhook_logs`.
  2. Valida-se o documento (CPF/CNPJ) do cliente enviado no payload.
  3. Aciona o contrato `ClientServiceInterface` para buscar ou registrar o cliente correspondente de forma desacoplada.
  4. Identifica a empresa ULO emissora (`company`).
  5. Insere ou atualiza o registro na tabela `billings` vinculando ao cliente.
