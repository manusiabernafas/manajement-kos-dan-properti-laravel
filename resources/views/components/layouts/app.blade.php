@props(['title' => 'Dashboard', 'breadcrumbs' => [], 'sidebarMenu' => null])
<!DOCTYPE html>
<html lang="id" data-default-theme="system">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>

    <script>
        (function () {
            var saved = localStorage.getItem('kostmanager-theme') || 'system';
            var isDark = saved === 'dark' || (saved === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) document.documentElement.classList.add('dark');
        })();
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <x-sidebar :menu="$sidebarMenu ?? config('sidebar')" />
        <div class="sidebar-backdrop" data-sidebar-mobile-toggle></div>

        <div class="app-main">
            <x-navbar :title="$title" :breadcrumbs="$breadcrumbs" />

            <main class="app-content">
                @if(session('success'))
                    <span data-flash="success" data-flash-message="{{ session('success') }}" style="display:none"></span>
                @endif
                @if(session('error'))
                    <span data-flash="error" data-flash-message="{{ session('error') }}" style="display:none"></span>
                @endif
                @if(session('warning'))
                    <span data-flash="warning" data-flash-message="{{ session('warning') }}" style="display:none"></span>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <div class="toast-container"></div>

    @stack('scripts')
</body>
</html>
