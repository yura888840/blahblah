<style>
    .RecentTickets .status {
        text-align: center;
    }
</style>
<div class="panel widget">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default" data-dismiss="widget">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
        </div>
        Recent Tickets
    </div>
    <div class="panel-body">
        <div class="RecentTickets" id="RecentTickets">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Created</th>
                    <th>Subject</th>
                    <th class="status">Status</th>
                </tr>
                </thead>
                <tbody>
                {% for ticket in data %}
                <tr>
                    <td scope="row">{{ loop.index }}</td>
                    <td><a href="{{ url('tickets/viewTicket/') }}{{ ticket['id'] }}" target="_blank">{{ ticket['created'] }}</a></td>
                    <td><a href="{{ url('tickets/viewTicket/') }}{{ ticket['id'] }}" target="_blank">{{ ticket['subject'] }}</a></td>
                    <td class="status"><span class="badge status-badge status-{{ ticket['status'] }}" title="{{ ticket['status'] }}">{{ ticket['status'] }}</span></td>
                </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>
