{% include 'partials/head.volt' %}

<form class="form-login text-center" action="login" method="post">
    <h1 class="text-center logo">
        <i class="icon-crm-logo-big"></i>
    </h1>
    <p class="text-success">Your password has been sent to your e-mail</p>
    <p>{{ link_to("login", "Go to login page ...") }}</p>
    {{ content() }}
</form>

{% include 'partials/footer.volt' %}