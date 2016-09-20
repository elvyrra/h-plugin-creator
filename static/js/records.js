/* global app, $, Lang */

'use strict';

(function() {
    const listId = $('.list-wrapper', app.tabset.activeTab().content()).attr('id');

    $(`#${listId}`).on('click', '.delete-record', function() {
        if(confirm(Lang.get('h-plugin-creator.confirm-record-delete'))) {
            const collection = $(this).data('collection');
            const recordId = $(this).data('record');

            $.ajax({
                url : app.getUri('h-plugin-creator-delete-record', {
                    collection : collection,
                    recordId : recordId
                }),
                method : 'delete'
            })

            .done(() => {
                app.lists[listId].refresh();
            })

            .fail((xhr) => {
                window.app.alert('danger', xhr.responseJSON.message);
            });
        }
    });
})();
