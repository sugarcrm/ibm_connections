/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */
/**
 * History dashlet takes advantage of the tabbed dashlet abstraction by using
 * its metadata driven capabilities to configure its tabs in order to display
 * historic information about specific modules.
 *
 * @class View.Views.BaseHistoryView
 * @alias SUGAR.App.view.views.BaseHistoryView
 * @extends View.Views.BaseTabbedDashletView
 */
({
    extendsFrom: 'TabbedDashletView',

    /**
     * {@inheritDoc}
     *
     * @property {Number} _defaultSettings.filter Number of past days against
     *   which retrieved records will be filtered, supported values are '7',
     *   '30' and '90' days, defaults to '7'.
     * @property {Number} _defaultSettings.limit Maximum number of records to
     *   load per request, defaults to '10'.
     * @property {String} _defaultSettings.visibility Records visibility
     *   regarding current user, supported values are 'user' and 'group',
     *   defaults to 'user'.
     */
    _defaultSettings: {
        filter: 7,
        limit: 10,
        visibility: 'user'
    },

    /**
     * {@inheritDoc}
     */
    initialize: function(options) {
//        this.events['show #'+this.cid] = 'showtaskNodes';
//        this.events['show'] = 'showtaskNodes';
        options.meta = options.meta || {};
        options.meta.template = 'tabbed-dashlet';


        /*this.plugins = _.union(this.plugins, [
         'Connector'
         ]);*/

        this._super('initialize', [options]);
        this.tbodyTag = 'ul[data-action="pagination-body"]';

        //this.on('click [data-event="button:link_member2task"]', this.linkMember2task, this);
        //this.events['show #'+this.cid] = 'showtaskNodes';
        this.$el.on('show', _.bind(this.showtaskNodes, this));
    },

    initDashlet: function () {
        this._super('initDashlet', []);
        if (this.meta.config) {
            var communityCollect = app.data.createBeanCollection("ibm_connectionsCommunity", null, {});
            communityCollect.on('reset', this.fillCommunities, this);
            communityCollect.fetch();
        }
    },

    fillCommunities: function (communityCollect) {
        this.communityOptions = {};
        var communityField = _.find(this.fields, function (field) {
            return field.name == 'community_id';
        });

        _.each(communityCollect.models, function (community) {
            this.communityOptions[community.get('id')] = community.get('name');
        }, this);

        if (communityField) {
            // set the initial saved_report_id to the first report in the list
            // if there are reports to show and we have not already saved this
            // dashlet yet with a community ID
            if (communityCollect.models && !this.settings.has('community_id')) {
                this.settings.set({
                    community_id: _.first(communityCollect.models).id
                });
            }

            // set field options and render
            communityField.items = this.communityOptions;
            communityField._render();
        }

    },

    _getFilters: function(index) {
        return [{'community_id': this.settings.get('community_id') }];
    },

    showtaskNodes: function(ev){
        var tastEl = $(ev.target);
        if (tastEl.attr('subTaskShown')){
            return ;
        }
        tastEl.attr('subTaskShown', 1);
        // debugger;


        var collection = app.data.createBeanCollection('ibm_connectionsTaskNodes');
        collection.filterDef = [{'task_id':  tastEl.attr('id')  }]
        var recordsTpl = app.template.getView('ibm-connections.reports-task-node');

        collection.fetch({
            complete: function() {
                collection.dataFetched = true;
                tastEl.find('.subTaskHold').html(recordsTpl({collection:collection}));
//                callback(null);
            }
        });


        /*
         var recordsTpl = app.template.getView('ibm-connections.reports-task-node');
         this.recordsHtml = recordsTpl(this);

         debugger;
         */
    },

    /*_render: function()
     {
     //        debugger;
     //        this._super('_render', []);
     },*/

    /*_initEvents: function() {
     this._super('_initEvents', []);

     return this;
     },*/


    /**
     *
     * @param event
     * @param {Object} params Optional params to getaration create form
     * @param {String} params.module
     */
    addItem:function(event, params){
        var parentId = this._getIId(event);
        var parent = this.collection.get(parentId);
        var defVals = {community_id: this.settings.get('community_id')};
        _.each(params.fieldMap, function(pName, chName){
            defVals[chName] = parent.get(pName);
        });

        var self = this;
        var model = app.data.createBean(params.module, defVals);
        app.drawer.open({
            layout: 'create-actions',
            context: {
                create: true,
                module: params.module,
                model: model
            }
        }, function(model) {
            debugger;
            if (model) {
                self.layout.reloadDashlet();
//                self.context.trigger('panel-top:refresh', 'emails');
            }
        });

    },




    addLink:function(event, params){
        /*
         //        debugger;
         var id = this._getIId(event);
         var str = "addLink\n"+"community_id="+this.settings.get('community_id')+"\n"
         +"module="+params.module+"\n"
         +"link="+params.link+"\n"
         +"iid="+id+"\n";
         alert(str);
         */

        var self = this;
        debugger;
        var model = app.data.createBean("ibm_connectionsTasks", {community_id: this.settings.get('community_id') })
        app.drawer.open({
            layout: 'create-actions',
            context: {
                create: true,
                module: 'ibm_connectionsTasks',
                model: model
                /*prepopulate: {
                 related: this.model,
                 to_addresses: [{bean: this.model}],
                 community_id:this.settings.get('community_id')
                 }*/
            }
        }, function(model) {
            /*if (model) {
             self.layout.reloadDashlet();
             self.context.trigger('panel-top:refresh', 'emails');
             }*/
        });




        // $(arguments[0].currentTarget).parents('li:first')
//        debugger;
    },

    showMemberTasks: function(event){
        var id = this._getIId(event);
        var str = "showMemberTasks\n"
            + "community_id="+this.settings.get('community_id')+"\n"
            +"member id="+id+"\n";
        alert(str);
    },

    showMemberProfile: function(event){
        var id = this._getIId(event);
        var str = "showMemberProfile\n"
            + "community_id="+this.settings.get('community_id')+"\n"
            +"member id="+id+"\n";
        alert(str);
    },

    rmLink: function(event, opt)
    {
        var id = this._getIId(event);
        var str = "rmLink\n"+"community_id="+this.settings.get('community_id')+"\n"
            +"module="+opt.module+"\n"
            +"link="+opt.link+"\n"
            +"iid="+id+"\n";
        alert(str);
    },

    _getIId:function(event){
        return this.$(event.currentTarget).parents('li:first[iid]').attr("iid");
    },


    _renderHtml: function() {
        if (!this.meta.config) {
            var tab = this.tabs[this.settings.get('activeTab')];
            this.row_actions = tab.row_actions;
        }
        this._super('_renderHtml');
    }


//    /**
//     * New model related properties are injected into each model.
//     * Update the picture url's property for model's assigned user.
//     *
//     * @param {Bean} model Appended new model.
//     */
//    bindCollectionAdd: function(model) {
//        var tab = this.tabs[this.settings.get('activeTab')];
//        model.set('record_date', model.get(tab.record_date));
//        var pictureUrl = app.api.buildFileURL({
//            module: 'Users',
//            id: model.get('assigned_user_id'),
//            field: 'picture'
//        });
//        model.set('picture_url', pictureUrl);
//        this._super('bindCollectionAdd', [model]);
//    },
//
//    /**
//     * {@inheritDoc}
//     */
//    _dispose: function() {
//        this.$('.select2').select2('destroy');
//
//        this._super("_dispose");
//    },
//
//    /**
//     * Open up a drawer to archive email.
//     * @param event
//     * @param params
//     */
//    archiveEmail: function(event, params) {
//        var self = this;
//        app.drawer.open({
//            layout: 'archive-email',
//            context: {
//                create: true,
//                module: 'Emails',
//                prepopulate: {
//                    related: this.model,
//                    to_addresses: [{bean: this.model}]
//                }
//            }
//        }, function(model) {
//            if (model) {
//                self.layout.reloadDashlet();
//                self.context.trigger('panel-top:refresh', 'emails');
//            }
//        });
//    }

})
