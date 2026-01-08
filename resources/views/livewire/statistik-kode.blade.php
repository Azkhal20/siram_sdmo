<div>
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Laporan & Analisis Statistik</h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Analisis mendalam mengenai tren absensi dan tingkat kepatuhan.</p>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-8 bg-gradient-to-br from-red-500 to-rose-700 rounded-3xl text-white shadow-xl shadow-red-500/20 relative overflow-hidden group">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total TK</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['TK'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">PELANGGARAN TANPA KETERANGAN</p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
            </svg>
        </div>
        <div class="p-8 bg-gradient-to-br from-amber-500 to-orange-700 rounded-3xl text-white shadow-xl shadow-amber-500/20 relative overflow-hidden group">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total TM</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['TM'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">PELANGGARAN TELAT MASUK</p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path>
            </svg>
        </div>
        <div class="p-8 bg-gradient-to-br from-emerald-500 to-teal-700 rounded-3xl text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden group">
            <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">Total PC</h4>
            <p class="text-5xl font-black mb-1">{{ $stats['PC'] ?? 0 }}</p>
            <p class="text-xs font-bold opacity-70">INSIDEN PULANG CEPAT</p>
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 opacity-20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                <path d="M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z"></path>
            </svg>
        </div>
    </div>

    <!-- Top Violation Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-red-50/10">
                <h3 class="text-lg font-black text-red-600 dark:text-red-500 uppercase tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Top 5 Tanpa Keterangan (TK)
                </h3>
            </div>
            <div class="p-2">
                @forelse($topTK as $row)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-2xl transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center font-black">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $row->pesertaMagang->nama }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $row->pesertaMagang->kedeputian->nama }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-red-600">{{ $row->count }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase ml-1">KALI</span>
                    </div>
                </div>
                @empty
                <div class="p-10 text-center text-gray-400 text-xs italic font-bold">Belum ada pelanggaran TK terdeteksi.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-amber-50/10">
                <h3 class="text-lg font-black text-amber-600 dark:text-amber-500 uppercase tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    Top 5 Telat Masuk (TM)
                </h3>
            </div>
            <div class="p-2">
                @forelse($topTM as $row)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-2xl transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center font-black">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $row->pesertaMagang->nama }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $row->pesertaMagang->kedeputian->nama }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-amber-600">{{ $row->count }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase ml-1">KALI</span>
                    </div>
                </div>
                @empty
                <div class="p-10 text-center text-gray-400 text-xs italic font-bold">Belum ada pelanggaran TM terdeteksi.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>