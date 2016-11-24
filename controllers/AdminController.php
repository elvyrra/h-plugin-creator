<?php

/**
 * CollectionController.php
 */

namespace Hawk\Plugins\HPluginCreator;

class AdminController extends Controller{

    /**
     * Display the list of the managed collections
     */
    public function index() {
        $list = new ItemList(array(
            'id' => 'data-builder-collections-list',
            'model' => 'Collection',
            'controls' => array(
                'new' => array(
                    'class' => 'btn-success',
                    'label' => Lang::get($this->_plugin . '.collections-list-new-btn'),
                    'href' => App::router()->getUri($this->_plugin . '-manage-collection', array(
                        'collectionId' => 0
                    ))
                )
            ),
            'fields' => array(
                'name' => array(
                    'label' => Lang::get($this->_plugin . '.collections-list-name-label'),
                    'href' => function($value, $field, $collection) {
                        return App::router()->getUri($this->_plugin . '-manage-collection', array(
                            'collectionId' => $collection->id
                        ));
                    }
                ),

                'table' => array(
                    'independant' => true,
                    'display' => function($value, $field, $collection) {
                        return $collection->getTablename();
                    },
                    'label' => Lang::get($this->_plugin . '.collections-list-table-label')
                ),

                'menu' => array(
                    'label' => Lang::get($this->_plugin . '.collections-list-menu-label'),
                    'display' => function($value) {
                        if($value) {
                            return Icon::make(array(
                                'icon' => 'check',
                                'class' => 'text-primary',
                                'size' => 'lg'
                            ));
                        }

                        return ' ';
                    }
                )
            )
        ));

        if($list->isRefreshing()) {
            return $list->display();
        }

        return NoSidebarTab::make(array(
            'icon' => 'database',
            'title' => Lang::get($this->_plugin . '.admin-title'),
            'page' => $list->display()
        ));
    }


