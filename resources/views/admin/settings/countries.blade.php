@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <countries
            :prop-data='@json($page->data)'
    />
@endsection