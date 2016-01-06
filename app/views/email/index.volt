{{ content() }}
<div class="modal modal-email">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&#215;</button>
                <h4 class="modal-title">Compose</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input class="form-control" placeholder="To">
                </div>
                <div class="form-group">
                    <input class="form-control" placeholder="Subject">
                </div>
                <div class="form-group">
                    <textarea name="text" class="form-control textarea-lg">Best Regards {{ username }}.</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary save-form" type="button">Send</button>
                <button class="btn btn-default clean-form" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<ol class="breadcrumb">
    <li><a href="{{ url("dashboard") }}">Dashboard</a></li>
    <li class="active">E-mails</li>
</ol>
<div class="container-block">
    <div class="pull-right">
        <a href="#" class="btn btn-primary" type="button" data-toggle="modal" data-target=".modal">
            <i class="glyphicon glyphicon-cog"></i>
        </a>
    </div>
    <div class="pull-left">
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target=".modal-email">Compose</a>
    </div>
</div>
<div class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-3 col-lg-2">
                <div class="list-group">
                    <a href="#" class="list-group-item">Inbox (2)</a>
                    <a href="#" class="list-group-item">Starred</a>
                    <a href="#" class="list-group-item">Important</a>
                    <a href="#" class="list-group-item">Sent Mail</a>
                    <a href="#" class="list-group-item">Drafts (3)</a>
                    <a href="#" class="list-group-item">All mail</a>
                    <a href="#" class="list-group-item">Spam (31)</a>
                    <a class="list-group-item" data-toggle="collapse" data-target="#list-group-collapse">More <span
                                class="caret"></span></a>

                    <div class="collapse" id="list-group-collapse">
                        <a href="#" class="list-group-item">Charts</a>
                        <a href="#" class="list-group-item">Trash</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-9 col-lg-10">
                <div class="email-nav-header">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default ">
                            <div class="checkbox margin-0">
                                <label>
                                    <input type="checkbox" name="item">
                                    <span class="lbl"></span>
                                </label>
                            </div>
                        </button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">All</a></li>
                            <li><a href="#">None</a></li>
                            <li><a href="#">Read</a></li>
                            <li><a href="#">Unread</a></li>
                            <li><a href="#">Starred</a></li>
                            <li><a href="#">Unstarred</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-refresh"></span>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            More <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Mark all as read</a></li>
                            <li class="divider"></li>
                            <li class="text-center">
                                <small class="text-muted">Select messages to see more actions</small>
                            </li>
                        </ul>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-default">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </button>
                            <button type="button" class="btn btn-default">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </button>
                        </div>
                    </div>
                </div>
                {#<ul class="nav nav-tabs nav-justified">
                    <li class="active"><a href="#home" data-toggle="tab"><i class="glyphicon glyphicon-inbox"></i>
                            Primary</a></li>
                    <li><a href="#home" data-toggle="tab">Social</a></li>
                </ul>#}
                <div class="tab-content">
                    <table class="table table-hover table-email">
                        <tbody>
                        {% for i in 1..20 %}
                            <tr class="email-tr {% if loop.first %} first{% endif %}{% if loop.last %} last{% endif %}">
                                <td class="text-center text-nowrap hidden-xs">
                                    <label>
                                        <input class="email-checkbox" type="checkbox" name="item">
                                        <span class="lbl"></span>
                                    </label>
                                    <label class="icon-label icon-label-star">
                                        <i class="glyphicon glyphicon-star"></i>
                                    </label>
                                </td>
                                <td class="hidden-xs text-nowrap"><a class="btn-link" href="{{ url("email/list") }}"><b>Alex Alex Alex</b></a></td>
                                <td class="col-lg-12">
                                    <div class="visible-xs"><b>Alex Alex Alex</b></div>
                                    Modern versions of assistive technologies will announce CSS
                                    generated content
                                </td>
                                <td class="text-nowrap">4:38 pm</td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".icon-label-star").on("click", function () {
            if($(this).is('.color-1')){
                $(this).removeClass('color-1').addClass('color-2');
            }
            else if($(this).is('.color-2')){
                $(this).removeClass('color-2');
            }
            else{
                $(this).addClass('color-1');
            }
        });
    });
</script>