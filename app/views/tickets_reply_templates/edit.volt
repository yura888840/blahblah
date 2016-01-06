{{ content() }}
<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
<script src="http://cdn.ckeditor.com/4.4.7/standard/adapters/jquery.js"></script>
<script>
    $(document).ready(function () {
        colHeight();
    });
    $(window).resize(function () {
        colHeight();
    });
    $(document).ready(function () {
        $('textarea#editor1').ckeditor();
    });
</script>

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

                <div class="ticket-col-inner" style="padding-left: 20px;padding-right: 20px;">


                    {{ flashSession.output() }}
                    <h2>Editing reply template</h2>
                    <br>
                    <input type="hidden" id="tpl_id" value="{{ tpl._id }}">
                    <label for="tpl_name">Name:</label>
                    <input type="text" id="tpl_name" value="{{ tpl.name }}">
                    <br><br>
                    <label for="editor1">Template Body:</label>
                    <textarea id="editor1">{{ tpl.body }}</textarea>
                    <br><br>
                    <button class="btn btn-primary" name="Save" id="save_template">Save Template</button>


                </div>
            </div>

            <div class="ticket-col ticket-col-footer col-md-9 col-sm-9 col-lg-10">

            </div>

        </div>
    </div>

</div>


<script>
    $("#replytemplates").addClass('active').removeAttr('href');
</script>

<script>

    $(document).ready(function () {
        $("#save_template").on("click", function (e) {
            var id = $("#tpl_id").val(),
                    name = $("#tpl_name").val(),
                    body = $("#editor1").val();

            $.ajax({

                method: "POST",
                url: '/api/reply_templates/savetemplate/' + id,
                dataType: "json",
                data: {name: name, body: body},
                statusCode: parseStatusCode(),

                success: function (data) {
                    checkAuthorizationJson(data);

                    window.location.href = "{{ url("tickets_reply_templates") }}";
                }

            });

        });
    });
</script>