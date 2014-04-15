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
        this.model.set('name', this.model.get('filename'));
    }

})