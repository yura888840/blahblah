Changes in {{ entity }}:
<br/>
{% for r in changes %}
    <hr>
    Form Element : {{ r['element'] }}
    <br/><br/>
    Old value : {{ r['oldValue'] }}
    <br/>
    New value: {{ r['newValue'] }}
    <br/><br/>
    Changed by: {{ r['changedBy'] }}
    <br/>
    Changed at: {{ r['timestamp'] }}
    <br/><br/>
{% endfor %}

<hr>
Kind regards Ecomitize team