<div class="list-group margin-0">
    <a href="{{ url('tickets') }}" class="list-group-item" id="inbox">Inbox
        {% if count_new > 0 %}<span class="badge">{{ count_new }}</span>{% endif %}</a>
    <a href="{{ url("tickets_reply_templates") }}" class="list-group-item" id="replytemplates">Templates</a>
    <a href="{{ url("tickets/personal") }}" class="list-group-item" id="personal_tasks">Personal Tasks</a>
    {% if router.getActionName() == 'viewTicket' %}
    <hr>
    <a class="list-group-item" id="other_tickets_user"
       data-toggle="collapse" href="#collapse-other_tickets_user"
       aria-expanded="false">Other tickets of this user</a>
    <div id="collapse-other_tickets_user" class="list-group collapse">
        <div class="panel-body">

        </div>
    </div>
    <a class="list-group-item" id="history_ticket"
       data-toggle="collapse" href="#collapse-history_ticket"
       aria-expanded="false">History Ticket</a>
    <div id="collapse-history_ticket" class="list-group collapse">
        <div class="panel-body">

        </div>
    </div>
    {% endif %}
</div>