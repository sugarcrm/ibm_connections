({
    extendsFrom: 'CreateView',

    _render: function () {
        this._super('_render', []);
        var taskId = this.getField('task_id'), taskCollect = app.data.createBeanCollection('ibm_connectionsTasks');
        SUGAR.jssource.modules.ibm_connectionsTodos.views.base['create-actions'].controller.fillEnum.apply(this, [taskId, taskCollect]);
    }


})
