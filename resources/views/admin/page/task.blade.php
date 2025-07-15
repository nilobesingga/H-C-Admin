@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
    <admin-task
        :page_data='@json($page)'
        :user='@json($user)'
        :module='@json($module)'
    />
@endsection
