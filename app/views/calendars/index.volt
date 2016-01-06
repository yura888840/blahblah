<script>
    window.calendar={
        ajaxUrl: '{{ url("calendars/getAjaxEvents") }}',
        clientId: "{{ clientId }}",
    }
</script>
{{ assets.outputJs('headerJsCalendar') }}
{{ assets.outputCss('headerCssCalendar') }}

<ol class="breadcrumb">
    <li><a href="{{ url("dashboard") }}">Dashboard</a></li>
    <li class="active">Calendar</li>
</ol>
<div class="container-block">
    <div class="btn-group" id="actions_on_events" style="visibility:hidden">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Google Actions<span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a id="synchronize_google" href="#">Synchronize all my tickets to google</a></li>
            <li><a id="change_google_calendar" href="#">Change google calendar</a></li>
        </ul>
        <button type="button" id="change_crm_calendar" class="btn btn-info" data-type-calendar="company">Company calendar</button>
    </div>

    <div class="panel" style="visibility:hidden">
        <div class="panel-body" style="padding:10px">
            <div id="calendar_container"></div>
        </div>
    </div>
</div>
<script>
    var calendar = {};
    calendar.authUrl = "{{ authUrl }}";
    calendar.clientId = "{{ clientId }}";
    calendar.apiKey = "{{ apiKey }}";
    calendar.ajaxUrl = '{{ url("calendars/getAjaxEvents") }}';
    calendar.url_propsave = '{{ url("api/tickets/propsave") }}/';
    calendar.url_api = '{{ url("api/findDb") }}/';
    calendar.url_restore_event = '{{ url("calendars/restoreEvent") }}';
    calendar.url_change_calendar = '{{ url("calendars/changeGoogleCalendarForUser") }}/';
    calendar.userGoogleCalendarId = '{{ userGoogleCalendarId }}';
</script>


<!--
*****************************************************************
Этот фрагмент содержит заготовки динамически создаваемых объектов
*****************************************************************
-->
<div class="calendar_templates_collection" style="display:none; ">

    <div id="auxMenuEvent" style="position:absolute; border: solid 1px #7aa; background-color: #dee; min-width:99px; min-height: 77px; display: none; z-index:2">

        <div style="text-align: center; font-weight: bolder;border-bottom: dotted 1px gray; background-color: #ddc; width:100%; height: 19px">
            <span id="auxMenuEvent_header"></span>
            <button id="auxMenuEvent_close" class="close btn btn-sm close" style="top:-3px;position: relative"> &times;</button>
        </div>

        <div id="auxMenuEvent_body" style="margin: 9px;"></div>


  <span id="auxMenuEvent_cbcont">
      <input id="auxMenuEvent_cb" type="checkbox" style="opacity: 100; position:relative;top:4px;">
      <span id="auxMenuEvent_cbtext"></span>
  </span>


        <center id="auxMenuEvent_footer" style="margin: 9px;">
            <button id="okBut" type="button" class="btn btn-default btn-xs" style="margin-right: 3px">Ok</button>
            <button id="ok2But" type="button" class="btn btn-default btn-xs" style="margin-right: 3px">Overwrite in Сrm</button>
            <button id="cancelBut" type="button" class="btn btn-default btn-xs">Cancel</button>
        </center>

    </div>

        <button type="button" class="auxMenuEvent_but btn btn-default btn-xs" style="width: 100%; margin-bottom: 7px;font-size:15px">
            <i class="fa fa-spinner fa-pulse fa-fw"></i> <!--http://fontawesome.ru/examples/-->
            <span style="position: relative;">Button</span>
        </button>


    <div id="auxMenuMsg" style="position:absolute; border: solid 1px #7aa; background-color: #dee; min-width:99px; min-height: 77px; display: none; z-index:999">
        <span  id="auxMenuMsg_header" style="text-align: center; font-weight: bolder"></span>
        <button class="close btn btn-sm close"> &times;</button>
        <div id="auxMenuMsg_content"></div>
    </div>

    <div class="auxMenuIcon btn btn-default btn-xs" style="position:relative;width:15px; height: 15px; padding: 0; margin-right: 3px;">
        <span class="caret" style="position:absolute;top:6px;left:3px;"></span>
    </div>
</div>

<script id='template-calendar-modal' type='text/ractive'>
<div id="calendar-modal" class="modal fade">
    <div class="modal-dialog" style="display: block;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><% title %></h4>
            </div>
            <div class="modal-body" style="position: relative;">
            <%#if !processResult%>
                <%#if !error%>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Crm events</th>
                            <th>Sync direct</th>
                            <th>Google events</th>
                          </tr>
                        </thead>
                        <tbody>
                        <%#each eventsCrm%>
                          <tr data-id='<%id%>'>
                            <td class="event-title-crm"  data-updated_at="<%updatedInt%>"><%title%></td>
                            <td class="sync-direct" style="cursor: pointer;"></td>
                            <td class="event-title-google"></td>
                          </tr>
                        <%/each%>
                        <%#each eventsGoogle%>
                          <tr data-id='<%id%>'>
                            <td class="event-title-crm" data-updated_at="<%eventCrm.updatedInt%>"><%#if eventCrm.title%><%eventCrm.title%><%/if%></td>
                            <td class="sync-direct" style="cursor: pointer;"><%#if syncDirect%><%syncDirect%><%/if%></td>
                            <td class="event-title-google" data-updated_at="<%updatedInt%>"><%summary%></td>
                          </tr>
                        <%/each%>
                        </tbody>
                      </table>
                <%else%>
                    Error
                <%/if%>
            <%else%>
            <%#each processResult%>
                <div style="color: <%color%>"><b><%title%></b><%description%></div>
            <%/each%>
            <%/if%>
            </div>
            <div class="modal-footer">
            <%#if !processResult%>
                <%#if !error%>
                <button type="button" class="btn btn-primary btn-run-sync">Run sync</button>
                <%/if%>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <%else%>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
            <%/if%>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</script>

<script id='template-change-calendar' type='text/ractive'>
<div id="change-calendar-modal" class="modal fade">
    <div class="modal-dialog" style="display: block;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><% title %></h4>
            </div>
            <div class="modal-body" style="position: relative;">
            <%#if !processResult%>
                <%#if !error%>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Google calendar</th>
                          </tr>
                        </thead>
                        <tbody>
                        <%#each callist%>
                          <tr <%#if crm_gcal_id == id%>class="selected"<%/if%> data-id='<%id%>'>
                            <td class="event-title-crm"><%summary%></td>
                          </tr>
                        <%/each%>
                        </tbody>
                      </table>
                <%else%>
                    Error
                <%/if%>
            <%else%>
            <%#each processResult%>
                <div style="color: <%color%>"><b><%title%></b><%description%></div>
            <%/each%>
            <%/if%>
            </div>
            <div class="modal-footer">
            <%#if !processResult%>
                <%#if !error%>
                <button type="button" class="btn btn-primary btn-change-calendar">Change</button>
                <%/if%>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <%else%>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
            <%/if%>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</script>

<div id="container-calendar-modal">

</div>
<div id="container-change-calendar">

</div>
<!--************** конец фрагмента *************-->
