<div>
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Daftar Peserta Magang</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Manajemen basis data peserta magang Biro SDMO.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-blue-600 dark:bg-blue-500 text-white text-sm font-black rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 active:scale-95">
            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Peserta
        </button>
    </div>

    @if(session()->has('message'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-2xl font-bold text-sm">
        {{ session('message') }}
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="relative max-w-sm">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live="search" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 font-medium placeholder-gray-400" placeholder="Cari nama atau NIP...">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-[12px] font-black uppercase bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-white">
                    <tr>
                        <th class="px-6 py-4">NIP/No. Induk</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Universitas</th>
                        <th class="px-6 py-4">Kedeputian</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($pesertas as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                        <td class="px-6 py-4 font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ $p->no_induk }}</td>
                        <td class="px-6 py-4 font-bold text-gray-600 dark:text-gray-300">{{ $p->nama }}</td>
                        <td class="px-6 py-4 font-medium text-gray-500 dark:text-gray-400">{{ $p->univ ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                {{ $p->kedeputian->nama }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button wire:click="edit({{ $p->id }})" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-500 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button onclick="confirm('Yakin ingin menghapus?') || event.stopImmediatePropagation()" wire:click="delete({{ $p->id }})" class="p-2 text-gray-400 hover:text-red-500 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $pesertas->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
        <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-zoom-in">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $pesertaId ? 'Edit Data Peserta' : 'Tambah Peserta Baru' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-5">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                    <input wire:model="nama" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-bold" placeholder="Masukkan nama...">
                    @error('nama') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Nomor Induk / NIP</label>
                    <input wire:model="no_induk" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-black uppercase tracking-tighter" placeholder="Contoh: 19990101...">
                    @error('no_induk') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Universitas / Instansi</label>
                    <input wire:model="univ" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-bold" placeholder="Asal universitas...">
                    @error('univ') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Penempatan Kedeputian</label>
                    <select wire:model="kedeputian_id" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-bold">
                        <option value="">Pilih Kedeputian</option>
                        @foreach($kedeputians as $kd)
                        <option value="{{ $kd->id }}">{{ $kd->nama }}</option>
                        @endforeach
                    </select>
                    @error('kedeputian_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="flex-1 px-5 py-3 text-gray-500 dark:text-gray-400 text-sm font-black rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all uppercase tracking-widest border border-gray-100 dark:border-gray-700">Batal</button>
                    <button type="submit" class="flex-1 px-5 py-3 bg-blue-600 dark:bg-blue-500 text-white text-sm font-black rounded-2xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 active:scale-95 uppercase tracking-widest">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>