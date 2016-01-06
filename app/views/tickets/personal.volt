{{ content() }}

<div class="modal center-modal" id="ajax-delete-tablesorter-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horisontal" action="/api/tickets/ajaxDeleteTicket" id="ajax-delete-tablesorter">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Are you sure you want to delete this ticket?</h4>
                </div>
                <div class="modal-body">
                    <p><b>Subject: </b><span class="ajax-delete-info" ajax-delete-info="subject"></span></p>

                    <p><b>Type: </b><span class="ajax-delete-info" ajax-delete-info="type"></span></p>

                    <input type="text" name="_id" class="hidden not-clean" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary save-form" type="button">Delete</button>
                    <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="panel-ticket panel-ticket-list">

    <div class="panel panel-ticket-height">
        <div class="panel-body relative panel-ticket-height">

            <div class="ticket-col ticket-col-header col-md-9 col-sm-9 col-lg-10 hidden-xs">
                <ul class="nav nav-tabs nav-tabs-ticket-header pull-left"></ul>
                <ul class="nav nav-tabs nav-tabs-header-sort pull-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right not-hide">
                            <li class="dropdown-padding">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="">
                                        <span class="lbl"></span>
                                        Priority
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-padding">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="">
                                        <span class="lbl"></span>
                                        Type
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-padding">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="">
                                        <span class="lbl"></span>
                                        Department
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-padding">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="">
                                        <span class="lbl"></span>
                                        Status
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-padding">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="">
                                        <span class="lbl"></span>
                                        Date
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
                {{ partial('tickets/partials/list/head_tickets_list') }}
            </div>

            <div class="ticket-col ticket-col-left col-md-3 col-sm-3 col-lg-2">
                <div class="ticket-col-inner">

                    <button class="btn btn-primary btn-add btn-block" data-toggle="modal"
                            data-target="#modal-window-for-add-new-ticket-id">Add Task
                    </button>

                    <hr>

                    {{ partial("tickets/partials/left_menu", ['params': '']) }}
                </div>
            </div>

            <div class="ticket-col ticket-col-center col-md-9 col-sm-9 col-lg-10">
                <div class="ticket-col-inner">
                    <div class="responsive-table">
                        <table class="table table-crm table-hover ajax-tablesorter table-tickets"
                               id="ajax-table-date-tickets-list"
                               ajax-table-request-url='{{ url('api/tickets/list/ajaxGetPersonalTicketTable') }}'
                               ajax-filter-option-request-url='{{ url('api/tickets/list/ajaxGetFilterOptions') }}'>
                            <thead>
                            <tr>
                                <th class="first" data-sorter="false" data-filter="false"></th>
                                <th class="sorter-subject">Subject</th>
                                <th class="filter-select filter-select-nosort">Priority</th>
                                <th class="filter-select filter-select-nosort">Type</th>
                                <th class="filter-select filter-select-nosort">Department</th>
                                <th class="filter-select filter-select-nosort sorter-status">Status</th>
                                <th class="text-nowrap table-column-date"
                                    style="font-family: sans-serif;">Date
                                </th>
                                <th class="sorter-btn" data-filter="false"></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class='hidden delete-tablesorter-element-modal' data-toggle='modal'
                         data-target='#ajax-delete-tablesorter-modal' id='ajax-delete-tablesorter'></div>
                    <div class='hidden remove-element'></div>
                </div>
            </div>

            <div class="ticket-col ticket-col-footer col-md-9 col-sm-9 col-lg-10">
                <div class="ticket-col-inner text-center">
                    <nav class="pager" data-table-id="ajax-table-date-tickets-list">
                        {{ partial("partials/tablesorter/pager") }}
                    </nav>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    $("#personal_tasks").addClass('active').removeAttr('href');
</script>