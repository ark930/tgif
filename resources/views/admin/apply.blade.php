@extends('admin.main')

@section('admin_content')
    <!-- Nav tabs -->
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

    <!-- Table -->
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
            <tr data-toggle="modal" data-target="#modal_{{ $user['id'] }}">
                <td height="50">{{ $user['id'] }}</td>
                <td>{{ $applyInfo['name'] }}</td>
                <td>{{ $applyInfo['company_name'] }}</td>
                <td>{{ $applyInfo['position'] }}</td>
                <td>{{ $user['invite_count'] }}</td>
                <td>{{ $user['created_at'] }}</td>
            </tr>
        @endforeach
    </table>
    <!-- End of table -->

    <!-- Modal -->
    @foreach ($users as $user)
        <?php $applyInfo = $user->getApplyInfo(); ?>
        <div class="modal fade" id="modal_{{ $user['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Nav tabs -->
                <ul class="nav nav-tabs nav-justified modal-header" role="tablist">
                    <li role="presentation" class="active"><a href="#apply_info_{{ $user['id'] }}" role="tab" data-toggle="tab">审核信息</a></li>
                    <li role="presentation"><a href="#invitees_{{ $user['id'] }}" role="tab" data-toggle="tab">被邀请人</a></li>
                </ul>

                <!-- Modal Tab panes -->
                <div class="tab-content">
                    <!-- Content of apply_info -->
                    <div role="tabpanel" class="tab-pane active" id="apply_info_{{ $user['id'] }}">
                        <div class="modal-body">
                            <dl class="dl-horizontal">
                                <dt>姓名</dt><dd>{{ $applyInfo['name'] }}</dd>
                                <dt>手机</dt><dd>{{ $user['tel'] }}</dd>
                                <dt>公司</dt><dd>{{ $applyInfo['company_name'] }}</dd>
                                <dt>公司人数</dt><dd>{{ $applyInfo['company_count'] }}</dd>
                                <dt>职位</dt><dd>{{ $applyInfo['position'] }}</dd>
                                <dt>粉丝数</dt><dd>{{ $user['invite_count'] }}</dd>
                                <dt>问题</dt><dd>{{ $applyInfo['question1'] }}</dd>
                            </dl>
                            <?php $inviter = $user->inviter; ?>
                            @if($inviter)
                                <hr>
                                <h5>邀请人</h5>
                                <?php $inviterApplyInfo = $inviter->getApplyInfo(); ?>
                                <table class="table table-hover table-responsive">
                                    <tr>
                                        <th class="col-md-2">ID</th>
                                        <th class="col-md-2">姓名</th>
                                        <th class="col-md-2">公司</th>
                                        <th class="col-md-2">职位</th>
                                        <th class="col-md-2">粉丝数</th>
                                        <th class="col-md-2">创建时间</th>
                                    </tr>
                                    <tr>
                                        <td height="50">{{ $inviter['id'] }}</td>
                                        <td>{{ $inviterApplyInfo['name'] }}</td>
                                        <td>{{ $inviterApplyInfo['company_name'] }}</td>
                                        <td>{{ $inviterApplyInfo['position'] }}</td>
                                        <td>{{ $inviter['invite_count'] }}</td>
                                        <td>{{ $inviter['created_at'] }}</td>
                                    </tr>
                                </table>
                            @endif
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
                    <!-- End of content of apply_info -->

                    <!-- Content of followers -->
                    <div role="tabpanel" class="tab-pane" id="invitees_{{ $user['id'] }}">
                        <div class="modal-body">
                            <?php $invitees = $user->invitees ?>
                            @if(count($invitees) > 0)
                            <table class="table table-hover table-responsive">
                                <tr>
                                    <th class="col-md-2">ID</th>
                                    <th class="col-md-2">姓名</th>
                                    <th class="col-md-2">公司</th>
                                    <th class="col-md-2">职位</th>
                                    <th class="col-md-2">粉丝数</th>
                                    <th class="col-md-2">创建时间</th>
                                </tr>

                                @foreach ($invitees as $invitee)
                                    <?php $inviteeApplyInfo = $invitee->getApplyInfo(); ?>
                                    <tr>
                                        <td height="50">{{ $invitee['id'] }}</td>
                                        <td>{{ $inviteeApplyInfo['name'] }}</td>
                                        <td>{{ $inviteeApplyInfo['company_name'] }}</td>
                                        <td>{{ $inviteeApplyInfo['position'] }}</td>
                                        <td>{{ $invitee['invite_count'] }}</td>
                                        <td>{{ $invitee['created_at'] }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            @else
                                <p>没有数据</p>
                            @endif
                        </div>
                    </div>
                    <!-- End of Content of followers -->
                </div>
                <!-- End of Modal Tab panes -->

            </div>
        </div>
    </div>
    @endforeach
    <!-- End of modal -->

    <!-- Table paginate -->
    {!! $users->links() !!}
@endsection