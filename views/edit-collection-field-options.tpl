{icon icon="plus-circle add-option" size="2x" class="text-primary pointer" e-click="$parent.addOption.bind($parent)"}

<ul class="field-options">
    <li e-each="options" class="field-option">
        {icon icon="times-circle" e-click="function() { $root.deleteOption.call($root, $index) }" class="text-danger"}

        <input type="text" placeholder="Value" maxlength="${$root.maxlength || ''}" e-value="value"/>
        =>
        <input type="text" placeholder="Label" e-value="label"/>
    </li>
</ul>