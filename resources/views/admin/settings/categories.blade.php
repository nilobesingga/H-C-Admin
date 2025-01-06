@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <categories
            :prop-data='@json($page->data)'
    />
@endsection