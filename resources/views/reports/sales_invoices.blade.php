@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <sales-invoices
        :page_data='@json($page)'
    />
@endsection
