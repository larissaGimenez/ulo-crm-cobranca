<?php

namespace Modules\Billings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Clients\Models\Client;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'document_number',
        'installment',
        'invoice_number',
        'due_date',
        'expected_payment_date',
        'last_payment_date',
        'amount',
        'net_amount',
        'tax_withheld',
        'discount',
        'interest_fine',
        'amount_received',
        'amount_to_receive',
        'category',
        'operation',
        'salesperson',
        'project',
        'bank_account',
        'boleto_number',
        'document_type',
        'nsu',
        'issue_date',
        'registration_date',
        'client_order_number',
        'contract_number',
        'status',
        'notes',
        'omie_created_at',
        'omie_updated_at',
        'omie_created_by',
        'omie_updated_by',
        'company',
    ];

    /**
     * Relação lógica com o modelo de cliente
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
