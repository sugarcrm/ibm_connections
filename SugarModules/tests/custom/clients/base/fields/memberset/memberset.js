describe('Base.Field.Memberset', function () {

    var app, field, roles = {'role1': 'roleTit1', 'role2': 'roleTit2' }, sinonSandbox;

    beforeEach(function () {
        sinonSandbox = sinon.sandbox.create();
        app = SugarTest.app;
        SugarTest.testMetadata.init();
        SugarTest.testMetadata.set();
        var fieldDef = {
            'type': 'memberset',
            'name': 'members',
            'label': 'Add member',
            'module': 'ibm_connectionsMembers'
        };


        app.data.declareModels({'ibm_connectionsCommunity': 'ibm_connectionsCommunity'});
        var model = app.data.createBean('ibm_connectionsCommunity');
        field = SugarTest.createField("base", "members", "memberset", "edit", fieldDef, '../custom', model, null, true);
        field.roles = roles;
    });

    afterEach(function () {
        app.cache.cutAll();
        app.view.reset();
        sinonSandbox.restore();
        field.model = null;
        field = null;
    });

    it("test format empty value", function () {
        var val = field.format();
        expect(field._currentIndex).toBe(0);
        expect(val[0]['role']).toBe(_.first(_.keys(roles)));
        expect(val[0]['add_button']).toBe(true);
        expect(val[0]['remove_button']).toBeUndefined();
    });

    it("test format add row", function () {

        field._currentIndex = 1;
        var val = field.format([1]);
        expect(val[field._currentIndex]['role']).toBe(_.first(_.keys(roles)));
        expect(val[field._currentIndex]['add_button']).toBe(true);
        expect(val[field._currentIndex]['remove_button']).not.toBeUndefined();
    });

    it("test format one row value", function () {
        var baseValue = [
                {id: 'id1', name: 'name1', role: 'role1'}
            ],
            expVal = [
                {id: 'id1', name: 'name1', role: 'role1', 'add_button': true}
            ];
        field._currentIndex = 0;
        var val = field.format(baseValue);
        expect(val).toEqual(expVal);
    });

    it("test format two row value", function () {
        var baseValue = [
                {id: 'id1', name: 'name1', role: 'role1'},
                {id: 'id2', name: 'name2', role: 'role2'}
            ],
            expVal = [
                {id: 'id1', name: 'name1', role: 'role1', remove_button: true},
                {id: 'id2', name: 'name2', role: 'role2', 'add_button': true, remove_button: true}
            ];
        field._currentIndex = 1;
        var val = field.format(baseValue);
        expect(val).toEqual(expVal);
    });

    it('test getDefaultRole', function () {
        field.roles = {'role1': 'roleTit1', 'role2': 'roleTit2' };
        var role = field.getDefaultRole();
        expect(role).toEqual('role1');
    });

    it('testing changeRole', function () {
        var event = {
            currentTarget: $('<input type="hidden" data-index="1" value="roleVal" >').get(0)
        }
        field.value = [
            {},
            {}
        ];
        // function _updateAndTriggerChange on parent prototype
        field._updateAndTriggerChange = function () {
        };
        var triggerChangeStub = sinonSandbox.stub(field, "_updateAndTriggerChange");
        field.changeRole(event);

        expect(triggerChangeStub).toHaveBeenCalledWith([
            {},
            {role: 'roleVal'}
        ]);
    });

    it('testing render for edit template', function () {

        field.tplName = 'edit';
        sinonSandbox.spy(field, "$");

        field._render();
        expect(field.$).toHaveBeenCalledWith(field.roleTag);
        expect(field.$).toHaveBeenCalledWith(field.fieldTag);
    });

});
