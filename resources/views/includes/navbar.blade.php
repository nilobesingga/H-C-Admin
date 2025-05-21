@php
    $userModules = $page->user->modules;
    $currentSegment = request()->segment(2) ?: request()->segment(1);

    // Find the module matching the current route segment
    $currentModule = $userModules->firstWhere('slug', $currentSegment);

    // Determine the parent module
    $parentModule = $currentModule?->parent_id ? $currentModule->parent : $currentModule;

    // Fetch visible child modules that the user has access to
    $childModules = $parentModule
        ? $parentModule->children->filter(fn($mod) => $userModules->contains('id', $mod->id))->sortBy('order')
        : collect();

    // Check if any child routes are currently active
    $isChildActive = fn($mod) => request()->routeIs($mod->route_name);
@endphp

@if($parentModule && $childModules->isNotEmpty())
    <div class="bg-[--tw-header-bg] dark:bg-[--tw-header-bg-dark] border-b border-b-neutral-200 header-navigation">
        <div class="container-fluid pl-3 pr-2 flex flex-wrap justify-between items-center gap-2">
            <div class="grid">
                <div class="scrollable-x-auto">
                    <div class="menu gap-5 lg:gap-10" data-menu="true">
                        @foreach($childModules as $module)
                            <div class="menu-item py-2 border-b-2 border-b-transparent hover:border-b-neutral-900 transition-all duration-300 {{ $isChildActive($module) ? '!border-b-brand-active' : '' }}">
                                <a class="menu-link gap-2.5" href="{{ route($module->route_name) }}">
                                    <span class="menu-title text-nowrap font-medium text-sm tracking-tight text-black">
                                        {{ $module->name }}
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

