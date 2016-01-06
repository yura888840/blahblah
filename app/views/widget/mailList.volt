<div class="panel widget">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default" data-dismiss="widget">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
        </div>
        Last 5 Emails
    </div>
    <div class="panel-body">
        <div class="responsive-table">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                {% for mail in mails %}
                <tr>
                    <th scope="row">{{ mail['row'] }}</th>
                    <td>{{ mail['from_name'] }}</td>
                    <td>{{ mail['subject'] }}</td>
                    <td>{{ mail['date'] }}</td>
                </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>