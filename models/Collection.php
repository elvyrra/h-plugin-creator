<?php

namespace Hawk\Plugins\HPluginCreator;

use \Hawk\User as User;

class Collection extends Model {
    protected static $tablename = 'HPluginCreatorCollection';

    protected static $primaryColumn = 'id';

    public static $fields = array(
        'id' => array(
            'type' => 'int(11)',
            'auto_increment' => true
        ),

        // The collection name
        'name' => array(
            'type' => 'varchar(32)'
        ),

        // The table containing the collection data
        'table' => array(
            'type' => 'varchar(64)'
        ),

        // The collection fields
        'fields' => array(
            'type' => 'text',
        ),

        // The collection constraints (unicity)
        'constraints' => array(
            'type' => 'text',
        ),

        // Defines if the collection is linked to another one by a parentality link
        'linkedTo' => array(
            'type' => 'varchar(32)'
        ),

        'listOptions' => array (
            'type' => 'text',
        ),

        'formOptions' => array(
            'type' => 'text',
        ),

        'privileges' => array(
            'type' => 'text',
        ),

        // Define if a menu must be created to access the list containing the collection data
        'menu' => array(
            'type' => 'tinyint(1)'
        ),

        'menuTitle' => array(
            'type' => 'varchar(256)'
        )
    );

    public static $constraints = array(
        'name' => array(
            'type' => 'unique',
            'fields' => array('name')
        )
    );


    public function __construct($data = array()) {
        parent::__construct($data);

        $this->decodedFields = array();

        if(!empty($this->fields)) {
            $decodedFields = json_decode($this->fields, true);
            foreach($decodedFields as $field) {
                $this->decodedFields[$field['name']] = $field;
            }
        }
    }

    /**
     * Get a collection by it name
     * @param  string $name The collection name
     * @return Collection       The found instance
     */
    public static function getByName($name) {
        return self::getByExample(new DBExample(array(
            'name' => $name
        )));
    }

    /**
     * Get the classname of the collection
     * @returns string The classname of the collection
     */
    public function getClassname() {
        return ucfirst($this->name) . 'CollectionModel';
    }

    /**
     * Get the file containing the model class
     * @returns string The filename containing the definition of the collecion class
     */
    public function getClassFilename() {
        return Plugin::current()->getUserfile('models/' . $this->getClassname() . '.php');
    }

    /**
     * Require the file containing the collection model definition and returns the classname
     * @returns string The classname
     */
    public function getClass() {
        require_once $this->getClassFilename();

        return Plugin::current()->getNamespace() . '\\' . $this->getClassname();
    }

    public function getTablename() {
        return Plugin::current()->getShortNamespace() . 'Collection' . ucfirst($this->name);
    }


    public function getListDisplayFunction($fieldname) {
        $field = $this->decodedFields[$fieldname];

        if($fieldname === 'id') {
            return function($value) {
                return '#' . $value;
            };
        }

        switch($field['type']) {
            case 'date' :
                return function($value) {
                    if($value !== '0000-00-00') {
                        return date(Lang::get('main.date-format'), strtotime($value));
                    }

                    return '';
                };

            case 'boolean' :
                return function($value) {
                    if($value) {
                        return Icon::make(array(
                            'icon' => 'check',
                            'class' => 'text-primary'
                        ));
                    }

                    return '';
                };

            case 'float' :
                return function($value) use($field) {
                    return number_format($value, $field['decimals'], ',', ' ');
                };
        }

        return null;
    }

    public function getListSearchParam($fieldname) {
        $field = $this->decodedFields[$fieldname];

        switch($field['type']) {
            case 'date' :
                return array(
                    'type' => 'date'
                );

            case 'boolean' :
                return array(
                    'type' => 'checkbox'
                );

            default :
                return true;
        }
    }

    /**
     * Get the privieleges of a user on a collection
     * @param  User|null $user The user to get the privileges. If not set, the current session user is used
     * @return Array          The user privieleges
     */
    public function getUserPrivileges(User $user = null) {
        if(!$user) {
            $user = App::session()->getUser();
        }

        if(empty($this->privileges)) {
            return array();
        }

        if(!isset($this->decodedPrivileges)) {
            $this->decodedPrivileges = json_decode($this->privileges, true);
        }

        return array(
            'read' => $user->roleId == Role::ADMIN_ROLE_ID || !empty($this->decodedPrivileges['read'][$user->roleId]),
            'write' => $user->roleId == Role::ADMIN_ROLE_ID || !empty($this->decodedPrivileges['write'][$user->roleId]),
            'delete' => $user->roleId == Role::ADMIN_ROLE_ID || !empty($this->decodedPrivileges['delete'][$user->roleId]),
        );
    }


    /**
     * Check if a user can read the collection data
     * @param  User|null $user The user to get the privileges. If not set, the current session user is used
     * @return [type]          True if the user can read the collection data, else False
     */
    public function canRead(User $user = null) {
        if(!$user) {
            $user = App::session()->getUser();
        }

        $privileges = $this->getUserPrivileges($user);

        return $privileges['read'];
    }


    /**
     * Check if a user can create / modify collection records
     * @param  User|null $user The user to get the privileges. If not set, the current session user is used
     * @return [type]          True if the user can write the collection data, else False
     */
    public function canWrite(User $user = null) {
        if(!$user) {
            $user = App::session()->getUser();
        }

        $privileges = $this->getUserPrivileges($user);

        return $privileges['write'];
    }


    /**
     * Check if a user can delete collection records
     * @param  User|null $user The user to get the privileges. If not set, the current session user is used
     * @return [type]          True if the user can delete collection records
     */
    public function canDelete(User $user = null) {
        if(!$user) {
            $user = App::session()->getUser();
        }

        $privileges = $this->getUserPrivileges($user);

        return $privileges['delete'];
    }
}