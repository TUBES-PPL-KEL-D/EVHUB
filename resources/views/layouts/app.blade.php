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
        <div class="container mx-auto font-bold text-xl">EV-HUB</div>
    </nav>

    <main class="container mx-auto p-4 min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-white text-center p-4 mt-6 shadow-inner">
        <p>&copy; 2026 EV-HUB Kelompok D</p>
    </footer>
</body>
</html>