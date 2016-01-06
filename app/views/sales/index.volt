{{ content() }}
<ol class="breadcrumb">
    <li><a href="{{ url("dashboard") }}">Dashboard</a></li>
    <li><a href="{{ url("sales") }}">Sales</a></li>
    <li class="active">Orders</li>
</ol>
<div class="container-block">
    <div class="pull-right">
        <a href="#" class="btn btn-primary" type="button" data-toggle="modal" data-target=".modal">
            <i class="glyphicon glyphicon-cog"></i>
        </a>
    </div>
    <div class="pull-left">
        <a href="#" class="btn btn-primary">Add</a>
    </div>
</div>
<div class="panel">
    {{ flashSession.output() }}
    <table class="table table-hover table-crm tablesorter">
        <thead>
        <tr>
            <th class="first" data-sorter="false" data-filter="false"></th>
            <th>Subject</th>
            <th class="filter-select filter-onlyAvail">Priority</th>
            <th>Department</th>
            <th class="filter-select filter-onlyAvail">Status</th>
            <th class="filter-select filter-onlyAvail">Status</th>
            <th data-sorter="false" data-filter="false"></th>
        </tr>
        </thead>
        <tbody>
        {% for i in 1..10 %}
        <tr>
            <td class="hidden-xs text-center">
                <label>
                    <input type="checkbox" name="item">
                    <span class="lbl padding-8"></span>
                </label>
            </td>
            <td><a href="#">test</a></td>
            <td>test</td>
            <td>test</td>
            <td>test</td>
            <td>test</td>
            <td class="text-right text-nowrap">
                <a class="btn" href="#"><i class="glyphicon glyphicon glyphicon-pencil"></i></a>
                <a class="btn" href="#"><i class="glyphicon glyphicon glyphicon-remove"></i></a>
            </td>
        </tr>
        {% endfor %}
        </tbody>
    </table>
</div>
