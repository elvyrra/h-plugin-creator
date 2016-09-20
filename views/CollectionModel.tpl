<?php

namespace Hawk\Plugins\HPluginCreator;

class {{ classname }} extends Model {
    protected static $tablename = '{{ tablename }}';

    protected static $primaryColumn = 'id';

    public static $fields = {{ fields }};

    public static $constraints = {{ constraints }};
}