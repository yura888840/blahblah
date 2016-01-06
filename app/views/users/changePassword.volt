<form class="form-login change-password" action="{{ url("users/changePassword") }}" method="post" autocomplete="off">
    <h1 class="text-center">
        <img class="logo" src="{{ url("image/crm.svg") }}">
    </h1>
    <div class="form-group">
        <label for="password">Password</label>
        {{ form.render("password") }}
    </div>
    <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        {{ form.render("confirmPassword") }}
    </div>
    <div class="form-group">
    {{ submit_button("Change Password", "class": "btn btn-primary") }}
    </div>
    <div class="text-center">
        {{ content() }}
    </div>
</form>