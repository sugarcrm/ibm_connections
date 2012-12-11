
var OPPORTUNITIES = "opportunities";
var ACTIVEOPPORTUNITY = "activeOpportunity";



//Start: Event Handler Callbacks
/*
All the functions in this section are invoked because they are
listening on callbacks in the OpenSocial Data Contxt
 */


function renderOpportunitiesList(aKey){
/*
aKey[0] is static value for: ACTIVEOPPORTUNITY
 */
 	$("#loading").remove();
    var oppsListElement = $("#opportunitiesList");
    var opportunities = opensocial.data.getDataContext().getDataSet(OPPORTUNITIES);
    var activeOp = opensocial.data.getDataContext().getDataSet(aKey[0])

    oppsListElement.append($('<li class="nav-header">Opportunities</li>'));

    _.each(opportunities, function(anOpp, aList){
        var listItem = $("<li></li>");
        var anchor = $("<a></a>");

        listItem.attr("id", anOpp.name_value_list.id.value);
        listItem.click(setActiveOpportunity);
        anchor.html(anOpp.name_value_list.account_name.value);
        listItem.append(anchor);
        oppsListElement.append(listItem);
    } );

    opensocial.data.getDataContext().putDataSet(ACTIVEOPPORTUNITY,opportunities[0]);

};
//End Event Handler Callbacks


//View Support Functions

function renderActiveOpportunityListItem() {
    var activeOp = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
    $("li").removeClass("active");
    var activeElement = $("#" + activeOp.name_value_list.id.value);
    activeElement.addClass("active");
};

function setActiveOpportunity(event){
    var selectedOppId = event.target.parentElement.id;
    var oppList = opensocial.data.getDataContext().getDataSet(OPPORTUNITIES);

    var activeOp = _.find(oppList, function(anOpp){
        return anOpp.id == this;
    }, selectedOppId);
    opensocial.data.getDataContext().putDataSet(ACTIVEOPPORTUNITY, activeOp);
};

//End View Supprt functions




//Start App Initialization
function initData() {
    /*
    Illustrates the capability of pre-load. Note: OSAPI.makeRequest does not work with pre-load.
    */
    var params = {};
    params[gadgets.io.RequestParameters.CONTENT_TYPE] = gadgets.io.ContentType.TEXT;
    gadgets.io.makeRequest("http://mgmarum.com:8080/osJumpstart/rest/sugar/entries/raw", extractOpportunities, params);
};


function extractOpportunities(result){
    var ops = (gadgets.json.parse(result.data)).entry_list;
    opensocial.data.getDataContext().putDataSet(OPPORTUNITIES,ops);

};


function registerListners() {
    var osDataContext = opensocial.data.getDataContext();

    osDataContext.registerListener(
    	OPPORTUNITIES, renderOpportunitiesList);

    osDataContext.registerListener(
    	ACTIVEOPPORTUNITY, renderActiveOpportunityListItem);
};
//End App Initializations



//Application Entry Point
gadgets.util.registerOnLoadHandler(function() {
    var target = document.getElementById('loading');
	new Spinner().spin(target);
   registerListners();
   initData();
  // resize app window to fit content
  gadgets.window.adjustHeight();
});
