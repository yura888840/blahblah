<div class="col-lg-12">
    <div class="form-group">
        {{ data['label'] }}

        <div class="input-group">
            {{ data['element'] }}

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
</div>