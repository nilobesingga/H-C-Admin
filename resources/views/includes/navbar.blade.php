@php
    $cashReportsChildModules = $page->user->modules->where('parent_id', 1)->sortBy('order');
    $cashPoolChildModules = $page->user->modules->where('parent_id', 23)->sortBy('order');

@endphp
@if(request()->is('reports*'))
    @if($cashReportsChildModules->isNotEmpty())
        <div class="bg-[--tw-header-bg] dark:bg-[--tw-header-bg-dark] border-b border-b-neutral-200 header-navigation">
            <div class="container-fluid pl-3 pr-2 flex flex-wrap justify-between items-center gap-2">
                <div class="grid">
                    <div class="scrollable-x-auto">
                        <div class="menu gap-5 lg:gap-10" data-menu="true">
                            @foreach($cashReportsChildModules as $module)
                                @if($module->parent_id !== 0)
                                    @php
                                        $routeName = 'reports.' . $module->slug;
                                    @endphp
                                    <div class="menu-item py-2 border-b-2 border-b-transparent hover:border-b-neutral-900 transition-all duration-300 {{ request()->routeIs($routeName) ? '!border-b-brand-active' : '' }}">
                                        <a class="menu-link gap-2.5 " href="{{ route($routeName) }}">
                                        <span class="menu-title text-nowrap font-medium text-sm tracking-tight text-black">
                                            {{ $module->name }}
                                        </span>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@if(request()->is('cash-pool*'))
    @if($cashPoolChildModules->isNotEmpty())
        <div class="bg-[--tw-header-bg] dark:bg-[--tw-header-bg-dark] border-b border-b-neutral-200">
            <div class="container-fluid pl-3 pr-2 flex flex-wrap justify-between items-center gap-2">
                <div class="grid">
                    <div class="scrollable-x-auto">
                        <div class="menu gap-5 lg:gap-10" data-menu="true">
                            @foreach($cashPoolChildModules as $module)
                                @if($module->parent_id !== 0)
                                    @php
                                        $routeName = 'cash-pool.' . $module->slug;
                                    @endphp
                                    <div class="menu-item py-2 border-b-2 border-b-transparent hover:border-b-neutral-900 transition-all duration-300 {{ request()->routeIs($routeName) ? '!border-b-brand-active' : '' }}">
                                        <a class="menu-link gap-2.5 " href="{{ route($routeName) }}">
                                        <span class="menu-title text-nowrap font-medium text-sm tracking-tight text-black">
                                            {{ $module->name }}
                                        </span>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
