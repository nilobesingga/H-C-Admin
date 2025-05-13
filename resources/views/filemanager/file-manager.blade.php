@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <file-manager :page_data='@json($page)'/>
@endsection
