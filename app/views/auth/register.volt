{% include 'partials/head.volt' %}
{{ content() }}

<div class="container">
    {{ form('class': 'form-register form-center') }}
    <h1 class="text-center logo text-center">
        <i class="icon-crm-logo-big"></i>
    </h1>

    <div class="row">
        <div class="col-md-6">
            <h2>Registration</h2>

            <div class="form-group">
                {{ form.label('email') }}
                {{ form.render('email') }}
                {{ form.messages('email') }}
            </div>
            <div class="form-group">
                {{ form.label('name') }}
                {{ form.render('name') }}
                {{ form.messages('name') }}
            </div>
            <div class="form-group">
                {{ form.label('password') }}
                {{ form.render('password') }}
                {{ form.messages('password') }}
            </div>
            <div class="form-group">
                {{ form.label('confirmPassword') }}
                {{ form.render('confirmPassword') }}
                {{ form.messages('confirmPassword') }}
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" class="checkbox">
                    <span class="lbl padding-8"></span>
                    <span class="lbl-text">Please send notifications to this email</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <h2>Terms and Conditions</h2>

            <p>In order to use most aspects of the Services, you must register for and maintain an active user Services
                account ("Account").
            </p>

            <p>Account registration requires you to submit to Ecomitize certain personal information, such as your name,
                address, etc.
                You agree to maintain accurate, complete, and up-to-date information in your Account.
            </p>

            <p>You are responsible for all activity that occurs under your Account, and, as such, you agree to maintain
                the security and secrecy of your Account username and password at all times.
            </p>

            <p>Unless otherwise permitted by Ecomitize in writing, you may only possess one Account.</p>
            {{ form.render('csrf', ['value': security.getToken()]) }}
            {{ form.messages('csrf') }}
            {{ form.render('Register') }}
        </div>
    </div>
    </form>
</div>