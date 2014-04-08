({
    extendsFrom: 'CreateView',

    _render: function () {
        this._super('_render', []);
        /*app.view.invokeParent(this, {
            type: 'view',
            name: 'create-actions',
            module: 'ibm_connectionsTodos',
            method: 'fillEnum',
            args: ['task_id', 'ibm_connectionsTasks']
        });*/

        SUGAR.jssource.modules.ibm_connectionsTodos.views.base['create-actions'].controller.fillEnum.apply(this, ['task_id', 'ibm_connectionsTasks'])
    }


})
