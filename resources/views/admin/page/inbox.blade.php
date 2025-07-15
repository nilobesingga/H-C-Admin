@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <admin-inbox
        :page_data='@json($page)'
        :user='@json($page->user)'
        :module='@json($module)'
    />
@endsection
