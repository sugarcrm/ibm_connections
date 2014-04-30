({
    extendsFrom: 'CreateView',

    initialize: function(options) {
        var createViewEvents = {};
        createViewEvents['change input:file'] = 'populateFileName';
        this.events = _.extend({}, this.events, createViewEvents);
        this._super('initialize', [options]);
    },

    populateFileName:function()
    {
        if (_.isEmpty(this.model.get('name'))){
            this.model.set('name', this.model.get('filename'));
        }
    },

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
