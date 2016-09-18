@extends('admin.main')

@section('admin_content')
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li role="presentation" class="{{ Request::url() == url()->route('admin_apply') ? 'active' : '' }}">
            <a href="{{ url()->route('admin_apply') }}">待审核</a>
        </li>
        <li role="presentation" class="{{ Request::url() == url()->route('admin_approve') ? 'active' : '' }}">
            <a href="{{ url()->route('admin_approve') }}">通过</a>
        </li>
        <li role="presentation" class="{{ Request::url() == url()->route('admin_reject') ? 'active' : '' }}">
            <a href="{{ url()->route('admin_reject') }}">拒绝</a>
        </li>
    </ul>

    <table class="table table-hover table-responsive">
        <tr>
            <th class="col-md-2">ID</th>
            <th class="col-md-2">姓名</th>
            <th class="col-md-2">公司</th>
            <th class="col-md-2">职位</th>
            <th class="col-md-2">粉丝数</th>
            <th class="col-md-2">创建时间</th>
        </tr>

        @foreach ($users as $user)
            <?php $applyInfo = $user->getApplyInfo(); ?>
            <tr data-toggle="modal" data-target="#{{ $user['id'] }}">
                <td height="50">{{ $user['id'] }}</td>
                <td>{{ $applyInfo['name'] }}</td>
                <td >{{ $applyInfo['company_name'] }}</td>
                <td>{{ $applyInfo['position'] }}</td>
                <td>{{ $user['invite_count'] }}</td>
                <td>{{ $user['created_at'] }}</td>
{{--                @if(Request::url() == url()->route('admin_apply'))--}}

                {{--@endif--}}
            </tr>
            <div class="modal fade" id="{{ $user['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">审核信息</h4>
                        </div>
                        <div class="modal-body">
                            <dl class="dl-horizontal">
                                <dt>姓名</dt>
                                <dd>{{ $applyInfo['name'] }}</dd>
                                <dt>公司</dt>
                                <dd>{{ $applyInfo['company_name'] }}</dd>
                                <dt>职位</dt>
                                <dd>{{ $applyInfo['position'] }}</dd>
                                <dt>问题</dt>
                                <dd>{{ $applyInfo['question1'] }}</dd>
                            </dl>
                        </div>
                        <div class="modal-footer">
                            <form method="post" action="/admin/apply/approve/{{ $user['id'] }}" style="display: inline;">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-sm btn-success">通过</button>
                            </form>
                            <form method="post" action="/admin/apply/reject/{{ $user['id'] }}" style="display: inline;">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-sm btn-danger">拒绝</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </table>
    {!! $users->links() !!}
@endsection