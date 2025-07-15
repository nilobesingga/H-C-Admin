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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    @vite(['resources/css/app.scss'])
</head>
<body class="antialiased flex h-full text-base text-neutral-700 [--tw-header-height:54px] [--tw-sidebar-width:200px] [--tw-header-bg:var(--tw-light)] [--tw-header-bg-dark:var(--tw-coal-500)] bg-light dark:bg-coal-500">
    <script>
        window.env = @json($env);
        const defaultThemeMode = 'light';
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('theme')) {
                themeMode = localStorage.getItem('theme');
            } else if (document.documentElement.hasAttribute('data-theme-mode')) {
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

    <div id="app" class="flex grow app">
        <div class="flex flex-col grow">
            @include('includes.header')
            @include('includes.navbar')
            <div class="flex w-full pt-20 bg-fixed bg-top bg-no-repeat bg-cover container-fluid bg-[#F5F6FA] page-bg">
                <main class="grow" role="content">
                    @yield('content')
                </main>
            </div>
            @include('includes.footer')
        </div>
    </div>

    @vite(['resources/js/app.js', 'resources/assets/custom/custom.js'])

</body>
</html>
