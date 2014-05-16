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
        }
    },

    plugins: ['Dashlet', 'ToggleVisibility', 'Tooltip'],

    /**
     * {@inheritDoc}
     */
    initialize: function (options) {

        this.events['click [name=community_add]'] = 'addCommunity';

        this.initCustomBeans();
        options.meta = options.meta || {};
        options.meta.template = 'tabbed-dashlet';

        this._super('initialize', [options]);
        this.tbodyTag = 'ul[data-action="pagination-body"]';

        //this.on('click [data-event="button:link_member2task"]', this.linkMember2task, this);


        this.$el.on('show', _.bind(function (ev) {
            this.openTaskNodes($(ev.target).attr('id'));
        }, this));
        this.on('button:delete_item:click', this.deleteModel, this);
        this.context.on('tasknodes:remove tasknodes:change:completed tasknodes:reset', this.recalcTask, this);

        Handlebars.registerHelper('dateFormat', function (dateString) {
            var formattedDateString = app.date.format(new Date(dateString), app.user.getPreference('datepref'));

            var wrapper = "<span class=\"relativetime\" " + " >" +
                formattedDateString +
                "</span>";
            return new Handlebars.SafeString(wrapper);
        });

    },

    recalcTask: function () {
        var task_id = this.settings.get('task_id'),
            view = this.getSubView('task-nodes', task_id),
            task = this.collection.get(task_id);

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
        task.set(val);
        this.getSubView('task-status', task.id).render();
    },

    dropAttachment: function (event) {
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

        app.alert.show('ibmconn-uploading',
            {level: 'process',
                title: app.lang.getAppString('LBL_UPLOADING'),
                autoClose: false});

        event.stopPropagation();
        event.preventDefault();
    },

    initDashlet: function () {
        var self = this;
        if (this.meta.config) {
            var communityCollect = app.data.createBeanCollection("ibm_connectionsCommunity", null, {});
            communityCollect.on('reset', this.fillCommunities, this);
            communityCollect.fetch({
                fields: ['id', 'name'],
                error: function () {
                    self.template = app.template.get(self.name + '.ibm-connections-need-configure');
                    self._render();
                }
            });

            this.communityModel = app.data.createBean('ibm_connectionsCommunity');
            this.communityModel.on("error:validation", function(){
                app.alert.show('invalid-data', {
                    level: 'error',
                    messages: 'ERR_RESOLVE_ERRORS',
                    autoClose: true
                });

            }, this);
        }

        this._super('initDashlet', []);

        this.$el.on('dragenter', function (event) {
//            self.$(event.currentTarget).addClass("dragdrop");
            return false;
        });

        this.$el.on('dragover', function (event) {
            return false;
        });

        this.$el.on('dragleave', function (event) {
            event.stopPropagation();
            event.preventDefault();
//            self.$(event.currentTarget).removeClass("dragdrop");
            return false;
        });

        this.$el.on('drop', _.bind(this.dropAttachment, this));

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

    _getFilters: function (index) {
        return [
            {'community_id': this.settings.get('community_id') }
        ];
    },

    openTaskNodes: function (task_id, force) {
        this.settings.set('task_id', task_id);
        var taskNodeView = this.getSubView('task-nodes', task_id);
        this.$el.find('#' + task_id).append(taskNodeView.el);
        taskNodeView.loadData({task_id: task_id});
        taskNodeView.render();

        if (force) {
            $('#' + task_id).collapse('show');
        }
    },

    getSubView: function (name, id) {
        if (!this.subView[name]['cache'][id]) {
            var meta = _.extend(this.subView[name]['meta'], {context: this.context, layout: this});
            this.subView[name]['cache'][id] = app.view.createView(meta);
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
                    var task_id = model.get('task_id');
                    self.unsetSubView('task-nodes', task_id);
                    self.render();
                    self.$el.find('#' + task_id).collapse('show');
                    self.refreshTabsForModule('ibm_connectionsMembers');
                } else {
                    self.settings.unset('task_id');
                    self.refreshTabsForModule(model.module);
                }
            }
        );
    },

    addLink: function (event, params) {
        /*
         var id = this._getIId(event);
         var str = "addLink\n"+"community_id="+this.settings.get('community_id')+"\n"
         +"module="+params.module+"\n"
         +"link="+params.link+"\n"
         +"iid="+id+"\n";
         alert(str);
         */

        var self = this;
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
        }, function (model) {

        });
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
                messages: 'You cannot unlink community owner',
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
            if ('ibm_connectionsTasks' == this.collection.module) {
                _.each(this.collection.models, function (model) {
                    var statusView = this.getSubView('task-status', model.id);
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
            var task_id = this.settings.get('task_id');
            if (!_.isEmpty(task_id)) {
                this.$el.find('#' + task_id).collapse('show');
            }
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
    }
})
