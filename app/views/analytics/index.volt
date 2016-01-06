<?php $this->assets->outputJs('headerDashboardJS') ?>
<?php $this->assets->outputCss('headerDashboardCSS') ?>
<ol class="breadcrumb">
    <li><a href="{{ url("dashboard") }}">Dashboard</a></li>
    <li class="active">Analytics</li>
</ol>
<script>
    $(document).ready(function () {
        $('#animate-number-1').animateNumber({ number: 16500 });
        $('#animate-number-2').animateNumber({ number: 16000 });
        $('#animate-number-3').animateNumber({ number: 1500 });
    });
</script>
<div class="row panel-row sortable">
    <div class="col-sm-6">
        <div class="panel">
            {{ widgetSales }}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel">
            {{ widgetProfitability }}
        </div>
    </div>
</div>