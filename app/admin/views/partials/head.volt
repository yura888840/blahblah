<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt9"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt9"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8 ielt9"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <title>CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ url("img/favicon.png") }}">
     {{ assets.outputJs('headerJs') }}
     {{ assets.outputCss('headerCss') }}
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    {{ assets.outputJs('headerJsIe') }}
    <![endif]-->
</head>
<script>
    crm = {};
    crm.username = '{{ username }}';
    crm.socketDomain = '{{ socketDomain }}';
    crm.url = '{{ url("") }}';
</script>
<body>
<div class="wrapper">

    <nav class="navbar navbar-inverse navbar-static">
        <div class="container-fluid">
            <div class="navbar-header">
                <button class="navbar-toggle btn btn-primary collapsed" type="button" data-toggle="collapse"
                        data-target=".js-navbar-collapse">
                    <i class="glyphicon glyphicon-menu-hamburger"></i>
                </button>
                <a class="navbar-toggle btn btn-primary" href="{{ url("settings") }}">
                    <i class="glyphicon glyphicon-cog"></i>
                </a>
                <a class="navbar-toggle btn btn-primary" href="{{ url("tickets") }}">
                    <i class="glyphicon glyphicon-comment"></i>
                </a>
                <a class="navbar-brand" href="#">
                    <i class="icon-crm-logo"></i>
                </a>
            </div>
            <div class="collapse navbar-collapse js-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="{{ url("admin/index") }}">Admin dashboard</a></li>
                    <li><a href="{{ url("admin/users") }}">Users</a></li>
                    <li><a href="{{ url("register") }}">Add new User</a></li>
                    <li><a href="{{ url("admin/resources") }}">Roles & Resources permissions</a></li>
                    <li><a href="{{ url("admin/permissions") }}">Permissions</a></li>
                    <li><a href="{{ url("admin/widgets/install") }}">Install new widget</a></li>
                    <!--<li><a href="{{ url("admin/widgets") }}">Widgets</a></li>
                <li><a href="{{ url("admin/widgets/default") }}">Default Widgets sets</a></li>-->
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Widgets <span
                                    class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url("admin/widgets") }}">Widgets availability for each role</a></li>
                            <li><a href="{{ url("admin/widgets/default") }}">Default widgets Sets for roles</a></li>
                        </ul>
                    </li>

                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                    class="glyphicon glyphicon-plus hidden-xs"></i><span class="visible-xs">Fast create <span
                                        class="caret"></span></span></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header">Fast create:</li>
                            <li class="divider"></li>
                            <li><a href="#" data-toggle="modal" data-target="#modal">Add new role</a></li>
                            <li><a href="#" data-toggle="modal" data-target="#modal">Add new user</a></li>
                            <li><a href="#" data-toggle="modal" data-target="#modal">Add new widget</a></li>
                        </ul>
                    </li>
                    <li class="hidden-xs">
                        <a href="{{ url("email") }}" class="animated flash"><i class="glyphicon glyphicon-comment"></i></a>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                    class="glyphicon glyphicon-user hidden-xs"></i><span
                                    class="visible-xs">Account <span class="caret"></span></span></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header">Hello {{ username }}!</li>
                            <li class="divider"></li>
                            <li><a href="{{ url('dashboard') }}">Panel for users</a></li>
                            <li class="divider"></li>
                            <li class="disabled"><a href="#">My account</a></li>
                            <li class="disabled"><a href="#">Global settings</a></li>
                            <li><a id="drop3" href="{{ url("logout") }}" title="logout">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.nav-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

