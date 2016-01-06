<!--
<ul class="nav nav-tabs nav-tabs-ticket-header pull-left" id="tickets-header-tabs-placeholder">
    <br/>

</ul>
-->
<div id="top-header-tabs-template" class="hidden">
    <li {active}>
        <span ticket_id="{ticket_id}">
            <i class="fa fa-ticket nav-tabs-icon"></i>
            <span data-href="{{ url('tickets/viewTicket/') }}{ticket_id}" class="nav-tabs-link">{fulltext}</span>
        </span>
    </li>
</div>