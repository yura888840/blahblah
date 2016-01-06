<ol class="breadcrumb">
    <li><a href="{{ url("dashboard") }}">Dashboard</a></li>
    <li class="active">Personal settings</li>
</ol>

<div class="container-block">
    <div class="panel">
        <div class="panel-body" style="padding:10px">
            <div id="my_container">
                <br/><br/>
                <h4>&nbsp;&nbsp;&nbsp;You can change your password here</h4>
                <!-- temporary fix -->
                <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

                <form class="form-login change-password" action="{{ url("account") }}" method="post" autocomplete="off">
                    <h4 class="text-center">
                        Type your new password here
                    </h4>

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
            </div>
        </div>
    </div>
</div>
