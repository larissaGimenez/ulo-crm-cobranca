<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">
    <head>
        @include('partials.head')
        <!-- Fonte Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased" x-data="{ sidebarOpen: false, sidebarMinimized: $persist(false) }">
        
        <!-- Top Header -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 fixed top-0 left-0 right-0 z-50">
            <div class="flex items-center gap-3">
                <!-- Botão Toggle Mobile -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden btn btn-ghost btn-sm p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                
                <div class="bg-red-100 border border-red-200 text-red-600 font-bold text-[10px] w-7 h-7 flex items-center justify-center rounded uppercase">
                    U
                </div>
                <span class="font-semibold text-lg text-slate-800 tracking-tight">ULO Matriz</span>
            </div>
            
            <div class="flex items-center gap-5">
                <button class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center hover:bg-amber-200 transition-all border-none" title="Tema">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.727l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>
                <div class="avatar placeholder">
                    <div class="bg-slate-200 text-slate-700 rounded-full w-9 h-9 font-semibold text-sm uppercase">
                        {{ auth()->user() ? auth()->user()->initials() : 'U' }}
                    </div>
                </div>
            </div>
        </header>

        <div class="flex pt-16 min-h-[calc(100vh-64px)]">
            
            <!-- Sidebar Left (Fixed Navigation) -->
            <aside 
                class="bg-white border-r border-slate-200 p-4 flex flex-col justify-between fixed top-16 bottom-0 left-0 z-40 transition-all duration-300"
                :class="{
                    'w-60': !sidebarMinimized,
                    'w-20': sidebarMinimized,
                    '-translate-x-full lg:translate-x-0': !sidebarOpen,
                    'translate-x-0': sidebarOpen
                }"
            >
                <div class="space-y-6">
                    <!-- Dashboard Section (Sem título de grupo) -->
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                           :class="{ 'justify-center': sidebarMinimized }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            <span x-show="!sidebarMinimized">Dashboard</span>
                        </a>
                    </div>

                    <!-- Apps Section -->
                    <div class="space-y-2">
                        <h3 x-show="!sidebarMinimized" class="text-[10px] font-bold uppercase text-slate-400 tracking-wider px-3">Apps</h3>
                        <div class="space-y-1">
                            <a href="{{ route('billings.index') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('billings.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                               :class="{ 'justify-center': sidebarMinimized }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!sidebarMinimized">Cobrança</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                               :class="{ 'justify-center': sidebarMinimized }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span x-show="!sidebarMinimized">Negociações</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                               :class="{ 'justify-center': sidebarMinimized }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-show="!sidebarMinimized">Comissões</span>
                            </a>
                        </div>
                    </div>

                    <!-- Gestão Section -->
                    <div class="space-y-2">
                        <h3 x-show="!sidebarMinimized" class="text-[10px] font-bold uppercase text-slate-400 tracking-wider px-3">Gestão</h3>
                        <div class="space-y-1">
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                               :class="{ 'justify-center': sidebarMinimized }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-show="!sidebarMinimized">Usuários</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                               :class="{ 'justify-center': sidebarMinimized }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span x-show="!sidebarMinimized">Perfil de acesso</span>
                            </a>
                            <a href="{{ route('webhook-logs.index') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('webhook-logs.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                               :class="{ 'justify-center': sidebarMinimized }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="!sidebarMinimized">Logs de Auditoria</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer / Collapse & Logout -->
                <div class="border-t border-slate-200 pt-4 space-y-2">
                    <button @click="sidebarMinimized = !sidebarMinimized" class="flex items-center gap-3 w-full text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium px-3 py-2.5 rounded-lg transition-all"
                            :class="{ 'justify-center': sidebarMinimized }">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0" :class="{'scale-x-[-1]': sidebarMinimized}">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span x-show="!sidebarMinimized">Colapsar</span>
                    </button>
                    
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-red-600 hover:bg-red-50 text-sm font-medium px-3 py-2.5 rounded-lg transition-all"
                                :class="{ 'justify-center': sidebarMinimized }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 stroke-[2] shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            <span x-show="!sidebarMinimized">Sair</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Overlay para Mobile -->
            <div 
                class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden" 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <!-- Workspace Area -->
            <main 
                class="flex-1 p-8 lg:p-10 max-w-[1600px] w-full transition-all duration-300"
                :class="{
                    'lg:ml-60': !sidebarMinimized,
                    'lg:ml-20': sidebarMinimized
                }"
            >
                {{ $slot }}
            </main>
        </div>

        @persist('toast')
            <!-- Placeholder para Toasts -->
        @endpersist
    </body>
</html>

