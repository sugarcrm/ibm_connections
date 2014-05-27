({
    initialize: function (options) {
        var self = this, collection = app.data.createBeanCollection(options.module);
        collection.on('change:completed', function (model) {

            app.alert.show('ibm-tasknodes-save',
                {level: 'process',
                    title: app.lang.getAppString('LBL_SAVING'),
                    autoClose: false});

            model.save({}, {success: function () {
                self.render();
                self.layout.$el.trigger('tasknodes:change:completed');
                app.alert.dismiss('ibm-tasknodes-save');
            }});
        })

        collection.on("reset", function () {
            self.layout.$el.trigger('tasknodes:reset');
        });

        options.collection = collection;

        this._super('initialize', [options]);

        this.on('button:delete_button:click', this.deleteModel, this);
    },

    deleteModel: function (model) {
        var delModel = model, self = this;
        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: app.utils.formatString(app.lang.get('NTC_DELETE_CONFIRMATION_FORMATTED'), [delModel.get('name')]),
            onConfirm: function () {

                app.alert.show('ibm-tasknodes-del',
                    {level: 'process',
                        title: app.lang.getAppString('LBL_DELETING'),
                        autoClose: false});

                delModel.destroy({success: function () {
                    self.collection.remove(delModel);
                    self.render();
                    self.layout.$el.trigger('tasknodes:remove');
                    app.alert.dismiss('ibm-tasknodes-del');
                }});
            }
        });
    },

    loadData: function (options) {
        options = options || {}
        this._super('loadData', [options]);
        this.collection.filterDef = app.utils.deepCopy(options.filter);
        var self = this;
        this.collection.fetch({
            success: function () {
                self.collection.dataFetched
                self.render();
            }
        });
    }

})
