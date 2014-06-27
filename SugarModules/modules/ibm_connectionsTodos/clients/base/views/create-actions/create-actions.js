({
    extendsFrom: 'CreateView',

    _render: function () {
        this._super('_render', []);
        this.fillEnum(this.getField('assigned_user_id'), app.data.createBeanCollection('ibm_connectionsMembers'));
        this.fillEnum(this.getField('task_id'), app.data.createBeanCollection('ibm_connectionsTasks'));
    },

    fillEnum: function (fldEnum, collect) {
        collect.filterDef = [{'community_id': this.model.get('community_id') } ];

        collect.on('reset', function (list) {
            var options = {};
            _.each(list.models, function (model) {
                options[model.get('id')] = model.get('name');
            }, this);
            fldEnum.items = options;
            fldEnum._render();
        }, this);
        collect.fetch({fields: ['id', 'name'], limit: 150, max_num: 150 });
    }

})
