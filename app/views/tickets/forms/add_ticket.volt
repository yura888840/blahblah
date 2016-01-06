<div class="modal" id="modal-window-for-add-new-ticket-id">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                <div class="modal-header">
                    <button class="close clean-form" type="button" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add New Ticket</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horisontal" action="{{ url('api/tickets/ajaxAddTicket') }}"
                          id="ajax-table-date-tickets-add">
                    <div class="form-group row">
                        <div class="col-sm-4 form-post-item">
                            {{ form.label("priority") }}
                            {{ form.render("priority") }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-4 form-post-item">
                            {{ form.label("type") }}
                            {{ form.render("type") }}
                            <div class="item-msgs"></div>
                        </div>
                        <div class="col-sm-4 form-post-item">
                            {{ form.label("department") }}
                            {{ form.render("department") }}
                            <div class="item-msgs"></div>
                        </div>
                    </div>
                    <div class="form-group form-post-item">
                        {{ form.label("assignTo") }}
                        {{ form.render("assignTo") }}
                        <div class="item-msgs"></div>
                    </div>
                    <div class="form-group form-post-item">
                        {{ form.label("notify[]") }}
                        {{ form.render("notify[]") }}
                    </div>
                    <div class="form-group form-post-item">
                        {{ form.label("subject") }}
                        {{ form.render("subject") }}
                        <div class="item-msgs"></div>
                    </div>
                    <div class="form-group form-post-item">
                        {{ form.label("description") }}
                        {{ form.render("description") }}
                        <div class="item-msgs"></div>
                    </div>
                </form>
                <div class="form-group form-post-item">
                    <form id="my-awesome-dropzone-add" class="dropzone"></form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary save-form" type="button">Save</button>
                <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>