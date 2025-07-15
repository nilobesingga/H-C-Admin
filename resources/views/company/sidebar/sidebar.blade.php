<div class="flex-col items-stretch hidden w-[--tw-sidebar-width] py-4 text-white bg-[#252B36] fixed top-[--tw-header-height] bottom-0 left-0 lg:flex shrink-0"
    data-drawer="true" data-drawer-class="top-[--tw-header-height] bottom-0 drawer drawer-start"
    data-drawer-enable="true|lg:false" id="sidebar">
    <div class="flex flex-col h-full overflow-hidden" id="sidebar_content">
        <!-- Scrollable Content Area -->
        <div class="relative flex-col items-center flex-grow w-full gap-4 overflow-y-auto" data-scrollable="true"
            data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content">

            <!-- Company Switcher -->
            <div class="w-full mb-2">
                <button id="company-switcher-btn" type="button"
                    class="flex items-center w-full gap-3 px-1 py-2 text-left bg-[#3B414B] hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-maroon-600">
                    <img src="{{ asset($company_data['logo'] ?? 'img/Logo/SADIQA.svg') }}" alt="{{ $company_id }} Logo"
                        class="border-2 border-white rounded-full shadow w-9 h-9" />
                    <span class="text-sm font-semibold capitalize">{{ $company_data['name'] }}</span>
                    <svg class="w-4 h-4 ml-auto text-gray-400 transition-transform duration-200"
                        id="company-switcher-arrow" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="company-switcher-submenu" class="hidden mx-2 mt-2 bg-white rounded shadow-lg">
                    <ul class="py-1">
                        @if($company_list)
                        @foreach($company_list as $key => $company)
                        <li>
                            <a href="{{ route('company.switch', ['company' => $company['company_id']]) }}"
                                class="flex items-center gap-3 px-4 py-2 text-xs capitalize font-semibold text-black rounded hover:bg-[#E6E7EB] {{ $company_id == $key ? 'bg-[#E6E7EB]' : '' }}">
                                @if(isset($company['logo']) && $company['logo'])
                                    <img src="{{ asset($company['logo']) }}" alt="{{ $company['name'] }} Logo"
                                    class="w-8 h-8 border-2 border-red-200 rounded-full" />
                                @else
                                    <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-sm font-bold text-gray-500 border-2 border-red-200 rounded-full">{{ $company['name'] ? strtoupper(substr($company['name'],0,2)) : '--' }}</span>
                                @endif
                                {{ $company['name'] }}
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <!-- End Company Switcher -->
            <hr class="my-3" />
            <!-- Menu Items -->
            <nav class="w-full">
                @if ($module)
                @foreach($module as $key => $submodule)
                    @if (isset($submodule['children']) && count($submodule['children']) > 0)
                        @foreach ($submodule['children'] as $item)
                        <a href="{{ route($item['route'], ['company' => $company_id]) }}"
                            class="flex items-center p-2.5 text-sm rounded hover:bg-gray-700 {{ request()->routeIs($item["route"]) ? 'border-l-4 border-orange-400 bg-gray-700' : '' }}">
                            <img src="{{ asset('icon/'.$item['icon'] ?? '') }}" class="mr-2 size-6" alt="task">
                            <span class="text-sm font-semibold">{{ $item['name'] }}</span>
                            @if (isset($item['count']) && $item['count'] > 0)
                            <span
                                class="px-2 py-1 ml-auto text-xs {{ request()->routeIs($item['route']) ? 'bg-[#FFA348]' :  'bg-gray-700'}} rounded-full">{{
                                $item['count'] }}</span>
                            @endif
                        </a>
                        @endforeach
                    @endif
                @endforeach
                @endif

                <a href="https://cresco.accountants/" target="_blank"
                    class="flex items-center p-2.5 text-sm hover:bg-gray-700 rounded">
                    <img src="{{ asset('icon/accounting_portal.svg') }}" class="mr-2 size-6" alt="accounting_portal">
                    <span class="text-sm font-semibold">Accounting Portal</span>
                </a>
                <div class="p-4 mx-4 my-6 text-sm text-gray-400 bg-[#444B56] border rounded hover:bg-gray-700">
                    <div class="flex item-center">
                        <img src="{{ asset('icon/explore.svg') }}" class="mr-2 size-6" alt="services">
                        <span>Explore our full range of additional services.</span>
                    </div>
                    <a href="#" class="flex items-center block mt-1 ml-4 text-orange-500 hover:text-orange-400">Other
                        services <img src="{{ asset('icon/arrow.svg') }}" class="mx-1"></a>
                </div>
            </nav>
            <!-- Fixed Bottom Navigation -->
            <div class="absolute left-0 w-full py-3 mt-auto text-center bg-[#252B36] bottom-16">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center mx-6 p-3 text-sm my-4 bg-[#252B36] border rounded hover:bg-gray-700">
                    <img src="{{ asset('icon/reply.svg') }}" class="ml-2 mr-2 size-8" alt="back">
                    <span>Back to Home</span>
                </a>
                <a href="{{ route('company.quickchat', ['company' => $company_id]) }}"
                    class="flex items-center p-4 mx-6 -mb-2 text-sm text-white transition bg-orange-500 rounded hover:bg-orange-600">
                    <img src="{{ asset('icon/chat.svg') }}" class="ml-2 mr-2 size-8" alt="quickchat">
                    <span class="text-xl font-semibold">Quick Chat</span>
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('company-switcher-btn');
        const submenu = document.getElementById('company-switcher-submenu');
        const arrow = document.getElementById('company-switcher-arrow');

        if (btn && submenu) {
            // Toggle submenu when button is clicked
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                submenu.classList.toggle('hidden');
                if (arrow) {
                    arrow.style.transform = submenu.classList.contains('hidden') ? '' : 'rotate(180deg)';
                }
            });

            // Close submenu when clicking elsewhere on the page
            document.addEventListener('click', function (e) {
                if (!submenu.classList.contains('hidden')) {
                    submenu.classList.add('hidden');
                    if (arrow) arrow.style.transform = '';
                }
            });

            // Prevent submenu from closing when clicking inside it
            submenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            // Add click event listeners to company links for smooth transition
            const companyLinks = submenu.querySelectorAll('a');
            companyLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Show loading indicator or animation if desired
                    const company_idName = this.textContent.trim();
                    const company_idLogo = this.querySelector('img').src;

                    // Update the main button with the newly selected company (for immediate visual feedback)
                    const buttonImg = btn.querySelector('img');
                    const buttonText = btn.querySelector('span');

                    if (buttonImg && buttonText) {
                        buttonImg.src = company_idLogo;
                        buttonImg.alt = `${company_idName} Logo`;
                        buttonText.textContent = company_idName;
                    }

                    // Close the submenu
                    submenu.classList.add('hidden');
                    if (arrow) arrow.style.transform = '';
                });
            });
        }
    });
</script>
