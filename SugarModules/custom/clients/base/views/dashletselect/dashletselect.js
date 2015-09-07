/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
({
    extendsFrom: 'DashletselectView',

    initialize: function (options) {
        this._super('initialize', [options]);
    },

    selectDashlet: function (metadata) {
        var model = app.data.createBean("Dashboards");

        app.drawer.load({
            layout: {
                type: 'dashletconfiguration',
                components: [{
                    view: _.extend({}, metadata.config, {
                        label: app.lang.get(metadata.label, metadata.config.module),
                        type: metadata.type,
                        config: true,
                        module: metadata.config.module || metadata.module
                    })
                }]
            },
            context: {module: metadata.config.module || metadata.module, model: model, forceNew: true, skipFetch: true}
        });
    }
})