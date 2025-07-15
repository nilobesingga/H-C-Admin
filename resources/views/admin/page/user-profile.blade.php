@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <user-profile
        :page_data='@json($page)'
        :user='@json($page->user)'
        :module='@json($module)'
    />
@endsection
