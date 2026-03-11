<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'Kle - İnternetin Kalbi' }}">
    <title>{{ $title ?? 'Kle - İnternetin Kalbi' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('klelogo.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col bg-[#DAE0E6] overflow-y-scroll">
    <!-- Reddit-Style Header -->
    <header class="bg-white border-b border-gray-300 sticky top-0 z-50">
        <div class="h-12 flex items-center px-4 max-w-full">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 mr-4">
                <img src="{{ asset('klelogo.svg') }}" alt="Kle Logo" class="w-8 h-8">
            </a>

            <!-- Search Bar -->
            <form action="{{ route('posts.index') }}" method="GET" class="flex-1 max-w-2xl mx-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kle'de ara"
                        class="w-full bg-gray-100 border border-gray-300 rounded-full px-4 py-1.5 text-sm focus:outline-none focus:border-blue-500 focus:bg-white hover:bg-white hover:border-gray-400 transition-colors">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded hover:bg-gray-200 transition-colors" aria-label="Ara">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Right Menu -->
            <div class="flex items-center gap-2">
                @if(session('api_token'))
                    <a href="{{ route('dashboard') }}"
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 hover:bg-gray-100 rounded transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="text-sm font-medium">Oluştur</span>
                    </a>
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @keydown.escape.window="open = false"
                            class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-100 rounded cursor-pointer transition-colors">
                            <img
                                src="https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'U') }}&background=random"
                                alt="Avatar"
                                class="w-6 h-6 rounded-full">
                            <span class="text-sm font-medium hidden md:block">Profil</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-cloak x-show="open" @click.outside="open = false"
                             x-transition.origin.top.right
                             class="absolute right-0 top-full mt-2 w-56 bg-white rounded-md shadow-lg py-1 border border-gray-200 z-[60]">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Yazı Oluştur</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Çıkış Yap</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="/login"
                        class="px-4 py-1.5 text-sm font-bold text-[#0079D3] border border-[#0079D3] rounded-full hover:bg-blue-50 transition-colors">
                        Giriş Yap
                    </a>
                    <a href="/register"
                        class="px-4 py-1.5 text-sm font-bold bg-[#FF4500] text-white rounded-full hover:bg-[#ff5414] transition-colors">
                        Kayıt Ol
                    </a>
                @endif
            </div>
        </div>

        <!-- Subnav -->
        <div class="border-t border-gray-200 bg-white">
            <div class="flex items-center gap-4 px-4 h-10 text-sm overflow-x-auto">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-1 px-3 py-1.5 hover:bg-gray-100 rounded transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="font-medium">Ana Sayfa</span>
                </a>
                <a href="{{ route('categories') }}"
                    class="px-3 py-1.5 hover:bg-gray-100 rounded transition-colors font-medium whitespace-nowrap">
                    Topluluklar
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-300 bg-white mt-12">
        <div class="max-w-5xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('klelogo.svg') }}" alt="Kle Logo" class="w-6 h-6">
                    <h3 class="font-bold text-gray-900 mb-1">Kle</h3>
                </div>
                <p class="text-sm text-gray-600">Her şeye dalın</p>

                <div class="flex gap-6 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Ana Sayfa</a>
                    <a href="{{ route('categories') }}" class="text-gray-600 hover:text-gray-900">Topluluklar</a>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-6 pt-6 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Kle Blog Inc. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>