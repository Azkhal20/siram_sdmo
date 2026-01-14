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
    <div class="mt-6 px-6">
        {{ $logs->links() }}
    </div>
</div>