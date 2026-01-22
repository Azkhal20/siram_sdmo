<div>
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Import Data Absensi</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Upload file PDF untuk memproses data kehadiran peserta magang.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-1 space-y-4">
            <div class="p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-3xl">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Kedeputian</label>
                <select wire:model.live="selectedKedeputian" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="">-- Pilih Kedeputian --</option>
                    @foreach($kedeputianList as $kd)
                        <option value="{{ $kd->id }}">{{ $kd->nama }}</option>
                    @endforeach
                </select>
                @error('selectedKedeputian') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-3xl text-center relative hover:border-blue-500 transition-colors">
                <input type="file" wire:model="pdfFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" {{ !$selectedKedeputian ? 'disabled' : '' }}>
                <div class="space-y-4 {{ !$selectedKedeputian ? 'opacity-50' : '' }}">
                    <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/40 rounded-full flex items-center justify-center mx-auto text-blue-600 dark:text-blue-500">
                        <div wire:loading wire:target="pdfFile">
                            <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div wire:loading.remove wire:target="pdfFile">
                            @if($isProcessing)
                                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Klik atau seret file PDF</p>
                        <p class="text-xs text-gray-400 mt-1">Hanya file .pdf (Max 10MB)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            @if(session()->has('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800 rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-4">Informasi Penting</h4>
                <ul class="space-y-3 text-sm text-gray-500 dark:text-white font-medium">
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                        <span>Pastikan format PDF sesuai dengan standar aplikasi absensi BKN.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                        <span>Sistem akan mendeteksi Nama dan Kehadiran (TK, TM, PC, dll) secara otomatis.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                        <span>Anda dapat memeriksa dan <strong>mengedit Tanggal/Keterangan</strong> sebelum menyimpan.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if($showPreview)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-lg overflow-hidden transition-all duration-500 animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30">
            <div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Preview Hasil Parsing</h3>
                <p class="text-xs font-bold text-gray-400 mt-0.5">Ditemukan {{ count($previewData) }} baris data potensial.</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="$set('showPreview', false)" class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm font-bold hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all">Batalkan</button>
                <button wire:click="saveData()" class="px-6 py-2 bg-blue-600 dark:bg-blue-500 text-white text-sm font-black rounded-xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 active:scale-95 flex items-center">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan ke Database
                </button>
            </div>
        </div>

        <div class="overflow-x-auto max-h-[600px] overflow-y-auto p-6 space-y-8 bg-gray-50 dark:bg-gray-900/50">
            @foreach(collect($previewData)->groupBy('nip') as $nip => $items)
            @php $firstItem = $items->first(); @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
<<<<<<< HEAD
                <div class="px-8 py-8 bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
=======
                <!-- Person Header -->
                <div class="p-6 bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
>>>>>>> fdd9ffff249840edd0ef7c7dfd926bf390a1b3e8
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-mono text-gray-500 tracking-wider font-bold">{{ $nip }}</span>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white leading-none tracking-tight">{{ $firstItem['nama'] }}</h4>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $firstItem['unit'] }}</span>
                    </div>
                    <div class="h-full flex items-center">
                        <div class="text-xs font-bold px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg shadow-sm border border-blue-100 dark:border-blue-800">
                            {{ $items->count() }} Data Absensi
                        </div>
                    </div>
                </div>

