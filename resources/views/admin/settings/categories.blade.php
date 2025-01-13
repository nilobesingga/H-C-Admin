@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <categories
        :page_data='@json($page)'
    />
@endsection
