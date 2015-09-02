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
 * @class View.Views.BaseIbmConnectionsView
 * @alias SUGAR.App.view.views.BaseIbmConnectionsView
 * @extends View.Views.BaseIbmConnectionsView
 */
({
    extendsFrom: 'TabbedDashletView',

    subView: {
        'task-nodes': {
            meta: {
                name: 'ibm-connections-records',
                module: 'ibm_connectionsTaskNodes'
            },
            cache: {}
        },
        'task-status': {
            meta: {
                name: 'ibm-connections-status',
                module: 'ibm_connectionsTasks'
            },
            cache: {}
        },
        'member-todos': {
            meta: {
                name: 'ibm-connections-records',
                module: 'ibm_connectionsTaskNodes'
            },
            cache: {}
        },
        'member-status': {
            meta: {
                name: 'ibm-connections-status',
                module: 'ibm_connectionsMembers'
            },
            cache: {}
        }
    },

    accordionOpts: {
        'ibm_connectionsMembers': {
            filterFld: 'assigned_user_id',
            view: 'member-todos',
            statusView: 'member-status'
        },
        'ibm_connectionsTasks': {
            filterFld: 'task_id',
            view: 'task-nodes',
            statusView: 'task-status'
        }

    },

    plugins: ['Dashlet', 'ToggleVisibility', 'Tooltip'],

    reloadMap: {'ibm_connectionsMembers': 'ibm_connectionsTasks',
        'ibm_connectionsTasks': 'ibm_connectionsMembers'},
    /**
     * {@inheritDoc}
     */
    initialize: function (options) {

        this.events = _.extend({}, this.events, {
            'click [name=community_add]': 'addCommunity',
            'show': _.bind(function (ev) {
                var $el = $(ev.target);
                this.openTaskNodes($el.data('module'), $el.attr('id'));
            }, this),
            'tasknodes:remove': 'recalcTaskUpdate',
            'tasknodes:change:completed': 'recalcTaskUpdate',
            'tasknodes:reset': 'recalcTask',
        });

        this.initCustomBeans();
        options.meta = options.meta || {};
        options.meta.template = 'tabbed-dashlet';

        this._super('initialize', [options]);
        this.tbodyTag = 'ul[data-action="pagination-body"]';

        if (this.meta.config) {
            this.communityModel = app.data.createBean('ibm_connectionsCommunity');
            this.populateCommunityName();
            this.getCommunities();
        }

        Handlebars.registerHelper('dateFormat', this.hbsHelpers.dateFormat);
        Handlebars.registerHelper('fileSizeFormat', this.hbsHelpers.fileSizeFormat);
    },

    /**
     * Populating community name on configuration dashlet
     */
    populateCommunityName: function () {
        if ('Home' != this.context.parent.get('module') && 'record' == this.context.parent.get('layout')) {
            this.communityModel.set('name', this.context.parent.get('model').get('name'));
        }
    },

    recalcTaskUpdate: function(){
        this.recalcTask();
        this.refreshTabsForModule(this.reloadMap[this.collection.module]);
    },

    recalcTask: function () {
        var module =  this.collection.module, opt = this.accordionOpts[module];
        var iid = this.settings.get(opt.filterFld),
            view = this.getSubView(opt.view, iid),
            parent = this.collection.get(iid);

        var val = _.reduce(view.collection.models, function (memo, model) {

            if ('todo' == model.get('node_type')) {
                memo.total_todos++;
                if (true === model.get('completed') || '1' === model.get('completed')) {
                    memo.completed_todos++;
                }
                memo.completion = memo.completed_todos / memo.total_todos * 100;
            }
            return memo;
        }, {total_todos: 0, completed_todos: 0, completion: 0});
        parent.set(val);
        this.getSubView(opt.statusView, parent.id).render();
    },

    dropAttachment: function (event) {
        app.alert.show('ibmconn-uploading',
            {level: 'process',
                title: app.lang.getAppString('LBL_UPLOADING'),
                autoClose: false});

        var files = event.originalEvent.dataTransfer.files, uploadedCnt = 0, self = this;
        for (var i = 0; i < files.length; i++) {
            var formData = new FormData();
            formData.append('filename', files[i]);
            formData.append('community_id', this.settings.get('community_id'));
            formData.append('name', files[i].name);
            formData.append('OAuth-Token', app.api.getOAuthToken());

            var uploadURL = app.api.buildURL('ibm_connectionsFiles', 'create', null, {viewed: "1"});
            var jqXHR = $.ajax({
                url: uploadURL,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    uploadedCnt++;
                    if (uploadedCnt == files.length) {
                        app.alert.dismiss('ibmconn-uploading');
                        self.refreshTabsForModule('ibm_connectionsFiles');
                    }
                }
            });
        }
        event.stopPropagation();
        event.preventDefault();
    },

    /**
     * Hannling server errors
     */
    handleServerError: function (error) {
        debugger;
        var tplMap = {ERROR_NEED_AUTHORIZE: 'ibm-connections-need-configure',
            ERROR_CANNOT_CONNECT: 'error'};

        this.template = app.template.get(this.name + '.' + tplMap[error.message]);
        this._render();
    },

    _initEvents: function () {
        // drag and drop for upload files
        this.$el.on('dragenter', function () { return false; });
        this.$el.on('dragover', function () { return false; });
        this.$el.on('dragleave', function (ev) {
            ev.stopPropagation();
            ev.preventDefault();
            return false;
        });
        this.$el.on('drop', _.bind(this.dropAttachment, this));


        // Error validation on create community 
        if (this.meta.config) {
            this.communityModel.on("error:validation", function(){
                app.alert.show('invalid-data', {
                    level: 'error',
                    messages: 'ERR_RESOLVE_ERRORS',
                    autoClose: true
                });

            }, this);
        }

        return this._super('_initEvents', []);
    },

    /**
     * Makes a fetch community follection to get all communites
     */
    getCommunities: function()
    {
        var communityCollect = app.data.createBeanCollection("ibm_connectionsCommunity", null, {});
        communityCollect.on('reset', this.parseCommunities, this);
        communityCollect.fetch({
            fields: ['id', 'name'],
            error: _.bind(this.handleServerError, this)
        });
    },

    /**
     * Parses items passed back from collection into enum options
     *
     * @param {collect}
     */
    parseCommunities: function (communityCollect) {

        this.communityOptions = {};
        var communityField = _.find(this.fields, function (field) {
            return field.name == 'community_id';
        });

        _.each(communityCollect.models, function (community) {
            this.communityOptions[community.get('id')] = community.get('name');
        }, this);

        if (communityField) {
            // set the initial community_id to the first community in the list
            // if there are communites to show and we have not already saved this
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

    _getFilters: function (index) {
        return [
            {'community_id': this.settings.get('community_id') }
        ];
    },

    openTaskNodes: function (module, iid, force) {

        var opt = this.accordionOpts[module], fld = opt.filterFld, filter, filterOpt={};

        filterOpt[fld]=iid;
        filter = [filterOpt];

        if ('ibm_connectionsMembers' == module){
            filter.push({community_id:this.settings.get('community_id')}) ;
        }

        this.settings.set(fld, iid);
        var taskNodeView = this.getSubView(opt.view, iid);
        this.$el.find('#' + iid).append(taskNodeView.el);
        taskNodeView.loadData({filter:filter});
        taskNodeView.parentModule = module;
        taskNodeView.render();

        if (force) {
            $('#' + iid).collapse('show');
        }
    },

    getSubView: function (name, id) {
        if (!this.subView[name]['cache'][id]) {
            var meta = _.extend(this.subView[name]['meta'], {context: this.context, layout: this}),
                view = app.view.createView(meta);

            view.parentCid = this.cid;
            this.subView[name]['cache'][id] = view;
        }
        return this.subView[name]['cache'][id];
    },

    unsetSubView: function (name, id) {
        delete this.subView[name]['cache'][id];
    },

    getTaskNodeCollect: function (task_id) {
        if (!this.taskNodeCache[task_id]) {
            var collection = app.data.createBeanCollection('ibm_connectionsTaskNodes');
            collection.filterDef = [
                {task_id: task_id}
            ];
            this.taskNodeCache[task_id] = collection;
        }

        return this.taskNodeCache[task_id];
    },

    filterDefToObject: function (pairs) {
        var obj = {};
        _.each(pairs, function (condition) {
            obj[ _.keys(condition)[0] ] = _.values(condition)[0];

        });

        return obj;
    },

    /**
     *
     * @param event
     * @param {Object} params Optional params to getaration create form
     * @param {String} params.module
     */
    addItem: function (event, params) {
        var parentId = this._getIId(event);
        var parent = this.collection.get(parentId);
        var defVals = {community_id: this.settings.get('community_id')};
        _.each(params.fieldMap, function (pName, chName) {
            defVals[chName] = parent.get(pName);
        });

        var self = this;
        app.drawer.open({
                layout: 'create-actions',
                context: {
                    create: true,
                    module: params.module,
                    model: app.data.createBean(params.module, defVals)
                }
            }, function (context, model) {
                if (!model) {
                    return;
                }
                if (-1 != _.indexOf(['ibm_connectionsTodos', 'ibm_connectionsEntries'], model.module)) {
                    var opt = self.accordionOpts[self.collection.module], iid = model.get(opt.filterFld);
                    self.unsetSubView(opt.view, iid);
                    self.render();
                    self.$el.find('#' + iid).collapse('show');
                    if ('ibm_connectionsTodos' == model.module){
                        self.refreshTabsForModule(self.reloadMap[self.collection.module]);
                    }
                } else {
                    _.each(self.accordionOpts, function(opt){
                        self.settings.unset(opt.filterFld);
                    });
                    self.refreshTabsForModule(model.module);
                }
            }
        );
    },

    showMemberTasks: function (event) {
        var id = this._getIId(event);
        var str = "showMemberTasks\n"
            + "community_id=" + this.settings.get('community_id') + "\n"
            + "member id=" + id + "\n";
        alert(str);
    },

    showMemberProfile: function (event) {
        var id = this._getIId(event);
        var str = "showMemberProfile\n"
            + "community_id=" + this.settings.get('community_id') + "\n"
            + "member id=" + id + "\n";
        alert(str);
    },

    unlinkRow: function (event, opt) {

        var id = this._getIId(event);
        var delModel = this.collection.get(id), self = this;

        if ('ibm_connectionsMembers' == delModel.module && 'owner' == delModel.get('role')) {
            app.alert.show('upload_error', {
                level: 'error',
                messages: app.lang.get('LBL_IBM-CONNECTIONS_CANNOT_UNLINK_COMMUNITY_OWNER'),
                autoClose: false
            });
            return;
        }

        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: app.utils.formatString(app.lang.get('NTC_UNLINK_CONFIRMATION_FORMATTED'), [delModel.get('name')]),
            onConfirm: function () {
                var data = {
                    id: delModel.get('id'),
                    link: opt.link,
                    related: null,
                    relatedId: delModel.get(opt.rhs_key),
                };
                app.api.relationships('delete', delModel.module, data);
                self.collection.remove(delModel);
                self.render();
            }
        });

    },

    deleteRow: function (event, opt) {
        var id = this._getIId(event);
        var delModel = this.collection.get(id), self = this;

        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: app.utils.formatString(app.lang.get('NTC_DELETE_CONFIRMATION_FORMATTED'), [delModel.get('name')]),
            onConfirm: function () {
                delModel.destroy();
                self.collection.remove(delModel);
                self.render();
            }
        });
    },

    _getIId: function (event) {
        return this.$(event.currentTarget).parents('li:first[iid]').attr("iid");
    },

    _renderHtml: function () {
        if (!this.meta.config) {
            var tab = this.tabs[this.settings.get('activeTab')];
            this.row_actions = tab.row_actions;

            if ('ibm_connectionsFiles' == this.collection.module) {
                _.each(this.collection.models, function (model) {
                    var pictureUrl = app.api.buildFileURL({
                            module: this.collection.module,
                            id: model.get('id')
                        },
                        {htmlJsonFormat: false,
                            cleanCache: true
                        });
                    model.set('url', pictureUrl);
                }, this);
            }
        }
        this._super('_renderHtml');

        if (this.meta.config) {
            this.bindCommunityFlds2Model();
        } else {
            if (!_.isUndefined(this.accordionOpts[this.collection.module])) {
                _.each(this.collection.models, function (model) {
                    var viewName = this.accordionOpts[model.module]['statusView'],
                        statusView = this.getSubView(viewName, model.id);
                    statusView.model = model;
                    this.$el.find('[iid=' + model.id + '] .status').append(statusView.el);
                    statusView.render();
                }, this);
            }
        }

    },

    loadDataForTabs: function (tabs, options) {
        app.alert.show('ibm-connections',
            {level: 'process',
                title: app.lang.getAppString('LBL_LOADING'),
                autoClose: false});

        var self = this;
        options = options || {};
        if (!_.isFunction(options.complete)) {
            options.complete = function () {
            };
        }

        options.complete = _.wrap(options.complete, function (func) {
            func();
            _.each(self.accordionOpts, function(opt){
                var iid = self.settings.get(opt.filterFld);
                if (!_.isEmpty(iid)) {
                    self.$el.find('#' + iid).collapse('show');
                }
            });

            app.alert.dismiss('ibm-connections');
        });

        this._super('loadDataForTabs', [tabs, options]);
    },

    initCustomBeans: function () {
        if (!app.metadata.getModule('ibm_connectionsFiles')) {
            return;
        }

        var filesClass = app.data.getBeanClass("ibm_connectionsFiles");

        filesClass.prototype.save = function (attributes, options) {
            var url = app.api.buildURL(this.module, 'create', this.attributes, {viewed: "1"});
            var ajaxParams = {
                files: options.$files,
                processData: false,
                iframe: true
            };
            delete options.$files;

            if (!options || options.deleteIfFails !== false) {
                options = options || {};
                options.deleteIfFails = true;
            }
            var method = this.isNew() ? 'create' : (options.patch ? 'patch' : 'update');

            return app.api.call(method, url, this.attributes, options, ajaxParams);
        };

    },

    showMore: function () {
        app.alert.show('ibm-connections',
            {level: 'process',
                title: app.lang.getAppString('LBL_LOADING'),
                autoClose: false});

        var self = this;

        this.collection.paginate({
            limit: this.settings.get('limit'),
            add: true,
            success: function () {
                if (!self.disposed) {
                    self.render();
                    app.alert.dismiss('ibm-connections');
                }
            }
        });
    },

    /**
     * Adding community
     */
    addCommunity: function () {
        var self = this;

        var flds = this.getFields('ibm_connectionsCommunity');
        this.communityModel.doValidate(flds, function (isValid) {
            if (isValid) {

                app.alert.show('ibmconn-creating',
                    {level: 'process',
                        title: app.lang.getAppString('LBL_SAVING'),
                        autoClose: false});

                self.communityModel.save({}, {
                    success: function(community){

                        self.communityOptions[community.get('id')] = community.get('name');
                        self.getField('community_id').items = self.communityOptions;
                        self.settings.set('community_id', community.get('id'));
                        self.getField('community_id').render();
                        self.communityModel.clear();
                        /*_.each(self.getCommunityFields(), function (fld) {
                         fld.render();
                         });*/

                        app.alert.dismiss('ibmconn-creating');
                        app.alert.show('create-success', {
                            level: 'success',
                            messages: app.lang.getAppString('LBL_RECORD_SAVED'),
                            autoClose: true,
                            autoCloseDelay: 10000,
                            onLinkClick: function() {
                                app.alert.dismiss('create-success');
                            }
                        });

                    }
                });
            }
        });
    },

    /**
     * Function return list of fields for creation community
     */
    getCommunityFields: function () {
        if (_.isEmpty(this.fields)) {
            return app.logger.error("Empty fields list");
        }
        return _.filter(this.fields, function (fld) {
            return -1 == _.indexOf(['community_id', 'title'], fld.name)
        });
    },

    /**
     * Binding community fields to community model
     */
    bindCommunityFlds2Model:function(){
        _.each(this.getCommunityFields(), function (fld) {
            fld.model = this.communityModel;
            fld.model.on("error:validation:" + fld.name, fld.handleValidationError, fld);
            fld.bindDataChange();
        }, this);
    },

    /**
     * {@inheritDoc}
     *
     * Added support error reporting
     *
     */
    _createCollection: function (tab) {
        var self = this, collection = this._super('_createCollection', [tab]);
        collection.fetch = (function (old) {
            return function (opts) {
                opts = _.extend({}, opts, {
                    error: _.bind(self.handleServerError, self)
                });

                var res = old.apply(this, [opts]);
                return res;
            }
        })(collection.fetch);

        return collection;
    },

    /**
     * Handlebars helpers
     */
    hbsHelpers:{
        dateFormat: function (dateString) {
            var formattedDateString = app.date.format(new Date(dateString), app.user.getPreference('datepref'));

            var wrapper = "<span class=\"relativetime\" " + " >" +
                formattedDateString +
                "</span>";
            return new Handlebars.SafeString(wrapper);
        },

        fileSizeFormat: function (size) {
            var round = 0, sizes = ['B', 'KB', 'MB', 'GB'], size_index = 0;
            size = size || 0;

            while (size > 1024 && size_index < sizes.length - 1) {
                size_index++;
                size /= 1024;
            }
            if (size_index >= 2){
                round = 2;
            }
            size = parseInt(size).toFixed(round);

            return size + sizes[size_index];
        }
    }
})
