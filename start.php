<?php

namespace Hawk\Plugins\HPluginCreator;

App::router()->prefix('/h-plugin-creator', function() {
    App::router()->auth(App::session()->isAllowed('h-plugin-creator.manage-collections'), function() {
        App::router()->prefix('/collections', function() {
            App::router()->get('h-plugin-creator-manage-collections', '', array(
                'action' => 'AdminController.index'
            ));

            App::router()->any('h-plugin-creator-manage-collection', '/{collectionId}', array(
                'where' => array(
                    'collectionId' => '\d+'
                ),
                'action' => 'AdminController.edit',
                'duplicable' => true
            ));

            App::router()->get('h-plugin-creator-edit-collection-field', '/fields', array(
                'action' => 'AdminController.editField'
            ));
        });
    });

    App::router()->prefix('/records', function() {
        // The route to access the list of the records of a defined collection
        App::router()->get('h-plugin-creator-records', '/{collection}', array(
            'auth' => function($route) {
                if(!$route->getData()) {
                    return true;
                }

                $collection = Collection::getByName($route->getData('collection'));

                return $collection->canRead();
            },
            'where' => array(
                'collection' => '\w+'
            ),
            'action' => 'RecordController.index',
            'duplicable' => true
        ));

        // Delete a record
        App::router()->delete('h-plugin-creator-delete-record', '/{collection}/{recordId}/delete', array(
            'where' => array(
                'collection' => '\w+',
                'recordId' => '\d+'
            ),
            'action' => 'RecordController.delete',
            'duplicable' => true
        ));

        // The route to create / modify or delete a record
        App::router()->any('h-plugin-creator-record', '/{collection}/{recordId}', array(
            'where' => array(
                'collection' => '\w+',
                'recordId' => '\d+'
            ),
            'action' => 'RecordController.edit',
            'duplicable' => true
        ));
    });
});