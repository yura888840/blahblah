{# JS Templates  #}
<script id="hidden-comment-tpl-private" type="text/x-custom-template">
    <div class="media media-private animated fadeIn">
        <div class="dropdown ticket-media-dropdown pull-right">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="#">Save this template</a></li>
            </ul>
        </div>

        <div class="media-left">
            <a href="#">
                <span class="hexagon" propname="user_abbrev">::user_abbrev::</span>
            </a>
        </div>
        <div class="media-body">
            <h4 class="media-heading" propname="user_fullname">::user_fullname:: <span
                        class="badge private-badge">Private</span></h4>

            <div class="media-info">
                <p propname="comment_heading">::comment_heading::</p>
            </div>
            <div class="media-massage" propname="text">
                ::text::
            </div>
        </div>

    </div>
</script>

<script id="hidden-comment-tpl-public" type="text/x-custom-template">
    <div class="media media-reply animated fadeIn">
        <div class="dropdown ticket-media-dropdown pull-right">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="#">Save this template</a></li>
            </ul>
        </div>
        <div class="media-left">
            <a href="#">
                <span class="hexagon" propname="user_abbrev">::user_abbrev::</span>
            </a>
        </div>
        <div class="media-body">
            <h4 class="media-heading" propname="user_fullname">::user_fullname::</h4>

            <div class="media-info">
                <p propname="comment_heading">::comment_heading::</p>
            </div>
            <div class="media-massage" propname="text">::text::</div>
        </div>
    </div>
</script>



