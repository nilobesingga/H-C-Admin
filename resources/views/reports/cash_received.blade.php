@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <cash-received
        :page_data='@json($page)'
    />
@endsection
