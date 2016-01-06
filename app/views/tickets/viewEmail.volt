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

            <div class="ticket-col ticket-col-center col-md-7 col-sm-6">
                <div class="ticket-col-inner" id="central-part">
                    <!-- messages -->
                    {% for i in 1..1 %}
                        <div class="media media-ticket" id="main_data">
                    <div class="media-left">
                        <a href="javascript:void(0)">
                            <span class="hexagon">{{ ticket['userAbbrev'] }}</span>
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <span class="badge status-badge pull-right">EMAIL</span>
                            {{ ticket['subject'] }}
                        </h4>
                        <div class="media-info">
                            <p>{{ email["fromAddress"] }}{% if ticket["name"] %}{{ ticket["name"] }} {% endif %} {% if ticket["userName"] %}{{ ticket["userName"] }} {% endif %}{% if ticket["authorName"] %}{{ ticket["authorName"] }} {% endif %}
                                ,
                                GM{% if ticket["fromEmail"] %} ({{ ticket["fromEmail"] }}){% endif %} {{ ticket["created"] }}{{ email["date"] }}</p>

                            <p>To: {{ ticket['assignTo'] }}{{ email["toAddress"] }}</p>
                        </div>
                        <div class="media-massage">
                            {{ ticket["description"] }}{{ email["body"] }}
                        </div>
                    </div>
                </div>
                    {% endfor %}

                    {{ partial("tickets/partials/comments/list") }}


                    <!-- messages note -->
                </div>
            </div>

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
                            <li class="active"><a class="ticket-height-reload" href="#public-notes" data-toggle="tab">Public</a>
                            </li>
                            <li><a class="ticket-height-reload" href="#provide-notes" data-toggle="tab">Private</a></li>
                            <li class="dropdown hidden-xs">
                                <a href="" data-toggle="dropdown" id="use_template">Use Template <i
                                            class="fa fa-angle-up"></i></a>
                                <ul class="dropdown-menu not-hide dropdown-show-top" role="men2u" id="search_result">

                                    <!-- МОМЕНТАЛЬНЫЙ РЕЗУЛЬТАТ AJAX ПОИСКА -->
                                    <!--
                                    <li><a>Thanks for feedback + Resolve</a></li>
                                    <li><a>Thanks for feedback + Resolve Thanks for feedback + Resolve</a></li>
                                    -->
                                    <!-- /МОМЕНТАЛЬНЫЙ РЕЗУЛЬТАТ AJAX ПОИСКА -->

                                    <li class="dropdown-form">
                                        <input class="form-control" placeholder="Search" id="search">
                                    </li>
                                    <!--
                                    <li class="divider"></li>
                                    <li class="dropdown dropdown-submenu">
                                        <a href="" data-toggle="dropdown">Sample Template</a>
                                        <ul class="dropdown-menu">
                                            <li><a>Thanks for feedback + Resolve</a></li>
                                            <li><a>Thanks for feedback + Resolve</a></li>
                                            <li><a>Thanks for feedback + Resolve Thanks for feedback +
                                                    Resolve</a></li>
                                            <li><a>Thanks for feedback + Resolve Thanks for feedback + Resolve
                                                    Thanks for feedback + Resolve</a></li>
                                            <li><a>Thanks for feedback + Resolve</a></li>
                                            <li><a>Thanks</a></li>
                                            <li><a>Thanks</a></li>
                                            <li><a>Thanks for feedback + Resolve</a></li>
                                            <li><a>Thanks for feedback + Resolve</a></li>
                                            <li><a>Thanks for feedback + Resolve</a></li>
                                        </ul>
                                    </li>
                                    -->

                                </ul>
                            </li>
                        </ul>
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

            <div class="ticket-col ticket-col-right col-md-3 col-sm-3" data-toggle="tooltip" data-title="Saved!">

                <div class="progress">
                    <div class="progress-bar right" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                         aria-valuemax="100" style="width: 2%;">
                        <span class="sr-only">2% Complete</span>
                    </div>
                </div>


                <div class="ticket-col-inner">

                    <div class="row">
                        <!-- FORm fo right panel control -->
                    </div>
                    <!--
                    <label for="ticket_attachments">Original Email attachments list:</label>

                    <div class="row" id="ticket_attachments">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div id="my-awesome-attachments-view" class="dropzone">
                                    {% for i, a in ticket['attach'] %}
                                        <a class="comment-download-file"
                                           href="{{ url('files/Emails/') }}{{ ticket['_id'] }}/main/{{ a['uniqName'] }}"
                                           target="_blank">{{ a['originalName'] }}</a><br/>
                                    {% endfor %}
                                </div>
                            </div>
                        </div>
                    </div>
