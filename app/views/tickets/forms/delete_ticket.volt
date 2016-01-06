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