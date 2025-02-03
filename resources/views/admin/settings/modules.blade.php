@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <modules
        :page_data='@json($page)'
    />
@endsection
