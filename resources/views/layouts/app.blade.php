<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="true" data-theme-mode="light" dir="ltr">
<head><base href="../../">
    <meta charset="utf-8">
    <meta content="follow, index" name="robots"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
    <meta content="" name="description"/>
    <title>CRESCO - @yield('pageTitle')</title>
    <link rel="icon" href="{{ asset('storage/images/logos/CRESCO_faviicon.ico') }}" sizes="32x32"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    @vite('resources/css/app.css')
    @vite('resources/assets/theme/css/theme.css')
</head>
<body class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:#f6f6f6] [--tw-page-bg-dark:var(--tw-coal-200)] [--tw-content-bg:var(--tw-light)] [--tw-content-bg-dark:var(--tw-coal-500)] [--tw-content-scrollbar-color:#e8e8e8] [--tw-header-height:58px] [--tw-sidebar-width:58px] [--tw-navbar-height:56px] bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark] lg:overflow-hidden">
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

    <div id="app" class="flex grow">
        @include('includes.header')
        <div class="flex flex-col lg:flex-row grow pt-[--tw-header-height]">
            @include('includes.sidebar')
            @include('includes.navbar')
            <div class="flex grow rounded-b-xl bg-[--tw-content-bg] dark:bg-[--tw-content-bg-dark] border-x border-b border-gray-400 dark:border-gray-200 lg:mt-[--tw-navbar-height] mx-5 lg:ms-[--tw-sidebar-width] mb-5">
                <div class="flex flex-col grow lg:scrollable-y lg:[scrollbar-width:auto] lg:light:[--tw-scrollbar-thumb-color:var(--tw-content-scrollbar-color)] pt-7 lg:[&_.container-fluid]:pe-4" id="scrollable_content">
                    <main class="grow" role="content">
                        <div class="container-fluid">
                            @yield('content')
                        </div>
                    </main>
{{--                    @include('includes.footer')--}}
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    @vite('resources/assets/theme/js/theme.js')

</body>
</html>
