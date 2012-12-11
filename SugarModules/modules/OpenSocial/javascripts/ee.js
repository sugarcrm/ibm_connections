var OPPORTUNITIES = "opportunities";
var ACTIVEOPPORTUNITY = "activeOpportunity";

//Start: Event Handler Callbacks
/*
All the functions in this section are invoked because they are
listening on callbacks in the OpenSocial Data Contxt
 */


function renderEEForm(aKey){
     var activeOpp = opensocial.data.getDataContext().getDataSet(aKey[0]);

     //set values from active opportunity
			var values = activeOpp.name_value_list;
    		$("#oppName").html(values.name.value);
    		$("#accountName").html(values.account_name.value);
    		$("#amount").html("$"+$.formatNumber(values.amount_usdollar.value, {format:"#,###.00", locale:"us"}));
    		var probPercentage = values.probability.value;
    		// scale of green
    		var scaledColor = parseInt(200 * (probPercentage/100))+15;
    		$("#probability").css("background-color", "rgb(15,"+scaledColor+",15)");
    		$("#probability").html(probPercentage+"%");
    		var stage = values.sales_stage.value;
    		if(stage.indexOf("Close") == 0){  //starts with Close
    			$("#soon").hide();
    		} else {
    			$("#soon").show();
    		}
    		$("#stage").html(stage);
    		$("#close").html(values.date_closed.value);
    		$("#description").html(values.description.value);
    		$("#loading").remove();
    		$("#opportunityEE").fadeIn();
    		gadgets.window.adjustHeight();
    		gadgets.window.adjustWidth();
		};


function eeCallback(aKey) {
    //The EE context is placed into the data context by the common container code.
	var context = opensocial.data.getDataContext().getDataSet(aKey);
	if(context.target.context.opportunityId){
		osapi.http.post(
			{  'href': 'http://mgmarum.com:8080/osJumpstart/rest/sugar/entries/opportunities/'+context.target.context.opportunityId,
			 'format': 'json'}
		).execute(function(data){
             //extract the opportunity and put it in the ACTIVEOPPORTUNITY data context key
             opensocial.data.getDataContext().putDataSet(ACTIVEOPPORTUNITY,data.content.entry_list[0]);
        });

    } else if (context.target.context.chart) {
    	loadChart();
		osapi.http.post(
			{  'href': "http://mgmarum.com:8080/osJumpstart/rest/sugar/entries/opportunities/won",
			 'format': 'json'}
		).execute(function(data){
			if(data.content){
				_.each(_.first(data.content.entry_list,5),function(entry){
					var values = entry.name_value_list;
					var row = $("<tr><td>"+values.name.value.substring(0,40)+"</td><td>"+values.account_name.value.substring(0,40)+"</td><td>$"+$.formatNumber(values.amount_usdollar.value, {format:"#,###.00", locale:"us"})+"</td></tr>");
					$("#won_body").append(row);
				});
			}
			$("#loading").remove();
			$("#chartEE").fadeIn();
			gadgets.window.adjustHeight();
    		gadgets.window.adjustWidth();
        });
    } else {
    	gadgets.views.requestNavigateTo("home");
    }
    
};

function loadChart(){

	var data = google.visualization.arrayToDataTable([
	  ['Sales Stage', 'US Dollars'],
	  ['Prospecting',     214000],
	  ['Qualification',    125000],
	  ['Proposal/Price Quote',  83000],
	  ['Negotiation/Review', 64000],
	  ['Closed',    52000]
	]);

	var options = {
	  title: "Team's Opportunity Pipeline",
	  height: 500,
	  width: 700,
	  chartArea:{width:"85%",height:"85%"}
	};
	
	$("#containerDiv").removeClass("span8").addClass("span10");
	var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
	chart.draw(data, options);
      
}

//End Event Handler Callbacks



//End View Supprt functions


function registerListeners() {
    var osDataContext = opensocial.data.getDataContext();
	osDataContext.registerListener(
    	'org.opensocial.ee.context', eeCallback);

    osDataContext.registerListener(
    	ACTIVEOPPORTUNITY, renderEEForm);
};
//End App Initializations



//Application Entry Point
gadgets.util.registerOnLoadHandler(function() {
    var target = document.getElementById('loading');
	new Spinner().spin(target);
    registerListeners();

});
