<table class="table list table-hover">
    <tr>
        <th>{text key="h-plugin-creator.fields-list-name-label"}</th>
        <th>{text key="h-plugin-creator.fields-list-type-label"}</th>
        <th>{text key="h-plugin-creator.fields-list-param-label"}</th>
        <th>{text key="h-plugin-creator.fields-list-description-label"}</th>
        <th>{text key="h-plugin-creator.fields-list-unique-label"}</th>
        <th></th>

    </tr>
    <tbody>
        <tr e-each="fields">
            <td>${name}</td>

            <td>${type && Lang.get('h-plugin-creator.fields-list-type-' + type) || ''}</td>

            <td>
                <div e-show="maxlength && type === 'text'">
                    {text key='h-plugin-creator.field-maxlength-label'} : ${maxlength}
                </div>

                <div e-show="decimals && type === 'float'">
                    {text key='h-plugin-creator.field-decimals-label'} : ${decimals}
                </div>
            </td>

            <td>${description}</td>

            <td>{icon icon='check' e-show="unique"}</td>

            <td class="nowrap">
                {icon icon='pencil' size="lg" class='text-primary' e-click="startEdition" e-if="editable"}
                {icon icon="trash" size="lg" class="text-danger" e-click="$root.deleteField.bind($root)" e-if="editable"}
            </td>
        </tr>
    </tbody>
</table>

{icon class="text-primary pull-right pointer" size="2x" icon="plus-circle" e-click="addField"}