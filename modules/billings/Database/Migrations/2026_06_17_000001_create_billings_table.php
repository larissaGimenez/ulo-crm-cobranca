<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id'); // Relação lógica com clients
            $table->string('document_number'); // Número do Documento
            $table->string('installment'); // Parcela
            $table->string('invoice_number')->nullable(); // Nota Fiscal / Cupom Fiscal
            $table->date('due_date'); // Vencimento
            $table->date('expected_payment_date'); // Previsão de Recebimento
            $table->date('last_payment_date')->nullable(); // Último Recebimento
            $table->decimal('amount', 15, 2); // Valor da Conta
            $table->decimal('net_amount', 15, 2); // Valor Líquido
            $table->decimal('tax_withheld', 15, 2)->default(0.00); // Impostos Retidos
            $table->decimal('discount', 15, 2)->default(0.00); // Desconto
            $table->decimal('interest_fine', 15, 2)->default(0.00); // Juros e Multa
            $table->decimal('amount_received', 15, 2)->default(0.00); // Valor Recebido
            $table->decimal('amount_to_receive', 15, 2); // Valor a Receber
            $table->string('category')->nullable(); // Categoria
            $table->string('operation')->nullable(); // Operação
            $table->string('salesperson')->nullable(); // Vendedor
            $table->string('project')->nullable(); // Projeto
            $table->string('bank_account')->nullable(); // Conta Corrente
            $table->string('boleto_number')->nullable(); // Número do Boleto
            $table->string('document_type')->nullable(); // Tipo de Documento
            $table->string('nsu')->nullable(); // Número NSU
            $table->date('issue_date'); // Data de Emissão
            $table->date('registration_date'); // Data de Registro
            $table->string('client_order_number')->nullable(); // Nº do Pedido do Cliente
            $table->string('contract_number')->nullable(); // Nº do Contrato de Venda
            $table->string('status'); // Situação (Atrasado, Pago, Pendente)
            $table->text('notes')->nullable(); // Observação
            $table->datetime('omie_created_at')->nullable(); // Inclusão
            $table->datetime('omie_updated_at')->nullable(); // Última Alteração
            $table->string('omie_created_by')->nullable(); // Incluído por
            $table->string('omie_updated_by')->nullable(); // Alterado por
            $table->string('company')->nullable(); // Empresa (ulo01, ulo02, ..., ulo05)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
