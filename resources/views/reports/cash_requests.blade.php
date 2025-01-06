@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <cash-reports
        :page_data='@json($page)'
    />
@endsection
