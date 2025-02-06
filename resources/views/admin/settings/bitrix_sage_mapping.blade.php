@extends('layouts.admin')
@section('pageTitle', $page->title)
@section('content')
<bitrix-sage-mapping
    :page_data='@json($page)'
/>
@endsection
