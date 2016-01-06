{{ content() }}

<table width="100%">
    <tr>
        <td align="left">
            <a href="admin/role/index">Go Back</a>
        </td>
        <td align="right">
            <a href="/admin/role/new">Create</a>
        </td>
    </tr>
</table>

<table class="browse" align="center" >
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
         </tr>
    </thead>
    <tbody>
    {% for role in roles %}
        <tr>
            <td>{{ role['_id'] }}</td>
            <td width = "150px;" align="center">{{ role['name'] }}</td>
            <td><a href="/admin/role/edit/{{ role['_id'] }}">&nbsp;&nbsp;Edit&nbsp;&nbsp;</a></td>
            <td><a href="/admin/role/delete/{{ role['_id'] }}">&nbsp;&nbsp;Delete&nbsp;&nbsp;</a></td>
        </tr>
    {% endfor %}
    </tbody>
    <tbody>
        <tr>
            <td colspan="2" align="right">
            &nbsp;
            </td>
        </tr>
    </tbody>
</table>
