<div>
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Rekapitulasi Absensi</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Laporan menyeluruh data kehadiran peserta magang.</p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center px-5 py-2.5 bg-emerald-600 dark:bg-emerald-500 text-white text-sm font-black rounded-2xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="relative">
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Cari Nama</label>
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none top-7">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live="search" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3 font-medium placeholder-gray-400 transition-all" placeholder="Ketik nama...">
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Kedeputian</label>
                <select wire:model.live="filterKedeputian" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-bold">
                    <option value="">Semua Kedeputian</option>
                    @foreach($kedeputians as $kd)
                    <option value="{{ $kd->id }}">{{ $kd->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Dari Tanggal</label>
                <input wire:model.live="fromDate" type="date" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-bold">
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Sampai Tanggal</label>
                <input wire:model.live="toDate" type="date" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-bold">
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-black tracking-tight">
                    <tr>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-600 transition-colors" wire:click="sortBy('id')">
                            <div class="flex items-center gap-1"># @if($sortField === 'id') <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">{!! $sortDirection === 'asc' ? '
                                    <path d="M5 10l5-5 5 5H5z" />' : '
                                    <path d="M5 10l5 5 5-5H5z" />' !!}
                                </svg> @endif</div>
                        </th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Kedeputian</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-600 transition-colors" wire:click="sortBy('tanggal')">
                            <div class="flex items-center gap-1">Tanggal @if($sortField === 'tanggal') <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">{!! $sortDirection === 'asc' ? '
                                    <path d="M5 10l5-5 5 5H5z" />' : '
                                    <path d="M5 10l5 5 5-5H5z" />' !!}
                                </svg> @endif</div>
                        </th>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Masuk</th>
                        <th class="px-6 py-4">Pulang</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($absensis as $abs)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all group">
                        <td class="px-6 py-4 font-bold text-gray-400">{{ $abs->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $abs->pesertaMagang->nama }}</span>
                                <span class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">{{ $abs->pesertaMagang->no_induk ?? 'PM-000'.$abs->pesertaMagang->id }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase">
                                {{ $abs->pesertaMagang->kedeputian->nama }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600 dark:text-gray-400">
                            {{ $abs->tanggal->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $this->getBadgeClass($abs->kode) }} text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">
                                {{ $abs->kode }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $abs->jam_masuk ?? '--:--' }}</td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $abs->jam_pulang ?? '--:--' }}</td>
                        <td class="px-6 py-4 text-center">
                            <button class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-500 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-full mb-4">
                                    <svg class="w-16 h-16 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-400 uppercase italic">Belum Ada Data</h3>
                                <p class="text-xs text-gray-300 mt-1">Silakan lakukan import PDF melalui menu "Data Absensi".</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensis->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $absensis->links() }}
        </div>
        @endif
    </div>
</div>