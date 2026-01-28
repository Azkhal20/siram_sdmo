@php
$current = $paginator->currentPage();
$last = $paginator->lastPage();
$showPages = [];

// Always show first page
if ($last > 0) {
$showPages[] = 1;
}

// Calculate sliding window (3 pages around current)
$start = max(2, $current - 1);
$end = min($last - 1, $current + 1);

// Add ellipsis after first page if needed
$leftEllipsis = $start > 2;

// Add pages in the middle
for ($i = $start; $i <= $end; $i++) {
    if (!in_array($i, $showPages)) {
    $showPages[]=$i;
    }
    }

    // Add ellipsis before last page if needed
    $rightEllipsis=$end < $last - 1;

    // Always show last page if it's not page 1
    if ($last> 1 && !in_array($last, $showPages)) {
    $showPages[] = $last;
    }
    @endphp

    <div>
        @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
            <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </span>
            @else
            <button wire:click="previousPage" wire:loading.attr="disabled" class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            @endif

            {{-- First Page --}}
            @if ($current == 1)
            <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-xs font-black rounded-lg border border-gray-200 dark:border-gray-600">
                1
            </span>
            @else
            <button wire:click="gotoPage(1)" class="px-3 py-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-all text-xs font-black">
                1
            </button>
            @endif

            {{-- Left Ellipsis --}}
            @if ($leftEllipsis)
            <span class="px-1.5 text-gray-400 dark:text-gray-600 text-xs font-black">...</span>
            @endif

            {{-- Middle Pages --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i==$current)
                <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-xs font-black rounded-lg border border-gray-200 dark:border-gray-600">
                {{ $i }}
                </span>
                @else
                <button wire:click="gotoPage({{ $i }})" class="px-3 py-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-all text-xs font-black">
                    {{ $i }}
                </button>
                @endif
                @endfor

                {{-- Right Ellipsis --}}
                @if ($rightEllipsis)
                <span class="px-1.5 text-gray-400 dark:text-gray-600 text-xs font-black">...</span>
                @endif

                {{-- Last Page --}}
                @if ($last > 1)
                @if ($current == $last)
                <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-xs font-black rounded-lg border border-gray-200 dark:border-gray-600">
                    {{ $last }}
                </span>
                @else
                <button wire:click="gotoPage({{ $last }})" class="px-3 py-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-all text-xs font-black">
                    {{ $last }}
                </button>
                @endif
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                @else
                <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
                @endif
        </nav>
        @endif
    </div>