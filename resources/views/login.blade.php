@extends('layout')

@section('content')
    <h2>登录</h2>

    <hr>

    <form role="form" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="username">手机号</label>
            <span class="text-danger">{{ Session::get('error1') }}</span>
            <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}">
        </div>
        <div class="form-group">
            <label for="password">验证码</label>
            <button id="requireVerifyCode" type="button" class="btn btn-sm btn-default">获 取</button>
            <span class="text-danger">{{ Session::get('error2') }}</span>

            <input type="text" class="form-control" id="password" name="password" value="{{ old('password') }}">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-default">登入</button>
            <span class="text-danger">{{ Session::get('error3') }}</span>
        </div>
    </form>
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