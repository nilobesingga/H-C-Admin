@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <qashio-transactions
        :page_data='@json($page)'
    />
@endsection
