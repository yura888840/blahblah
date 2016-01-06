{{ content() }}

<script>

</script>

<div id="alert-success-id"></div>

<ol class="breadcrumb">
    <li><a href="{{ url('admin/dashboard') }}">Admin dashboard</a></li>
    <li class="active">Widgets</li>
</ol>
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-3 col-lg-2">
                        <div class="list-group">
                            {% set count = 0 %}
                            {% for name, perm in widgets %}
                                {% if count == 0 %}
                                    <a href="" class="list-group-item active vertical-tab-link" id="{{name}}">{{name}}</a>
                                {% else %}
                                    <a href="" class="list-group-item vertical-tab-link" id="{{name}}">{{name}}</a>
                                {% endif %}
                                {% set count += 1 %}
                            {% endfor %}
                             <a href="" class="list-group-item vertical-tab-link" id="all-tab-link"><b>All Widgets</b></a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-9 col-lg-10">
                        <table class="table table-hover" id="admin-widgets-table-id" ajax-url="{{ url('admin/widgets/update')}}">
                            <thead>
                            <tr>
                                <th>User group</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            {% set count = 0 %}
                            {% for name, perm in widgets %}
                                {% if count == 0 %}
                                    <tbody class="vertical-tab-body" id="{{name}}-tab-body">
                                {% else %}
                                    <tbody class="hidden vertical-tab-body" id="{{name}}-tab-body">
                                {% endif %}
                                {% set count += 1 %}
                                            <tr>
                                                <td colspan="2" style="text-align: center;">
                                                    {{name}}
                                                </td>
                                            </tr>
                                         {% for role, p in perm %}
                                            <tr>
                                                <td>
                                                    {{role}}
                                                </td>
                                                <td>
                                                    <label>
                                                        <input class="ajax-checked-input" type="checkbox" ajax-checked-role="{{ role }}"
                                                         ajax-checked-resource="{{ name }}" name="{{ role }}-{{ name }}"
                                                         ajax-checked-action="view"
                                                         {% if p==1 %}checked="checked" {% endif %}>
                                                         <span class="lbl padding-8"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        {% endfor %}
                                    </tbody>
                            {% endfor %}
                        </table>
                    </div>
                </div>
            </div>
        </div>