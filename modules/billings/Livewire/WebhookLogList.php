<?php

namespace Modules\Billings\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Billings\Models\WebhookLog;

#[Title('Logs de Webhooks')]
class WebhookLogList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = ''; // 'processed', 'failed'

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WebhookLog::orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('provider', 'like', '%' . $this->search . '%')
                  ->orWhere('error_message', 'like', '%' . $this->search . '%')
                  ->orWhere('payload', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status === 'processed') {
            $query->where('processed', true);
        } elseif ($this->status === 'failed') {
            $query->where('processed', false);
        }

        $logs = $query->paginate(20);

        return view('billings::webhook-log-list', [
            'logs' => $logs,
        ]);
    }
}
