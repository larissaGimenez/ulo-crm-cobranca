<div class="p-6 space-y-6 bg-slate-50 text-slate-900 min-h-screen">
    <!-- Cabeçalho -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">Títulos</h1>
            <p class="text-sm text-slate-500">Gerencie e acompanhe a carteira de recebíveis importada do Omie</p>
        </div>
        <div class="badge badge-outline badge-primary py-4 px-3 gap-2 border-slate-200 text-slate-600 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Integração Omie Ativa
        </div>
    </div>

    <!-- Barra de Filtros e Busca -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
        <div class="p-4 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-96">
                <input 
                    type="text" 
                    id="search-input"
                    placeholder="Buscar por cliente ou CNPJ..." 
                    class="input input-bordered w-full bg-slate-50 border-slate-200 text-sm focus:bg-white" 
                    wire:model.live.debounce.300ms="search"
                />
            </div>
            
            <div class="flex flex-wrap gap-4 w-full md:w-auto">
                <select class="select select-bordered bg-slate-50 border-slate-200 text-sm" wire:model.live="company">
                    <option value="">Todas as Empresas</option>
                    <option value="ulo01">ULO 01</option>
                    <option value="ulo02">ULO 02</option>
                    <option value="ulo03">ULO 03</option>
                    <option value="ulo04">ULO 04</option>
                    <option value="ulo05">ULO 05</option>
                </select>

                <select class="select select-bordered bg-slate-50 border-slate-200 text-sm" wire:model.live="status">
                    <option value="">Todos os Status</option>
                    <option value="Atrasado">Atrasado</option>
                    <option value="Atrasado (boleto gerado)">Atrasado (Boleto Gerado)</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Pago">Pago</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabela de Cobranças -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="cursor-pointer hover:bg-slate-100 p-4" wire:click="sortBy('due_date')">
                            Vencimento 
                            @if($sortField === 'due_date')
                                <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th class="cursor-pointer hover:bg-slate-100 p-4" wire:click="sortBy('client.name')">
                            Cliente 
                            @if($sortField === 'client.name')
                                <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th class="p-4">Empresa</th>
                        <th class="p-4">Documento</th>
                        <th class="p-4">Parcela</th>
                        <th class="cursor-pointer hover:bg-slate-100 p-4" wire:click="sortBy('amount')">
                            Valor Conta 
                            @if($sortField === 'amount')
                                <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th class="p-4">Valor a Receber</th>
                        <th class="p-4">Situação</th>
                        <th class="text-right p-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $billing)
                        <tr class="hover:bg-slate-50/55 transition-colors border-b border-slate-100">
                            <td class="font-medium p-4 text-slate-800">
                                {{ \Carbon\Carbon::parse($billing->due_date)->format('d/m/Y') }}
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-800">{{ $billing->client->name ?? 'Cliente Desconhecido' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $billing->client->document ?? '' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="badge bg-slate-100 text-slate-700 border-none font-semibold uppercase text-[10px] px-2 py-1 rounded">
                                    {{ $billing->company ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="text-xs text-slate-500">{{ $billing->document_number }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-xs font-medium text-slate-600 bg-slate-100/60 px-2 py-0.5 rounded border border-slate-200">{{ $billing->installment }}</span>
                            </td>
                            <td class="p-4 text-slate-800 font-medium">
                                R$ {{ number_format($billing->amount, 2, ',', '.') }}
                            </td>
                            <td class="p-4 font-semibold text-blue-600">
                                R$ {{ number_format($billing->amount_to_receive, 2, ',', '.') }}
                            </td>
                            <td class="p-4">
                                @if(str_contains(strtolower($billing->status), 'pago'))
                                    <span class="badge bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px] font-bold uppercase rounded px-2.5 py-1">Pago</span>
                                @elseif(str_contains(strtolower($billing->status), 'atrasado'))
                                    <span class="badge bg-rose-50 text-rose-700 border-rose-200 text-[10px] font-bold uppercase rounded px-2.5 py-1">{{ $billing->status }}</span>
                                @else
                                    <span class="badge bg-amber-50 text-amber-700 border-amber-200 text-[10px] font-bold uppercase rounded px-2.5 py-1">{{ $billing->status }}</span>
                                @endif
                            </td>
                            <td class="text-right p-4">
                                <div class="dropdown dropdown-end">
                                    <label tabindex="0" class="btn btn-ghost btn-xs text-slate-400 hover:text-slate-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                        </svg>
                                    </label>
                                    <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-white border border-slate-200 rounded-lg w-52 z-30">
                                        <li>
                                            <a class="text-xs text-slate-700 hover:bg-slate-50" onclick="alert('Boleto: {{ $billing->boleto_number ?? 'Não informado' }}\nNota Fiscal: {{ $billing->invoice_number ?? 'Não informado' }}\nObservação: {{ $billing->notes ?? 'Nenhuma' }}')">
                                                Visualizar Detalhes
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="font-medium text-slate-400 text-sm">Nenhuma cobrança encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        @if($billings->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $billings->links() }}
            </div>
        @endif
    </div>

    <!-- Script com jQuery para interação e logs amigáveis da busca do lado do cliente -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Log amigável ao iniciar o jQuery
            console.log("Busca e ordenação prontas com jQuery e Livewire.");

            // Um exemplo de feedback instantâneo usando jQuery ao digitar
            $('#search-input').on('input', function() {
                let val = $(this).val();
                if (val.length > 0) {
                    console.log("Buscando por: " + val);
                }
            });
        });
    </script>
</div>

