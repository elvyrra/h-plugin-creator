<?php

namespace Hawk\Plugins\HPluginCreator;

class IconSelector {
    public function getAllIcons() {
        $css = file_get_contents(Theme::getSelected()->getStaticCssFile());

        $allIcons = array();

        preg_match_all('/\.icon-([^\:]+)\:before\{content\: "(.+?)"\}/', $css, $matches, PREG_SET_ORDER);

        foreach($matches as $match) {
            $allIcons[$match[1]] = substr($match[2], 1);
        }

        return $allIcons;
    }
}