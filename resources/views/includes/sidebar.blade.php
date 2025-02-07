<div class="fixed w-[--tw-sidebar-width] lg:top-[--tw-header-height] top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 group py-3 lg:py-0" data-drawer="true" data-drawer-class="drawer drawer-start top-0 bottom-0" data-drawer-enable="true|lg:false" id="sidebar">
    <div class="flex grow shrink-0" id="sidebar_content">
        <div class="scrollable-y-auto grow gap-4 shrink-0 flex items-center flex-col relative" data-scrollable="true" data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content">
            <div class="flex items-center justify-center mb-4 mt-2">
                <button class="btn btn-icon btn-light btn-clear btn-sm -ms-2 lg:hidden" data-drawer-toggle="#sidebar">
                    <i class="ki-filled ki-menu"></i>
                </button>
                <a class="mx-1 w-[70%]" href="{{ route('dashboard') }}">
                    <img class="dark:hidden w-12" src="{{ asset('storage/images/logos/CRESCO_icon.png') }}"/>
                    {{-- <img class="hidden dark:block min-h-[24px]" src="{{ asset('storage/images/logos/CRESCO_icon.png') }}"/> --}}
                </a>
            </div>

            {{--      Dashboard      --}}
            <a class="rounded-none text-dark-active hover:text-primary transition-all duration-300
                {{ request()->routeIs('dashboard') ? 'text-dark' : '' }}"
                data-tooltip=""
                data-tooltip-placement="right"
               href="{{ route('dashboard') }}"
            >
                <span class="menu-icon"><i class="ki-duotone ki-graph-up text-4xl"></i></span>
                <span class="tooltip">Dashboard</span>
            </a>
            {{--      Reports      --}}
            @if(!$page->user->modules->isEmpty())
                <a class="rounded-none text-primary hover:text-primary [.active&amp;]:text-primary
                        {{ request()->is('reports*') ? 'active' : '' }}"
                   data-tooltip=""
                   data-tooltip-placement="right"
                   href="{{ route('reports.' . $page->user->modules->sortBy('order')->first()->slug) }}"
                >
                    <span class="menu-icon"><i class="ki-filled ki-tab-tablet text-4xl"></i></span>
                    <span class="tooltip">Reports</span>
                </a>
            @endif



        </div>
    </div>

</div>
