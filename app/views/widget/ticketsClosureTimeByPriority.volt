<div class="panel widget">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default" data-dismiss="widget">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
        </div>
        Ticket Closure Time By Priority
    </div>
    <div class="panel-body">
        <div id="pieChartTicketsClosure">

        </div>
    </div>
</div>

<script>
    pieChartTicketsClosure = {};
    pieChartTicketsClosure.data = JSON.parse('{{ data }}');

    google.load("visualization", "1.1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable
        (pieChartTicketsClosure.data);

        var options = {
            hAxis: {title: 'Priority', minValue: 0, maxValue: 10},
            vAxis: {title: 'Days'},
            legend: 'none',
            pointSize: 20,
            pointShape: 'square'
        };

        var chart = new google.visualization.LineChart(document.getElementById('pieChartTicketsClosure'));
        chart.draw(data, options);
    }
</script>