@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <qashio-admin
        :page_data='@json($page)'
    />
@endsection
