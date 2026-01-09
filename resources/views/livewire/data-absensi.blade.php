<div>
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Import Data Absensi</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Unggah file PDF rekap absensi untuk diproses oleh sistem.</p>
        </div>
        <div class="flex gap-3">
            <button class="inline-flex items-center px-4 py-2 border border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-500 text-sm font-bold rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all active:scale-95 shadow-sm">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Template Format
            </button>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-1">
            <div class="p-6 bg-white dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-3xl text-center relative hover:border-blue-500 transition-colors">
                <input type="file" wire:model="pdfFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <div class="space-y-4">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Klik atau seret file PDF</p>
                        <p class="text-xs text-gray-400 mt-1">Hanya file .pdf (Max 10MB)</p>
                    </div>
                </div>
                @error('pdfFile') <span class="text-red-500 text-xs mt-2 block font-semibold">{{ $message }}</span> @enderror
            </div>

            @if($pdfFile)
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"></path>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $pdfFile->getClientOriginalName() }}</p>
                            <p class="text-[10px] text-gray-500 uppercase">{{ round($pdfFile->getSize() / 1024, 2) }} KB</p>
                        </div>
                    </div>
                    <button wire:click="$set('pdfFile', null)" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <button
                    wire:click="parsePdf"
                    wire:loading.attr="disabled"
                    class="w-full py-2.5 bg-blue-600 text-white text-xs font-black rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 active:scale-[0.98] flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="parsePdf">PROSES FILE SEKARANG</span>
                    <span wire:loading wire:target="parsePdf" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        MENGOLAH DATA...
                    </span>
                </button>
            </div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">
            @if(session()->has('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
            @endif

            @if(session()->has('error'))
            <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
            @endif

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-4">Informasi Penting</h4>
                <ul class="space-y-3 text-sm text-gray-500 dark:text-gray-400 font-medium">
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                        <span>Pastikan format PDF sesuai dengan standar aplikasi absensi BKN.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                        <span>Sistem akan mendeteksi Nama dan Kode Absensi (TK, TM, PC, dll) secara otomatis.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                        <span>Anda dapat melakukan review data hasil parsing sebelum data disimpan ke database.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
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
        <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Unit Kerja</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Jam In/Out</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($previewData as $index => $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs">{{ $row['nip'] }}</td>
                        <td class="px-6 py-4">
                            <input type="text" wire:model="previewData.{{ $index }}.nama" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 font-bold">
                        </td>
                        <td class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase">{{ $row['unit'] }}</td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model="previewData.{{ $index }}.tanggal" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        </td>
                        <td class="px-6 py-4">
                            <select wire:model="previewData.{{ $index }}.kode" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 font-black uppercase">
                                <option value="HN">HN</option>
                                <option value="TK">TK</option>
                                <option value="TM">TM</option>
                                <option value="TMDHM">TMDHM</option>
                                <option value="PC">PC</option>
                                <option value="LN">LN</option>
                                <option value="LJ">LJ</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-[10px] font-black {{ $row['jam_masuk'] ? 'text-blue-600' : 'text-gray-300' }}">{{ $row['jam_masuk'] ?: '--:--' }}</span>
                            <span class="text-gray-300 mx-1">/</span>
                            <span class="text-[10px] font-black {{ $row['jam_pulang'] ? 'text-emerald-600' : 'text-gray-300' }}">{{ $row['jam_pulang'] ?: '--:--' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-bold text-gray-400">Tidak ada data yang cocok dengan kriteria absensi.</p>
                                <p class="text-xs text-gray-300 mt-1 italic">Silakan periksa kembali file PDF Anda atau edit manual baris di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>