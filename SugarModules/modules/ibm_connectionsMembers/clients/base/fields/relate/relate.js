({
    _render: function () {
        this._super('_render');
        if (this.def.hideMore){
            var plugin = this.$(this.fieldTag).data('select2');
            if (plugin){
                plugin.searchmore = true;
            }
        }
    }
})
