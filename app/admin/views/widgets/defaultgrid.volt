<?php $this->assets->outputJs('headerDashboardJS') ?>
<?php $this->assets->outputCss('headerDashboardCSS') ?>
<ol class="breadcrumb">
    <li class="active">Setting up default Dashboard for : {{ role }} <br>** Move Widgets across Dashboard, auto- saving
        is on
    </li>
</ol>
<input type="hidden" id="role" value="{{ role }}">
{% if grid != "" %}
    <div class="row">
        <div class="col-md-6">
            <div class="sortable" placeholderid="1">
                {% for i, widget in grid[1] %}
                    <div class="sortable-item" data-id="{{ widget['id'] }}">
                        {{ widget['widget'] }}
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="col-md-6">
            <div class="sortable" placeholderid="2">
                {% for i, widget in grid[2] %}
                    <div class="sortable-item" data-id="{{ widget['id'] }}">
                        {{ widget['widget'] }}
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
{% elseif widgets %}
    <div class="row">
        <div class="col-md-6">
            <div class="sortable" placeholderid="1">
                {% for i, widget in widgets %}
                    {% if i is even %}
                        <div class="sortable-item" data-id="{{ widget['id'] }}"
                             data-position="{{ widget['position'] }}">
                            {{ widget['widget'] }}
                        </div>
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="col-md-6">
            <div class="sortable" placeholderid="2">
                {% for i, widget in widgets %}
                    {% if i is odd %}
                        <div class="sortable-item" data-id="{{ widget['id'] }}"
                             data-position="{{ widget['position'] }}">
                            {{ widget['widget'] }}
                        </div>
                    {% endif %}
                {% endfor %}
            </div>
        </div>
    </div>
{% endif %}

