@extends('layouts.app')
@section('pageTitle', $page->title)

@section('content')
    <account-setting
    :page_data='@json($page)'
    :user='@json($user)'
    :contact='@json($contact)'
    />
@endsection
