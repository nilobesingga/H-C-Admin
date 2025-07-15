@extends('layouts.app')
@section('pageTitle', $page->title)
@section('content')
    <dashboard
    :page_data='@json($page)'
    :user='@json($profile)'
    :companies='@json($companies)'
    :kyc_documents='@json($kycDocuments)'
    :tasks='@json($tasks)'
    :slides='@json($slides)'
/>
@endsection
