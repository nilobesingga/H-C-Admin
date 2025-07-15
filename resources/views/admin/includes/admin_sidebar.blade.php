<div class="flex-col items-stretch hidden w-[--tw-sidebar-width] py-4 text-white bg-[#252B36] fixed top-[--tw-header-height] bottom-0 left-0 lg:flex shrink-0"
    data-drawer="true" data-drawer-class="top-[--tw-header-height] bottom-0 drawer drawer-start"
    data-drawer-enable="true|lg:false" id="sidebar">
    <div class="flex flex-col h-full overflow-hidden" id="sidebar_content">
        <!-- Scrollable Content Area -->
        <div class="relative flex-col items-center flex-grow w-full gap-4 overflow-y-auto" data-scrollable="true"
            data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content">
            <!-- Menu Items -->
            <nav class="w-full">
                @if ($module)
                    @foreach($module as $key => $submodule)
                        @if (isset($submodule['children']) && count($submodule['children']) > 0)
                            @foreach ($submodule['children'] as $item)
                                <a href="{{ route($item['route']) }}"
                                    class="flex items-center p-2.5 text-sm rounded hover:bg-gray-700 {{ request()->routeIs($item["route"]) ? 'border-l-4 border-orange-400 bg-gray-700' : '' }}">
                                    <img src="{{ asset($item['icon'] ?? '') }}" class="mr-2 size-6" alt="task">
                                    <span class="text-sm font-semibold">{{ $item['name'] }}</span>
                                    @if (isset($item['count']) && $item['count'] > 0)
                                    <span
                                        class="px-2 py-1 ml-auto text-xs {{ request()->routeIs($item['route']) ? 'bg-[#FFA348]' :  'bg-gray-700'}} rounded-full">{{
                                        $item['count'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route($submodule['route']) }}"
                                class="flex items-center p-2.5 text-sm rounded hover:bg-gray-700 {{ request()->routeIs($submodule["route"]) ? 'border-l-4 border-orange-400 bg-gray-700' : '' }}">
                                {{-- <img src="{{ asset($submodule['icon']) }}" class="mr-2 text-white size-6" alt="task"> --}}
                                <span class="font-semibold text-md">{{ $submodule['name'] }}</span>
                            </a>
                        @endif
                @endforeach
                @endif
            </nav>
            <!-- Fixed Bottom Navigation -->
            <div class="absolute left-0 w-full py-3 mt-auto text-center bg-[#252B36] bottom-16">
                <a href=""
                    class="flex items-center p-4 mx-6 -mb-2 text-sm text-white transition bg-orange-500 rounded hover:bg-orange-600">
                    <img src="{{ asset('icon/chat.svg') }}" class="ml-2 mr-2 size-8" alt="quickchat">
                    <span class="text-xl font-semibold">Quick Chat</span>
                </a>
            </div>
        </div>
    </div>
</div>

