<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-HUB - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <nav class="bg-white shadow p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="font-bold text-xl">EV-HUB</a>
            @auth
                @if(!Route::is('login') && !Route::is('register'))
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile') }}" class="text-gray-600 hover:text-blue-600 transition font-medium">
                        Profil saya
                    </a>

                    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                            Logout
                        </button>
                    </form>
                </div>
                @endif
            @endauth
        </div>
    </nav>

    <main class="container mx-auto p-4 min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-white text-center p-4 mt-6 shadow-inner">
        <p>&copy; 2026 EV-HUB Kelompok D</p>
    </footer>
</body>
</html>