@extends('layouts.app')
@section('pageTitle', 'Dashboard')
@section('content')
    <div class="container-fluid px-3 pt-2 flex justify-center items-center min-h-[90vh]">
        @if($page->user->modules->isEmpty())
            <div class="alert alert-danger text-center p-4 bg-red-100 text-red-700 rounded-md">
                No modules assigned to this user.
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7 w-3/5">
                @php
                    $cashReportsChildModules = $page->user->modules->where('parent_id', 1);
                    $cashPoolChildModules = $page->user->modules->where('parent_id', 23);
                    $modules = $page->user->modules->where('parent_id', 0)->sortBy('order');
                @endphp
                {{-- Cash Reports --}}
                @if($cashReportsChildModules->isNotEmpty())
                    <a href="{{ route('reports.' . $cashReportsChildModules->sortBy('order')->first()->slug) }}"
                       class="animate-in animate-delay-100 card group p-7 flex flex-col items-stretch justify-between gap-7 border-white overflow-hidden relative
                                rounded-none hover:bg-white hover:border-brand-active shadow-sm hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300"
                    >
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Cash Reports<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-start group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                        <span class="bg-icon">
                            <i class="ki-duotone ki-chart">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </a>
                @endif
                {{-- Cash Pool --}}
                @if($cashPoolChildModules->isNotEmpty())
                    <a href="{{ route('cash-pool.' . $cashPoolChildModules->sortBy('order')->first()->slug) }}"
                       class="animate-in animate-delay-100 card group p-7 flex flex-col items-stretch justify-between gap-7 border-white overflow-hidden relative
                                rounded-none hover:bg-white hover:border-brand-active shadow-sm hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300"
                    >
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Cash Pool<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-start group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                        <span class="bg-icon">
                            <i class="ki-duotone ki-chart">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </a>
                @endif
                @foreach($modules as $module)
                    <a href="{{ route('reports.' . $module->slug) }}"
                       class="animate-in animate-delay-100 card group p-7 flex flex-col items-stretch justify-between gap-10 border-white overflow-hidden relative
                       rounded-none hover:bg-white hover:border-brand-active shadow-sm hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300"
                    >
                        <div class="text-lg text-black font-bold leading-none tracking-tight">{{ $module->name }}</div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-start group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                        <span class="bg-icon">
                            {!! $module->icon !!}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
