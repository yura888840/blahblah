<div data-wdget-type="{{ typeWidget }}">
    <div class="panel widget">
        <div class="panel-heading">
            <div class="btn-group pull-right">
                <button class="btn btn-default dropdown-toggle line-chart" type="button">
                    <i class="fa fa-line-chart"></i>
                </button>
                <button class="btn btn-default dropdown-toggle area-chart" type="button">
                    <i class="fa fa-area-chart"></i>
                </button>
                <button class="btn btn-default dropdown-toggle discrete-bar-chart" type="button">
                    <i class="fa fa-bar-chart"></i>
                </button>
                <!--<button class="btn btn-default discrete-bar-chart" type="button">-->
                    <!--<i class="fa fa-pie-chart"></i>-->
                <!--</button>-->
                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="collapse" data-target="#widget-settings-{{ typeWidget }}">
                    <i class="glyphicon glyphicon-cog"></i>
                </button>
                <button type="button" class="btn btn-default" data-dismiss="widget">
                    <i class="glyphicon glyphicon-remove"></i>
                </button>
            </div>
            {{ name }}
        </div>
        <div class="panel-body sales-div">
            <div class="widget-settings collapse" id="widget-settings-{{ typeWidget }}">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <select class="form-control graf-block-select" data-filter-type="indicators">
                                    {% for filterVal in filter1 %}
                                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control graf-block-select" data-filter-type="intervals">
                                    {% for filterVal in filter2 %}
                                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <select class="form-control graf-block-select" data-filter-type="period">
                                    {% for filterVal in filter3 %}
                                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control graf-block-select" data-filter-type="company">
                                    {% for filterVal in filter4 %}
                                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <select class="form-control graf-block-select" data-filter-type="clients">
                                    {% for filterVal in filter5 %}
                                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <select class="form-control graf-block-select" data-filter-type="goods">
                                    {% for filterVal in filter6 %}
                                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="chart-div" id="{{idDiv}}"></div>
        </div>
        <script>
            idDiv='{{idDiv}}';
            typeWidget='{{ typeWidget }}';
            data{{idDiv}} = JSON.parse('{{ chartDataJson }}');
            typeChart = 'discreteBarChart';//"discreteBarChart" or "lineChart"
            widget.create (idDiv, data{{idDiv}}, typeChart, typeWidget);
        </script>
    </div>
</div>