<div>
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Data Peserta Magang</h2>
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
                        <th class="px-6 py-4">Kedeputian</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($pesertas as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                        <td class="px-6 py-4 font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ $p->nomor_induk }}</td>
                        <td class="px-6 py-4 font-bold text-gray-600 dark:text-gray-300">{{ $p->nama }}</td>
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
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center text-gray-400 dark:text-gray-600 italic text-sm font-semibold">
                            Tidak ada data peserta magang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-800/50">
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    Results: <span class="font-bold text-gray-900 dark:text-white">{{ $pesertas->firstItem() ?? 0 }} - {{ $pesertas->lastItem() ?? 0 }}</span> of <span class="font-bold text-gray-900 dark:text-white">{{ $pesertas->total() }}</span>
                </span>
                <div class="relative">
                    <select wire:model.live="perPage" style="appearance: none !important; -webkit-appearance: none !important; -moz-appearance: none !important; background-image: none !important;" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-1.5 pl-3 pr-9 font-bold cursor-pointer transition-all shadow-sm hover:border-blue-400">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end flex-1 w-full md:w-auto">
                {{ $pesertas->links('livewire.custom-pagination') }}
            </div>
        </div>
    </div>

  <!-- Modal Form -->
@if($showModal)
<div class="fixed inset-0 z-50 overflow-y-auto" 
     x-data 
     x-show="true" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" 
             wire:click="$set('showModal', false)"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"></div>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tight">
                            {{ $pesertaId ? 'Edit Data Peserta' : 'Tambah Peserta Baru' }}
                        </h3>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">
                            {{ $pesertaId ? 'Perbarui informasi peserta magang' : 'Daftarkan peserta magang baru' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('showModal', false)" 
                        class="text-white/80 hover:text-white hover:bg-white/10 rounded-xl p-2 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save" class="p-6 space-y-5 bg-white dark:bg-gray-800">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input wire:model="nama" 
                               type="text" 
                               class="bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-3.5 font-medium transition-all @error('nama') border-red-500 @enderror" 
                               placeholder="Masukkan nama lengkap...">
                    </div>
                    @error('nama') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Nomor Induk -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                        Nomor Induk / NIP <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <input wire:model="nomor_induk" 
                               type="text" 
                               class="bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-3.5 font-mono font-bold uppercase tracking-wider transition-all @error('nomor_induk') border-red-500 @enderror" 
                               placeholder="Contoh: 19990101...">
                    </div>
                    @error('nomor_induk') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Kedeputian -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                        Penempatan Kedeputian <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <select wire:model="kedeputian_id" 
                                class="bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-3.5 font-semibold transition-all @error('kedeputian_id') border-red-500 @enderror">
                            <option value="">Pilih Kedeputian</option>
                            @foreach($kedeputians as $kd)
                            <option value="{{ $kd->id }}">{{ $kd->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('kedeputian_id') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="pt-6 flex gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" 
                            wire:click="$set('showModal', false)" 
                            class="flex-1 px-5 py-3.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </span>
                    </button>
                    <button type="submit" 
                            class="flex-1 px-5 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 text-white text-sm font-bold rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/30 active:scale-95">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $pesertaId ? 'Update Data' : 'Simpan Data' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif