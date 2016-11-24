'use strict';

require(['emv', 'jquery', 'app', 'lang'], function(EMV, $, app, Lang) {
    const formId = $('.h-plugin-creator-collection-form', app.tabset.activeTab.content).attr('id');
    const form = app.forms[formId];
    const rootElement = form.node.parents('.no-sidebar-tab').first();

    let fields = JSON.parse(rootElement.find('[name="fields"]').val()) || [];
    let listOptions = JSON.parse(rootElement.find('[name="listOptions"]').val());
    let formOptions = JSON.parse(rootElement.find('[name="formOptions"]').val());
    let privileges = JSON.parse(rootElement.find('[name="privileges"]').val());
    let menu = rootElement.find('[name="menu"]').is(':checked');

    let collection;

    /**
     * Generate a unique id
     * @returns {[type]} [description]
     */
    function guid() {
        const s4 = () => {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        };

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }

    /**
     * This class describes the collection field behavior
     */
    class Field extends EMV {
        /**
         * Constructor
         * @param  {Object} data The initial data of the field
         */
        constructor(data) {
            data.uid = guid();
            data.editing = false;

            // The clone used during edition
            if(!data.maxlength) {
                data.maxlength = 0;
            }

            if(!data.decimals) {
                data.decimals = 2;
            }

            super({
                data : data,
                computed : {
                    editable : function() {
                        return this.name !== 'id' && this.name !== 'parentId';
                    },
                    defaultType : function() {
                        return this.getInputTypeOptions(this.type)[0].value;
                    }
                }
            });

            if(!this.inputType) {
                this.inputType = this.defaultType;
            }

            if(!this.options) {
                this.options = [];
            }

            this.$watch('type', () => {
                this.inputType = this.defaultType;
            });
        }


        /**
         * Get the input types to display
         * @param   {string} type The field type
         * @returns {Array}       The options
         */
        getInputTypeOptions(type) {
            const authorizedTypes = {
                text : ['text', 'select', 'radio'],
                boolean : ['checkbox'],
                integer : ['number', 'select', 'radio'],
                float : ['number', 'select', 'radio'],
                date : ['datetime'],
                default : ['text', 'select', 'radio']
            };

            let options = authorizedTypes[type] || authorizedTypes.default;

            return options.map(function(type) {
                return {
                    value : type,
                    label : Lang.get('h-plugin-creator.field-form-inputType-' + type)
                };
            });
        }

        /**
         * Start the edition of the field
         */
        startEdition() {
            if(!this.editable) {
                return;
            }

            this.$clean();

            this.clone = {};
            this.constructor.inputs.forEach((key) => {
                this.clone[key] = this[key];
            });

            // Add a defaul option (visual)
            if(!this.options.length) {
                this.addOption();
            }

            app.dialog(app.getUri('h-plugin-creator-edit-collection-field'))

            .done(function() {
                const form = app.forms['h-plugin-creator-collection-field-form'];

                form.submit = function() {
                    this.validEdition.call(this);

                    return false;
                }.bind(this);

                this.$apply(form.node.get(0));
            }.bind(this));
        }

        /**
         * Validate and ends the edition of the field
         *
         * @returns {boolean} false
         */
        validEdition() {
            const form = app.forms['h-plugin-creator-collection-field-form'];

            if(!form.isValid()) {
                form.displayErrorMessage(Lang.get('form.error-fill'));

                return false;
            }

            const allFields = collection.fields;

            if(this.clone.name) {
                for(let i = 0; i < allFields.length; i++) {
                    const field = allFields[i];

                    if(field.name === this.clone.name && field !== this) {
                        form.inputs.name.addError(Lang.get('form.unique-field'));

                        return false;
                    }
                }
            }

            if(this.clone.inputType === 'radio' || this.clone.inputType === 'select') {
                // Remove empty options
                this.clone.options = this.clone.options.filter((option) => {
                    return option.value || option.label;
                });
            }

            if(this.name && this.name !== this.clone.name) {
                this.oldName = this.name;
            }

            // The field is valid, register it
            Object.keys(this.clone).forEach((key) => {
                if(key !== 'oldName') {
                    this[key] = this.clone[key];
                }
            });

            if(this === collection.newField) {
                allFields.push(this);
                collection.newField = null;
            }

            app.dialog('close');

            return false;
        }

        /**
         * Cancel the field edition
         */
        cancelEdition() {
            if(this === collection.newField) {
                collection.newField = null;
            }

            app.dialog('close');
        }

        /**
         * Add an option for a select or radio box input
         */
        addOption() {
            this.clone.options.push({
                value : '',
                label : ''
            });
        }

        /**
         * Delete the option at the index
         * @param  {integer} index The index of the option to delete
         */
        deleteOption(index) {
            this.clone.options.splice(index, 1);
        }
    }


    Field.inputs = [
        'name',
        'type',
        'description',
        'maxlength',
        'decimals',
        'unique',
        'inputType',
        'required',
        'options',
        'minimum',
        'maximum',
        'min',
        'max'
    ];

    /**
     * This class describes the behavior of a collection
     */
    class Collection extends EMV {
        /**
         * Constructor
         */
        constructor() {
            // Initialise list options
            if(!listOptions.fields) {
                listOptions.fields = [];
            }

            super({
                data : {
                    id : 'coucou',
                    text : 'coucou',
                    disabled : 'disabled',
                    fields : fields.map((field) => {
                        return new Field(field);
                    }),
                    newField : null,
                    menu : menu,
                    listOptions : listOptions,
                    formOptions : formOptions,
                    privileges : privileges
                },
                computed : {
                    compiledFields : function() {
                        let compiled = this.fields.map((field) => {
                            const line = {};

                            Field.inputs.forEach((key) => {
                                const value = field[key];

                                switch(value) {
                                    case undefined :
                                        break;

                                    case null :
                                        line[key] = field[key];
                                        break;

                                    default :
                                        line[key] = field[key].valueOf();
                                }
                            });

                            line.list = field.list && field.list.valueOf();
                            if(field.oldName) {
                                line.oldName = field.oldName;
                            }

                            return line;
                        });

                        return JSON.stringify(compiled);
                    },
                    fieldsInList : function() {
                        return this.fields.filter((field) => {
                            return field.list;
                        })

                        .sort((field1, field2) => {
                            return field1.list.order < field2.list.order ? -1 : 1;
                        });
                    },
                    fieldsNotInList : function() {
                        return this.fields.filter((field) => {
                            return field.list === undefined;
                        });
                    }
                }
            });
        }

        /**
         * Add a field to the collection
         */
        addField() {
            let field = new Field({
                type : '',
                name : '',
                description : '',
                unique : false,
                options : [],
                inputType : 'text'
            });

            // this.fields.push(field);
            this.newField = field;

            field.startEdition();
        }

        /**
         * Delete a field from the collection
         * @param  {Field} field The field to remove
         * @param  {Event} event The event that triggers this method
         * @returns {boolean}       False
         */
        deleteField(field, event) {
            if(event) {
                event.stopImmediatePropagation();
            }

            if(confirm(Lang.get('h-plugin-creator.delete-field-confirmation'))) {
                let index = this.fields.indexOf(field);

                this.fields.splice(index, 1);
            }

            return false;
        }

        /**
         * Add a collection field to be displayed in the colleciton records list
         * @param {Field} field The field to add to the list
         */
        addFieldToList(field) {
            field.list = {
                order : this.fieldsInList.length
            };
        }

        /**
         * Remove a collection field to be displayed in the collection records list
         * @param  {Field} field The field to remove from the list
         */
        removeFieldFromList(field) {
            delete field.list;
        }

        /**
         * Up the position of the field in the list
         * @param  {Field} field The field to move the position up
         */
        upFieldInList(field) {
            let index = this.fieldsInList.indexOf(field);

            if(index) {
                let previous = this.fieldsInList[index - 1],
                    previousOrder = previous.list.order;

                previous.list.order = field.list.order;
                field.list.order = previousOrder;

                field.$notifySubscribers('list');
            }
        }

        /**
         * Down the potiion of the field in the list
         * @param  {Field} field The field to move the position down
         */
        downFieldInList(field) {
            let index = this.fieldsInList.indexOf(field);

            if(index < this.fieldsInList.length - 1) {
                let next = this.fieldsInList[index + 1],
                    nextOrder = next.list.order;

                next.list.order = field.list.order;
                field.list.order = nextOrder;

                field.$notifySubscribers('list');
            }
        }
    }

    collection = new Collection();

    collection.$apply(form.node.get(0));

    const origSubmit = form.submit;

    form.submit = function() {
        collection.$notifySubscribers('fields');

        origSubmit.call(this);

        return false;
    };

    form.onsuccess = function() {
        app.load(app.getUri('h-plugin-creator-manage-collections'));

        if(collection.menu !== menu) {
            app.refreshMenu();
            app.reloadRoutes();
        }
    };
});