<?php

namespace Modules\Billings\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Billings\Models\Billing;

#[Title('Listagem de Cobranças')]
class BillingList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $company = '';
    public string $sortField = 'due_date';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'company' => ['except' => ''],
        'sortField' => ['except' => 'due_date'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCompany()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Billing::with('client');

        // Busca
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('document_number', 'like', '%' . $this->search . '%')
                  ->orWhere('boleto_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function ($cq) {
                      $cq->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('document', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filtro de Status
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Filtro de Empresa
        if (!empty($this->company)) {
            $query->where('company', $this->company);
        }

        // Ordenação
        if ($this->sortField === 'client.name') {
            $query->join('clients', 'billings.client_id', '=', 'clients.id')
                ->select('billings.*')
                ->orderBy('clients.name', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $billings = $query->paginate(20);

        // Agrupado de estatísticas simples
        $totalOutstanding = Billing::where('status', '!=', 'Pago')->sum('amount_to_receive');
        $totalReceived = Billing::sum('amount_received');
        $totalOverdue = Billing::where('status', 'like', 'Atrasado%')->sum('amount_to_receive');

        return view('billings::billing-list', [
            'billings' => $billings,
            'totalOutstanding' => $totalOutstanding,
            'totalReceived' => $totalReceived,
            'totalOverdue' => $totalOverdue,
        ]);
    }
}
