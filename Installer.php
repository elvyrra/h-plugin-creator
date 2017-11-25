<?php
/**
 * Installer.class.php
 */

namespace Hawk\Plugins\HPluginCreator;

/**
 * This class describes the behavio of the installer for the plugin h-data-builder
 */
class Installer extends PluginInstaller{
    /**
     * Install the plugin. This method is called on plugin installation, after the plugin has been inserted in the database
     */
    public function install() {
        Collection::createTable();

        Permission::add($this->_plugin . '.manage-collections', 0, 0);

        mkdir(Plugin::current()->getUserfile('models'));
        mkdir(Plugin::current()->getUserfile('records'));
    }

    /**
     * Uninstall the plugin. This method is called on plugin uninstallation, after it has been removed from the database
     */
    public function uninstall() {
        Collection::dropTable();

        $permissions = $this->getPlugin()->getPermissions();

        foreach($permissions as $permission) {
            $permission->delete();
        }
    }

    /**
     * Activate the plugin. This method is called when the plugin is activated, just after the activation in the database
     */
    public function activate() {
        MenuItem::add(array(
            'plugin' => $this->_plugin,
            'name' => 'manage-collections',
            'parentId' => MenuItem::ADMIN_ITEM_ID,
            'labelKey' => $this->_plugin . '.manage-collections-menu-title',
            'action' => $this->_plugin . '-manage-collections',
            'icon'     => 'database'
        ));
    }

    /**
     * Deactivate the plugin. This method is called when the plugin is deactivated, just after the deactivation in the database
     */
    public function deactivate(){
        $menus = $this->getPlugin()->getMenuItems();

        foreach($menus as $menu) {
            $menu->delete();
        }
    }

    /**
     * Configure the plugin. This method contains a page that display the plugin configuration. To treat the submission of the configuration
     * you'll have to create another method, and make a route which action is this method. Uncomment the following function only if your plugin if
     * configurable.
     */
    /*
    public function settings(){

    }
    */
}