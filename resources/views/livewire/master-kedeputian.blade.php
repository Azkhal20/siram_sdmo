<div>
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Data Kedeputian</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Daftar unit kerja dan kedeputian di lingkungan BKN.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-blue-600 dark:bg-blue-500 text-white text-sm font-black rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 active:scale-95">
            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Unit
        </button>
    </div>

    @if(session()->has('message'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-2xl font-bold text-sm">
        {{ session('message') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($kedeputians as $kd)
        <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm hover:shadow-xl transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/40 rounded-2xl text-blue-600 dark:text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="edit({{ $kd->id }})" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-500 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                    <button onclick="confirm('Yakin ingin menghapus? Semua data peserta di unit ini akan terdampak.') || event.stopImmediatePropagation()" wire:click="delete({{ $kd->id }})" class="p-2 text-gray-400 hover:text-red-500 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <h4 class="text-[16px] font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">{{ $kd->nama }}</h4>
            <div class="flex items-center gap-2 text-sm font-bold text-gray-400">
                <span class="text-blue-600 dark:text-blue-400">{{ $kd->peserta_magang_count }}</span> Peserta Terdaftar
            </div>
        </div>
        @endforeach

        <!-- Add Card -->
        <button wire:click="create" class="p-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl flex flex-col items-center justify-center gap-3 text-gray-400 hover:border-blue-500 hover:text-blue-500 transition-all group">
            <div class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center group-hover:bg-blue-50 dark:group-hover:bg-blue-900/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <span class="text-xs font-black uppercase tracking-widest">Tambah Unit Baru</span>
        </button>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-zoom-in">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $kedeputianId ? 'Edit Nama Unit' : 'Registrasi Unit Baru' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-5">
                <div>
                    <label class="block mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Kedeputian / Unit</label>
                    <input wire:model="nama" type="text" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-300 dark:text-white text-xs rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 font-black uppercase" placeholder="Contoh: Kedeputian SINKA...">
                    @error('nama') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 px-5 py-4 bg-blue-600 dark:bg-blue-500 text-white text-sm font-black rounded-2xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 active:scale-95 uppercase tracking-widest">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>