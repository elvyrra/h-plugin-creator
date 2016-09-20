{assign name="formContent"}
    <div class="row">
        <div class="col-md-12">
            {{ $form->fieldsets['submits']}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{ $content }}
        </div>
    </div>
{/assign}

{form id="{$form->id}" content="{$formContent}"}