-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="my-awesome-dropzone-view"><i class="fa fa-paperclip"></i> Attach for new
                                    E-mail</label>

                                <form id="my-awesome-dropzone-view" class="dropzone"></form>
                            </div>
                        </div>
                    </div>
                    <!--
                    <label for="ticket_comments_attachments">Ticket comments attachments list:</label>

                    <div class="row" id="ticket_comments_attachments">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div id="my-awesome-attachments-view" class="dropzone">
                                    {% for i, a in comments_attachments %}
                                        <a class="comment-download-file"
                                           href="{{ url('files/Emails/') }}{{ ticket['_id'] }}/comments/{{ a['commentId'] }}/{{ a['uniqName'] }}"
                                           target="_blank">{{ a['originalName'] }}</a>
                                        <a href="#" data-placement="bottom" data-toggle="tooltip"
                                           data-original-title="Jump to: {{ a['comment_full'] }}">
                                            <small>{{ a['comment_header'] }}</small>
                                        </a>
                                        <br/>
                                    {% endfor %}
                                </div>
                            </div>
                        </div>
                    </div>
                    -->

                    <!--

                    <div class="list-group list-group-collapse margin-0">

                        <a class="list-group-item" data-toggle="collapse" href="#collapse-2"><i class="fa fa-angle-down"></i> Client's Orders</a>
                        <div id="collapse-2" class="collapse">

                            <div class="collapse-inner">

                                <div class="search-form-group">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="Search..." value="">
                                            <div class="input-group-addon">
                                                <a href="#" data-toggle="dropdown">
                                                    <i class="fa fa-calendar"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-block not-hide">
                                                    <li class="dropdown-padding-all">
                                                        <div class="datepicker"></div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {% for i in 1..5 %}
                                    <a class="list-group-item" href="#">
                                        <h4 class="media-heading text-ellipsis">Media heading</h4>
                                        <div class="media-info text-ellipsis">
                                            Leyla Seka, GM(support@Desk.com) May 18 2015, 7:35 PM
                                        </div>
                                        <span class="text-ellipsis">Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. lacinia congue felis in faucibus.</span>
                                    </a>
                                {% endfor %}
                                <div class="text-center">
                                    <ul class="pagination">
                                        <li class="active"><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#">...</a></li>
                                        <li><a href="#">21</a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <a class="list-group-item" data-toggle="collapse" href="#collapse-1"><i class="fa fa-angle-down"></i> Client's Tickets</a>
                        <div id="collapse-1" class="collapse">

                            <div class="collapse-inner">

                                <div class="search-form-group">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="Search..." value="">
                                            <div class="input-group-addon">
                                                <a href="#" data-toggle="dropdown">
                                                    <i class="fa fa-calendar"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-block not-hide">
                                                    <li class="dropdown-padding-all">
                                                        <div class="datepicker"></div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {% for i in 1..5 %}
                                    <a class="list-group-item" href="#">
                                        <h4 class="media-heading text-ellipsis">Media heading</h4>
                                        <div class="media-info text-ellipsis">
                                            Leyla Seka, GM(support@Desk.com) May 18 2015, 7:35 PM
                                        </div>
                                        <span class="text-ellipsis">Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. lacinia congue felis in faucibus.</span>
                                    </a>
                                {% endfor %}
                                <div class="text-center">
                                    <ul class="pagination">
                                        <li class="active"><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#">...</a></li>
                                        <li><a href="#">21</a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>

-->

                    <!-- CASE TIMEline -->

                    <!--
                        <a class="list-group-item" data-toggle="collapse" href="#collapse-3"><i class="fa fa-angle-down"></i> Case timeline</a>
                        <div id="collapse-3" class="collapse">

                            <div class="collapse-inner">
                            {% for i in 1..4 %}
                                <div class="media media-reply">
                                    <div class="media-info pull-right">
                                        15h ago
                                    </div>
                                    <div class="media-left">
                                        <a href="#">
                                            <span class="hexagon">AB</span>
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Alex Babanski</h4>
                                        <div class="media-info">
                                            Opened case
                                        </div>
                                    </div>
                                </div>
                            {% endfor %}
                            </div>

                            <div class="text-center">
                                <ul class="pagination">
                                    <li class="active"><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">...</a></li>
                                    <li><a href="#">21</a></li>
                                </ul>
                            </div>
                        </div>

-->

                    <!-- ! CASE TIMEline -->

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
    ticket.url_postcomment = '{{ url("api/tickets/sendmail") }}/';// + $("#ticket_id").val();
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

    $('div.datepicker[data-column="deadline"]').datepicker({
        altField: '[data-column="deadline"]input',
        onSelect: saveFieldFunc,
        {% if deadline is defined %}defaultDate: '{{ deadline }}'{% endif %}
    });


</script>

{{ assets.outputJs('JsViewTicket') }}
