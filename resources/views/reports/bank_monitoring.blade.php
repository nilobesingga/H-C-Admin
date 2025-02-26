@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <bank-monitoring
        :page_data='@json($page)'
    />
@endsection
