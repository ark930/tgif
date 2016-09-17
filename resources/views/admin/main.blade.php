<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>KYC</title>
    {{--        <link rel="stylesheet" href="{{ asset('css/app.css') }}">--}}
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>后台管理</h2>

    <hr>

    @include('admin.sidebar')

    <div class="col-md-10">
        @section('admin_content')

        @show
    </div>
</div>
{{--        <script src="{{ asset('js/app.js') }}"></script>--}}
<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>