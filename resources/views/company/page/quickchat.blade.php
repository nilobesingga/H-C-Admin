@extends('layouts.company')
@section('pageTitle', $page->title)
@section('content')
    <div class="w-full h-full p-0">
        <quick-chat
            :page_data='@json($page)'
            :page_title='@json($page->title)'
            :company_list='@json($company_list)'
            :company_id={{ $company_id }}
            :company_data='@json($company_data)'
            :module='@json($module)'
        />
    </div>
@endsection
