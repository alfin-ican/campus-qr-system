<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Auth' }} - {{ config('app.name') }}</title>
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </div>

    <script>
        lucide.createIcons();
    </script>

    <script>
        // Prevent back to authenticated pages
        (function() {
            if (window.history && window.history.pushState) {
                window.onpopstate = function() {
                    window.history.pushState('forward', null, window.location.href);
                };
                window.history.pushState('forward', null, window.location.href);
            }
        })();

        // Clear browser cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>