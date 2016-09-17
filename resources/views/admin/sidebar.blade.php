<div class="col-md-2">
    <div class="list-group">
        <a href="{{ url()->route('admin_apply') }}" class="list-group-item {{ Request::url() == url()->route('admin_apply') ? 'active' : '' }}">审核页面</a>
        <a href="{{ url()-> route('admin_data') }}" class="list-group-item {{ Request::url() == url()->route('admin_data') ? 'active' : '' }}">数据页面</a>
    </div>
</div>
