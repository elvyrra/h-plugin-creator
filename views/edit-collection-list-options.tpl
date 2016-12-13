<div>
    <fieldset>
        <legend>{text key="h-plugin-creator.collection-form-list-fields-legend"}</legend>

        <div class="row">
            <div class="col-xs-6 fields-in-list-wrapper">
                <ul class="fields-not-in-list">
                    <li e-each="fieldsNotInList">
                        {icon icon='plus-circle' class='text-primary pointer' e-click="$root.addFieldToList.bind($root)"}
                        <span class="field-name">${name}</span>
                    </li>
                </ul>
            </div>
            <div class="col-xs-6 fields-in-list-wrapper">
                <ul class="fields-in-list">
                    <li e-each="fieldsInList">
                        {icon icon='times-circle pointer' e-click="$root.removeFieldFromList.bind($root)"}
                        <span class="field-name">${name}</span>

                        <div class="pull-right up-down-buttons">
                            {button icon='chevron-up' e-click="$root.upFieldInList.bind($root)" e-disabled="$index === $root.fieldsInList.length - $root.fieldsInList.length"}
                            {button icon='chevron-down' e-click="$root.downFieldInList.bind($root)" e-disabled="$index === $root.fieldsInList.length - 1"}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </fieldset>


    <fieldset>
        <legend>{text key="h-plugin-creator.collection-form-list-controls-legend"}</legend>
        <div class="row">
            <div class="col-md-6">
                {{ $form->inputs['listTitle'] }}
                {{ $form->inputs['listIcon'] }}
            </div>
            <div class="col-md-6">
                {{ $form->inputs['listAddButton'] }}
                {{ $form->inputs['listRefreshButton'] }}
            </div>
        </div>
    </fieldset>
</div>