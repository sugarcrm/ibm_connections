describe('Base.Views.IbmConnections', function () {
    var moduleName = 'Accounts',
        viewName = "ibm-connections",
        layoutName = 'tabbed-layout',
        app, view, layout;

    beforeEach(function () {
        SugarTest.loadPlugin('Dashlet');
        app = SugarTest.app;
        app.data.declareModels({'../custom': 'ibm_connectionsCommunity', 'ibm_connectionsTasks': 'ibm_connectionsTasks', 'ibm_connectionsTaskNodes': 'ibm_connectionsTaskNodes'});
        SugarTest.testMetadata.init();

        SugarTest.loadHandlebarsTemplate(layoutName, 'layout', 'base');
        SugarTest.loadComponent('base', 'layout', layoutName);
        SugarTest.loadComponent('base', 'view', viewName);
        SugarTest.loadComponent('base', 'field', 'base');
        SugarTest.testMetadata.addViewDefinition(
            viewName,
            {
                'tabs': [
                    {
                        'module': 'Meetings',
                        'invitation_actions': {
                            'name': 'accept_status_users',
                            'type': 'invitation-actions'
                        }
                    }
                ],
                'panels': [
                    {
                        'name': 'panel_body',
                        'columns': 1,
                        'placeholders': true,
                        'fields': [
                            /* {name: 'visibility', type: 'base', label: 'visibility'} */
                        ]
                    }
                ]

            },
            '../custom'
        );

        SugarTest.testMetadata.set();
        app.data.declareModels();

        layout = SugarTest.createLayout('base', moduleName, layoutName);
        view = SugarTest.createView('base', '../custom', viewName, null, null, true, layout);
        view.settings = new Backbone.Model();
        view._defaultSettings = {
            filter: 7,
            limit: 10,
            visibility: 'user'
        };
    });

    afterEach(function () {
        sinon.collection.restore();
        view.dispose();
        SugarTest.testMetadata.dispose();
        app.view.reset();
        delete app.plugins.plugins['view']['Dashlet'];
        view = null;
        layout = null;
        app = null;
    });

    describe('test hbsHelpers fileSizeFormat', function () {
        _.each([
            [100, '100B'],
            [1024 * 3, '3KB'],
            [1024 * 3.5, '4KB'],
            [1024 * 1024 * 3.5, '3.50MB'] ,
            [1024 * 1024 * 1024 * 4.5, '4.50GB']
        ], function (val) {
            it("test hbsHelpers fileSizeFormat " + val[0] + '=>' + val[1], function () {
                expect(view.hbsHelpers.fileSizeFormat(val[0])).toEqual(val[1]);
            });
        });
    });

    it('test hbsHelpers dateFormat', function () {
        app.user.set('preferences', {datepref: 'Y.m.d'})
        var dt = view.hbsHelpers.dateFormat('2014-01-01');
        expect(dt.string).toContain('2014.01.01');
        ;
    });

    it('test recalcTask', function () {
        var opt = view.accordionOpts['ibm_connectionsTasks'], iid = 'iidVal';

        view.collection = app.data.createBeanCollection('ibm_connectionsTasks',
            [app.data.createBean('ibm_connectionsTasks', {id: iid})]
        );

        view.subView['task-nodes'].cache = {};
        view.subView['task-nodes'].cache[iid] = {
            collection: app.data.createBeanCollection('ibm_connectionsTaskNodes',
                [ 
                    app.data.createBean('ibm_connectionsTaskNodes', {node_type: 'todo', completed:true}),
                    app.data.createBean('ibm_connectionsTaskNodes', {node_type: 'todo' }),
                    app.data.createBean('ibm_connectionsTaskNodes', {node_type: 'entry' })
                ]
            )
        };

        view.settings.set(opt.filterFld, iid);
        view.recalcTask();
        
        expect(view.collection.get(iid).get('total_todos')).toEqual(2);
        expect(view.collection.get(iid).get('completed_todos')).toEqual(1);
        expect(view.collection.get(iid).get('completion')).toEqual(50);
    });

});
