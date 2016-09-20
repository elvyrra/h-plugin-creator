<table class="table table-stripped">
    <tr>
        <th>{text key='h-plugin-creator.collection-form-privileges-role-label'}</th>
        <th>{text key='h-plugin-creator.collection-form-privileges-read-label'}</th>
        <th>{text key='h-plugin-creator.collection-form-privileges-write-label'}</th>
        <th>{text key='h-plugin-creator.collection-form-privileges-delete-label'}</th>
    </tr>

    {foreach($roles as $role)}
        <tr>
            <td>{{ $role->getLabel() }}</td>
            <td>{input type="checkbox" e-value="{'privileges.read[' . $role->id . ']'}"}</td>
            <td>{input type="checkbox" e-value="{'privileges.write[' . $role->id . ']'}"}</td>
            <td>{input type="checkbox" e-value="{'privileges.delete[' . $role->id . ']'}"}</td>
        </tr>
    {/foreach}
</table>