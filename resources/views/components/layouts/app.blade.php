<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <title>{{ $title ?? 'Laravel Blog' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#DAE0E6] font-sans antialiased text-gray-900">

    <!-- Reddit-style Navbar -->
    <nav class="fixed w-full z-50 bg-white border-b border-gray-200 h-14 flex items-center px-4 justify-between">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 mr-4">
            <div class="w-8 h-8 bg-[#FF4500] rounded-full flex items-center justify-center text-white font-bold text-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a10 10 0 1010 10A10 10 0 0010 0zm0 18a8 8 0 110-16 8 8 0 010 16zm0-13a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1zm0 10a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>
            </div>
            <span class="text-xl font-bold hidden md:block">kleblog</span>
        </a>

        <!-- Search Bar -->
        <form action="{{ route('posts.index') }}" method="GET" class="flex-grow max-w-2xl mx-4">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" 
                    class="block w-full pl-10 pr-3 py-1.5 border border-transparent rounded-full leading-5 bg-gray-100 text-gray-900 placeholder-gray-500 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors hover:bg-white hover:border-blue-500" 
                    placeholder="Kleblog'da Ara">
            </div>
        </form>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-3">
            @if(session('api_token'))
                <a href="{{ route('dashboard') }}" 
                   class="hidden md:flex items-center gap-2 text-gray-700 hover:bg-gray-100 px-3 py-1.5 rounded-full transition-colors" title="Create Post">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>

                <div class="relative group cursor-pointer">
                    <button class="flex items-center gap-2 hover:bg-gray-100 px-2 py-1 rounded border border-transparent hover:border-gray-200 transition-all">
                        <div class="w-8 h-8 rounded bg-gray-200 overflow-hidden relative">
                             <img src="https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'U') }}&background=random" class="object-cover w-full h-full">
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-medium text-gray-900">u/{{ session('user_name') ?? 'User' }}</p>
                            <div class="flex items-center text-xs text-gray-400">
                                <svg class="w-3 h-3 text-[#FF4500] mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5 15l7-7 7 7"/></svg>
                                <span>1 karma</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown -->
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden md:block bg-blue-600 text-white px-8 py-1.5 rounded-full font-bold text-sm hover:bg-blue-700 transition">Log In</a>
                <a href="{{ route('register') }}" class="hidden md:block text-blue-600 hover:bg-blue-50 px-4 py-1.5 rounded-full font-bold text-sm border border-blue-600 transition">Sign Up</a>
                <a href="{{ route('login') }}" class="md:hidden text-gray-500 hover:bg-gray-100 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </a>
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-24 min-h-screen pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center text-gray-500">
            <p>&copy; {{ date('Y') }} Laravel Blog. Tüm hakları saklıdır.</p>
        </div>
    </footer>

</body>

</html>