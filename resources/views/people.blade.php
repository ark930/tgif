@extends('layout')

@section('content')
    <ul class="nav nav-pills" role="tablist">
        <li role="presentation"><a href="{{ url()->route('timeline') }}">时间线</a></li>
        <li role="presentation" class="active"><a href="{{ url()->route('people') }}">个人</a></li>
        <li role="presentation"><a href="{{ url()->route('surveys') }}">问题</a></li>
    </ul>

    <hr>


@endsection