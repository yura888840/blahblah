{{ content() }}
<div id="alert-success-id"></div>

<ol class="breadcrumb">
    <li><a href="{{ url('admin/index') }}">Admin dashboard</a></li>
    <li class="active">Users</li>
</ol>
<div class="container-block">
    <div class="btn btn-primary" data-toggle="modal" data-target="#modal-window-for-add-new-user-id">Add New User</div>
</div>

<div class="panel">
    <table class="table table-hover ajax-tablesorter" id="ajax-table-date-users"
           ajax-table-request-url='{{ url('admin/users/ajaxTablesorterUser') }}'
           ajax-filter-option-request-url='{{ url('admin/users/ajaxGetFilterOptions') }}'>
        <thead>
            <tr>
                <th class="width-5pr hidden-xs" data-sorter="false" data-filter="false"></th>
                <th class="width-30pr text-nowrap">Name</th>
                <th class="width-20pr text-nowrap hidden-xs">E-mail</th>
                <th class="width-10pr text-nowrap hidden-xs filter-select filter-select-nosort">Role</th>
                <th class="width-10pr text-nowrap hidden-xs filter-select filter-select-nosort">Status</th>
                <th class="width-15pr text-nowrap hidden-xs" data-filter="false">Last Modify</th>
                <th class="width-10pr" data-sorter="false" data-filter="false"></th>
            </tr>
         </thead>
         <tbody>
         </tbody>
    </table>
        <nav class="pager" id="ajax-table-date-users">
            {{ partial("partials/tablesorter/pager") }}
        </nav>
</div>


<div class="modal" id="modal-window-for-add-new-user-id">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horisontal" action="{{ url('/admin/users/ajaxAddUser') }}" id="ajax-table-date-users">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add New User</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ form.label('email') }}
                            {{ form.render('email') }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ form.label('name') }}
                            {{ form.render('name') }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ form.label('profile') }}
                            {{ form.render('profile') }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ form.label('status') }}
                            {{ form.render('status') }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ form.label('password') }}
                            {{ form.render('password') }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ form.label('confirmPassword') }}
                            {{ form.render('confirmPassword') }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary save-form" type="button">Save</button>
                    <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="ajax-edit-tablesorter-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horisontal" action="{{ url('admin/users/ajaxEditUser') }}" id="ajax-edit-tablesorter">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit User</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ forms.get('edit').label('email') }}
                            {{ forms.get('edit').render('email') }}

                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ forms.get('edit').label('name') }}
                            {{ forms.get('edit').render('name') }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ forms.get('edit').label('profile') }}
                            {{ forms.get('edit').render('profile') }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ forms.get('edit').label('status') }}
                            {{ forms.get('edit').render('status') }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ forms.get('edit').label('password') }}
                            {{ forms.get('edit').render('password') }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ forms.get('edit').label('confirmPassword') }}
                            {{ forms.get('edit').render('confirmPassword') }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <input type="text" name="_id" class="hidden not-clean" value="">
                    If yo do not want to change password please leave fields with passwords empty
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary save-form" type="button">Save</button>
                    <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal center-modal" id="ajax-delete-tablesorter-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horisontal" action="{{ url('admin/users/ajaxDeleteUser') }}" id="ajax-delete-tablesorter">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Are you sure you want to delete this user?</h4>
                </div>
                <div class="modal-body">
                    <p><b>Name: </b><span class="ajax-delete-info" ajax-delete-info="name"></span></p>
                    <p><b>E-mail: </b><span class="ajax-delete-info" ajax-delete-info="email"></span></p>
                    <input type="text" name="_id" class="hidden not-clean" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary save-form" type="button">Delete</button>
                    <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class='hidden edit-tablesorter-element-modal'  data-toggle='modal' data-target='#ajax-edit-tablesorter-modal' id='ajax-edit-tablesorter'></div>
<div class='hidden delete-tablesorter-element-modal'  data-toggle='modal' data-target='#ajax-delete-tablesorter-modal' id='ajax-delete-tablesorter'></div>
<div class='hidden remove-element'></div>
<script>
    ajaxTablesorterStack('#ajax-table-date-users', [[5,0]], '#ajax-edit-tablesorter', '#ajax-delete-tablesorter');
</script>
