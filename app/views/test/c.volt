<div class="row">
    <div class="form-group col-xs-12 col-md-6">
        <label>Start date</label>

        <div class="input-group">

            <input type="text" data-column="date1"
                   class="form-control text-center"
                   data-table-id="ajax-table-date-profit-product">


            <div class="input-group-addon">
                <a href="#" data-toggle="dropdown">
                    <i class="fa fa-calendar"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-block not-hide">
                    <li class="dropdown-padding-all">
                        <div class="param-datepicker datepicker" data-column="date1"
                             data-table-id="ajax-table-date-profit-product"></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="form-group">
    <label for="deadline">Deadline</label>

    <div class="input-group">
        <input type="text" id="deadline" name="deadline" value="06/28/2015" class="form-control"
               data-table-id="ajax-table-date-deadline" data-column="deadline"/>

        <div class="input-group-addon">
            <a href="#" data-toggle="dropdown">
                <i class="fa fa-calendar"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-block not-hide">
                <li class="dropdown-padding-all">
                    <div class="param-datepicker datepicker" data-column="deadline"
                         data-table-id="ajax-table-date-profit-product"></div>
                </li>
            </ul>
        </div>
    </div>
</div>


<script>
    $('div.datepicker[data-column="date1"]').datepicker({altField: '[data-column="date1"]input'});

    $('div.datepicker[data-column="date1"]').datepicker('setDate', '01/26/2014');

    $('div.datepicker[data-column="deadline"]').datepicker({altField: '[data-column="deadline"]input'});
</script>