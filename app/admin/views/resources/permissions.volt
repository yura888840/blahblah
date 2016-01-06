<table id="ajax-table-date-roles"
       class="table table-hover ajax-tablesorter tablesorter tablesorter-bootstrap hasFilters hasStickyHeaders"
       ajax-filter-option-request-url="{{ url('admin/Resources/ajaxGetFilterOptions') }}"
       ajax-table-request-url="{{ url('admin/Resources/ajaxTablesorterRole') }}" role="grid"
       aria-describedby="ajax-table-date-roles_pager_info">

    {% for name, perm in widgets %}
        <tr role="row">
            <td>{{ name }}</td>
            {% for role, p in perm %}
                <td class="text-center hidden-xs">

                    <label>
                        <input class="tablesorter-checkbox" type="checkbox" ajax-checked-role="{{ role }}"
                               ajax-checked-resource="{{ name }}" name="{{ role }}-{{ name }}"
                               {% if p==1 %}checked="checked" {% endif %}>
                        <span class="lbl padding-8"></span>
                    </label>
                </td>
            {% endfor %}
        </tr>
    {% endfor %}

</table>
<script>
    ajaxTablesorterStack('#ajax-table-date-roles', [[3,0]], '#ajax-edit-tablesorter-role', '#ajax-delete-tablesorter');
</script>