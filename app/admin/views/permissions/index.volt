{{ content() }}
<div id="alert-success-id"></div>

<ol class="breadcrumb">
    <li><a href="{{ url('admin/dashboard') }}">Admin dashboard</a></li>
    <li class="active">Permissions</li>
</ol>

<div class="container-block">
    <div class="panel">
        <div class="panel-body" style="padding:10px">
            <div id="my_container">
                <!-- start block -->
                <div id="accordion-resources">

                    <div id="collapse-1" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">


                            <div class="row">

                                <div class="col-xs-12 col-sm-3 col-lg-2">
                                    <div class="list-group">
                                        <strong>
                                            <i class="glyphicon glyphicon-info-sign"></i>
                                            <font color="#999">
                                                Information<br/><br/>
                                                In permission table shown only active Users<br/><br/>
                                                Changes will apply automatically when you click on boxes
                                            </font>
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-9 col-lg-10">
                                    <table class="table table-hover resources-checkbox-table1">
                                        <thead>
                                        <tr>
                                            <th>Users</th>
                                            <th>Assign Ticket to another person</th>
                                            <th>See either only their calendar or company calendar</th>
                                        </tr>
                                        </thead>

                                        <tbody class="vertical-tab-body-2"
                                               ajax-checked-role-id="1"
                                               ajax-checked-resource="common" id="user_id-tab-body-2">
                                        {% for ind, user in users %}
                                            <tr>
                                                <td>
                                                    {{ user['name'] }}
                                                </td>
                                                <td>
                                                    <label>
                                                        <input
                                                                class="ajax-checked-input1"
                                                                type="checkbox"
                                                                name="item"
                                                                ajax-checked-resource="common"
                                                                ajax-checked-action="ticket_assign"
                                                                user-id="{{ user['id'] }}"
                                                                ajax-checked-role-id="0"
                                                                {% if user['permissions']['ticket_assign'] %} checked="checked" {% endif %}
                                                                >
                                                        <span class="lbl padding-8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input
                                                                class="ajax-checked-input1"
                                                                type="checkbox"
                                                                name="item"
                                                                ajax-checked-resource="common"
                                                                ajax-checked-action="google_calendar"
                                                                user-id="{{ user['id'] }}"
                                                                ajax-checked-role-id="0"
                                                                {% if user['permissions']['google_calendar'] %} checked="checked" {% endif %}
                                                                >
                                                        <span class="lbl padding-8"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        {% endfor %}
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                </div>


            </div>
                <!-- end block -->
            </div>
        </div>
    </div>
</div>
