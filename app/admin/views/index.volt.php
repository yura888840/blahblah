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
     <?php echo $this->assets->outputJs('headerJs'); ?>
     <?php echo $this->assets->outputCss('headerCss'); ?>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <?php echo $this->assets->outputJs('headerJsIe'); ?>
    <![endif]-->
</head>

<body>

<div class="wrapper">

<nav class="navbar navbar-inverse navbar-static">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-example-js-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <img class="svg" src="<?php echo $assets_path_tpl; ?>img/crm-white.svg">
            </a>
        </div>
        <div class="collapse navbar-collapse bs-example-js-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/admin/index">Admin dashboard</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li><a href="/register">Add new User</a></li>
                <li><a href="/admin/resources">Resources</a></li>                    
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/">Panel for users</a></li>
                <li><a><?php echo $username; ?></a></li>
                <li>
                    <a id="drop3" href="/logout" title="logout"><i class="glyphicon glyphicon-log-out"></i></a>
                </li>
            </ul>
        </div><!-- /.nav-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container-fluid">
<?php echo $this->getContent(); ?>
</div>

</div><!-- /.wrapper -->

<footer>&#169; <a href="#">Ecomitize.com</a></footer>

</body>

</html>
