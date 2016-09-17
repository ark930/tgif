<!DOCTYPE html>
<!-- we work for human beings, currently-->
<!-- we are looking for designers, scientists and artists-->
<html>
<head lang="en">
    <title>健康你的公司</title>
    <!-- Main Meta-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link href="{{ asset('style/index.css') }}" rel="stylesheet" media="screen">
    @yield('css')
    <!-- web font-->
</head>
<body>
    @section('content')

    @show

    @if($errors->count() > 0)
        <script src="//cdn.bootcss.com/jquery/3.1.0/jquery.js"></script>

        <script>
            $(function() {
                alert('{{ $errors->first() }}');
            });
        </script>
    @endif

    @yield('custom_script')
</body>
</html>