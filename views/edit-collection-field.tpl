<fieldset>
    <legend>{text key="h-plugin-creator.field-form-global-legend"}</legend>

    {{ $form->inputs['name'] }}
    {{ $form->inputs['type'] }}
    {{ $form->inputs['description'] }}

    <div e-show="type === 'text'">
        {{ $form->inputs['maxlength'] }}
    </div>

    <div e-show="type === 'float'">
        {{ $form->inputs['decimals'] }}
    </div>

    <div e-show="type !== 'text' || maxlength">
        {{ $form->inputs['unique'] }}
    </div>
</fieldset>


<fieldset>
    <legend>{text key="h-plugin-creator.field-form-form-parameters-legend"}</legend>
    {{ $form->inputs['inputType'] }}
    {{ $form->inputs['required'] }}

    <div e-show="type === 'date'">
        {{ $form->inputs['date-minimum'] }}
        {{ $form->inputs['date-maximum'] }}
    </div>

    <div e-show="type === 'integer' || type === 'float'">
        {{ $form->inputs['number-minimum'] }}
        {{ $form->inputs['number-maximum'] }}
    </div>


    <div e-show="inputType === 'radio' || inputType === 'select'">
        {{ $form->inputs['options'] }}
    </div>
</fieldset>

{{ $form->fieldsets['submits'] }}