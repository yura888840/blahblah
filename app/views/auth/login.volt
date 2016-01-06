{% include 'partials/head.volt' %}

<form class="form-login" action="login" method="post">
    <h1 class="text-center logo">
        <i class="icon-crm-logo-big"></i>
    </h1>
    <input type="text" class="form-control" name="email" placeholder="Email Address" required="" autofocus=""
           value="{{ login }}">
    <input type="password" class="form-control" name="password" placeholder="Password" required="">
    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
    <p>{{ link_to("forgotpassword", "Forgot your password?") }}</p>
    <div class="text-center">
        {{ flashSession.output() }}
        {{ content() }}
    </div>
</form>

{% include 'partials/footer.volt' %}