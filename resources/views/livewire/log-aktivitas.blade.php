<div class="text-gray-900 dark:text-white">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight">Log Aktivitas</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Riwayat aktivitas pengguna dalam sistem SIRAM SDMO.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search Input -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live="search" type="text"
                    placeholder="Cari aktivitas..."
                    class="w-full md:w-96 ps-10 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm font-medium placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-white transition-all" />
            </div>

            <!-- Per Page Selector -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">Tampilkan</span>
                <select wire:model.live="perPage"
                    class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 p-2.5 font-bold cursor-pointer transition-colors hover:border-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">data</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">{{ $log->id }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $log->user->name ?? 'Guest' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $actionColor = match(true) {
                                    str_contains($log->action, 'Create') => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    str_contains($log->action, 'Update') => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    str_contains($log->action, 'Delete') => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    str_contains($log->action, 'Import') => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                    str_contains($log->action, 'Export') => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <span class="{{ $actionColor }} text-xs font-bold px-2.5 py-1 rounded-full uppercase">
                                {{ $log->action }}
                            </span>
                        </td>
            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 max-w-lg" style="white-space: normal;">
    {{ $log->description }}
</td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-500">{{ $log->ip_address }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('d M Y H:i:s') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-400 dark:text-gray-500 uppercase">Tidak Ada Data Log</h3>
                                <p class="text-sm text-gray-400 dark:text-gray-600 mt-1">Belum ada aktivitas yang tercatat dalam sistem.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50 dark:bg-gray-900/50">
            <!-- Info -->
            <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                Menampilkan 
                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $logs->firstItem() ?? 0 }}</span>
                -
                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $logs->lastItem() ?? 0 }}</span>
                dari
                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $logs->total() }}</span>
                data
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center gap-2">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>