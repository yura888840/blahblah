{% include 'partials/head.volt' %}
<div class="modal" id="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&#215;</button>
                <h4 class="modal-title">Title</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>
<header>
    <nav class="navbar navbar-inverse navbar-static">
        <div class="container-fluid">
            <div class="navbar-header">
                <button class="navbar-toggle btn btn-primary collapsed" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
                    <i class="glyphicon glyphicon-menu-hamburger"></i>
                </button>
                <a class="navbar-toggle btn btn-primary" href="{{ url("settings") }}">
                    <i class="glyphicon glyphicon-cog"></i>
                </a>
                <a class="navbar-toggle btn btn-primary" href="{{ url("tickets") }}">
                    <i class="glyphicon glyphicon-comment"></i>
                </a>
                <a class="navbar-brand" href="{{ url("dashboard") }}">
                    <i class="icon-crm-logo"></i>
                </a>
            </div>
            <div class="collapse navbar-collapse js-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="{{ url("dashboard") }}">Dashboard</a></li>
                    <li><a href="{{ url("calendars") }}">Calendar</a></li>
                    <!--
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url("sales/orders") }}">Orders</a></li>
                            <li><a href="{{ url("sales/marketplace") }}">Marketplace</a></li>
                            <li><a href="{{ url("sales/inventory") }}">Inventory</a></li>
                            <li><a href="{{ url("analytics") }}">Analytics</a></li>
                            <li><a href="{{ url("sales/reports") }}">Reports</a></li>
                            <li><a href="{{ url("sales/leads") }}">Leads</a></li>
                            <li><a href="{{ url("sales/organizations") }}">Organizations</a></li>
                        </ul>
                    </li>
                    -->
                    <li><a href="{{ url("tickets") }}">Emails</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li id="header-new-messages" class="dropdown hidden-xs"><a href="#" class="dropdown-toggle animated flash" data-toggle="dropdown"><i class="glyphicon glyphicon-comment"></i></a>
                        <ul class="dropdown-menu not-hide">
                            <li class="dropdown-header">{{ count_new }} new message(s):</li>
                            <li class="divider"></li>
                            {% for n in new_messages %}
                                <li><a href="{{ url("tickets/viewTicket") }}/{{ n['_id'] }}"><b>{{ n['name'] }}
                                            :</b><br>{{ n['subject'] }}</a></li>
                            {% endfor %}
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user hidden-xs"></i><span class="visible-xs">Account <span class="caret"></span></span></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header hidden-xs">Hello {{ username }}!</li>
                            <li class="divider"></li>
                            {% if isAdmin %}
                            <li><a href="{{ url("admin/index") }}">Admin page</a></li>
                            <li class="divider"></li>
                            {% endif %}
                            <!--<li><a href="{{ url("account/widget") }}">My Widget</a></li>
                            <li class="divider"></li>-->
                            <li><a href="{{ url("account") }}">My account</a></li>
                            <li class="disabled"><a href="#">Global settings</a></li>
                            <li class="disabled"><a href="{{ url("account/support") }}">Support (Help)</a></li>
                            <li><a href="{{ url("logout") }}" id="logout">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.nav-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>

<div id="container-info-messages-php">

</div>

<div id="container-info-messages-java-script">

</div>

<script id='info-messages' type='text/ractive'>
    <%#if messages%>
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <ul>
                <%#each messages.error:num%>
                <li>
                    <strong>Warning!</strong> <%messages.error[num]%>
                </li>
                <%/each%>
            </ul>
        </div>
    <%/if%>
</script>

<div class="btn-fixed-add">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <span class="first"></span>
        <span class="last"></span>
    </a>
    <ul class="dropdown-menu dropdown-show-top dropdown-menu-right">
        {% if router.getControllerName() == 'dashboard' %}
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-target="#modalAddWidget">Widget</a>
            <ul class="dropdown-menu dropdown-show-top dropdown-menu-right not-hide" id="modalAddWidget">
                <li>
                    <ul>

                    </ul>
                </li>
            </ul>
        </li>
        {% endif %}
        {% if router.getActionName() != 'viewTicket' %}
        <li class="last"><a href="#" data-toggle="modal" data-target="#modal-window-for-add-new-ticket-id">Ticket</a></li>
        {% endif %}
        {% if router.getActionName() == 'viewTicket' %}
        <li class="last"><a href="javascript:void(0);">Nothing to add</a></li>
        {% endif %}
    </ul>
</div>

<div class="container-fluid">
    <noscript>
        <div class="alert alert-danger fade in">
            <h4 id="oh-snap!-you-got-an-error!">JavaScript error!</h4>
            <p>Please enable JavaScript and then refresh the page.</p>
        </div>
    </noscript>
{{ content() }}
</div>

{{ assets.outputJs('headerJsViewTicket') }}
{{ assets.outputCss('headerCssViewTicket') }}

{{ partial('tickets/forms/add_ticket_formTicket') }}
{% if deadline is not defined %}
    <script>
        $('div.datepicker[data-column="deadline"]').datepicker({
            altField: '[data-column="deadline"]input',
        });
    </script>
{% endif %}
{% include 'partials/footer.volt' %}