    public function edit() {
        $allIcons = (new IconSelector)->getAllIcons();

        $iconOptions = array();

        foreach($allIcons as $name => $code) {
            $iconOptions[$name] = '&#x' . $code . '&#9;' . $name;
        }


        $form = new Form(array(
            'id' => 'h-plugin-creator-collection-form-' . $this->collectionId,
            'class' => 'h-plugin-creator-collection-form',
            'model' => 'Collection',
            'reference' => array(
                'id' => $this->collectionId
            ),
            'fieldsets' => array (
                'form' => array(
                    new HiddenInput(array(
                        'name' => 'fields',
                        'default' => json_encode(array(
                            array(
                                'name' => 'id',
                                'type' => 'integer',
                                'description' => Lang::get($this->_plugin . '.fields-list-id-description'),
                                'unique' => true,
                                'primary' => true
                            )
                        )),
                        'attributes' => array(
                            'e-value' => 'compiledFields'
                        )
                    )),

                    new HiddenInput(array(
                        'name' => 'listOptions',
                        'default' => '{}',
                        'attributes' => array(
                            'e-value' => 'listOptions.toString()'
                        )
                    )),
                    new HiddenInput(array(
                        'name' => 'formOptions',
                        'default' => '{}',
                        'attributes' => array(
                            'e-value' => 'formOptions.toString()'
                        )
                    )),
                    new HiddenInput(array(
                        'name' => 'privileges',
                        'default' => json_encode(array(
                            'read' => array(
                                Role::ADMIN_ROLE_ID => true
                            ),
                            'write' => array(
                                Role::ADMIN_ROLE_ID => true
                            ),
                            'delete' => array(
                                Role::ADMIN_ROLE_ID => true
                            )
                        )),
                        'attributes' => array(
                            'e-value' => 'privileges.toString()'
                        )
                    )),

                    new TextInput(array(
                        'name' => 'name',
                        'required' => true,
                        'unique' => true,
                        'pattern' => '/^\w+$/',
                        'label' => Lang::get($this->_plugin . '.collection-form-name-label'),
                        'readonly' => $this->collectionId
                    )),

                    new CheckboxInput(array(
                        'name' => 'menu',
                        'label' => Lang::get($this->_plugin . '.collection-form-menu-label'),
                        'attributes' => array(
                            'e-value' => 'menu'
                        )
                    )),

                    new TextInput(array(
                        'name' => 'menuTitle',
                        'label' => Lang::get($this->_plugin . '.collection-form-menu-title-label'),
                        'labelClass' => 'required',
                        'required' => App::request()->getBody('menu')
                    ))
                ),

                'listOptions' => array(
                    new CheckboxInput(array(
                        'name' => 'listAddButton',
                        'independant' => true,
                        'label' => Lang::get($this->_plugin . '.collection-form-list-options-new-btn'),
                        'attributes' => array(
                            'e-value' => 'listOptions.newButton'
                        )
                    )),

                    new CheckboxInput(array(
                        'name' => 'listRefreshButton',
                        'independant' => true,
                        'label' => Lang::get($this->_plugin . '.collection-form-list-options-refresh-btn'),
                        'attributes' => array(
                            'e-value' => 'listOptions.refreshButton'
                        )
                    )),

                    new TextInput(array(
                        'name' => 'listTitle',
                        'required' => true,
                        'independant' => true,
                        'label' => Lang::get($this->_plugin . '.collection-form-list-options-page-title-label'),
                        'attributes' => array(
                            'e-value' => 'listOptions.pageTitle'
                        )
                    )),


                    new SelectInput(array(
                        'name' => 'listIcon',
                        'independant' => true,
                        'class' => 'icon',
                        'label' => Lang::get($this->_plugin . '.collection-form-list-options-page-icon-label'),
                        'options' => $iconOptions,
                        'attributes' => array(
                            'e-value' => 'listOptions.pageIcon'
                        )
                    ))
                ),

                'formOptions' => array(
                    new TextInput(array(
                        'name' => 'formTitle',
                        'required' => true,
                        'independant' => true,
                        'label' => lang::get($this->_plugin . '.collection-form-form-options-title-label'),
                        'attributes' => array(
                            'e-value' => 'formOptions.title'
                        )
                    )),

                    new SelectInput(array(
                        'name' => 'formIcon',
                        'independant' => true,
                        'class' => 'icon',
                        'label' => Lang::get($this->_plugin . '.collection-form-form-options-page-icon-label'),
                        'options' => $iconOptions,
                        'attributes' => array(
                            'e-value' => 'formOptions.icon'
                        )
                    )),

                    new CheckboxInput(array(
                        'name' => 'formDialog',
                        'independant' => true,
                        'label' => Lang::get($this->_plugin . '.collection-form-form-options-dialog-label'),
                        'attributes' => array(
                            'e-value' => 'formOptions.dialog'
                        )
                    ))
                ),

                'submits' => array(
                    new SubmitInput(array(
                        'name' => 'valid',
                        'value' => Lang::get('main.valid-button'),
                    )),

                    new DeleteInput(array(
                        'name' => 'delete',
                        'value' => Lang::get('main.delete-button'),
                        'notDisplayed' => !$this->collectionId
                    )),

                    new ButtonInput(array(
                        'name' => 'cancel',
                        'value' => Lang::get('main.cancel-button'),
                        'href' => App::router()->getUri($this->_plugin . '-manage-collections')
                    ))
                )
            )
        ));

        if(!$form->submitted()) {
            $this->addJavaScript($this->getPlugin()->getJsUrl('admin.js'));

            $this->addCss($this->getPlugin()->getCssUrl('admin.less'));

            $this->addkeysToJavaScript(
                $this->_plugin . '.fields-list-type-text',
                $this->_plugin . '.fields-list-type-boolean',
                $this->_plugin . '.fields-list-type-integer',
                $this->_plugin . '.fields-list-type-float',
                $this->_plugin . '.fields-list-type-date',
                $this->_plugin . '.fields-list-type-datetime',
                $this->_plugin . '.delete-field-confirmation',
                $this->_plugin . '.field-form-inputType-text',
                $this->_plugin . '.field-form-inputType-select',
                $this->_plugin . '.field-form-inputType-radio',
                $this->_plugin . '.field-form-inputType-checkbox',
                $this->_plugin . '.field-form-inputType-number',
                $this->_plugin . '.field-form-inputType-datetime',
                $this->_plugin . '.field-form-inputType-number'
            );

            $roles = Role::getAll(null, array(), array(), true);

            $accordion = Accordion::make(array(
                'panels' => array(
                    'global' => array(
                        'title' => Lang::get($this->_plugin . '.collection-form-global-legend'),
                        'type' => 'info',
                        'content' => View::make($this->getPlugin()->getView('edit-collection-global-data.tpl'), array(
                            'form' => $form
                        ))
                    ),
                    'fields' => array(
                        'title' => Lang::get($this->_plugin . '.collection-form-fields-legend'),
                        'type' => 'primary',
                        'content' => View::make($this->getPlugin()->getView('edit-collection-fields-list.tpl'))
                    ),

                    'privileges' => array(
                        'title' => Lang::get($this->_plugin . '.collection-form-privileges-legend'),
                        'type' => 'primary',
                        'content' => View::make($this->getPlugin()->getView('edit-collection-privileges.tpl'), array(
                            'roles' => $roles
                        ))
                    ),

                    'list' => array(
                        'title' => Lang::get($this->_plugin . '.collection-form-list-options-legend'),
                        'type' => 'primary',
                        'content' => View::make($this->getPlugin()->getView('edit-collection-list-options.tpl'), array(
                            'form' => $form
                        ))
                    ),

                    'form' => array(
                        'title' => Lang::get($this->_plugin . '.collection-form-form-options-legend'),
                        'type' => 'primary',
                        'content' => View::make($this->getPlugin()->getView('edit-collection-form-options.tpl'), array(
                            'form' => $form
                        ))
                    )
                )
            ));

            $page = View::make($this->getPlugin()->getView('edit-collection.tpl'), array(
                'form' => $form,
                'content' => $accordion,
                'editFieldForm' => $this->editField()
            ));

            return NoSidebarTab::make(array(
                'icon' => 'database',
                'title' => Lang::get($this->_plugin . '.admin-title'),
                'page' => $page
            ));
        }
        else {
            try {
                if($form->submitted() === 'delete') {
                    $collection = $form->object;

                    // Delete a collection
                    $collection->delete();

                    // Delete the table
                    $class = $collection->getClass();
                    $class::dropTable();

                    // Delete the model class file
                    unlink($collection->getClassFilename());

                    // Delete the menu item
                    $menu = MenuItem::getByName($this->_plugin . '.records-' . $collection->name);
                    if($menu) {
                        $menu->delete();
                    }

                    return $form->response(Form::STATUS_SUCCESS);
                }
                elseif($form->check()) {
                    // Register the collection parameters
                    $form->register(Form::NO_EXIT);

                    // Create the model class
                    $template = file_get_contents($this->getPlugin()->getView('CollectionModel.tpl'));

                    $collection = $form->object;

                    $objectFields = json_decode($collection->fields, true);
                    $fields = array();
                    $constraints = array();

                    foreach($objectFields as $parameters) {
                        $name = $parameters['name'];

                        $fields[$name] = array();

                        if($name === 'id') {
                            $fields[$name]['auto_increment'] = true;
                        }
                        switch($parameters['type']) {
                            case 'text' :
                                if(!empty($parameters['maxlength'])) {
                                    $fields[$name]['type'] = 'varchar(' . $parameters['maxlength'] . ')';
                                }
                                else {
                                    $fields[$name]['type'] = 'text';

                                    $parameters['unique'] = false;
                                }
                                break;

                            case 'boolean' :
                                $fields[$name]['type'] = 'tinyint(1)';
                                break;

                            case 'float' :
                                $decimals = empty($parameters['decimals']) ? 2 : $parameters['decimals'];

                                $fields[$name]['type'] = 'float(10,' . $decimals . ')';
                                break;

                            case 'integer' :
                                $fields[$name]['type'] = 'int(11)';
                                break;

                            case 'date' :
                                $fields[$name]['type'] = 'date';
                                break;

                            case 'datetime' :
                                $fields[$name]['type'] = 'datetime';
                                break;
                        }

                        if(!$form->new && !empty($parameters['oldName'])) {
                            $fields[$name]['oldName'] = $parameters['oldName'];
                        }

                        if(!empty($parameters['unique']) && $name !== 'id') {
                            $constraints[$name . 'unique'] = array(
                                'type' => 'unique',
                                'fields' => array($name)
                            );
                        }
                    }

                    $data = array(
                        'classname' => $collection->getClassname(),
                        'tablename' => $collection->getTablename(),
                        'fields' => var_export($fields, true),
                        'constraints' => var_export($constraints, true)
                    );

                    $classDefinition = preg_replace_callback('/\{\{ (\w+) \}\}/', function($match) use($data) {
                        return $data[$match[1]];
                    }, $template);

                    file_put_contents($collection->getClassFilename(), $classDefinition);

                    // Create or update the table schema
                    $class = $collection->getClass();

                    if($form->new) {
                        $class::createTable();
                    }
                    else {
                        $class::updateTable();
                    }

                    // Create the menu to access the collection
                    $menu = MenuItem::getByName($this->_plugin . '.records-' . $collection->name);

                    if($collection->menu) {
                        if(empty($menu)) {
                            MenuItem::add(array(
                                'plugin' => $this->_plugin,
                                'name' => 'records-' . $collection->name,
                                'parentId' => 0,
                                'labelKey' => $form->getData('menuTitle'),
                                'action' => 'h-plugin-creator-records',
                                'actionParameters' => array(
                                    'collection' => $collection->name,
                                )
                            ));
                        }
                    }
                    else {
                        if($menu) {
                            $menu->delete();
                        }
                    }
                    return $form->response(Form::STATUS_SUCCESS);
                }
            }
            catch(\Exception $e) {
                if($form->new) {
                    $form->delete();
                }
                return $form->response(Form::STATUS_ERROR, $e->getMessage());
            }
        }
    }

