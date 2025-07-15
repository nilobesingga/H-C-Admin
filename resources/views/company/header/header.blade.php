<header
    class="flex items-center transition-[height] shrink-0 h-[--tw-header-height] border-b border-b-neutral-200 top-header bg-[#252B36] w-full left-0 right-0 z-50 fixed top-0 sticky-header"
    data-sticky="true"
    data-sticky-class="shadow-sm backdrop-blur-md bg-[#252B36]/95 dark:bg-coal-500/95 dark:border-b dark:border-b-coal-100"
    data-sticky-name="header" data-sticky-offset="0" id="header">
    <!-- Container -->
    <div class="flex flex-wrap items-center justify-between pl-3 pr-2 container-fluid lg:gap-4" id="header_container">
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
        <div class="flex items-center gap-2 lg:gap-3 " x-data="{ openNotifications: false }">
            <!-- Notification Bell and Offcanvas in same Alpine scope -->
            <button class="rounded-full menu-toggle btn btn-icon" id="notification-bell" x-on:click="openNotifications = true;">
                <i class="text-white ki-outline ki-notification"></i>
            </button>
            <div x-show="openNotifications" class="fixed inset-0 flex justify-end z-100">
                <div class="relative flex flex-col w-full h-full max-w-sm transition-all animate-[wiggle_1s_ease-in-out_infinite] duration-75 bg-white shadow-2xl"
                >
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold">Notifications</h2>
                        <button x-on:click="openNotifications = false" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="z-50 flex-1 overflow-y-auto">
                        <div class="px-6 pt-4">
                            <div class="flex gap-4 pb-2 mb-2 border-b">
                                <button class="pb-1 mr-4 font-semibold text-gray-700 border-b-2 border-orange-500">Alerts <span class="px-2 ml-1 text-xs text-orange-600 bg-orange-100 rounded-full">3</span></button>
                                <button class="pb-1 font-semibold text-gray-500">Activities <span class="px-2 ml-1 text-xs text-gray-600 bg-gray-100 rounded-full">5</span></button>
                            </div>
                            <!-- Alerts -->
                            <div class="space-y-6">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full">
                                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/></svg>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-800">Your corporate license will expire on <b>30 DEC 2024</b>. Take action immediately.</span>
                                        </div>
                                        <div class="flex gap-2 mt-2">
                                            <button class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700">Renew License</button>
                                            <button class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Decline</button>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">5 days ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="mt-1">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 rounded-full">
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Your request has been rejected due to missing documents.</span>
                                        <div class="flex gap-2 mt-2">
                                            <button class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Update</button>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">3 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="mt-1">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 rounded-full">
                                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m4 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/></svg>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">The server will be unavailable on Saturday, 16 December 2024 from 13:00 - 18:00</span>
                                        <div class="mt-1 text-xs text-gray-400">20 days ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-8 h-8 rounded-full"/>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">You have complete the task <b>Submit documents</b> from <b>Onboarding</b> list</span>
                                        <div class="flex items-center gap-2 mt-2">
                                            <input type="checkbox" checked disabled class="text-blue-600 rounded form-checkbox"/>
                                            <span class="text-xs text-gray-400">Submit personal documents</span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">4 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-yellow-700 bg-yellow-100 rounded-full">DT</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Dom Terry requested your feedback in <b>Support Request #4848</b></span>
                                        <div class="flex gap-2 mt-2">
                                            <button class="px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded hover:bg-green-200">Approve</button>
                                            <button class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Review</button>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">6 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-orange-700 bg-orange-100 rounded-full">SC</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Steph Craig comment in your <b>Refund Case #4588</b></span>
                                        <div class="mt-1 text-xs text-gray-400">7 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-blue-700 bg-blue-100 rounded-full">AM</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">All hands meeting will take place</span>
                                        <div class="mt-1 text-xs text-gray-400">8 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-8 h-8 rounded-full"/>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">You have complete the task <b>Submit documents</b> from <b>Onboarding</b> list</span>
                                        <div class="flex items-center gap-2 mt-2">
                                            <input type="checkbox" checked disabled class="text-blue-600 rounded form-checkbox"/>
                                            <span class="text-xs text-gray-400">Submit personal documents</span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">4 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-yellow-700 bg-yellow-100 rounded-full">DT</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Dom Terry requested your feedback in <b>Support Request #4848</b></span>
                                        <div class="flex gap-2 mt-2">
                                            <button class="px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded hover:bg-green-200">Approve</button>
                                            <button class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Review</button>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">6 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-orange-700 bg-orange-100 rounded-full">SC</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Steph Craig comment in your <b>Refund Case #4588</b></span>
                                        <div class="mt-1 text-xs text-gray-400">7 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-blue-700 bg-blue-100 rounded-full">AM</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">All hands meeting will take place</span>
                                        <div class="mt-1 text-xs text-gray-400">8 hours ago</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Activities -->
                            <div class="mt-8 space-y-6">
                                <div class="flex items-start gap-3">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-8 h-8 rounded-full"/>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">You have complete the task <b>Submit documents</b> from <b>Onboarding</b> list</span>
                                        <div class="flex items-center gap-2 mt-2">
                                            <input type="checkbox" checked disabled class="text-blue-600 rounded form-checkbox"/>
                                            <span class="text-xs text-gray-400">Submit personal documents</span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">4 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-yellow-700 bg-yellow-100 rounded-full">DT</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Dom Terry requested your feedback in <b>Support Request #4848</b></span>
                                        <div class="flex gap-2 mt-2">
                                            <button class="px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded hover:bg-green-200">Approve</button>
                                            <button class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Review</button>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">6 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-orange-700 bg-orange-100 rounded-full">SC</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">Steph Craig comment in your <b>Refund Case #4588</b></span>
                                        <div class="mt-1 text-xs text-gray-400">7 hours ago</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 font-bold text-blue-700 bg-blue-100 rounded-full">AM</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-800">All hands meeting will take place</span>
                                        <div class="mt-1 text-xs text-gray-400">8 hours ago</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-4 mr-10 menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="0px, -38px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                    <div class="flex items-center space-x-2 rounded-full menu-toggle btn btn-icon ">
                        <img alt="{{ Auth::user()->name ?? 'User' }}"
                            class="justify-center transition-all duration-300 border border-black rounded-full shadow-lg size-8 shrink-0 ring-1 ring-transparent hover:border-white hover:ring-brand-active hover:shadow-brand-shadow"
                            src="{{ profile()->profilePhoto ?? asset('path/to/default-avatar.jpg') }}">
                        <span class="text-sm font-medium text-white">{{ getFirstChars(profile()->name, true) ?? 'User' }}</span>
                    </div>
                    <div class="menu-dropdown menu-default rounded-none backdrop-blur light:border-black w-screen max-w-[250px]">
                        <div class="flex items-center justify-between px-5 py-1.5 gap-1.5 relative">
                            <div class="flex flex-col gap-2">
                                <div>
                                    <img src="{{ profile()->profilePhoto ?? 'path/to/default-avatar.jpg' }}" class="inline-block w-12 h-12 border border-white rounded-full shadow-lg ring-2 ring-brand-active shadow-brand-shadow"/>
                                </div>
                                <div class="flex flex-col gap-1.5 mt-4 text-black">
                                    <span class="text-sm font-semibold leading-none text-black">{{ profile()->name }}</span>
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
