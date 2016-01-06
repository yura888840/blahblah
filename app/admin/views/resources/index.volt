{{ content() }}
<div id="alert-success-id"></div>

<ol class="breadcrumb">
    <li><a href="{{ url('admin/dashboard') }}">Admin dashboard</a></li>
    <li class="active">Resources</li>
</ol>
<div class="container-block">
    <div class="btn btn-primary" data-toggle="modal" data-target="#modal-window-for-add-new-role-id">Add New Role</div>
</div>
{% set numActions = actions|length %}
<div id="accordion-resources">
    {% for i, profile in profiles %}
        <div class="panel">
            <div class="panel-heading">
                <div class="btn-group btn-group-sm pull-right">
                    <a class="btn btn-default ajax-role-edit" ajax-element-id="edit-{{ profile['id'] }}"
                       role_id="{{ profile['id'] }}"
                       active="{{ profile['is_active'] }}" modified_at="{{ profile['last_modified'] }}"
                       role_name="{{ profile['name'] }}">
                        <i class="glyphicon glyphicon glyphicon-pencil"></i>
                    </a>
                    <a class="btn btn-default" ajax-element-id="delete-{{ profile['id'] }}"
                       id="ajax-role-delete">
                        <i class="glyphicon glyphicon glyphicon-remove"></i>
                    </a>
                </div>
                <a data-toggle="collapse" data-parent="#accordion-resources" href="#collapse-{{ i }}"
                   aria-expanded="true"
                   aria-controls="collapse-{{ i }}">
                    {{ profile['name'] }} {% if profile['is_active'] != 'Y' %} <strong>INACTIVE</strong> {% endif %}
                </a>
            </div>
            <div id="collapse-{{ i }}" class="panel-collapse collapse">
                <div class="panel-body">


                    <div class="row">
                        <div class="col-xs-12 col-sm-3 col-lg-2">
                            <div class="list-group">
                                {% for iRes, resource in resources %}
                                    <a href=""
                                       class="list-group-item {% if iRes == 0 %} active {% endif %} vertical-tab-link-2"
                                       ajax-checked-role-id="{{ profile['id'] }}"
                                       ajax-checked-resource="{{ resource }}">{{ resource }}</a>
                                {% endfor %}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-9 col-lg-10">
                            <table class="table table-hover resources-checkbox-table">
                                <thead>
                                <tr>
                                    <th>Users</th>
                                    {% for action in actions %}
                                        <th>{{ action }}</th>
                                    {% endfor %}
                                </tr>
                                </thead>
                                {% for iRes, resource in resources %}
                                    <tbody class="vertical-tab-body-2 {% if iRes != 0 %} hidden {% endif %}"
                                           ajax-checked-role-id="{{ profile['id'] }}"
                                           ajax-checked-resource="{{ resource }}" id="{{ profile['id'] }}-tab-body-2">
                                    <tr>
                                        <td colspan="{{ numActions + 1 }}"
                                            style="text-align: center;">{{ resource }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Default for all users in group</strong>
                                        </td>
                                        {% for action in actions %}
                                        <td>
                                            <label>
                                                <input class="ajax-checked-input" type="checkbox" name="item"
                                                       ajax-checked-resource="{{ resource }}"
                                                       ajax-checked-action="{{ action }}"
                                                       user-id="group_{{ profile['name'] }}" ajax-checked-role-id="0"
                                                       checked="checked">

                                                <span class="lbl padding-8"></span>
                                            </label>
                                        </td>
                                        {% endfor %}
                                    </tr>
                                    {% for ind, user in users[profile['name']] %}
                                        <tr>
                                            <td>
                                                {{ user['name'] }}
                                            </td>

                                            {% for action in actions %}
                                                <td>
                                                    <label>
                                                        <input
                                                                class="ajax-checked-input"
                                                                type="checkbox"
                                                                name="item"
                                                                ajax-checked-resource="{{ resource }}"
                                                                ajax-checked-action="{{ action }}"
                                                                user-id="{{ user['id'] }}"
                                                                ajax-checked-role-id="0"
                                                                checked="checked"
                                                                >
                                                        <span class="lbl padding-8"></span>
                                                    </label>
                                                </td>
                                            {% endfor %}
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                {% endfor %}
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    {% endfor %}

<div class="modal" id="modal-window-for-add-new-role-id">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horisontal" action="/admin/Resources/ajaxAddRole" id="ajax-table-date-roles">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add New Role</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ roleForm.label('name') }}
                            {{ roleForm.render('name') }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            {{ roleForm.label('active') }}
                            {{ roleForm.render('active') }}
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

    <div class="modal" id="ajax-edit-tablesorter-role-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horisontal" action="{{ url('/admin/resources/ajaxEditRole') }}"
                      id="ajax-edit-tablesorter-role">
                    <div class="modal-header">
                        <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Role</h4>
                </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-sm-6 form-post-item">
                                {{ roleForm.label('name') }}
                                {{ roleForm.render('name') }}
                                <div class="item-msgs"></div>
                            </div>
                            <div class="col-sm-6 form-post-item">
                                {{ roleForm.label('active') }}
                                {{ roleForm.render('active') }}
                                <div class="item-msgs"></div>
                            </div>
                        </div>

                        <input type="text" name="_id" class="hidden not-clean" value="aa">
                        <input type="hidden" name="fl" value="1">
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
            <form class="form-horisontal" action="/admin/resources/ajaxDeleteRole" id="ajax-delete-tablesorter">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Are you sure you want to delete this role?</h4>
                </div>
                <div class="modal-body">
                    <p><b>Role: </b><span class="ajax-delete-info" ajax-delete-info="name"></span></p>
                    <p><b>Active: </b><span class="ajax-delete-info" ajax-delete-info="active"></span></p>

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

<div class='hidden edit-tablesorter-element-modal'  data-toggle='modal' data-target='#ajax-edit-tablesorter-role-modal' id='ajax-edit-tablesorter-role'></div>
<div class='hidden delete-tablesorter-element-modal'  data-toggle='modal' data-target='#ajax-delete-tablesorter-modal' id='ajax-delete-tablesorter'></div>
<div class='hidden remove-element'></div>