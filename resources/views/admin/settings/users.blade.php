@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <users
        :page_data='@json($page)'
    />
@endsection
