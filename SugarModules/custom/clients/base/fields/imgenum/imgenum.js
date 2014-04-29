({
    extendsFrom: 'EnumField',

    getSelect2Options: function (optionsKeys) {
        var self = this,  select2Options = this._super('getSelect2Options', [optionsKeys]);


        if (self.def.imgUrl){
            select2Options.formatResult = function (state) {
                if (!state.id) return state.text; // optgroup
                var url = app.utils.formatString(self.def.imgUrl, [state.id]);
                return "<img style='height: 28px; margin-right:10px; ' src='" + url + "'/>" + state.text;
            }
        }

        return select2Options;
    }

})
