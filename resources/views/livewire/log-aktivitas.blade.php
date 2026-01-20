<div class="text-gray-900 dark:text-white">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight">Log Aktivitas</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Riwayat aktivitas pengguna dalam sistem SIRAM SDMO.</p>
        </div>
        <div class="flex gap-2">
            <input wire:model.debounce.300ms="search" type="text"
                placeholder="Cari aktivitas..."
                class="w-full md:w-96 px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-semibold placeholder-gray-400 dark:placeholder-gray-600" />
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-3xl border border-gray-300 dark:border-gray-700 shadow-md">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs font-semibold">
                    <th class="px-6 py-4 text-left">ID</th>
                    <th class="px-6 py-4 text-left">User</th>
                    <th class="px-6 py-4 text-left">Action</th>
                    <th class="px-6 py-4 text-left">Keterangan</th>
                    <th class="px-6 py-4 text-left">IP Address</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <td class="px-6 py-3 font-mono text-gray-500 dark:text-gray-400">{{ $log->id }}</td>
                    <td class="px-6 py-3 font-semibold text-gray-900 dark:text-white">{{ $log->user->name ?? 'Guest' }}</td>
                    <td class="px-6 py-3 text-gray-900 dark:text-white">{{ $log->action }}</td>
                    <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ $log->description }}</td>
                    <td class="px-6 py-3 font-mono text-gray-600 dark:text-gray-500">{{ $log->ip_address }}</td>
                    <td class="px-6 py-3 font-mono text-gray-600 dark:text-gray-500 whitespace-nowrap">
                        {{ $log->created_at->format('d M Y H:i:s') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center text-gray-400 dark:text-gray-600 italic text-sm font-semibold">
                        Tidak ada data log aktivitas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-800/50 rounded-3xl">
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                Results: <span class="font-bold text-gray-900 dark:text-white">{{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }}</span> of <span class="font-bold text-gray-900 dark:text-white">{{ $logs->total() }}</span>
            </span>
            <div class="relative">
                <select wire:model.live="perPage" style="appearance: none !important; -webkit-appearance: none !important; -moz-appearance: none !important; background-image: none !important;" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-1.5 pl-3 pr-9 font-bold cursor-pointer transition-all shadow-sm hover:border-blue-400">
                    <option value="15">15</option>
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
            {{ $logs->links('livewire.custom-pagination') }}
        </div>
    </div>
</div>