    /**
     * Display the form to edit a field
     * @return string The HTML result
     */
    public function editField() {
        $form = new Form(array(
            'id' => $this->_plugin . '-collection-field-form',
            'attributes' => array(
                'e-with' => 'clone',
                // 'e-submit' => '$parent.validEdition.bind($parent)'
            ),
            'fieldsets' => array(
                'form' => array(
                    new TextInput(array(
                        'name' => 'name',
                        'required' => true,
                        'pattern' => '^\w+$',
                        'maxlength' => 32,
                        'label' => Lang::get($this->_plugin . '.field-form-name-label'),
                        'attributes' => array(
                            'e-value' => 'name'
                        )
                    )),

                    new SelectInput(array(
                        'name' => 'type',
                        'required' => true,
                        'options' => array(
                            'text' => Lang::get($this->_plugin . '.field-form-type-text'),
                            'boolean' => Lang::get($this->_plugin . '.field-form-type-boolean'),
                            'integer' => Lang::get($this->_plugin . '.field-form-type-integer'),
                            'float' => Lang::get($this->_plugin . '.field-form-type-float'),
                            'date' => Lang::get($this->_plugin . '.field-form-type-date'),
                        ),
                        'label' => Lang::get($this->_plugin . '.field-form-type-label'),
                        'attributes' => array(
                            'e-value' => 'type',
                            'e-change' => '$parent.changeInputType'
                        )
                    )),

                    new TextInput(array(
                        'name' => 'description',
                        'required' => true,
                        'label' => Lang::get($this->_plugin . '.field-form-description-label'),
                        'attributes' => array(
                            'e-value' => 'description'
                        )
                    )),

                    new IntegerInput(array(
                        'name' => 'maxlength',
                        'minimum' => 0,
                        'label' => Lang::get($this->_plugin . '.field-form-maxlength-label'),
                        'attributes' => array(
                            'e-value' => 'maxlength'
                        )
                    )),

                    new IntegerInput(array(
                        'name' => 'decimals',
                        'minimum' => 1,
                        'label' => Lang::get($this->_plugin . '.field-form-decimals-label'),
                        'attributes' => array(
                            'e-value' => 'decimals'
                        )
                    )),

                    new CheckboxInput(array(
                        'name' => 'unique',
                        'label' => Lang::get($this->_plugin . '.field-form-unique-label'),
                        'attributes' => array(
                            'e-value' => 'unique'
                        ),
                        'after' => Icon::make(array(
                            'icon' => 'question-circle',
                            'size' => 'lg',
                            'title' => Lang::get($this->_plugin . '.fields-list-unique-explication')
                        ))
                    )),

                    new SelectInput(array(
                        'name' => 'inputType',
                        'label' => Lang::get($this->_plugin . '.field-form-inputType-label'),
                        'required' => true,
                        'attributes' => array(
                            'e-options' => '{$data : $parent.getInputTypeOptions(type), $value : "value", $label : "label", $selected: inputType}',
                            'e-value' => 'inputType'
                        )
                    )),

                    new CheckboxInput(array(
                        'name' => 'required',
                        'label' => Lang::get($this->_plugin . '.field-form-required-label'),
                        'attributes' => array(
                            'e-value' => 'required'
                        )
                    )),

                    new HtmlInput(array(
                        'name' => 'options',
                        'label' => Lang::get($this->_plugin . '.field-form-options-label'),
                        'value' => View::make($this->getPlugin()->getView('edit-collection-field-options.tpl'))
                    )),


                    new NumberInput(array(
                        'name' => 'number-minimum',
                        'label' => Lang::get($this->_plugin . '.field-form-number-minimum-label'),
                        'attributes' => array(
                            'e-value' => 'minimum'
                        )
                    )),

                    new NumberInput(array(
                        'name' => 'number-maximum',
                        'label' => Lang::get($this->_plugin . '.field-form-number-maximum-label'),
                        'attributes' => array(
                            'e-value' => 'maximum'
                        )
                    )),

                    new DatetimeInput(array(
                        'name' => 'date-minimum',
                        'label' => Lang::get($this->_plugin . '.field-form-date-minimum-label'),
                        'attributes' => array(
                            'e-value' => 'min'
                        )
                    )),

                    new DatetimeInput(array(
                        'name' => 'date-maximum',
                        'label' => Lang::get($this->_plugin . '.field-form-date-maximum-label'),
                        'attributes' => array(
                            'e-value' => 'max'
                        )
                    )),
                ),

                'submits' => array(
                    new SubmitInput(array(
                        'name' => 'valid',
                        'value' => Lang::get('main.valid-button')
                    )),

                    new ButtonInput(array(
                        'name' => 'cancel',
                        'value' => Lang::get('main.cancel-button'),
                        'attributes' => array(
                            'e-click' => '$parent.cancelEdition.bind($parent)'
                        )
                    ))
                )
            )
        ));

        $formContent = View::make($this->getPlugin()->getView('edit-collection-field.tpl'), array(
            'form' => $form
        ));

        return Dialogbox::make(array(
            'icon' => 'table',
            'title' => Lang::get($this->_plugin . '.field-form-title'),
            'page' => $form->wrap($formContent)
        ));
    }
}