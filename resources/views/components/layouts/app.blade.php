<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIRAM SDMO - BKN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body
    x-data="{ isCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }"
    x-init="$watch('isCollapsed', value => localStorage.setItem('sidebar-collapsed', value))"
    class="bg-gray-50 dark:bg-gray-900 antialiased font-sans transition-colors duration-200">

    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button @click="isCollapsed = !isCollapsed" class="p-2 text-gray-600 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <a href="/" class="flex ms-2 md:me-24 items-center">
                        <div class="bg-blue-600 p-1.5 rounded-lg me-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <span class="self-center text-xl font-extrabold sm:text-2xl whitespace-nowrap dark:text-white">SIRAM <span class="text-blue-600">SDMO</span></span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="flex items-center ms-3">
                        <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <img class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700" src="https://ui-avatars.com/api/?name=Admin+SDMO&background=2563eb&color=fff" alt="user photo">
                        </button>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">Admin SDMO</p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">admin@bkn.go.id</p>
                            </div>
                            <ul class="py-1" role="none">
                                <li><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Settings</a></li>
                                <li><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Sign out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside
        id="logo-sidebar"
        class="fixed top-0 left-0 z-40 h-screen pt-20 transition-all duration-300 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
        :class="isCollapsed ? 'w-20' : 'w-64'"
        aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800 scrollbar-hide">
            <ul class="space-y-2 font-medium">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded-xl group transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-500' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="isCollapsed ? 'justify-center mx-1' : ''">
                        <svg class="w-6 h-6 transition duration-75 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span class="ms-3 whitespace-nowrap" x-show="!isCollapsed">Dashboard</span>
                    </a>
                </li>

                <!-- Data Absensi -->
                <li x-data="{ open: {{ request()->routeIs('absensi.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-xl group {{ request()->routeIs('absensi.*') ? 'bg-blue-50/50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="isCollapsed ? 'justify-center mx-1' : ''">
                        <svg class="flex-shrink-0 w-6 h-6 transition duration-75 {{ request()->routeIs('absensi.*') ? 'text-blue-600' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="flex-1 ms-3 text-left whitespace-nowrap" x-show="!isCollapsed">Data Absensi</span>
                        <svg x-show="!isCollapsed" class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <ul x-show="open && !isCollapsed" x-transition class="py-2 space-y-1 ms-4 border-l border-gray-100 dark:border-gray-700">
                        <li><a href="{{ route('absensi.import') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg ps-4 group hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm {{ request()->routeIs('absensi.import') ? 'text-blue-600 font-bold' : 'text-gray-600 dark:text-gray-400' }}">Import/Export PDF</a></li>
                        <li><a href="{{ route('absensi.rekap') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg ps-4 group hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm {{ request()->routeIs('absensi.rekap') ? 'text-blue-600 font-bold' : 'text-gray-600 dark:text-gray-400' }}">Rekap Absensi</a></li>
                    </ul>
                </li>

                <!-- Master Data -->
                <li x-data="{ open: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-xl group {{ request()->routeIs('master.*') ? 'bg-blue-50/50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="isCollapsed ? 'justify-center mx-1' : ''">
                        <svg class="flex-shrink-0 w-6 h-6 transition duration-75 {{ request()->routeIs('master.*') ? 'text-blue-600' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        <span class="flex-1 ms-3 text-left whitespace-nowrap" x-show="!isCollapsed">Master Data</span>
                        <svg x-show="!isCollapsed" class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <ul x-show="open && !isCollapsed" x-transition class="py-2 space-y-1 ms-4 border-l border-gray-100 dark:border-gray-700">
                        <li><a href="{{ route('master.peserta') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg ps-4 group hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm {{ request()->routeIs('master.peserta') ? 'text-blue-600 font-bold' : 'text-gray-600 dark:text-gray-400' }}">Peserta Magang</a></li>
                        <li><a href="{{ route('master.kedeputian') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg ps-4 group hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm {{ request()->routeIs('master.kedeputian') ? 'text-blue-600 font-bold' : 'text-gray-600 dark:text-gray-400' }}">Kedeputian</a></li>
                    </ul>
                </li>

                <!-- Laporan & Analisis -->
                <li x-data="{ open: {{ request()->routeIs('analisis.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-xl group {{ request()->routeIs('analisis.*') ? 'bg-blue-50/50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="isCollapsed ? 'justify-center mx-1' : ''">
                        <svg class="flex-shrink-0 w-6 h-6 transition duration-75 {{ request()->routeIs('analisis.*') ? 'text-blue-600' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="flex-1 ms-3 text-left whitespace-nowrap" x-show="!isCollapsed">Laporan & Analisis</span>
                        <svg x-show="!isCollapsed" class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <ul x-show="open && !isCollapsed" x-transition class="py-2 space-y-1 ms-4 border-l border-gray-100 dark:border-gray-700">
                        <li><a href="{{ route('analisis.statistik') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg ps-4 group hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm {{ request()->routeIs('analisis.statistik') ? 'text-blue-600 font-bold' : 'text-gray-600 dark:text-gray-400' }}">Statistik Kode</a></li>
                        <li><a href="#" class="flex items-center w-full p-2 transition duration-75 rounded-lg ps-4 group hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm text-gray-600 dark:text-gray-400">Log Aktivitas</a></li>
                    </ul>
                </li>
            </ul>

            <div x-show="!isCollapsed" class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800 transition-all duration-300">
                <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Status Sistem</p>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs font-semibold text-gray-900 dark:text-white">Semua Sistem Online</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <main class="transition-all duration-300 transform" :class="isCollapsed ? 'ml-20' : 'ml-0 sm:ml-64'">
        <div class="p-4 mt-16 sm:p-6 lg:p-8">
            {{ $slot }}
        </div>
    </main>

    <script>
        // Set initial theme icon
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        // Add observer to sync icons when toggleDarkMode is called
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    if (document.documentElement.classList.contains('dark')) {
                        themeToggleLightIcon.classList.remove('hidden');
                        themeToggleDarkIcon.classList.add('hidden');
                    } else {
                        themeToggleLightIcon.classList.add('hidden');
                        themeToggleDarkIcon.classList.remove('hidden');
                    }
                }
            });
        });
        observer.observe(document.documentElement, {
            attributes: true
        });
    </script>
    @livewireScripts
</body>

</html>