<div>
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg flex items-center justify-between shadow-lg">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
        <button @click="show = false"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg></button>
    </div>
    @endif

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Rekapitulasi Absensi</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Laporan menyeluruh data kehadiran peserta magang.</p>
        </div>
        <button wire:click="exportExcel" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 text-white text-sm font-black rounded-2xl hover:bg-emerald-700 transition-all shadow-lg active:scale-95">
            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Excel
        </button>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="relative">
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Cari Nama</label>
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none top-7"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg></div>
                <input wire:model.live="search" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3 font-medium placeholder-gray-400 transition-all" placeholder="Ketik nama...">
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Kedeputian</label>
                <select wire:model.live="filterKedeputian" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-semibold">
                    <option value="">Semua Kedeputian</option>
                    @foreach($kedeputians as $kd)<option value="{{ $kd->id }}">{{ $kd->nama }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Dari Tanggal</label>
                <input wire:model.live="fromDate" type="date" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-semibold">
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Sampai Tanggal</label>
                <input wire:model.live="toDate" type="date" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-semibold">
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden min-w-full">
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
            <table class="w-full min-w-[1200px] text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-[12px] font-black uppercase bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-blue-600 transition-colors" wire:click="sortBy('tanggal')">
                            <div class="flex items-center gap-1">Tanggal @if($sortField === 'tanggal')<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">{!! $sortDirection === 'asc' ? '
                                    <path d="M5 10l5-5 5 5H5z" />' : '
                                    <path d="M5 10l5 5 5-5H5z" />' !!}
                                </svg>@endif</div>
                        </th>
                        <th class="px-6 py-4 text-center">Kehadiran</th>
                        <th class="px-6 py-4 text-center">Masuk</th>
                        <th class="px-6 py-4 text-center">Pulang</th>
                        <th class="px-6 py-4 text-center">Telat Masuk</th>
                        <th class="px-6 py-4 text-center">Pulang Cepat</th>
                        <th class="px-6 py-4 text-left">Keterangan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($absensis as $abs)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all group">
                        <td class="px-6 py-4 font-bold text-gray-400">{{ ($absensis->currentPage() - 1) * $absensis->perPage() + $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $abs->pesertaMagang?->nama ?? 'N/A' }}</span>
                                <span class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">{{ $abs->pesertaMagang?->nomor_induk ?? 'PM-000'.($abs->peserta_magang_id ?? '0') }}</span>
                                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase bg-blue-50 dark:bg-blue-900/20 px-1.5 py-0.5 rounded w-fit">{{ $abs->pesertaMagang?->kedeputian?->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600 dark:text-gray-400">{{ $abs->tanggal?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-center"><span class="{{ $this->getBadgeClass($abs->kehadiran ?? '') }} text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">{{ $abs->kehadiran ?? '-' }}</span></td>
                        <td class="px-6 py-4 font-mono text-xs text-center">{{ ($abs->jam_masuk && strtotime($abs->jam_masuk)) ? date('H:i', strtotime($abs->jam_masuk)) : '--:--' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-center">{{ ($abs->jam_pulang && strtotime($abs->jam_pulang)) ? date('H:i', strtotime($abs->jam_pulang)) : '--:--' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-center text-red-500 font-bold">{{ $abs->telat_masuk ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-center text-orange-500 font-bold">{{ $abs->pulang_cepat ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-600 dark:text-gray-400 max-w-[200px]">{{ $abs->keterangan_detail ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openEditModal({{ $abs->id }})" class="p-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all transform hover:scale-110"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg></button>
                                <button wire:click="openDeleteModal({{ $abs->id }})" class="p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all transform hover:scale-110"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-full mb-4"><svg class="w-16 h-16 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg></div>
                                <h3 class="text-lg font-black text-gray-400 uppercase italic">Belum Ada Data</h3>
                                <p class="text-xs text-gray-300 mt-1">Silahkan lakukan import PDF melalui menu "Import PDF".</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-800/50">
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Results: <span class="font-black text-gray-900 dark:text-white">{{ $absensis->firstItem() ?? 0 }} - {{ $absensis->lastItem() ?? 0 }}</span> of <span class="font-black text-gray-900 dark:text-white">{{ $absensis->total() }}</span></span>
                <div class="relative">
                    <select wire:model.live="perPage" style="appearance: none !important; -webkit-appearance: none !important; -moz-appearance: none !important; background-image: none !important;" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-1.5 pl-3 pr-9 font-bold cursor-pointer transition-all shadow-sm hover:border-blue-400">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg></div>
                </div>
            </div>
            <div class="flex items-center justify-end flex-1 w-full md:w-auto">{{ $absensis->links('livewire.custom-pagination') }}</div>
        </div>
    </div>

    @if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeEditModal"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-2xl"><svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg></div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Data Absensi</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ubah informasi kehadiran peserta</p>
                            </div>
                        </div>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg></button>
                    </div>
                    <form wire:submit.prevent="updateAbsensi" class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status Kehadiran <span class="text-red-500">*</span></label>
                            <select wire:model="editKehadiran" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition @error('editKehadiran') border-red-500 @enderror">
                                <option value="">Pilih Status</option>
                                <option value="HN">Hadir Normal</option>
                                <option value="TK">Tanpa Keterangan</option>
                                <option value="TMDHM">Tidak Absen Masuk</option>
                                <option value="TMDHP">Tidak Absen Pulang</option>
                                <option value="TM">Terlambat Masuk</option>
                                <option value="TM1">Terlambat < 30 menit</option>
                                <option value="TM2">Terlambat > 30 menit</option>
                                <option value="TM3">Terlambat > 1 jam</option>
                                <option value="PC">Pulang Cepat</option>
                                <option value="PC1">Pulang Cepat < 30 menit</option>
                                <option value="PC2">Pulang Cepat > 30 menit</option>
                                <option value="PC3">Pulang Cepat > 1 jam</option>
                                <option value="S">Sakit</option>
                                <option value="I">Izin</option>
                                <option value="C">Cuti</option>
                                <option value="DL">Dinas Luar</option>
                                <option value="LJ">Libur Sabtu/Minggu</option>
                                <option value="LN">Libur Nasional</option>
                            </select>
                            @error('editKehadiran')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jam Masuk</label>
                                <input type="time" wire:model="editJamMasuk" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition @error('editJamMasuk') border-red-500 @enderror">
                                @error('editJamMasuk')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jam Pulang</label>
                                <input type="time" wire:model="editJamPulang" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition @error('editJamPulang') border-red-500 @enderror">
                                @error('editJamPulang')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Keterangan</label>
                            <textarea wire:model="editKeterangan" rows="3" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition resize-none @error('editKeterangan') border-red-500 @enderror" placeholder="Tambahkan keterangan (opsional)"></textarea>
                            @error('editKeterangan')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="closeEditModal" class="flex-1 px-5 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">Batal</button>
                            <button type="submit" class="flex-1 px-5 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30 active:scale-95">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" wire:click="closeDeleteModal"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                    <div class="flex items-start mb-6">
                        <div class="flex items-center justify-center w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-2xl">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Data Absensi?</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Anda yakin ingin menghapus data absensi untuk <span class="font-bold text-gray-900 dark:text-white">{{ $deletingPesertaNama }}</span>?
                                <span class="block mt-2 text-red-600 dark:text-red-400 font-semibold">Data yang terhapus tidak dapat dikembalikan!</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="closeDeleteModal" class="flex-1 px-5 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteAbsensi" class="flex-1 px-5 py-3 bg-red-600 text-white font-bold rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-500/30 active:scale-95">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif