@extends('layouts.app')
@section('pageTitle', 'Dashboard')
@section('content')
    <div class="container-fluid px-3 pt-2 flex justify-center items-center min-h-[90vh]">
        @if($page->user->modules->isEmpty())
            <div class="alert alert-danger text-center p-4 bg-red-100 text-red-700 rounded-md">
                No modules assigned to this user.
            </div>
        @else
            {{-- <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5"> --}}
            <div class="container-fluid px-3 pt-2 flex justify-center items-center min-h-[90vh]">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5 w-2/4">
                    <a href="https://reports.cresco.org/holding" target="_blank"
                       class="animate-in animate-delay-100 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Holding<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/accounting" target="_blank"
                       class="animate-in animate-delay-150 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Accounting<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/sage" target="_blank"
                       class="animate-in animate-delay-200 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Sage<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/compliance" target="_blank"
                       class="animate-in animate-delay-250 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Hensley&amp;Cook<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/orchid/banks" target="_blank"
                       class="animate-in animate-delay-300 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">OrchidX<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/accounting/expensecalendar" target="_blank"
                       class="animate-in animate-delay-350 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Expense Overview<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="{{ route('reports.' . $page->user->modules->sortBy('order')->first()->slug) }}"
                       class="animate-in animate-delay-400 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Cash Reports<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/entity/relationships" target="_blank"
                       class="animate-in animate-delay-450 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Relationships Report<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="https://reports.cresco.org/hr/checkIn" target="_blank"
                       class="animate-in animate-delay-500 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">HR Reports<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif
{{--        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5">--}}
{{--            <a href="{{ route('reports.purchase-invoices') }}"--}}
{{--               class="card p-5 lg:p-7.5 lg:pt-7 flex flex-col gap-4 hover:bg-gray-100 hover:border-[--tw-primary] transition-colors">--}}
{{--                <div class="flex items-center justify-between gap-2">--}}
{{--                    <i class="ki-filled ki-badge text-2xl link"></i>--}}
{{--                    --}}{{-- <div class="font-semibold text-2xl text-gray-800">24</div>--}}
{{--                </div>--}}
{{--                <div class="flex flex-col gap-3">--}}
{{--                    <div class="text-base font-medium leading-none text-gray-900 hover:text-primary-active">Reports</div>--}}
{{--                    <span class="text-2sm text-gray-700 leading-5">We're open to partnerships, guest posts, promo banners, and more.</span>--}}
{{--                </div>--}}
{{--            </a>--}}
{{--        </div>--}}
    </div>
@endsection
