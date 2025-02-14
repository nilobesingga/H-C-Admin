{{--   Dashboard     --}}
@if(request()->routeIs('admin.home'))
    <div class="container-fluid flex justify-between items-stretch gap-5">
        <div class="grid items-stretch">
            <div class="scrollable-x-auto flex items-stretch">
                <div class="menu gap-5 lg:gap-7.5" data-menu="true">
                    <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900">
                        <a class="menu-link gap-2.5" href="{{ route('admin.home') }}" tabindex="0">
                        <span class="menu-title text-nowrap text-sm text-gray-800 menu-item-active:text-gray-900 menu-item-active:font-medium menu-item-here:text-gray-900 menu-item-here:font-medium menu-item-show:text-gray-900 menu-link-hover:text-gray-900">
                            Dashboard
                        </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{--   Settings     --}}
