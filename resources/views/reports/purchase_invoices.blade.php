@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <purchase-invoices
        :page_data='@json($page)'
    />
@endsection
