@extends('layouts.company')
@section('pageTitle', $page->title)
@section('content')
    <request
        :page_data='@json($page)'
        :company_list='@json($company_list)'
        :company_id={{ $company_id }}
        :company_data='@json($company_data)'
        :data='@json($data)'
        :module='@json($module)'
    />
@endsection
