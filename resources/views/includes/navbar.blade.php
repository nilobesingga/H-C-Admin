<div class="flex items-stretch lg:fixed z-5 top-[--tw-header-height] start-[--tw-sidebar-width] end-5 h-[--tw-navbar-height] mx-5 lg:mx-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]" id="navbar">
    <div class="rounded-t-xl border border-gray-400 dark:border-gray-200 border-b-gray-300 dark:border-b-gray-200 bg-[--tw-content-bg] dark:bg-[--tw-content-bg-dark] flex items-stretch grow">
        {{--   Dashboard     --}}
        @if(request()->routeIs('dashboard'))
            <div class="container-fluid flex justify-between items-stretch gap-5">
                <div class="grid items-stretch">
                    <div class="scrollable-x-auto flex items-stretch">
                        <div class="menu gap-5 lg:gap-7.5" data-menu="true">
                            <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900 {{ request()->is('dashboard') ? 'active' : '' }}">
                                <a class="menu-link gap-2.5" href="{{ route('dashboard') }}" tabindex="0">
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
        {{--   Reports     --}}
{{--        @if(request()->is('reports*'))--}}
{{--            <div class="container-fluid content-center">--}}
{{--                <div class="flex gap-2.5 w-full" data-tabs="true">--}}
{{--                    <a class="btn btn-primary btn-clear active">--}}
{{--                        Tab 1--}}
{{--                    </a>--}}
{{--                    <a class="btn btn-primary btn-clear">--}}
{{--                        Tab 2--}}
{{--                    </a>--}}
{{--                    <a class="btn btn-primary btn-clear">--}}
{{--                        Tab 3--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="container-fluid flex justify-between items-stretch gap-5">--}}
{{--                <div class="grid items-stretch">--}}
{{--                    <div class="scrollable-x-auto flex items-stretch">--}}
{{--                        <div class="menu gap-5 lg:gap-7.5" data-menu="true">--}}
{{--                            --}}{{-- Purchase Invoices --}}
{{--                            <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900 {{ request()->routeIs('reports.purchase-invoices') ? 'active' : '' }}">--}}
{{--                                <a class="menu-link gap-2.5" href="{{ route('reports.purchase-invoices') }}" tabindex="0">--}}
{{--                                <span class="menu-title text-nowrap text-sm text-gray-800 menu-item-active:text-gray-900 menu-item-active:font-medium menu-item-here:text-gray-900 menu-item-here:font-medium menu-item-show:text-gray-900 menu-link-hover:text-gray-900">--}}
{{--                                    Purchase Invoices--}}
{{--                                </span>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                            --}}{{-- Cash Reports --}}
{{--                            <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900 {{ request()->routeIs('reports.cash-requests') ? 'active' : '' }}">--}}
{{--                                <a class="menu-link gap-2.5" href="{{ route('reports.cash-requests') }}" tabindex="0">--}}
{{--                                <span class="menu-title text-nowrap text-sm text-gray-800 menu-item-active:text-gray-900 menu-item-active:font-medium menu-item-here:text-gray-900 menu-item-here:font-medium menu-item-show:text-gray-900 menu-link-hover:text-gray-900">--}}
{{--                                    Cash Reports--}}
{{--                                </span>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                            --}}{{-- Bank Transfers --}}
{{--                            <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900 {{ request()->routeIs('reports.bank-transfers') ? 'active' : '' }}">--}}
{{--                                <a class="menu-link gap-2.5" href="{{ route('reports.bank-transfers') }}" tabindex="0">--}}
{{--                                <span class="menu-title text-nowrap text-sm text-gray-800 menu-item-active:text-gray-900 menu-item-active:font-medium menu-item-here:text-gray-900 menu-item-here:font-medium menu-item-show:text-gray-900 menu-link-hover:text-gray-900">--}}
{{--                                    Bank Transfers--}}
{{--                                </span>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                            --}}{{-- Sales Invoices --}}
{{--                            <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900 {{ request()->routeIs('reports.sales-invoices') ? 'active' : '' }}">--}}
{{--                                <a class="menu-link gap-2.5" href="{{ route('reports.sales-invoices') }}" tabindex="0">--}}
{{--                                <span class="menu-title text-nowrap text-sm text-gray-800 menu-item-active:text-gray-900 menu-item-active:font-medium menu-item-here:text-gray-900 menu-item-here:font-medium menu-item-show:text-gray-900 menu-link-hover:text-gray-900">--}}
{{--                                    Sales Invoices--}}
{{--                                </span>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                            --}}{{-- Sales Invoices --}}
{{--                            <div class="menu-item border-b-2 border-b-transparent menu-item-active:border-b-gray-900 menu-item-here:border-b-gray-900 {{ request()->routeIs('reports.bank-summary') ? 'active' : '' }}">--}}
{{--                                <a class="menu-link gap-2.5" href="{{ route('reports.bank-summary') }}" tabindex="0">--}}
{{--                                <span class="menu-title text-nowrap text-sm text-gray-800 menu-item-active:text-gray-900 menu-item-active:font-medium menu-item-here:text-gray-900 menu-item-here:font-medium menu-item-show:text-gray-900 menu-link-hover:text-gray-900">--}}
{{--                                    Bank Summary--}}
{{--                                </span>--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif --}}
        {{--   Reports     --}}
        @if(request()->is('reports*'))
            <div class="container-fluid flex justify-between items-stretch gap-5">
                <div class="grid items-stretch">
                    <div class="scrollable-x-auto flex items-stretch">
                        <div class="content-center">
                            <div class="flex gap-2.5 w-full" data-tabs="true">
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.purchase-invoices') ? 'active' : '' }}" href="{{ route('reports.purchase-invoices') }}">
                                    Purchase Invoices
                                </a>
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.cash-requests') ? 'active' : '' }}"  href="{{ route('reports.cash-requests') }}">
                                    Cash Reports
                                </a>
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.bank-transfers') ? 'active' : '' }}" href="{{ route('reports.bank-transfers') }}">
                                    Bank Transfers
                                </a>
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.sales-invoices') ? 'active' : '' }}" href="{{ route('reports.sales-invoices') }}">
                                    Sales Invoices
                                </a>
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.proforma-invoices') ? 'active' : '' }}" href="{{ route('reports.proforma-invoices') }}">
                                    Proforma Invoices
                                </a>
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.bank-summary') ? 'active' : '' }}" href="{{ route('reports.bank-summary') }}">
                                    Bank Summary
                                </a>
                                <a class="btn btn-primary btn-clear {{ request()->routeIs('reports.expense-planner') ? 'active' : '' }}" href="{{ route('reports.expense-planner') }}">
                                    Expense Planner
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
