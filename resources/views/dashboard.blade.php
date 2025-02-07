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
            <div>
                <a href="{{ route('reports.' . $page->user->modules->sortBy('order')->first()->slug) }}"
                   class="card p-8 flex flex-col w-80 gap-7 border-gray-300 rounded-none hover:bg-white hover:border-black hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between gap-2">
                        <i class="ki-filled ki-badge text-5xl text-brand-active"></i>
                        {{-- <div class="font-semibold text-2xl text-gray-800">24</div>--}}
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="text-xl font-bold leading-none tracking-tight text-gray-900">Cash Reports</div>
                    </div>
                </a>
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
