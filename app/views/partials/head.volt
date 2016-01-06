<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt9"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt9"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8 ielt9"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <base href="{{ baseuri }}" id="base">
    <title>Ecomitize CRM 1.0</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ url("img/favicon.png") }}">
     {{ assets.outputJs('headerJs') }}
     {{ assets.outputCss('headerCss') }}
    <!--[if lt IE 9]>
    {{ assets.outputJs('headerJsIe') }}
    <![endif]-->
</head>
<input type="hidden" id="site_url" value="{{ url('') }}">
<input type="hidden" id="uid" value="{{ username }}">
<body class="no-js">

<noscript>
    <style>
        .no-js .wrapper {
            display: none;
        }
        .no-js .alert.alert-no-js {
            width: 400px;
            position: absolute;
            left: 50%;
            top: 50%;
            -ms-transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            text-align: center;
            margin: 0;
            padding-top: 30px;
            padding-bottom: 30px;
        }
        .no-js .alert.alert-no-js .logo {
            color: #a94442;
            margin-top: 0;
        }
    </style>
    <div class="alert alert-danger alert-no-js">
        <h1 class="text-center logo">
            <i class="icon-crm-logo-big"></i>
        </h1>
        <h4 id="oh-snap!-you-got-an-error!">JavaScript error!</h4>
        <p>Please enable JavaScript and then refresh the page.</p>
    </div>
</noscript>

<script>
    crm = {};
    crm.username = '{{ username }}';
    crm.socketDomain = '{{ socketDomain }}';
    crm.url = '{{ url("") }}';
</script>

<div class="wrapper">