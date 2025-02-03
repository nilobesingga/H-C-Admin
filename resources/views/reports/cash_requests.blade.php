@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <cash-requests
        :page_data='@json($page)'
    />
@endsection
