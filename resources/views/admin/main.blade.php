@extends('layout')

@section('content')
    <h2>后台管理</h2>

    <hr>

    <div class="col-md-2">
        @yield('sidebar')
    </div>

    <div class="col-md-10">
        @section('admin_content')

        @show
    </div>
@endsection