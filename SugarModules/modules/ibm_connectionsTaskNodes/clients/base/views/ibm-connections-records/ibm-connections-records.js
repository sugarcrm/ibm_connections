({
    initialize: function (options) {
        var self = this, collection = app.data.createBeanCollection(options.module);
        collection.on('change:completed', function (model) {
            model.save({}, {success: function () {
                self.render()
            }});
        })

        options.collection = collection;

        this._super('initialize', [options]);

        this.on('button:delete_button:click', this.deleteModel, this);
    },
    
    deleteModel:function (model) {
        var delModel = model;
        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: app.utils.formatString(app.lang.get('NTC_DELETE_CONFIRMATION_FORMATTED'), [delModel.get('name')]),
            onConfirm: function () {
                delModel.destroy();
            }
        });
    }, 

    loadData: function (options) {
        options = options || {}
        this._super('loadData', [options]);

        this.collection.filterDef = [
            {task_id: options.task_id}
        ];
        var self = this;
        this.collection.fetch({
            success: function () {
                self.collection.dataFetched
                self.render();
            }
        });
    }

})
