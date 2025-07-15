<div class="fixed w-[--tw-sidebar-width] lg:top-[--tw-header-height] top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 group py-3 lg:py-0" data-drawer="true" data-drawer-class="top-0 bottom-0 drawer drawer-start" data-drawer-enable="true|lg:false" id="sidebar">
    <div class="flex grow shrink-0" id="sidebar_content">
        <div class="relative flex flex-col items-center gap-4 scrollable-y-auto grow shrink-0" data-scrollable="true" data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content">
            <div class="flex items-center justify-center mt-2 mb-4">
                <button class="btn btn-icon btn-light btn-clear btn-sm -ms-2 lg:hidden" data-drawer-toggle="#sidebar">
                    <i class="ki-filled ki-menu"></i>
                </button>
                <a class="mx-1 w-[70%]" href="{{ route('dashboard') }}">
                    <img class="w-12 dark:hidden" src="{{ asset('storage/images/logos/CRESCO_icon.png') }}"/>
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
                <span class="menu-icon"><i class="text-4xl ki-duotone ki-graph-up"></i></span>
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
                    <span class="menu-icon"><i class="text-4xl ki-filled ki-tab-tablet"></i></span>
                    <span class="tooltip">Reports</span>
                </a>
            @endif



        </div>
    </div>

</div>
