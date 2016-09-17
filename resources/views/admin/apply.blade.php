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
            <th class="col-md-2">姓名</th>
            <th class="col-md-2">公司</th>
            <th class="col-md-2">职位</th>
            @if(Request::url() == url()->route('admin_apply'))
                <th class="col-md-4">问题</th>
                <th class="col-md-2">操作</th>
            @else
                <th class="col-md-6">问题</th>
            @endif

        </tr>

        @foreach ($users as $user)
            <?php $applyInfo = $user->getApplyInfo(); ?>
            <tr>
                <td height="50">{{ $applyInfo['name'] }}</td>
                <td >{{ $applyInfo['company_name'] }}</td>
                <td>{{ $applyInfo['position'] }}</td>
                <td>{{ $applyInfo['question1'] }}</td>
                @if(Request::url() == url()->route('admin_apply'))
                    <td>
                        <form method="post" action="/admin/apply/approve/{{ $user['id'] }}" style="display: inline;">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-sm btn-success">通过</button>
                        </form>
                        <form method="post" action="/admin/apply/reject/{{ $user['id'] }}" style="display: inline;">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-sm btn-danger">拒绝</button>
                        </form>
                    </td>
                @endif
            </tr>
        @endforeach
    </table>
    {!! $users->links() !!}
@endsection