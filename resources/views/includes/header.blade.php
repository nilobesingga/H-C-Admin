<header
    class="flex items-center transition-[height] shrink-0 bg-[--tw-header-bg] dark:bg-[--tw-header-bg-dark] h-[--tw-header-height] border-b border-b-neutral-200"
    data-sticky="true"
    data-sticky-class="transition-[height] fixed z-10 top-0 left-0 right-0 shadow-sm backdrop-blur-md bg-white/70 dark:bg-coal-500/70 dark:border-b dark:border-b-coal-100"
    data-sticky-name="header" data-sticky-offset="100px" id="header">
    <!-- Container -->
    <div class="container-fluid pl-3 pr-2 flex flex-wrap justify-between items-center lg:gap-4" id="header_container">
        <!-- Logo -->
        <div class="flex items-center gap-3">
            <button class="btn btn-icon btn-light btn-clear btn-sm -ms-2 lg:hidden" data-drawer-toggle="#sidebar">
                <i class="ki-filled ki-menu">
                </i>
            </button>
            <a href="{{ route('dashboard') }}">
                <img class="dark:hidden w-8" src="{{ asset('storage/images/logos/CRESCO_icon.png') }}"/>
            </a>

            <!-- Navs -->
            <div class="hidden lg:flex items-center">
                <!-- Nav -->
                <div class="menu menu-default" data-menu="true">
                    <div class="flex items-center gap-3">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}"
                           href="{{ route('dashboard') }}"
                        >
                           Dashboard
                        </a>

                        @if(!$page->user->modules->isEmpty())
                            <a class="nav-link {{ request()->is('reports*') ? 'nav-active' : '' }}"
                               href="{{ route('reports.' . $page->user->modules->sortBy('order')->first()->slug) }}"
                            >
                                Cash Reports
                            </a>
                        @endif
                    </div>


                </div>
            </div>
            <!-- End of Navs -->
        </div>
        <!-- End of Logo -->

        @if(env('APP_ENV') != 'production')
            <div class="flex items-center ml-auto">
                <span class="badge badge-sm badge-pill badge-primary">{{ env('APP_ENV') }}</span>
            </div>
        @endif
        <!-- Topbar -->
        <div class="flex items-center gap-2 lg:gap-3.5">
            <div class="menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="0px, -38px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                    <div class="menu-toggle btn btn-icon rounded-full">
                        <img alt="" class="size-8 rounded-full transition-all duration-300 justify-center border border-black shrink-0 ring-1 ring-transparent hover:border-white hover:ring-brand-active shadow-lg hover:shadow-brand-shadow" src="{{ Auth::user()->profile->bitrix_profile_photo ?? '' }}">
                    </div>
                    <div class="menu-dropdown menu-default rounded-none backdrop-blur light:border-black w-screen max-w-[250px]">
                        <div class="flex items-center justify-between px-5 py-1.5 gap-1.5 relative">
                            <div class="flex flex-col gap-2">
                                <div>
                                    <img alt="" class="w-12 h-12 inline-block rounded-full ring-2 ring-brand-active border border-white shadow-lg shadow-brand-shadow" src="{{ Auth::user()->profile->bitrix_profile_photo ?? '' }}" />
                                </div>
                                <div class="flex flex-col gap-1.5 mt-4">
                                    <span class="text-sm text-black font-semibold leading-none">{{ Auth::user()->profile->bitrix_name ?? '' }} {{ Auth::user()->profile->bitrix_last_name ?? '' }}</span>
                                    <span class="text-xs text-neutral-600 leading-none">{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                            @if(Auth::user()->is_admin)
                                <span class="absolute top-1 right-3 badge badge-xs !shadow-md !shadow-tec-active/30 !bg-tec-active/80 !text-white !border-tec-active badge-outline">Admin</span>
                            @endif
                        </div>
                        <div class="menu-separator"></div>
                        <div class="flex flex-col">
                            @if(auth()->user()->is_admin)
                                <div class="menu-item mb-0.5 px-1.5">
                                    <div class="menu-link hover:!bg-black/5 !rounded-none">
                                        <span class="menu-icon"><i class="ki-outline ki-security-user !text-brand-active"></i></span>
                                        <a class="menu-title !text-neutral-800" href="{{ route('admin.settings.main') }}">Administration</a>
                                    </div>
                                </div>
                            @endif
                            <div class="menu-item px-4 py-1.5">
                                <a class="btn btn-light btn-cresco !bg-white !border-black !text-black focus:!border-brand-active" href="{{ route('logout') }}">
                                    Log out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Topbar -->
    </div>
    <!-- End of Container -->
</header>
