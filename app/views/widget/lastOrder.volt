<div class="panel widget">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default" data-dismiss="widget">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
        </div>
        Last 5 Orders
    </div>
    <div class="panel-body">
        <div class="responsive-table">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Order Amount</th>
                </tr>
                </thead>
                <tbody>
                {% for order in orders %}
                <tr>
                    <th data-title="#" scope="row">{{ order['row'] }}</th>
                    <td data-title="Date">{{ order['date'] }}</td>
                    <td data-title="Customer Name">{{ order['customer_name'] }}</td>
                    <td data-title="Order Amount">{{ order['subtotal'] }}</td>
                </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>