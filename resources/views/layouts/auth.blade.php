<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="true" data-theme-mode="light" dir="ltr">
<head>
    <base href="../../">
    <meta charset="utf-8">
    <meta content="follow, index" name="robots"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
    <meta content="" name="description"/>
    <title>CRESCO - @yield('pageTitle')</title>
    <link rel="icon" href="{{ asset('storage/images/logos/CRESCO_favicon.ico') }}" sizes="32x32"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    @vite('resources/css/app.scss')
</head>
<body class="flex h-full text-base antialiased text-gray-700 dark:bg-coal-500">
    <script>
        const defaultThemeMode = 'light';
        let themeMode;

        if ( document.documentElement ) {
            if ( localStorage.getItem('theme')) {
                themeMode = localStorage.getItem('theme');
            } else if ( document.documentElement.hasAttribute('data-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>
    <style>
        html, body {
            overflow-y: hidden;
            margin: 0;
            padding: 0;
        }
        .page-bg {
            background-image: url('hc-bg.svg');
        }
        .dark .page-bg {
            background-image: url('hc-bg.svg');
        }
    </style>
    @yield('content')
</body>
</html>
