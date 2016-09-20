<?php

namespace Hawk\Plugins\HPluginCreator;

class RecordController extends Controller {
    public function index() {
        $collection = Collection::getByName($this->collection);

        $options = json_decode($collection->listOptions);
        $formOptions = json_decode($collection->formOptions);

        $param = array(
            'id' => $this->_plugin . '-records-' . $this->collection,
            'class' => $this->_plugin . '-records',
            'model' => $collection->getClass(),
            'controls' => array(),
            'fields' => array(
                '$actions' => $collection->canWrite() || $collection->canDelete() ? array(
                    'independant' => true,
                    'display' => function($value, $field, $record) use($collection, $formOptions) {
                        $result = '';

                        if($collection->canWrite()) {
                            $result .= Icon::make(array(
                                'icon' => 'pencil',
                                'class' => 'text-primary',
                                'href' => App::router()->getUri('h-plugin-creator-record', array(
                                    'collection' => $this->collection,
                                    'recordId' => $record->id
                                )),
                                'target' => empty($formOptions->dialog) ? '' : 'dialog'
                            ));
                        }

                        if($collection->canDelete()) {
                            $result .= Icon::make(array(
                                'icon' => 'times',
                                'class' => 'text-danger delete-record',
                                'data-record' => $record->id,
                                'data-collection' => $this->collection
                            ));
                        }

                        return $result;
                    },
                    'search' => false,
                    'sort' => false
                ) : null
            )
        );

        $fields = json_decode($collection->fields, true);
        $fields = array_filter($fields, function($field) {
            return !empty($field['list']);
        });
        uasort($fields, function($field1, $field2) {
            return $field2['list']['order'] < $field1['list']['order'];
        });

        foreach($fields as $field) {
            $name = $field['name'];

            $data =  array(
                'label' => $name == 'id' ? 'id' : $field['description'],
                'display' => $collection->getListDisplayFunction($name),
                'search' => $collection->getListSearchParam($name)
            );

            $param['fields'][$name] = $data;
        }



        if(!empty($options->newButton) && $collection->canWrite()) {
            $param['controls'][] = array (
                'icon' => 'plus',
                'class' => 'btn-success',
                'label' => Lang::get($this->_plugin . '.records-list-new-button'),
                'href' => App::router()->getUri('h-plugin-creator-record', array(
                    'collection' => $this->collection,
                    'recordId' => 0
                )),
                'target' => empty($formOptions->dialog) ? '' : 'dialog'
            );
        }

        if(!empty($options->refreshButton)) {
            $param['controls'][] = array (
                'template' => 'refresh'
            );
        }

        $list = new ItemList($param);

        if($list->isRefreshing()) {
            return $list->display();
        }

        $this->addJavaScript($this->getPlugin()->getJsUrl('records.js'));
        $this->addKeysToJavaScript($this->_plugin . '.confirm-record-delete');

        return NoSidebarTab::make(array(
            'title' => $options->pageTitle,
            'icon' => $options->pageIcon,
            'page' => $list->display()
        ));
    }


    public function edit() {
        $collection = Collection::getByName($this->collection);

        $collectionClass = $collection->getClass();

        $options = json_decode($collection->formOptions);

        $formParam = array(
            'id' => $this->_plugin . '-record-form-' . $this->collection . '-' . $this->recordId,
            'class' => $this->_plugin . '-record-form',
            'model' => $collectionClass,
            'reference' => array(
                'id' => $this->recordId
            ),
            'fieldsets' => array(
                'form' => array(),
                'submits' => array(
                    new SubmitInput(array(
                        'name' => 'valid',
                        'value' => Lang::get('main.valid-button')
                    )),
                    new DeleteInput(array(
                        'name' => 'delete',
                        'value' => Lang::get('main.delete-button'),
                        'notDisplayed' => !$this->recordId
                    )),
                    new ButtonInput(array(
                        'name' => 'cancel',
                        'value' => Lang::get('main.cancel-button'),
                        'href' => !empty($options->dialog) ? null : App::router()->getUri($this->_plugin . '-records', array(
                            'collection' => $this->collection
                        )),
                        'onclick' => empty($options->dialog) ? null : 'app.dialog("close")'
                    ))
                )
            ),
            'onsuccess' => empty($options->dialog) ?
                'app.load(app.getUri("h-plugin-creator-records", {
                    collection : "' . $this->collection . '"
                }));' :
                'app.dialog("close"); app.lists["' . $this->_plugin . '-records-' . $this->collection . '"].refresh();'
        );

        $fields = json_decode($collection->fields, true);
        $fields = array_filter($fields, function($field) use($collectionClass) {
            return $field['name'] !== $collectionClass::getPrimaryColumn();
        });



        foreach($fields as $field) {
            $name = $field['name'];

            $inputClass = '\\Hawk\\' . ucfirst($field['inputType']) . 'Input';

            $inputParam = array(
                'name' => $name,
                'label' => $field['description'],
                'required' => !empty($field['required']),
                'maxlength' => !empty($field['maxlength']) ? $field['maxlength'] : null,
                'unique' => !empty($field['unique']) ? true : false,
                'minimum' => !empty($field['minimum']) ? $field['minimum'] : null,
                'maximum' => !empty($field['maximum']) ? $field['maximum'] : null,
                'min' => !empty($field['min']) ? $field['min'] : null,
                'max' => !empty($field['max']) ? $field['max'] : null,
            );

            if(!empty($field['options'])) {
                $inputParam['options'] = [];

                foreach($field['options'] as $option) {
                    if(!empty($option)) {
                        $inputParam['options'][$option['value']] = $option['label'];
                    }
                }
            }

            $formParam['fieldsets']['form'][] = new $inputClass($inputParam);
        }

        $form = new Form($formParam);

        if(!$form->submitted()) {
            if(empty($options->dialog)) {
                return NoSidebarTab::make(array(
                    'icon' => $options->icon,
                    'title' => $options->title,
                    'page' => $form->display()
                ));
            }

            return Dialogbox::make(array(
                'icon' => $options->icon,
                'title' => $options->title,
                'page' => $form->display()
            ));
        }
        else {
            return $form->treat();
        }
    }


    public function delete() {
        $collection = Collection::getByName($this->collection);

        $class = $collection->getClass();

        $record = $class::getById($this->recordId);

        if(!$record) {
            throw new PageNotFoundException('The record ' . $this->recordId . ' of the collection ' . $this->collection . ' does not exists');
        }

        $record->delete();

        App::response()->setStatus(204);
    }
}