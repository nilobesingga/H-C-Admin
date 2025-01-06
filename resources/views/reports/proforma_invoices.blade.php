@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <proforma-invoices
        :page_data='@json($page)'
    />
@endsection
