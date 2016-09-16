@extends('admin.main')

@section('sidebar')
    <div class="list-group">
        <a href="{{ url()->route('admin_apply') }}" class="list-group-item">审核页面</a>
        <a href="{{ url()-> route('admin_data') }}" class="list-group-item active">数据页面</a>
    </div>
@endsection
@section('admin_content')
    数据页面
@endsection
