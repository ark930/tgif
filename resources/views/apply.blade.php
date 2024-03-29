@extends('layout')

@section('css')
    <link href="{{ asset('style/queue.css') }}" rel="stylesheet" media="screen">
@show

@section('left_top_button')
    <a href="{{ url('/logout') }}" class="right btn_login btn_cta">{{ Session::get('user')['tel'] }}</a>
@endsection

@section('content')
    <div class="hero"><span id="queue">{{ Session::get('user')->rank() }}</span><br>
        <p>个用户正排在你前面</p>
        <p>要更早开始使用，你可以通过一下几种方式</p>
    </div>

    <div class="step step1">
        <div class="number">1</div>
        <form method="post">
            {{ csrf_field() }}
            <input type="hidden" name="save_question" value="true">
            <h6 class="title">说说你最想问你的员工哪些问题？</h6>
            <textarea id="fill_questions" wrap="on" type="text" name="question1">{{ null !== old('question1') ? old('question1') : $question1 }}</textarea>
            <span class="text-danger">{{ Session::get('error_question1') }}</span>
            <button id="submit_question" class="submit btn_disable" disabled>保存</button>
        </form>
    </div>

    <div class="step step2">
        <div class="number">2</div>
        <p>通过你的分享链接，邀请你的 CEO 朋友一起来使用</p><span>{{ $invite_link }}</span>
    </div>

    <div class="step step3">
        <div class="number">3</div>
        <h6>完善你的基本信息</h6>
        <form method="post">
            {{ csrf_field() }}
            <input type="hidden" name="save_basic_info" value="true">
            <div class="formitem">
                <h6 class="title">你的姓名</h6>
                <input id="name_input" type="text" name="name" value="{{ null !== old('name') ? old('name') : $name }}">
                <span class="text-danger">{{ Session::get('error_name') }}</span>
            </div>
            <div class="formitem">
                <h6 class="title">你的公司</h6>
                <input id="company_name_input" type="text" name="company_name" value="{{ null !== old('company_name') ? old('company_name') : $company_name }}">
                <span class="text-danger">{{ Session::get('error_company_name') }}</span>
            </div>
            <div class="formitem">
                <h6 class="title">你的职位</h6>
                <input id="position_input" type="text" name="position" value="{{ null !== old('position') ? old('position') : $position }}">
                <span class="text-danger">{{ Session::get('error_position') }}</span>
            </div>
            <div class="formitem">
                <h6 class="title">公司人数</h6>
                <input id="company_count_input" type="text" name="company_count" value="{{ null !== old('company_count') ? old('company_count') : $company_count }}">
                <span class="text-danger">{{ Session::get('error_company_count') }}</span>
            </div>
            <button id="submit_personinfo" class="submit btn_disable" disabled>保存</button>
        </form>
    </div>
@endsection

@section('custom_script')
    <script src="//cdn.bootcss.com/jquery/3.1.0/jquery.js"></script>

    <script>
        $(function() {
            var fill_questions_old_val = $('#fill_questions').val();
            var name_input_old_val = $('#name_input').val();
            var company_name_input_old_val = $('#company_name_input').val();
            var position_input_old_val = $('#position_input').val();
            var company_count_input_old_val = $('#company_count_input').val();

            button_handler(fill_questions_old_val, $('#fill_questions'), $('#submit_question'));
            button_handler(name_input_old_val, $('#name_input'), $('#submit_personinfo'));
            button_handler(company_name_input_old_val, $('#company_name_input'), $('#submit_personinfo'));
            button_handler(position_input_old_val, $('#position_input'), $('#submit_personinfo'));
            button_handler(company_count_input_old_val, $('#company_count_input'), $('#submit_personinfo'));
        });

        function button_handler(old_val, text, button)
        {
            text.bind('change keyup paste', function() {
                var current_val = $(this).val();
                if(current_val == old_val) {
                    disable_button(button);
                    return; //check to prevent multiple simultaneous triggers
                }
                enable_button(button);

            });
        }

        function disable_button(button)
        {
            button.attr("disabled", true);
            button.removeClass('btn_cta');
            button.addClass('btn_disable');
        }

        function enable_button(button)
        {
            button.attr("disabled", false);
            button.removeClass('btn_disable');
            button.addClass('btn_cta');
        }
    </script>
@endsection