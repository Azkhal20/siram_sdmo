<div>
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Dashboard Overview</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Real-time statistik absensi periode terpilih.</p>
        </div>

        <div class="flex flex-wrap md:flex-nowrap items-center gap-2">
            <div class="relative w-[300px] md:w-[520px]">
                <select wire:model.live="selectedKedeputian" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-[11px] font-black uppercase text-gray-900 dark:text-white rounded-2xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-shadow shadow-sm hover:shadow-md cursor-pointer truncate">
                    <option value="">Semua Unit</option>
                    @foreach($kedeputians as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative min-w-[120px]">
                <select wire:model.live="selectedMonth" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-[11px] font-black uppercase text-gray-900 dark:text-white rounded-2xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-shadow shadow-sm hover:shadow-md cursor-pointer">
                    @foreach($months as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative min-w-[90px]">
                <select wire:model.live="selectedYear" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-[11px] font-black uppercase text-gray-900 dark:text-white rounded-2xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-shadow shadow-sm hover:shadow-md cursor-pointer">
                    @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Export Button (White Style) -->
            <a href="{{ route('dashboard.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'kedeputian_id' => $selectedKedeputian, 'active_detail' => $activeDetail]) }}" target="_blank" class="flex items-center justify-center p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm hover:shadow-md hover:border-red-500 transition-all active:scale-95 group" title="Export PDF">
                <svg class="w-5 h-5 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="ml-2 text-[11px] font-black uppercase text-gray-900 dark:text-white hidden sm:inline-block">PDF</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards (Premium Style) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Peserta -->
        <div class="p-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl text-white shadow-xl shadow-blue-500/20 relative overflow-hidden group">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total Peserta</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['total_peserta'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">AKTIF DALAM UNIT</p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
            </svg>
        </div>

        <!-- Total TK -->
        <div wire:click="setActiveDetail('TK')" class="cursor-pointer p-8 bg-gradient-to-br from-red-500 to-rose-700 rounded-3xl text-white shadow-xl shadow-red-500/20 relative overflow-hidden group hover:scale-[1.02] active:scale-[0.98] transition-all {{ $activeDetail === 'TK' ? 'ring-4 ring-red-300 dark:ring-red-900 scale-[1.02]' : '' }}">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total TK</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['total_tk'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">
                {{ $activeDetail === 'TK' ? 'KLIK UNTUK TUTUP' : 'KLIK UNTUK LIHAT DATA' }}
            </p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
            </svg>
        </div>

        <!-- Total TM -->
        <div wire:click="setActiveDetail('TM')" class="cursor-pointer p-8 bg-gradient-to-br from-amber-500 to-orange-700 rounded-3xl text-white shadow-xl shadow-amber-500/20 relative overflow-hidden group hover:scale-[1.02] active:scale-[0.98] transition-all {{ $activeDetail === 'TM' ? 'ring-4 ring-amber-300 dark:ring-amber-900 scale-[1.02]' : '' }}">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total TM</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['total_tm'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">
                {{ $activeDetail === 'TM' ? 'KLIK UNTUK TUTUP' : 'KLIK UNTUK LIHAT DATA' }}
            </p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path>
            </svg>
        </div>

        <!-- Total PC -->
        <div wire:click="setActiveDetail('PC')" class="cursor-pointer p-8 bg-gradient-to-br from-emerald-500 to-teal-700 rounded-3xl text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden group hover:scale-[1.02] active:scale-[0.98] transition-all {{ $activeDetail === 'PC' ? 'ring-4 ring-emerald-300 dark:ring-emerald-900 scale-[1.02]' : '' }}">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total PC</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['total_pc'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">
                {{ $activeDetail === 'PC' ? 'KLIK UNTUK TUTUP' : 'KLIK UNTUK LIHAT DATA' }}
            </p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z"></path>
            </svg>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Top Violation Lists -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <!-- Top TK Card -->
                <div class="flex flex-col h-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden min-h-[480px]">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-red-50/10">
                        <h3 class="text-xs font-black text-red-600 dark:text-red-500 uppercase tracking-widest flex items-center gap-2">
                            <div class="p-1.5 bg-red-100 dark:bg-red-900/30 rounded-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            Top 5 Tanpa Keterangan
                        </h3>
                    </div>
                    <div class="flex-1 p-5 space-y-6">
                        @forelse($topTK as $row)
                        @php $percent = ($row->count / $maxTK) * 100; @endphp
                        <div class="group relative">
                            <div class="flex items-start justify-between relative z-10">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-50 dark:bg-gray-700/50 text-xs font-black text-gray-400 group-hover:text-red-600 group-hover:bg-red-50 dark:group-hover:bg-red-900/20 rounded-xl flex items-center justify-center transition-all border border-gray-100 dark:border-gray-700">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="max-w-[140px] sm:max-w-[180px]">
                                        <p class="text-sm font-extrabold text-gray-900 dark:text-white leading-tight mb-0.5 truncate">{{ $row->pesertaMagang->nama }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight line-clamp-1 italic">{{ $row->pesertaMagang->kedeputian->nama }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end">
                                    <span class="text-lg font-black text-red-600 leading-none">{{ $row->count }}</span>
                                    <span class="text-[9px] font-black text-gray-400 uppercase">KALI</span>
                                </div>
                            </div>
                            <div class="mt-3 h-1.5 w-full bg-gray-50 dark:bg-gray-700/30 rounded-full overflow-hidden border border-gray-100/50 dark:border-gray-700/50">
                                <div class="h-full bg-gradient-to-r from-red-400 to-red-600 rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(239,68,68,0.3)]" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="h-full flex flex-col items-center justify-center py-10 opacity-40">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-xs font-bold italic">Belum ada data TK.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top TM Card -->
                <div class="flex flex-col h-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden min-h-[480px]">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-amber-50/10">
                        <h3 class="text-xs font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest flex items-center gap-2">
                            <div class="p-1.5 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            Top 5 Telat Masuk
                        </h3>
                    </div>
                    <div class="flex-1 p-5 space-y-6">
                        @forelse($topTM as $row)
                        @php $percent = ($row->count / $maxTM) * 100; @endphp
                        <div class="group relative">
                            <div class="flex items-start justify-between relative z-10">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-50 dark:bg-gray-700/50 text-xs font-black text-gray-400 group-hover:text-amber-600 group-hover:bg-amber-50 dark:group-hover:bg-amber-900/20 rounded-xl flex items-center justify-center transition-all border border-gray-100 dark:border-gray-700">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="max-w-[140px] sm:max-w-[180px]">
                                        <p class="text-sm font-extrabold text-gray-900 dark:text-white leading-tight mb-0.5 truncate">{{ $row->pesertaMagang->nama }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight line-clamp-1 italic">{{ $row->pesertaMagang->kedeputian->nama }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end">
                                    <span class="text-lg font-black text-amber-600 leading-none">{{ $row->count }}</span>
                                    <span class="text-[9px] font-black text-gray-400 uppercase">KALI</span>
                                </div>
                            </div>
                            <div class="mt-3 h-1.5 w-full bg-gray-50 dark:bg-gray-700/30 rounded-full overflow-hidden border border-gray-100/50 dark:border-gray-700/50">
                                <div class="h-full bg-gradient-to-r from-amber-400 to-amber-600 rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(245,158,11,0.3)]" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="h-full flex flex-col items-center justify-center py-10 opacity-40">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs font-bold italic">Belum ada data TM.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Action Quick Link -->
        <div class="space-y-6">
            <!-- Unified Control Center -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-[2rem] shadow-sm overflow-hidden flex flex-col">
                <!-- Top Section: Primary Action (Import) -->
                <div class="p-8 bg-gradient-to-br from-blue-600 to-indigo-700 text-white relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-xl font-black mb-2 tracking-tight">Kontrol Cepat</h4>
                        <p class="text-sm text-blue-100 mb-6 font-medium leading-relaxed">Kelola data absensi, peserta, dan laporan dalam satu tempat.</p>

                        <a href="{{ route('absensi.import') }}" class="inline-flex items-center w-full justify-center px-6 py-3 bg-white text-blue-600 text-sm font-black rounded-2xl hover:bg-blue-50 transition-all active:scale-95 shadow-xl shadow-blue-900/20">
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Mulai Import PDF
                        </a>
                    </div>
                    <!-- Decorative Icon -->
                    <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-white/10 group-hover:scale-110 transition-transform duration-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1-3l3 3h-3v-3z"></path>
                    </svg>
                </div>

                <!-- Bottom Section: Secondary Actions -->
                <div class="p-5 grid grid-cols-1 gap-3 bg-gray-50/50 dark:bg-gray-700/30">
                    <a href="{{ route('master.peserta') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all group">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest leading-none mb-1">Database</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">Tambah Peserta</p>
                        </div>
                        <svg class="w-4 h-4 ms-auto text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('absensi.rekap') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-md transition-all group">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest leading-none mb-1">Laporan</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">Export Rekap</p>
                        </div>
                        <svg class="w-4 h-4 ms-auto text-gray-300 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Activity Table (Full Width) -->
    <!-- Recent Activity / Detail Table (Full Width) -->
    <div
        id="detail-table-section"
        x-data="{ scrollToTable() { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); } }"
        @scroll-to-table.window="scrollToTable()"
        class="mt-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-[2.5rem] shadow-sm overflow-hidden min-h-[400px]">
        <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
            @php
            $headerColor = match($activeDetail) {
            'TK' => 'text-red-600 dark:text-red-500',
            'TM' => 'text-amber-600 dark:text-amber-500',
            'PC' => 'text-emerald-600 dark:text-emerald-500',
            default => 'text-blue-600 dark:text-blue-400',
            };
            $dotColor = match($activeDetail) {
            'TK' => 'bg-red-600',
            'TM' => 'bg-amber-600',
            'PC' => 'bg-emerald-600',
            default => 'bg-blue-600 animate-pulse',
            };
            @endphp

            <h3 class="text-[12px] font-black uppercase tracking-[0.2em] flex items-center gap-3 {{ $headerColor }}">
                <div class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></div>
                {{ $activeDetail === 'recent' ? 'Data Absensi Terbaru' : 'Detail Data ' . ($activeDetail == 'TK' ? 'Tanpa Keterangan (TK)' : ($activeDetail == 'TM' ? 'Terlambat Masuk (TM)' : 'Pulang Cepat (PC)')) }}
            </h3>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[10px] font-black rounded-lg uppercase tracking-wider border border-gray-100 dark:border-gray-700">
                    Total: {{ $detailData->total() }} Data
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 dark:bg-gray-800 text-[12px] font-black uppercase text-gray-900 dark:text-white border-b border-gray-300 dark:border-gray-700">
                    <tr>
                        @if($activeDetail === 'recent')
                        <th class="px-8 py-5">Peserta Magang</th>
                        <th class="px-8 py-5">Unit Kedeputian</th>
                        <th class="px-8 py-5 text-center">Tanggal Absensi</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5">Keterangan Sistem</th>
                        @else
                        <th class="px-8 py-5 text-center w-20">No</th>
                        <th class="px-8 py-5">Nama Peserta</th>
                        <th class="px-8 py-5">Unit Kedeputian</th>
                        <th class="px-8 py-5 text-right">Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($detailData as $item)
                    @php
                    $hoverClass = match($activeDetail) {
                    'TK' => 'hover:bg-red-50/50 dark:hover:bg-red-900/10',
                    'TM' => 'hover:bg-amber-50/50 dark:hover:bg-amber-900/10',
                    'PC' => 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10',
                    default => 'hover:bg-blue-50/30 dark:hover:bg-blue-900/10',
                    };
                    $textHoverClass = match($activeDetail) {
                    'TK' => 'group-hover:text-red-600',
                    'TM' => 'group-hover:text-amber-600',
                    'PC' => 'group-hover:text-emerald-600',
                    default => 'group-hover:text-blue-600',
                    };
                    @endphp
                    @if($activeDetail === 'recent')
                    <tr class="{{ $hoverClass }} transition-colors group cursor-pointer">
                        <td class="px-8 py-5">
                            <p class="font-extrabold text-gray-900 dark:text-white {{ $textHoverClass }} transition-colors">{{ $item->pesertaMagang->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $item->pesertaMagang->nip }}</p>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">{{ $item->pesertaMagang->kedeputian->nama }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <p class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</p>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1.5 rounded-xl text-[10px] font-black {{ $this->getBadgeClass($item->kehadiran) }} uppercase tracking-widest shadow-sm">
                                {{ $item->kehadiran }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm">
                                {{ $item->keterangan_detail }}
                            </p>
                        </td>
                    </tr>
                    @else
                    <!-- Aggregated View for Detail Stats -->
                    <tr class="{{ $hoverClass }} transition-colors group cursor-pointer">
                        <td class="px-8 py-5 text-center font-bold text-gray-400">
                            {{ ($detailData->currentPage() - 1) * $detailData->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-8 py-5">
                            <p class="font-extrabold text-gray-900 dark:text-white {{ $textHoverClass }} transition-colors">{{ $item->pesertaMagang->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $item->pesertaMagang->nip }}</p>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">{{ $item->pesertaMagang->kedeputian->nama }}</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-sm font-black {{ $activeDetail === 'TK' ? 'text-red-600' : ($activeDetail === 'TM' ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $item->total_count }} Kali
                            </span>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="{{ $activeDetail === 'recent' ? 5 : 4 }}" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-xs font-black uppercase tracking-widest text-gray-400 italic">Belum ada aktivitas terekam untuk periode ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-800/50">
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Results: <span class="font-black text-gray-900 dark:text-white">{{ $detailData->firstItem() ?? 0 }} - {{ $detailData->lastItem() ?? 0 }}</span> of <span class="font-black text-gray-900 dark:text-white">{{ $detailData->total() }}</span>
                </span>
                <div class="relative">
                    <select wire:model.live="perPage" wire:change="$dispatch('scroll-to-table')" style="appearance: none !important; -webkit-appearance: none !important; -moz-appearance: none !important; background-image: none !important;" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-1.5 pl-3 pr-9 font-bold cursor-pointer transition-all shadow-sm hover:border-blue-400">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end flex-1 w-full md:w-auto">
                {{ $detailData->links('livewire.custom-pagination', ['scrollTo' => '#detail-table-section']) }}
            </div>
        </div>
    </div>
</div>