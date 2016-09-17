@extends('layout')

@section('content')
    <h2>后台管理</h2>

    <hr>

    @include('admin.sidebar')

    <div class="col-md-10">
        @section('admin_content')

        @show
    </div>
@endsection