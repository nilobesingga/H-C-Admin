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
<body class="antialiased h-full text-base text-neutral-700 [--tw-header-height:54px] [--tw-sidebar-width:250px] [--tw-header-bg:var(--tw-light)] [--tw-header-bg-dark:var(--tw-coal-500)] bg-neutral-100 dark:bg-coal-500 overflow-x-hidden">
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

    <div id="app" class="relative flex flex-col h-full app">
        <RequestNotification />
        <!-- Header (Sticky position) -->
        @include('company.header.header')

        <!-- Main wrapper with content and sidebar -->
        <div class="flex mt-[--tw-header-height]">
            <!-- Sidebar (Fixed position) -->
            @include('company.sidebar.sidebar')

            <!-- Main Content Container -->
            <div class="flex flex-col w-full min-h-screen lg:ml-[--tw-sidebar-width]">
            <!-- Navbar/Sub-navigation -->
            {{-- @include('company.navbar.navbar') --}}

            <!-- Main Content Area -->
            <div class="flex flex-col flex-grow w-full px-4 pb-4 bg-fixed bg-top bg-no-repeat bg-cover container-fluid bg-neutral-100 page-bg {{ request()->routeIs('company.quickchat') ? 'p-0' : '' }}">
                <!-- Success Notification -->
                @if (session('success'))
                <div id="success-notification" class="fixed z-50 flex items-center p-4 mb-4 text-green-800 bg-green-100 border-l-4 border-green-500 rounded-md shadow-lg top-4 right-4" role="alert">
                    <i class="mr-2 fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-green-800 rounded-lg p-1.5 hover:bg-green-200 inline-flex h-8 w-8 justify-center items-center" onclick="this.parentElement.remove()">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <script>
                    // Auto-hide the notification after 5 seconds
                    setTimeout(() => {
                        const notification = document.getElementById('success-notification');
                        if (notification) {
                            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                            setTimeout(() => notification.remove(), 500);
                        }
                    }, 5000);
                </script>
                @endif

                <main class="flex-grow" role="content">
                    @yield('content')
                </main>
            </div>

            <!-- Footer -->
            @include('company.footer.footer')
        </div>
    </div>

    @vite(['resources/js/app.js', 'resources/assets/custom/custom.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
