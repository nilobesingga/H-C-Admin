@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <bank-accounts
        :page_data='@json($page)'
    />
@endsection
