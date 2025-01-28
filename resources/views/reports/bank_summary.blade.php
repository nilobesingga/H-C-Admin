@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <div class="container-fluid pl-10 pt-3 sub-module">
        <div class="flex justify-between items-center gap-5">
            <div class="grid items-stretch">
                <div class="scrollable-x-auto flex items-center">
                    <div class="content-center">
                        <div class="flex gap-2.5 w-full items-center" data-tabs="true">
                            <a class="btn btn-primary btn-xs btn-clear {{ request()->get('section') === 'overview' ? 'active' : '' }}" href="{{ route('reports.bank-summary', ['section' => 'overview']) }}">Overview</a> |
                            <a class="btn btn-primary btn-xs btn-clear {{ request()->get('section') === 'cheque-register-outgoing' ? 'active' : '' }}" href="{{ route('reports.bank-summary', ['section' => 'cheque-register-outgoing']) }}">Cheque Register - Outgoing</a> |
                            <a class="btn btn-primary btn-xs btn-clear {{ request()->get('section') === 'cheque-register-incoming' ? 'active' : '' }}" href="{{ route('reports.bank-summary', ['section' => 'cheque-register-incoming']) }}">Cheque Register - Incoming</a> |
                            <a class="btn btn-primary btn-xs btn-clear {{ request()->get('section') === 'cash-by-currency' ? 'active' : '' }}" href="{{ route('reports.bank-summary', ['section' => 'cash-by-currency']) }}">Cash By Currency</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $section = request()->get('section');
    @endphp
    @if($section === 'overview')
        <bank-summary
            :page_data='@json($page)'
        />
    @endif
    @if($section === 'cheque-register-outgoing')
        <cheque-register
            :page_data='@json($page)'
        />
    @endif
    @if($section === 'cheque-register-incoming')
        <cheque-register
            :page_data='@json($page)'
        />
    @endif
    @if($section === 'cash-by-currency')
        <bank-summary
            :page_data='@json($page)'
        />
    @endif

@endsection
