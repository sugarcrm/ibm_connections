var OPPORTUNITIES = "opportunities";
var ACTIVEOPPORTUNITY = "activeOpportunity";
var CURRENT_USER = "currentUser";

function loadEEContext(contextKey) {
	/*
	Our context looks something like this:
	{
		"id": "350397c6-f5e2-41f9-a14c-50f06018a81d",
        "name": "5 Anvils",
        "amount": 5281,
        "date_closed": "2013-01-24",
        "probability": 40,
        "next_step": "Be Awesome",
        "account": {
          "id": "96874ca1-3af9-614f-758b-50e5bf2f6126",
          "name": "Acme Co."
        }
	 }
	*/
    //The EE context is placed into the data context by the common container code.
	var context = opensocial.data.getDataContext().getDataSet(contextKey);
	osapi.http.get({
		'href': context.site + "/load_opportunities.php?user_id=" + context.owner,
		'format': 'json'
	}).execute(function(data){
		// Let's put focus on the opportunity id we received via the activity stream post
		var opportunities = data.content.opportunities;
		var opportunity   = _.find(opportunities, function(o) { return o.id == context.opportunity.id });
    	//extract the opportunity and put it in the ACTIVEOPPORTUNITY data context key
		opensocial.data.getDataContext().putDataSet(OPPORTUNITIES, opportunities);
       	opensocial.data.getDataContext().putDataSet(ACTIVEOPPORTUNITY, opportunity);
    });
};

function renderEEForm() {
	var activeOpportunity = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
	$('#opportunityName').html(activeOpportunity.name);
	$('#opportunityName').click(function() {
	    window.open(activeOpportunity.url);
        return false;
	});
	
	$('#accountName').html(activeOpportunity.account.name);
	$('#amount').html("$"+$.formatNumber(activeOpportunity.amount, {format:"#,###", locale:"us"}));

	$.timeago.settings.allowFuture = true;		
	$('#date_closed').html($.timeago(activeOpportunity.date_closed));

	var scaledColor = parseInt(200 * (activeOpportunity.probability/100))+15;
	$("#probability").css("background-color", "rgb(15,"+scaledColor+",15)");
	$('#probability').html(activeOpportunity.probability + "%");
	
	$('#stage').html(activeOpportunity.sales_stage);
	if (ownedByCurrentUser()) {
    	$('#stage').click(function() {
    	    $(this).html(renderSalesStageSelect(activeOpportunity.sales_stage));
        	// When we actually change the value
        	$('#salesStage').change(function() {
        	    updateSalesStage();
    	    });
    	});
	}
	$('#nextStep').html(activeOpportunity.next_step);
	$('#description').html(activeOpportunity.description);
	$('#owner').html(renderOwner());
	
	renderOpportunityList();
	gadgets.window.adjustHeight();
	//gadgets.window.adjustWidth();
}

function renderOwner() {
    var opp  = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
    var user = opensocial.data.getDataContext().getDataSet(CURRENT_USER);
    var html = '';
    var link = '/profiles/html/profileView.do?userid=' + user.id.split(":")[4];
    if (ownedByCurrentUser()) {
        html = '<a href="' + link + '" target="_blank">' + 'Me' + '</a>';
    } else {
        html = '<a href="' + link + '" target="_blank">' + opp.owner.name + '</a>';
    }
    return html;
}

function updateSalesStage() {
    var activeOpportunity = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
    var opportunities = opensocial.data.getDataContext().getDataSet(OPPORTUNITIES);
    var context = opensocial.data.getDataContext().getDataSet('org.opensocial.ee.context');
    
    stage = $('#salesStage').val();
    osapi.http.post({
		'href': context.site + "/update_opportunity.php",
		'body': {
		    "opportunity": { "id": activeOpportunity.id, "sales_stage": stage },
		    "owner": context.owner
		},
		'format': 'json'
	}).execute(function(data) {
	    var opportunity = data.content.opportunity;
	    var newOpps = new Array();
	    _.each(opportunities, function(o) { 
	        if (o.id == opportunity.id) {
	            newOpps.push(opportunity);
	        } else {
	            newOpps.push(o);
	        }
	    });
	    opensocial.data.getDataContext().putDataSet(OPPORTUNITIES, newOpps);
	    opensocial.data.getDataContext().putDataSet(ACTIVEOPPORTUNITY, opportunity);
	});
}

function ownedByCurrentUser() {
    var opp  = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
    var user = opensocial.data.getDataContext().getDataSet(CURRENT_USER);
    return (opp.owner.email == user.emails[0].value);
}

function renderSalesStageSelect(salesStage) {
    var stages = ["Prospecting", 
        "Qualification", "Needs Analysis", 
        "Value Proposition", "Id. Decision Makers", 
        "Perception Analysis", "Proposal/Price Quote",
        "Negotiation/Review", "Closed Won", "Closed Lost"];
    var select = $('<select name="salesStage" id="salesStage" class="btn-mini span2" title></select>');
    _.each(stages, function(stage) {
        select.append('<option value="' + stage + '">' + stage + '</option>');
    });
    select.val(salesStage);
    return select;
}

function renderOpportunityList() {
	var oppsListElement = $("#opportunitiesList");
    var opportunities = opensocial.data.getDataContext().getDataSet(OPPORTUNITIES);
    var activeOp = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
	oppsListElement.empty();
    _.each(opportunities, function(anOpp, aList){
        var listItem = $("<li></li>");
        var anchor = $("<a></a>");
        listItem.attr("id", anOpp.id);
        listItem.click(setActiveOpportunity);
        anchor.html(anOpp.name + " ");
        listItem.append(anchor);
        oppsListElement.append(listItem);
    } );
	renderActiveOpportunityListItem();
}

function setActiveOpportunity(event){
    var selectedOppId = event.target.parentElement.id;
    var oppList = opensocial.data.getDataContext().getDataSet(OPPORTUNITIES);

    var activeOp = _.find(oppList, function(anOpp){
        return anOpp.id == this;
    }, selectedOppId);
    opensocial.data.getDataContext().putDataSet(ACTIVEOPPORTUNITY, activeOp);
};

function setCurrentUser() {
	osapi.people.get().execute(function(data) {
	    console.log(data);
	    opensocial.data.getDataContext().putDataSet(CURRENT_USER, data);
	});
}

function renderActiveOpportunityListItem() {
    var activeOp = opensocial.data.getDataContext().getDataSet(ACTIVEOPPORTUNITY);
    $("li").removeClass("active");
    var activeElement = $("#" + activeOp.id);
    activeElement.addClass("active");
};

function registerListeners() {
    var osDataContext = opensocial.data.getDataContext();
	osDataContext.registerListener('org.opensocial.ee.context', loadEEContext);
	osDataContext.registerListener(ACTIVEOPPORTUNITY, renderEEForm);
};

gadgets.util.registerOnLoadHandler(function() {
    registerListeners();
    setCurrentUser();
});
