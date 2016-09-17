@extends('layout')

@section('content')
    <div class="header raw_lr">
        <a href="{{ url('/login') }}" class="home left">
            <img src="{{ asset('img/logo.svg') }}" alt="TGIF" class="logo">
        </a>
        <a href="{{ url('/login') }}" class="right btn_login">登入</a>
    </div>
    <div class="form_login">
        <form style="padding: 0.5rem" class="phone" method="post">
            {{ csrf_field() }}
            <input type="text" placeholder="输入你的手机" name="username" value="{{ old('username') }}" id="username">
            <button class="submit" id="requireVerifyCode">发送验证码</button>
            <span class="text-danger">{{ Session::get('error1') }}</span>

            <input type="text" placeholder="输入短信验证码" name="password" value="{{ old('password') }}">
            <button class="submit">下一步</button>
            <span class="text-danger">{{ Session::get('error2') }}</span>
            <span class="text-danger">{{ Session::get('error3') }}</span>
        </form>
    </div>


    <script src="//cdn.bootcss.com/jquery/3.1.0/jquery.js"></script>

    <script>
        $(function() {
            $('#requireVerifyCode').click(function (e) {
                e.preventDefault();

                var username = $('#username').val();
                if (!username) {
                    alert('请输入手机号');
                    return;
                }

                $.ajax({
                    url: 'api/v1/verifycode',
                    data: {
                        tel: username
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        console.log('success');
                        countdown(60, $('#requireVerifyCode'));
                    },
                    error: errorHandler
                });
            });

            function errorHandler(data)
            {
                var error = JSON.parse(data.responseText);
                alert(error.msg);
            }

            function countdown(time, button) {
                console.log(time);
                if (time == 0) {
                    button.attr("disabled", false);
                    button.text("获取");
                } else {
                    button.attr("disabled", true);
                    button.text("重新发送(" + time + ")");
                    time--;
                    setTimeout(function () {
                        countdown(time, button)
                    }, 1000);
                }
            }
        });
    </script>
@endsection