{% include 'partials/head.volt' %}

<form class="form-login form-login-remind" method="post">
    <h1 class="text-center logo">
        <i class="icon-crm-logo-big"></i>
    </h1>
    {{ form.render('email') }}
    <button class="btn btn-lg btn-primary btn-block" type="submit">Remind me</button>
    <p>{{ link_to("login", "I remember my password. Let me in!") }}</p>
    {{ content() }}
</form>

{% include 'partials/footer.volt' %}