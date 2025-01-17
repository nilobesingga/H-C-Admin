@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <countries
            :page_data='@json($page)'
    />
@endsection
