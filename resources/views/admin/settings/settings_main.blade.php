@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <div class="container-fluid px-3 pt-2 flex justify-center items-center min-h-[90vh]">
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5 w-2/4">
            <a href="{{ route('admin.settings.categories') }}"
               class="animate-in animate-delay-100 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-tec-active hover:shadow-xl hover:shadow-tec-active/20 hover transition-all duration-300">
                <div class="text-lg text-black font-semibold leading-none tracking-tight">Categories</div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-tec-active group-hover:scale-150 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <a href="{{ route('admin.settings.countries') }}"
               class="animate-in animate-delay-150 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-tec-active hover:shadow-xl hover:shadow-tec-active/20 hover transition-all duration-300">
                <div class="text-lg text-black font-semibold leading-none tracking-tight">Countries</div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-tec-active group-hover:scale-150 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <a href="{{ route('admin.settings.users') }}"
               class="animate-in animate-delay-200 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-tec-active hover:shadow-xl hover:shadow-tec-active/20 hover transition-all duration-300">
                <div class="text-lg text-black font-semibold leading-none tracking-tight">Users</div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-tec-active group-hover:scale-150 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <a href="{{ route('admin.settings.modules') }}"
               class="animate-in animate-delay-250 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-tec-active hover:shadow-xl hover:shadow-tec-active/20 hover transition-all duration-300">
                <div class="text-lg text-black font-semibold leading-none tracking-tight">Modules</div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-tec-active group-hover:scale-150 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <a href="{{ route('admin.settings.bitrix-sage-mapping') }}"
               class="animate-in animate-delay-300 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-tec-active hover:shadow-xl hover:shadow-tec-active/20 hover transition-all duration-300">
                <div class="text-lg text-black font-semibold leading-none tracking-tight">Cetrix Sage Mapping</div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-tec-active group-hover:scale-150 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>
@endsection
