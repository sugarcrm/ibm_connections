
function eeCallback(contextKey) {
    //The EE context is placed into the data context by the common container code.
	var context = opensocial.data.getDataContext().getDataSet(contextKey);
	console.log(context);
};

function registerListeners() {
    var osDataContext = opensocial.data.getDataContext();
	osDataContext.registerListener('org.opensocial.ee.context', eeCallback);
    osDataContext.registerListener(ACTIVEOPPORTUNITY, renderEEForm);
};

gadgets.util.registerOnLoadHandler(function() {
	
	gadgets.log("OnLoadHandler");
    registerListeners();
});