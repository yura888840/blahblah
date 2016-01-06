<?php $this->assets->outputJs('headerDashboardJS') ?>
<?php $this->assets->outputCss('headerDashboardCSS') ?>
<ol class="breadcrumb">
    <li class="active">Dashboard</li>
</ol>
<script>
    $(document).ready(function () {

        $('#animate-number-1').animateNumber({number: 35.15});
        $('#animate-number-2').animateNumber({number: 1234.56});
        $('#animate-number-3').animateNumber({number: 25});
    });
</script>
<!-- Тут - однострочный виджет -->
<div class="row">
    <div class="col-md-6">
        <div class="sortable" placeholderid="1">
            {% for i, widget in grid[1] %}
                {{ widgets[widget] }}
            {% endfor %}
        </div>
    </div>
    <div class="col-md-6">
        <div class="sortable" placeholderid="2">
            {% for i, widget in grid[2] %}
                {{ widgets[widget] }}
            {% endfor %}
        </div>
    </div>
</div>