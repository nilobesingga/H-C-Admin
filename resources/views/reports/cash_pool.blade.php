@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <div class="h-screen w-full p-0 m-0">
        @if(isset($finalUrl))
            <iframe
                src="{{ $finalUrl }}"
                class="w-full h-full min-h-[calc(100vh-4rem)] border-none"
            ></iframe>
        @else
            <p class="text-red-500 text-center">Error: Unable to load the cash pool report.</p>
        @endif
    </div>
@endsection
