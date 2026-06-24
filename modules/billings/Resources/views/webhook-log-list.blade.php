<div class="p-6 space-y-6 bg-slate-50 text-slate-900 min-h-screen">
    <!-- Cabeçalho -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">Logs de Webhooks</h1>
            <p class="text-sm text-slate-500">Monitore e analise os eventos recebidos da integração Omie em tempo real</p>
        </div>
        <div class="badge badge-outline badge-primary py-4 px-3 gap-2 border-slate-200 text-slate-600 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Escutando Eventos
        </div>
    </div>

    <!-- Barra de Filtros e Busca -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
        <div class="p-4 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-96">
                <input 
                    type="text" 
                    placeholder="Buscar nos logs ou payload..." 
                    class="input input-bordered w-full bg-slate-50 border-slate-200 text-sm focus:bg-white" 
                    wire:model.live.debounce.300ms="search"
                />
            </div>
            
            <div class="flex gap-4 w-full md:w-auto">
                <select class="select select-bordered bg-slate-50 border-slate-200 text-sm" wire:model.live="status">
                    <option value="">Todos os Status</option>
                    <option value="processed">Processados com Sucesso</option>
                    <option value="failed">Com Falha / Pendentes</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabela de Logs -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <th class="p-4">Data/Hora</th>
                        <th class="p-4">Provedor</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Payload</th>
                        <th class="p-4">Mensagem de Retorno / Erro</th>
                        <th class="text-right p-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/55 transition-colors border-b border-slate-100">
                            <td class="font-medium p-4 text-slate-800 text-xs">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="p-4 text-xs font-semibold text-slate-600">
                                {{ strtoupper($log->provider) }}
                            </td>
                            <td class="p-4">
                                @if($log->processed)
                                    <span class="badge bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px] font-bold uppercase rounded px-2.5 py-1">Processado</span>
                                @else
                                    <span class="badge bg-rose-50 text-rose-700 border-rose-200 text-[10px] font-bold uppercase rounded px-2.5 py-1">Falhou</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs">
                                @php
                                    $payloadStr = json_encode($log->payload);
                                @endphp
                                <button 
                                    class="btn btn-xs btn-outline border-slate-200 hover:bg-slate-50 text-slate-600 gap-1 rounded"
                                    onclick="showPayloadModal({{ $log->id }}, {{ $payloadStr }})"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                    </svg>
                                    Ver Payload
                                </button>
                            </td>
                            <td class="p-4 text-xs">
                                @if($log->error_message)
                                    <button 
                                        class="btn btn-xs btn-error btn-outline gap-1 rounded"
                                        onclick="showErrorModal({{ $log->id }}, `{{ addslashes($log->error_message) }}`)"
                                    >
                                        Ver Detalhes do Erro
                                    </button>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="text-right p-4">
                                <span class="text-[11px] text-slate-400 font-mono">ID: {{ $log->id }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="font-medium text-slate-400 text-sm">Nenhum webhook recebido ainda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        @if($logs->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal para visualização completa do Payload -->
    <div id="payload-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-all duration-300">
        <!-- Backdrop close handler -->
        <div class="absolute inset-0 cursor-pointer" onclick="closePayloadModal()"></div>
        
        <!-- Modal Content Container -->
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-slate-200/80 flex flex-col max-h-[80vh] overflow-hidden transform scale-95 transition-all duration-300 z-10">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-lg text-slate-800">Payload do Webhook</h3>
                    <p class="text-xs text-slate-400 mt-0.5">ID do Registro: <span id="modal-log-id" class="font-semibold text-slate-600">-</span></p>
                </div>
                <button onclick="closePayloadModal()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-white">
                <pre id="modal-payload-content" class="bg-slate-950 text-slate-200 p-5 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed border border-slate-800 shadow-inner select-all"></pre>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <button onclick="copyPayloadToClipboard()" class="btn btn-sm btn-ghost gap-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.16-7.5-8.875a9.06 9.06 0 0 0-1.5-.124m-7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25m-1.5 1.5h1.5m-1.5 3h1.5m-1.5-6h1.5m3-3h.008v.008H12V4.5Zm0 3h.008v.008H12V7.5Zm0 3h.008v.008H12v-.008Zm0 3h.008v.008H12v-.008Z" />
                    </svg>
                    Copiar Payload
                </button>
                <button class="btn btn-sm btn-outline border-slate-300 text-slate-700 bg-white hover:bg-slate-50 px-5" onclick="closePayloadModal()">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal para visualização de Erros -->
    <div id="error-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-all duration-300">
        <!-- Backdrop close handler -->
        <div class="absolute inset-0 cursor-pointer" onclick="closeErrorModal()"></div>
        
        <!-- Modal Content Container -->
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-slate-200/80 flex flex-col max-h-[80vh] overflow-hidden transform scale-95 transition-all duration-300 z-10">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-rose-50/50">
                <div>
                    <h3 class="font-bold text-lg text-rose-700">Erro no Processamento</h3>
                    <p class="text-xs text-rose-500/80 mt-0.5">ID do Registro: <span id="modal-error-log-id" class="font-semibold">-</span></p>
                </div>
                <button onclick="closeErrorModal()" class="text-rose-400 hover:text-rose-600 transition-colors p-1 rounded-lg hover:bg-rose-100/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-white">
                <pre id="modal-error-content" class="bg-rose-50/60 text-rose-800 p-5 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed border border-rose-100 whitespace-pre-wrap select-text"></pre>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-end px-6 py-4 border-t border-slate-100 bg-rose-50/30">
                <button class="btn btn-sm btn-outline border-slate-300 text-slate-700 bg-white hover:bg-slate-50 px-5" onclick="closeErrorModal()">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        function showPayloadModal(id, payload) {
            document.getElementById('modal-log-id').innerText = id;
            document.getElementById('modal-payload-content').innerText = JSON.stringify(payload, null, 4);
            
            const modal = document.getElementById('payload-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }

        function closePayloadModal() {
            const modal = document.getElementById('payload-modal');
            modal.classList.remove('opacity-100');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function showErrorModal(id, errorMessage) {
            document.getElementById('modal-error-log-id').innerText = id;
            document.getElementById('modal-error-content').innerText = errorMessage;
            
            const modal = document.getElementById('error-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }

        function closeErrorModal() {
            const modal = document.getElementById('error-modal');
            modal.classList.remove('opacity-100');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function copyPayloadToClipboard() {
            const content = document.getElementById('modal-payload-content').innerText;
            navigator.clipboard.writeText(content).then(() => {
                alert('Payload copiado para a área de transferência!');
            }).catch(err => {
                console.error('Erro ao copiar payload: ', err);
            });
        }

        // Fechamento com tecla ESC
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePayloadModal();
                closeErrorModal();
            }
        });
    </script>
</div>
