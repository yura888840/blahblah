<div class="panel widget">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <button class="btn btn-default collapsed" type="button" data-toggle="collapse" data-target="#panel-settings-profit" aria-expanded="false">
                <i class="glyphicon glyphicon-cog"></i>
            </button>
            <button type="button" class="btn btn-default" data-dismiss="widget">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
        </div>
        Profit analytic
        <div class="panel-heading-footer">
            <span id="ajax-table-date-profit-product-date1"></span>
            <span id="ajax-table-date-profit-product-date2"></span>
            <span id="ajax-table-date-profit-product-channel"></span>
            <span>Percent profit</span>
            <span id="ajax-table-date-profit-product-percent"></span>
        </div>
        <div class="panel-heading-footer">
            <label>The total for the report</label>
            <label id="total-sum">-</label>
            <label>$</label>
        </div>
    </div>
    <div class="panel-body">
        <div class="widget-settings collapse" id="panel-settings-profit" aria-expanded="false">
            <div class="row">
                <div class="form-group col-xs-12 col-md-6">
                    <label>Start date</label>
                    <div class="input-group">
                        <input type="text" data-column="date1"
                               class="form-control text-center"
                               data-table-id = "ajax-table-date-profit-product">

                        <div class="input-group-addon">
                            <a href="#" data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-block not-hide">
                                <li class="dropdown-padding-all">
                                    <div class="param-datepicker datepicker" data-column="date1"
                                         data-table-id = "ajax-table-date-profit-product"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-md-6">
                    <label>End date</label>
                    <div class="input-group">
                        <input type="text" data-column="date2"
                               class="form-control text-center"
                               data-table-id = "ajax-table-date-profit-product">

                        <div class="input-group-addon">
                            <a href="#" data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-block not-hide">
                                <li class="dropdown-padding-all">
                                    <div class="param-datepicker datepicker" data-column="date2"
                                         data-table-id = "ajax-table-date-profit-product"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Channel</label>
                    <select
                            class="param-select form-control"
                            data-table-id = "ajax-table-date-profit-product"
                            data-column="company"
                            data-filter-type="company">
                        {% for filterVal in stores %}
                        <option value="{{ filterVal['value'] }}">{{ filterVal['text'] }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Percent profit</label>
                    <div class="input-group spinner">
                        <input type="text"
                               data-column="percentProfit"
                               class="param form-control text-left"
                               data-table-id = "ajax-table-date-profit-product"
                               value="{{percentProfit}}">
                        <div class="input-group-btn-vertical">
                            <button class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                            <button class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <input type="button" class="btn btn-primary pull-right profit-button" value="Apply">
                </div>
            </div>
        </div>
        <div class="responsive-table">
            <table class="table table-hover ajax-tablesorter table-widget-profit-product" id="ajax-table-date-profit-product"
                   ajax-table-request-url="/api/profitProducts">
                <thead>
                <tr>
                    <th>Sku</th>
                    <th>Product Name</th>
                    <th data-filter="false">Profit($)</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <nav class="pager" data-table-id = "ajax-table-date-profit-product">
            {{ partial("partials/tablesorter/pager") }}
        </nav>
    </div>
</div>
<script>
    $('div.datepicker[data-column="date1"]').datepicker({ altField: '[data-column="date1"]input' });
    $('div.datepicker[data-column="date2"]').datepicker({ altField: '[data-column="date2"]input' });
    widgetProfitAnalytic = {};
    widgetProfitAnalytic.baseUri = '{{baseUri}}';
    ajaxTablesorterStack('#ajax-table-date-profit-product', [[2,0]]);
    $( ".profit-button" ).click(function() {
        updateAddParamInStorageFromForm ("ajax-table-date-profit-product");
        $.tablesorter.destroy( $("#ajax-table-date-profit-product"), true, function(table){});
        ajaxTablesorterStack('#ajax-table-date-profit-product', [[2,0]]);
    });
    $(function(){
        $('#ajax-table-date-profit-product').bind('filterStart sortStart pagerBeforeInitialized ' +
        'pagerChange', function(){
            $("#ajax-table-date-profit-product").closest('.panel.widget').prepend('<div class="loading active"></div>');
        });
    });

    $('#ajax-table-date-profit-product').on('ajaxSuccess', function(event, data){
        $('label#total-sum').html(data.sum_total);
        $('span#ajax-table-date-profit-product-date1').html($('[data-column=date1][data-table-id=ajax-table-date-profit-product]').val());
        $('span#ajax-table-date-profit-product-date2').html($('[data-column=date2][data-table-id=ajax-table-date-profit-product]').val());
        if ($('[data-column=company][data-table-id=ajax-table-date-profit-product]').find("option:selected").val() == 0) {
            channel = 'All channel';
        } else {
            channel = $('[data-column=company][data-table-id=ajax-table-date-profit-product]').find("option:selected").html();
        }
        $('span#ajax-table-date-profit-product-channel').html(channel);
        $('span#ajax-table-date-profit-product-percent').html($('[data-column=percentProfit][data-table-id=ajax-table-date-profit-product]').val());
        $('#ajax-table-date-profit-product tr').on('click', function(){
            $('#ajax-table-date-profit-product tr').not(this).popover('hide');
        });
        popoverSet ();
    });

    function popoverSet () {
        $('#ajax-table-date-profit-product tbody tr').popover({
            placement : 'bottom',
            html: true,
            title: 'Through the channels',
            content: function (){
                var div_id =   "tmp-id-"  + $.now();
                link = '{{ url("api/profitProductOne?") }}'
                    +getAddUrl('ajax-table-date-profit-product').substr(1)
                    +'&product_id='+$(this).children("td").children('span').attr('id');
                return details_in_popup ( link, div_id );
            }
        });
    }

    function details_in_popup(link, div_id){
        $.ajax({
            url: link,
            success: function(response){
                $('#'+div_id).html(response);
            }
        });
        return '<div id="'+ div_id +'">Loading...</div>';
    }
</script>
