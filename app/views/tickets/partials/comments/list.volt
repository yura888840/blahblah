{% for i, comment in comments %}
    {% if comment['isPrivate'] %}
        <div class="media media-private {% if i == 0 and needAnimate %}animated fadeIn{% endif %}">
            <div class="dropdown ticket-media-dropdown pull-right">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="tickets-comments-templates" href="javascript:void(0)">Save this template</a></li>
                </ul>
            </div>
            <div class="media-left">
                <a href="javascript:void(0)">
                    <span class="hexagon">{{ comment['userAbbrev'] }}</span>
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">{{ comment['userFullname'] }} <span
                            class="badge private-badge">Private</span></h4>

                <div class="media-info">
                    <p>{{ comment['created_date_text'] }}</p>
                </div>
                <div class="media-massage">
                    {{ comment['text'] }}
                </div>
            </div>
        </div>
    {% else %} {# public comment #}
        <div class="media media-reply {% if i == 0 and needAnimate %}animated fadeIn{% endif %}">
            <div class="dropdown ticket-media-dropdown pull-right">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="tickets-comments-templates" href="javascript:void(0)">Save this template</a></li>
                </ul>
            </div>
            <div class="media-left">
                <a href="javascript:void(0)">
                    <span class="hexagon">{{ comment['userAbbrev'] }}</span>
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">{{ comment['userFullname'] }}</h4>

                <div class="media-info">
                    <!-- <p>To: "{{ comment['recepient_name'] }}"
                                            &laquo;my@email&raquo; -->{{ comment['created_date_text'] }}</p>
                </div>
                <div class="media-massage">
                    {{ comment['text'] }}
                    <!--<br/><br/>
                    <label for="{{ comment['_id'] }}">Attachments</label>

                    <div class="dropzone" id="{{ comment['_id'] }}"></div>-->
                </div>
            </div>
        </div>
    {% endif %}

{% endfor %}