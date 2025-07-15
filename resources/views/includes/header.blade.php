<header
    class="flex items-center transition-[height] shrink-0 h-[--tw-header-height] border-b border-b-neutral-200 top-header bg-[#252B36] fixed z-10 top-0 left-0 right-0 shadow-sm backdrop-blur-md dark:border-b dark:border-b-coal-100"
    id="header"
    style="will-change: transform;">
    <!-- Container -->
    <div class="flex flex-wrap items-center justify-between pl-3 pr-2 container-fluid lg:gap-4 " id="header_container">
        <!-- Logo -->
        <div class="flex items-center gap-3">
            <button class="btn btn-icon btn-light btn-clear btn-sm -ms-2 lg:hidden" data-drawer-toggle="#sidebar">
                <i class="ki-filled ki-menu">
                </i>
            </button>
            <a href="{{ route('dashboard') }}">
                <img class="w-40 ml-5 dark:hidden" src="{{ asset('storage/images/logos/Logo.svg') }}"/>
            </a>

            <!-- Navs -->
            <div class="items-center hidden lg:flex">
                <!-- Nav -->
                <div class="menu menu-default" data-menu="true">
                    <div class="flex items-center gap-3">
                        <span class="absolute m-2 mx-10 text-sm font-semibold text-white right-40">
                            {{ now()->format('l, d F Y') }} // {{ now()->format('H:i:s') }}
                        </span>
                        @if($page->user->modules->isNotEmpty())
                            @php
                                $userModules = $page->user->modules;
                                $segment = request()->segment(2) ?: request()->segment(1);
                                $currentModule = $userModules->firstWhere('slug', $segment);
                                $parentModule = $currentModule?->parent;
                                $linkModule = $parentModule?->children
                                ->filter(fn($mod) => $userModules->contains('id', $mod->id))
                                ->sortBy('order')
                                ->first();
                            @endphp

                            @if($parentModule && $linkModule)
                                <a class="nav-link nav-active" href="{{ route($linkModule->route_name) }}">
                                    {{ $parentModule->name }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <!-- End of Navs -->
        </div>
        <!-- End of Logo -->
        <!-- Topbar -->
        <div class="flex items-center gap-2 lg:gap-3.5 ">
            <div class="menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="0px, -38px" data-menu-item-placement="bottom-end"
                    data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown"
                    data-menu-item-trigger="click|lg:click">
                    <button class="rounded-full menu-toggle btn btn-icon">
                        <i class="text-white ki-outline ki-notification"></i>
                    </button>
                    <div
                        class="menu-dropdown menu-default rounded-none backdrop-blur light:border-black w-screen max-w-[250px]">
                        <div class="px-5 py-3">
                            <h6 class="font-semibold">Notifications</h6>
                        </div>
                        <div class="menu-separator"></div>
                        <div class="p-5 text-sm text-center text-gray-500">
                            No new notifications
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-4 mr-10 menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="0px, -38px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                    {{-- <div class="rounded-full menu-toggle btn btn-icon">
                        <img alt="" class="justify-center transition-all duration-300 border border-black rounded-full shadow-lg size-8 shrink-0 ring-1 ring-transparent hover:border-white hover:ring-brand-active hover:shadow-brand-shadow" src="{{ Auth::user()->profile->bitrix_profile_photo ?? '' }}">
                    </div> --}}
                    <div class="flex items-center space-x-2 rounded-full menu-toggle btn btn-icon ">
                        <img alt="{{ profile()->name ?? 'User' }}"
                            class="justify-center transition-all duration-300 border border-black rounded-full shadow-lg size-8 shrink-0 ring-1 ring-transparent hover:border-white hover:ring-brand-active hover:shadow-brand-shadow"
                            src="{{ profile()->profilePhoto ?? asset('path/to/default-avatar.jpg') }}">
                        <span class="text-sm font-medium text-white">{{ getFirstChars(profile()->name, true) ?? 'Unknown' }}</span>
                    </div>
                    <div class="menu-dropdown menu-default rounded-none backdrop-blur light:border-black w-screen max-w-[250px]">
                        <div class="flex items-center justify-between px-5 py-1.5 gap-1.5 relative">
                            <div class="flex flex-col gap-2">
                                <div>
                                    <img alt="" class="inline-block w-12 h-12 border border-white rounded-full shadow-lg ring-2 ring-brand-active shadow-brand-shadow" src="{{ profile()->profilePhoto ?? '' }}" />
                                </div>
                                <div class="flex flex-col gap-1.5 mt-4">
                                    <span class="text-sm font-semibold leading-none text-black">{{ Auth::user()->profile->bitrix_name ?? '' }} {{ profile()->name ?? '' }}</span>
                                    <span class="text-xs leading-none text-neutral-600">{{ Auth::user()->email }}</span>
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
                            <div class="menu-item mb-0.5 px-1.5">
                                <div class="menu-link hover:!bg-black/5 !rounded-none">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-wallet !text-brand-active">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <a class="menu-title !text-neutral-800" href="{{ route('admin.settings.main') }}">Wallet</a>
                                </div>
                            </div>
                             <div class="menu-item mb-0.5 px-1.5">
                                <div class="menu-link hover:!bg-black/5 !rounded-none">
                                    <span class="menu-icon"><i class="ki-outline ki-security-user !text-brand-active"></i></span>
                                    <a class="menu-title !text-neutral-800" href="{{ route('admin.settings.main') }}">Other Services</a>
                                </div>
                            </div>
                            <div class="menu-separator"></div>
                            <div class="menu-item mb-0.5 px-1.5">
                                <div class="menu-link hover:!bg-black/5 !rounded-none">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-setting-2 !text-brand-active">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <a class="menu-title !text-neutral-800" href="{{ route('account-setting') }}">Account Settings</a>
                                </div>
                            </div>
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
