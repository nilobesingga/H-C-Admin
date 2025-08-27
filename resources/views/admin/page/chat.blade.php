@extends('layouts.admin')

@section('content')
 <quick-chat
        :page_data='@json($page)'
        :user='@json($page->user)'
        :module='@json($module)'
    />

@endsection
