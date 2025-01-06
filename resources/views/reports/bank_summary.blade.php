@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <bank-summary
        :page_data='@json($page)'
    />
@endsection
