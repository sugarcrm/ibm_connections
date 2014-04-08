({
    extendsFrom: 'CreateView',

    _render: function () {
        debugger;
        this._super('_render', []);
        this.fillEnum('assigned_user_id', 'ibm_connectionsMembers');
        this.fillEnum('task_id', 'ibm_connectionsTasks');
    },

    fillEnum: function (fldName, module) {

        var fldEnum = _.find(this.fields, function (fld) {
            return fld.name == fldName
        });

        var collect = app.data.createBeanCollection(module);
        collect.filterDef = [{'community_id': this.model.get('community_id') }];
        
        collect.on('reset', function (list) {
            var options = {};            
            _.each(list.models, function (model) {
                options[model.get('id')] = model.get('name');
            }, this);
//            debugger;
            fldEnum.items = options;
            fldEnum._render();
        }, this);
        collect.fetch();


    }



})
