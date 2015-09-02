({
    _render: function () {
        var self = this, res = this._super('_render', []), plugin = this.$(this.fieldTag).data('select2');

        if (plugin) {
            if (this.def.imgUrl) {
                plugin.opts.formatResult = function (state) {
                    if (!state.id) return state.text; // optgroup
                    var url = app.utils.formatString(self.def.imgUrl, [state.id]);
                    return "<img style='height: 28px; margin-right:10px; ' src='" + url + "'/>" + state.text;
                };
            }

        }

        return res;
    }


})
