@extends('layouts.app')
@section('pageTitle', 'Dashboard')
@section('content')
    <div class="container-fluid px-3 pt-2 flex justify-center items-center min-h-[90vh]">
        @if($page->user->modules->isEmpty())
            <div class="alert alert-danger text-center p-4 bg-red-100 text-red-700 rounded-md">
                No modules assigned to this user.
            </div>
        @else
            <div class="container-fluid px-3 pt-2 flex justify-center items-center min-h-[90vh]">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5 w-2/4">
                    <a href="{{ route('reports.' . $page->user->modules->sortBy('order')->first()->slug) }}"
                       class="animate-in animate-delay-100 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                        <div class="text-lg text-black font-bold leading-none tracking-tight">Cash Reports<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    @php
                        $modules = $page->user->modules->where('parent_id', 0)->sortBy('order');
                    @endphp
                    @foreach($modules as $module)
                        <a href="{{ route('reports.' . $module->slug) }}" target="_blank"
                           class="animate-in animate-delay-100 card group px-8 py-7 flex flex-col items-stretch justify-between gap-7 border-white rounded-none hover:bg-white hover:border-brand-active hover:shadow-xl hover:shadow-brand-shadow hover transition-all duration-300">
                            <div class="text-lg text-black font-bold leading-none tracking-tight">{{ $module->name }}<span class="transition-all duration-300 opacity-0 group-hover:opacity-100 text-brand-active">_</span></div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 self-end group-hover:text-brand-active group-hover:scale-150 transition-all duration-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
