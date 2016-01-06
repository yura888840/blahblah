<div class="modal" id="modal-window-for-add-new-ticket-id">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horisontal" action="{{ url('api/tickets/ajaxAddTicket') }}"
                  id="ajax-table-date-tickets-add">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add New Ticket</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-4 form-post-item">
                            {{ formTicket.label("priority") }}
                            {{ formTicket.render("priority") }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-4 form-post-item">
                            {{ formTicket.label("type") }}
                            {{ formTicket.render("type") }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-4 form-post-item">
                            {{ formTicket.label("department") }}
                            {{ formTicket.render("department") }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 form-post-item">
                            {{ formTicket.label("assignTo") }}
                            {{ formTicket.render("assignTo") }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-6 form-post-item">
                            <label for="deadline">{{ formTicket.label("deadline") }}</label>

                            <div class="input-group">
                                <input type="text" id="deadline" data-toggle="dropdown" name="deadline"
                                       class="form-control"
                                       data-table-id="ajax-table-date-deadline" data-column="deadline">

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

                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group form-post-item">
                        {{ formTicket.label("notify[]") }}
                        {{ formTicket.render("notify[]") }}
                    </div>
                    <div class="form-group form-post-item">
                        {{ formTicket.label("subject") }}
                        {{ formTicket.render("subject") }}
                        <div class="item-msgs"></div>
                    </div>
                    <div class="form-group form-post-item">
                        {{ formTicket.label("description") }}
                        {{ formTicket.render("description") }}
                        <div class="item-msgs"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary save-form" type="button">Save</button>
                    <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
            <div class="panel">
                <div class="panel-heading">
                    Attach:
                </div>
                <div class="panel-body">
                    <form id="my-awesome-dropzone-add" class="dropzone">

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>