<<<<<<< HEAD
             <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-[11px] font-black uppercase bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th class="px-4 py-4 tracking-wider">Tanggal</th>
            <th class="px-4 py-4 tracking-wider text-center">Kehadiran</th>
            <th class="px-4 py-4 text-center tracking-wider">Jam Masuk</th>
            <th class="px-4 py-4 text-center tracking-wider">Jam Pulang</th>
            <th class="px-4 py-4 text-center tracking-wider">Telat Masuk</th>
            <th class="px-4 py-4 text-center tracking-wider">Pulang Cepat</th>
            <th class="px-4 py-4 tracking-wider">Keterangan</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach($items as $row)
        @php
            $menitTelat = $row['menit_telat'] ?? 0;
            $menitPulangCepat = $row['menit_pulang_cepat'] ?? 0;
            $jamTelatStr = $row['jam_telat_str'] ?? '';
            $jamPulangCepatStr = $row['jam_pulang_cepat_str'] ?? '';
            $kehadiran = $row['kehadiran'] ?? 'HN';
            
            // Fungsi untuk menentukan warna badge berdasarkan kode
            $getBadgeClass = function($kode) {
                // Cek apakah kode gabungan
                if (str_contains($kode, '-')) {
                    return 'bg-pink-100 text-pink-700 border-pink-200 dark:bg-pink-900/30 dark:text-pink-400 dark:border-pink-800';
                }
                
                return match($kode) {
                    'TK' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                    'HN' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                    'DL' => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
                    'LJ', 'LN' => 'bg-indigo-100 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800',
                    'S', 'I', 'C' => 'bg-teal-100 text-teal-700 border-teal-200 dark:bg-teal-900/30 dark:text-teal-400 dark:border-teal-800',
                    'TM', 'TM1', 'TM2', 'TM3' => 'bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800',
                    'PC', 'PC1', 'PC2', 'PC3' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                    'TMDHM', 'TMDHP' => 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800',
                    default => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'
                };
            };
            
            $badgeClass = $getBadgeClass($kehadiran);
        @endphp
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            {{-- Tanggal --}}
            <td class="px-4 py-3">
                <input type="date" 
                    wire:model="previewData.{{ $row['_index'] }}.tanggal"
                    class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white font-mono text-xs font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
            </td>
            
            {{-- Kehadiran --}}
            <td class="px-4 py-2 text-center">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black border {{ $badgeClass }} uppercase whitespace-nowrap">
                    {{ $kehadiran }}
                </span>
            </td>
            
            {{-- Jam Masuk --}}
            <td class="px-4 py-2 text-center">
                <span class="font-mono text-xs font-bold {{ $row['jam_masuk'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600' }}">
                    {{ $row['jam_masuk'] ?? '-' }}
                </span>
            </td>
            
            {{-- Jam Pulang --}}
            <td class="px-4 py-2 text-center">
                <span class="font-mono text-xs font-bold {{ $row['jam_pulang'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-600' }}">
                    {{ $row['jam_pulang'] ?? '-' }}
                </span>
            </td>
            
            {{-- Telat Masuk --}}
            <td class="px-4 py-2 text-center">
                @if($menitTelat > 0)
                    <span class="font-mono text-xs font-bold px-2 py-1 rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        {{ $jamTelatStr }}
                    </span>
                @else
                    <span class="font-mono text-xs font-bold text-gray-400 dark:text-gray-600">-</span>
                @endif
            </td>
            
            {{-- Pulang Cepat --}}
            <td class="px-4 py-2 text-center">
                @if($menitPulangCepat > 0)
                    <span class="font-mono text-xs font-bold px-2 py-1 rounded bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                        {{ $jamPulangCepatStr }}
                    </span>
                @else
                    <span class="font-mono text-xs font-bold text-gray-400 dark:text-gray-600">-</span>
                @endif
            </td>
            
            {{-- Keterangan --}}
            <td class="px-4 py-2">
                <input type="text" 
                    wire:model="previewData.{{ $row['_index'] }}.keterangan" 
                    placeholder="-"
                    class="bg-transparent border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400 dark:placeholder-gray-600">
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
=======
                <!-- Attendance Table -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-[10px] font-black uppercase bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-white">
                            <tr>
                                <th class="px-6 py-4 tracking-wider text-center">Tanggal</th>
                                <th class="px-6 py-4 tracking-wider text-center">Kehadiran</th>
                                <th class="px-6 py-4 text-center tracking-wider">Jam Masuk</th>
                                <th class="px-6 py-4 text-center tracking-wider">Jam Pulang</th>
                                <th class="px-6 py-4 text-center tracking-wider text-red-500">Telat Masuk</th>
                                <th class="px-6 py-4 text-center tracking-wider text-orange-500">Pulang Cepat</th>
                                <th class="px-6 py-4 tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @foreach($items as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3 w-[15%]">
                                    <div class="relative">
                                        <input type="text"
                                            wire:model="previewData.{{ $row['_index'] }}.tanggal"
                                            class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white font-mono text-xs font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full text-center p-2 transition-colors hover:border-blue-400">
                                    </div>
                                </td>
                                <td class="px-6 py-2 text-center w-[10%]">
                                    @php
                                    $classes = match($row['kehadiran']) {
                                    'TK' => 'bg-red-100 text-red-700 border-red-200',
                                    'HN' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'TM', 'PC' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'TMDHM' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                                    };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black border {{ $classes }} uppercase tracking-wide text-center">
                                        {{ $row['kehadiran'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-2 font-mono text-xs text-center">
                                    <span class="{{ $row['jam_masuk'] ? 'text-gray-500 dark:text-gray-300 font-bold' : 'text-gray-300' }}">{{ $row['jam_masuk'] ?? '--:--' }}</span>
                                </td>
                                <td class="px-6 py-2 font-mono text-xs text-center">
                                    <span class="{{ $row['jam_pulang'] ? 'text-gray-500 dark:text-gray-300 font-bold' : 'text-gray-300' }}">{{ $row['jam_pulang'] ?? '--:--' }}</span>
                                </td>
                                <td class="px-6 py-2 font-mono text-xs text-center text-red-500 font-bold">
                                    {{ $row['telat_formatted'] }}
                                </td>
                                <td class="px-6 py-2 font-mono text-xs text-center text-orange-500 font-bold">
                                    {{ $row['pulang_cepat_formatted'] }}
                                </td>
                                <td class="px-6 py-2 w-[25%]">
                                    <!-- Editable Keterangan Input -->
                                    <input type="text"
                                        wire:model="previewData.{{ $row['_index'] }}.keterangan"
                                        placeholder="Tambah keterangan..."
                                        class="bg-transparent border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-xs font-bold focus:ring-0 focus:border-blue-500 block w-full p-2 placeholder-gray-300 transition-all">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
>>>>>>> fdd9ffff249840edd0ef7c7dfd926bf390a1b3e8
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>