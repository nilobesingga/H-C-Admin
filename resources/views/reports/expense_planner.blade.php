@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <expense-planner
        :page_data='@json($page)'
    />
@endsection
