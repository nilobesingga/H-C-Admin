@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <bank-transfers
        :page_data='@json($page)'
    />
@endsection
