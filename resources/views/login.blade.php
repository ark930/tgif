@extends('layout')

@section('left_top_button')
    <a href="{{ url('/') }}" class="right btn_login btn_cta">返回首页</a>
@endsection

@section('content')
    <div class="form_login">
        <form style="padding: 0.5rem" class="phone" method="post">
            {{ csrf_field() }}
            <input type="text" placeholder="输入你的手机" name="username" value="{{ old('username') ? old('username') : Session::get('username') }}" id="username">
            <button class="submit btn_cta" id="requireVerifyCode">发送验证码</button>
            <input type="text" placeholder="输入短信验证码" name="password" value="{{ old('password') }}">
            <button class="submitbtn_success btn_success">下一步</button>
        </form>
    </div>
@endsection

@section('custom_script')
    <script src="//cdn.bootcss.com/jquery/3.1.0/jquery.js"></script>

    <script>
        $(function() {
            $('#requireVerifyCode').click(function (e) {
                e.preventDefault();

                getVerifyCode();
            });

            @if(Session::has('get_verify_code'))
                console.log('get_verify_code');
                $('#requireVerifyCode').click();
            @endif

            function getVerifyCode()
            {
                var username = $('#username').val();

                $.ajax({
                    url: 'api/v1/verifycode',
                    data: {
                        username: username
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        console.log('success');
                        countdown(60, $('#requireVerifyCode'));
                    },
                    error: errorHandler
                });
            }

            function errorHandler(data)
            {
                var error = JSON.parse(data.responseText);
                console.log(error);
                alert(error.username[0]);
            }

            function countdown(time, button) {
                console.log(time);
                if (time == 0) {
                    button.attr("disabled", false);
                    button.addClass('btn_disable');
                    button.text("获取");
                } else {
                    button.attr("disabled", true);
                    button.removeClass('btn_disable');
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