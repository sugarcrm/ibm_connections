({
    extendsFrom: 'CreateView',


    /**
     * Create a new record
     * @param success
     * @param error
     */
    saveModel: function (success, error) {
        var self = this, options;
        options = {
            success: success,
            error: error,
            viewed: true,
            showAlerts: {
                'process': true,
                'success': false,
                'error': false //error callback implements its own error handler
            },
            lastSaveAction: this.context.lastSaveAction
        };

        options = _.extend({}, options, self.getCustomSaveOptions(options));
        self.model.save(null, options);
    },

    getCustomSaveOptions: function(options){
        options = options || {};
        var fld = _.find(this.fields, function (field) {
            return field.type == 'file';
        });
        options.$files = fld.$el.find('input'); ;
    },

})
