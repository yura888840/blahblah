<style>
    .pieChart {
        margin: 0px;
        padding: 0px;
        height: 250px;
        width: 100%;
    }
</style>
<div class="panel widget">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default" data-dismiss="widget">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
        </div>
        Open Tickets By Priority
    </div>
    <div class="panel-body">
        <div class="pieChart" id="divOpenTicketsByPriority">

        </div>
    </div>
</div>

<script>
   pieChartTicketsPriority = {};
   var testdata = JSON.parse('{{ data }}');
   text = '';
   datachart = [];
   datachart.push(['Priority', 'Count']);
   for (i in testdata) {
       datachart.push([testdata[i].key, testdata[i].y]);
   }

   google.load("visualization", "1", {packages:["corechart"]});
   google.setOnLoadCallback(drawChart);
   function drawChart() {
       var data = google.visualization.arrayToDataTable(datachart);

       var options = {
           legend: 'none',
           pieSliceText: 'label',
           is3D: true,
           'chartArea': {'width': '100%', 'height': '100%'}
       };

       var chart = new google.visualization.PieChart(document.getElementById('divOpenTicketsByPriority'));
       chart.draw(data, options);
   }
</script>