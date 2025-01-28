<div class="fixed w-[--tw-sidebar-width] lg:top-[--tw-header-height] top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 group py-3 lg:py-0" data-drawer="true" data-drawer-class="drawer drawer-start top-0 bottom-0" data-drawer-enable="true|lg:false" id="sidebar">
    <div class="flex grow shrink-0" id="sidebar_content">
        <div class="scrollable-y-auto grow gap-2.5 shrink-0 flex items-center flex-col" data-scrollable="true" data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content">
            {{--      Dashboard      --}}
            <a class="btn btn-icon btn-icon-lg rounded-full size-10 border border-transparent text-gray-600 hover:bg-light hover:text-primary hover:border-gray-300 [.active&amp;]:bg-light [.active&amp;]:text-primary [.active&amp;]:border-gray-300
                {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                data-tooltip=""
                data-tooltip-placement="right"
               href="{{ route('dashboard') }}"
            >
                <span class="menu-icon"><i class="ki-filled ki-chart-line-star"></i></span>
                <span class="tooltip">Dashboard</span>
            </a>
            {{--      Reports      --}}
            @if(!$page->user->modules->isEmpty())
                <a class="btn btn-icon btn-icon-lg rounded-full size-10 border border-transparent text-gray-600 hover:bg-light hover:text-primary hover:border-gray-300 [.active&amp;]:bg-light [.active&amp;]:text-primary [.active&amp;]:border-gray-300
                        {{ request()->is('reports*') ? 'active' : '' }}"
                   data-tooltip=""
                   data-tooltip-placement="right"
                   href="{{ route('reports.' . $page->user->modules->sortBy('order')->first()->slug) }}"
                >
                    <span class="menu-icon"><i class="ki-filled ki-tab-tablet"></i></span>
                    <span class="tooltip">Reports</span>
                </a>
            @endif
        </div>
    </div>
</div>
