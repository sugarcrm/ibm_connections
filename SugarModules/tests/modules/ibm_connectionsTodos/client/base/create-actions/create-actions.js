describe("ibm_connectionsTodos check fldEnum", function() {
    var moduleName = 'ibm_connectionsTodos', relateModuleName = 'ibm_connectionsMembers',
        app,
        viewName = 'create-actions',
        view,
        collectonStub, fetchStub, fldEnum = {items:null, _render:function(){}}, fldEnumRender;

    beforeEach(function() {
        SugarTest.testMetadata.init();
        SugarTest.loadComponent('base', 'view', viewName, moduleName);
        SugarTest.app.data.declareModel(relateModuleName, relateModuleName);
        app = SugarTest.app;
        view = SugarTest.createView("base", moduleName, viewName, null, null);
        fldEnumRender = sinon.stub(fldEnum, "_render");
        
        collectonStub = app.data.createBeanCollection(relateModuleName);
        fetchStub = sinon.stub(collectonStub, "fetch", function() {
            
            var list =  [
                app.data.createBean(relateModuleName, {id:1, name: 'name1' }  ),
                app.data.createBean(relateModuleName, {id:2, name: 'name2' }  )
            ];
            this.reset(list);
        });

    });

    afterEach(function() {
        view.dispose();
        view = null;
        fldEnumRender.restore();
        fetchStub.restore();
    });

    
    it("Check enum items", function() {
        view.fillEnum(fldEnum, collectonStub);
        expect(fldEnum.items[1]).toBe('name1');
        expect(fldEnum.items[2]).toBe('name2');
    });

    it("Check is collection fetched and fld enum rendered", function() {
        view.fillEnum(fldEnum, collectonStub);
        expect(fetchStub.called).toBe(true);
        expect(fldEnumRender.called).toBe(true);
        expect(fldEnumRender.calledAfter(fetchStub)).toBe(true);
    });

});
