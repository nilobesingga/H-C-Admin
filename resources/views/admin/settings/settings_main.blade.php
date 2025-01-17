@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <div class="container-fluid">
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5">
            <a href="{{ route('admin.settings.categories') }}"
               class="card p-5 lg:p-7.5 lg:pt-7 flex flex-col gap-4 hover:bg-gray-100 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <i class="ki-filled ki-badge text-2xl link"></i>
                    {{-- <div class="font-semibold text-2xl text-gray-800">24</div>--}}
                </div>
                <div class="flex flex-col gap-3">
                    <div class="text-base font-medium leading-none text-gray-900 hover:text-primary-active">Categories</div>
                    <span class="text-2sm text-gray-700 leading-5">We're open to partnerships, guest posts, promo banners, and more.</span>
                </div>
            </a>
            <a href="{{ route('admin.settings.countries') }}"
               class="card p-5 lg:p-7.5 lg:pt-7 flex flex-col gap-4 hover:bg-gray-100 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <i class="ki-filled ki-badge text-2xl link"></i>
                {{-- <div class="font-semibold text-2xl text-gray-800">24</div>--}}
                </div>
                <div class="flex flex-col gap-3">
                    <div class="text-base font-medium leading-none text-gray-900 hover:text-primary-active">Countries</div>
                    <span class="text-2sm text-gray-700 leading-5">We're open to partnerships, guest posts, promo banners, and more.</span>
                </div>
            </a>
            <a href="{{ route('admin.settings.users') }}"
               class="card p-5 lg:p-7.5 lg:pt-7 flex flex-col gap-4 hover:bg-gray-100 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <i class="ki-filled ki-badge text-2xl link"></i>
                {{-- <div class="font-semibold text-2xl text-gray-800">24</div>--}}
                </div>
                <div class="flex flex-col gap-3">
                    <div class="text-base font-medium leading-none text-gray-900 hover:text-primary-active">Users</div>
                    <span class="text-2sm text-gray-700 leading-5">We're open to partnerships, guest posts, promo banners, and more.</span>
                </div>
            </a>
        </div>
    </div>
@endsection
