{{ partial("tickets/partials/comment_templates") }}

<div class="panel-ticket">
    <input type="hidden" id="ticket_id" value="{{ email["_id"] }}">

    <div class="panel panel-ticket-height">
        <div class="panel-body relative panel-ticket-height">

            <div class="ticket-col ticket-col-header col-md-7 col-sm-6 hidden-xs">
                <ul class="nav nav-tabs nav-tabs-ticket-header pull-left">
                    <li class="active">
                        <span>
                            <i class="fa fa-ticket nav-tabs-icon"></i>
                            <span class="nav-tabs-link">{{ ticket["subject"] }}</span>
                        </span>
                    </li>

                </ul>
                {{ partial('tickets/partials/list/head_tickets_list') }}
            </div>
            <div class="ticket-col ticket-col-left col-md-2 col-sm-3">
                <div class="ticket-col-inner">

                    <button class="btn btn-primary btn-add btn-block" data-toggle="modal"
                            data-target="#modal-window-for-add-new-ticket-id">Add Task
                    </button>
                    <hr>
                    <div class="list-group margin-0">
                        {{ partial("tickets/partials/left_menu", ['params': '']) }}
                        <!--
                                                <br/>
                                                <strong>DEBUG PANEL (js )</strong>
                                                <br/>
                                                <a id="add_ticket_ontop">Add this ticket to top</a>
                                                <br/>
                                                <br/>
                                                <a id="storage_state">Show storage state</a>
                                                <br/>
                                                <a id="clear_storage">Clear local storage</a>
                                                <br/>
                                                <br/>
                                                <a id="render_header">Render top header</a>
                        -->
                    </div>
                </div>
            </div>

            <div class="ticket-col ticket-col-center col-md-7 col-sm-6" style="top: 84px; bottom: 211px;">

                <div class="media media-ticket" id="main_data">
                    <div class="media-left">
                        <a href="javascript:void(0)">
                            <span class="hexagon">Y</span>
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <span class="badge status-badge pull-right">EMAIL</span>
                            {{ email["subject"] }}
                        </h4>

                        <div class="media-info">
                            <p>From: {{ email["date"] }}
                                {{ email["fromAddress"] }}</p>

                            <p>To: {{ email["toAddress"] }}</p>
                        </div>
                        <hr>
                        <div class="media-massage">
                            {{ email["body"] }}
                        </div>
                    </div>
                </div>

                <!--
                <div class="ticket-col ticket-col-center col-md-7 col-sm-6">
                    <div class="ticket-col-inner" id="central-part">

                    {% for i in 1..1 %}
                        <div class="media media-ticket" id="main_data">
                        <div class="media-left">
                            <a href="javascript:void(0)">
                                <span class="hexagon">{{ ticket['userAbbrev'] }}</span>
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">
                                <span class="badge status-badge pull-right">{{ ticket['status'] }}</span>
                               {{ ticket['subject'] }}
                            </h4>
                            <div class="media-info">
                                <p>{% if ticket["name"] %}{{ ticket["name"] }} {% endif %} {% if ticket["userName"] %}{{ ticket["userName"] }} {% endif %}{% if ticket["authorName"] %}{{ ticket["authorName"] }} {% endif %}
                                    ,
                                    GM{% if ticket["fromEmail"] %} ({{ ticket["fromEmail"] }}){% endif %} {{ ticket["created"] }}</p>

                                <p>To: {{ ticket['assignTo'] }}</p>
                            </div>
                            <div class="media-massage">
                                {{ ticket["description"] }}
                            </div>
                        </div>
                    </div>
                    {% endfor %}

                        {{ partial("tickets/partials/comments/list") }}


                        <!-- messages note -->
        </div>
    </div>

        <!-- start replies form -->
        <div class="ticket-col ticket-col-footer col-md-7 col-sm-6">
            <!-- form for reply on ticket -->
            <div class="ticket-col-inner">
                <div class="progress">
                    <div class="progress-bar bottom" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                         aria-valuemax="100" style="width: 2%;">
                        <span class="sr-only">2% Complete</span>
                    </div>
                </div>
                <form>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-ticket">
                        <li class="active"><a class="ticket-height-reload" href="#public-notes"
                                              data-toggle="tab">Public</a>
                        </li>
                        <li><a class="ticket-height-reload" href="#provide-notes" data-toggle="tab">Private</a></li>
                        <!--
                        <li class="dropdown hidden-xs">
                            <a href="" data-toggle="dropdown" id="use_template">Use Template <i
                                        class="fa fa-angle-up"></i></a>
                            <ul class="dropdown-menu not-hide dropdown-show-top" role="men2u" id="search_result">

                                <li class="dropdown-form">
                                    <input class="form-control" placeholder="Search" id="search">
                                </li>

                            </ul>
                        </li>
                        -->
                    </ul>
                    <!--
                    form1fieldlist
                    templates1
                    formname1
                    -->
                    <!-- sub -form -->
                    <div class="ticket-form-group">
                        <div class="tab-content">
                            {% for field in form1fieldlist %}
                                {{ partial("tickets/partials/" ~ templates1[field], ['data': ['label': forms.get(formname1).label(field), 'element': forms.get(formname1).render(field)]]) }}
                            {% endfor %}
                        </div>
                    </div>
                    <!-- end of sub -form -->
                </form>
            </div>
        </div>
        <!-- end replies bottom -->
    <div class="ticket-col ticket-col-footer col-md-7 col-sm-6">
        <!-- form for reply on ticket -->
        <div class="ticket-col-inner">
            <div class="progress">
                <div class="progress-bar bottom" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                     aria-valuemax="100" style="width: 2%;">
                    <span class="sr-only">2% Complete</span>
                </div>
            </div>

        </div>
    </div>

    <div class="ticket-col ticket-col-right col-md-3 col-sm-3" data-toggle="tooltip" data-title="Saved!">

        <div class="progress">
            <div class="progress-bar right" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                 style="width: 2%;">
                <span class="sr-only">2% Complete</span>
            </div>
        </div>


        <div class="ticket-col-inner">


        </div>

    </div>
</div>

</div>
</div>

</div>


<script>

    ticket = {};
    ticket.url_base = '{{ url("") }}';
    ticket.url_propsave = '{{ url("api/tickets/propsave") }}/';
    ticket.url_getBatchData = '{{ url("api/tickets/getBatchData") }}/';
    ticket.url_postcomment = '{{ url("api/tickets/sendmail") }}/';
    ticket.url_history = '{{ url("api/tickets/history?ticket_id=") }}' + $("#ticket_id").val();
    ticket.url_other_tickets_user = '{{ url("api/tickets/otherTicketsUser?user_id=") }}';

    var saveFieldFunc = function () {
        var val = $(this).val(),
                name = $(this).attr('id'),
                id = $("#ticket_id").val();

        var exclusion = ['publicreply', 'subject', 'privatereply', 'notifiers'];

        if (!(exclusion.indexOf(name) >= 0)) {
            saveField(id, name, val);
        }
    }


</script>

{{ assets.outputJs('JsViewTicket') }}


