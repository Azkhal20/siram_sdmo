<div>
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Dashboard Overview</h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Real-time statistik absensi periode ini.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/40 rounded-xl text-blue-600 dark:text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase text-gray-400">Total Peserta</h5>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_peserta'] }}</p>
                </div>
            </div>
        </div>
        <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-50 dark:bg-red-900/40 rounded-xl text-red-600 dark:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase text-gray-400">Tanpa Keterangan</h5>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_tk'] }}</p>
                </div>
            </div>
        </div>
        <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-900/40 rounded-xl text-amber-600 dark:text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase text-gray-400">Telat Masuk</h5>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_tm'] }}</p>
                </div>
            </div>
        </div>
        <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/40 rounded-xl text-emerald-600 dark:text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase text-gray-400">Pulang Cepat</h5>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_pc'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Data Absensi Terbaru</h3>
                    <a href="{{ route('absensi.rekap') }}" class="text-xs font-bold text-blue-600 dark:text-blue-500 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Nama</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($absensis->take(5) as $abs)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $abs->pesertaMagang->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $abs->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="{{ $this->getBadgeClass($abs->kehadiran) }} text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">
                                        {{ $abs->kehadiran }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- System Settings Quick Link -->
        <div class="space-y-6">
            <div class="p-6 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl text-white shadow-xl shadow-blue-500/20 relative overflow-hidden group">
                <div class="relative z-10">
                    <h4 class="text-lg font-black mb-2">Impor Data Baru?</h4>
                    <p class="text-sm text-blue-100 mb-6 font-medium">Lakukan import file PDF absensi terbaru untuk memperbarui dashboard statistik.</p>
                    <a href="{{ route('absensi.import') }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 text-sm font-bold rounded-xl hover:bg-blue-50 transition-all active:scale-95 shadow-lg">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Mulai Import
                    </a>
                </div>
                <svg class="absolute -right-8 -bottom-8 w-40 h-40 text-white/10 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1-3l3 3h-3v-3z"></path>
                </svg>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-4">Aksi Cepat</h4>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('master.peserta') }}" class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-center transition-all group">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Add Peserta</span>
                    </a>
                    <a href="{{ route('absensi.rekap') }}" class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-center transition-all group">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Export Rekap</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>