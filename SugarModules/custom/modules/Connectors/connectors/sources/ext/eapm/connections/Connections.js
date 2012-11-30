/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

YAHOO.namespace("connections");

YAHOO.connections.data = {
    parent_type : "",
    parent_id : "",
    page_number : 1
};

// Deprecated. Use loadTabGroup instead
function loadTabs() {

    //load connections apps tabs
    YAHOO.connections.connectionsTabView = new YAHOO.widget.TabView();
    YAHOO.connections.connectionsTabView.addTab( new YAHOO.widget.Tab({
        label: getLabel('LBL_FILES_TAB'),
        dataSrc: createURL('method=loadFilesList&tab_name=Files'),
        cacheData: false,
        active: true
    }));

    YAHOO.connections.connectionsTabView.addTab( new YAHOO.widget.Tab({
        label: getLabel('LBL_MEMBERS_TAB'),
        dataSrc: createURL('method=loadMembersList&tab_name=Members'),
        cacheData: false,
        active: false
    }));

    YAHOO.connections.connectionsTabView.appendTo('subpanel_connections');

    //load connections communities tabs
    YAHOO.connections.communitiesTabView = new YAHOO.widget.TabView();
    YAHOO.connections.communitiesTabView.addTab( new YAHOO.widget.Tab({
         label: getLabel('LBL_MY_COMMUNITIES_TAB'),
         dataSrc: createURL('method=loadConnectionsCommunities&tab_name=MyCommunities'),
         cacheData: false,
         active: false
     }));

    YAHOO.connections.communitiesTabView.addTab( new YAHOO.widget.Tab({
         label: getLabel('LBL_PUBLIC_COMMUNITIES_TAB'),
         dataSrc: createURL('method=loadConnectionsCommunities&tab_name=PublicCommunities'),
         cacheData: false,
         active: false
     }));

    YAHOO.connections.communitiesTabView.appendTo('connections_communities');
}

// BEGIN: Replacement for loadTabs()
function loadTabGroup(tab_group) {
    if(typeof(tab_group) == 'undefined' || tab_group == 'DOMReady')
        tab_group = 'connectionsTabView';

    YAHOO.connections[tab_group] = new YAHOO.widget.TabView();

    for(tab_name in YAHOO.connections.tabs_meta) {
        if(YAHOO.connections.tabs_meta[tab_name].tabView == tab_group && YAHOO.connections.tabs_meta[tab_name].onload) {
            newTab(tab_name, tab_group);
        }
    }

    if(tab_group == 'connectionsTabView')
        YAHOO.connections[tab_group].appendTo('subpanel_connections');
    if(tab_group == 'communitiesTabView')
        YAHOO.connections[tab_group].appendTo('connections_communities');
}

function newTab(tab_name, tab_group) {
    var method = YAHOO.connections.tabs_meta[tab_name].method;
    var tabActive = (YAHOO.connections.tabs_meta[tab_name].active == true)?true:false;
    var url = createURL('method='+method+'&tab_name='+tab_name);
    var label = getLabel(YAHOO.connections.tabs_meta[tab_name].label);

    YAHOO.connections[tab_group].addTab( new YAHOO.widget.Tab({
        label: label,
        dataSrc: url,
        cacheData: false,
        active: tabActive
    }));
}
// END: Replacement for loadTabs()

function createNewConnectionsObject(object_name) {
    var tabView = getTabView(object_name);
    var label = getLabel(YAHOO.connections.tabs_meta[object_name].label);

    var method = YAHOO.connections.tabs_meta[object_name].method;
    var url = "method="+method+"&tab_name="+object_name;
    //console.log(url);

    tabView.addTab( new YAHOO.widget.Tab({
        label: label,
        dataSrc: createURL(url),
        cacheData: false,
        active: true
    }));

    var tabCount = tabView.get('tabs').length;
    YAHOO.connections.tabs_meta[object_name].order = tabCount-1;
    
}

function addMemberAutocomplete()
{
if( typeof(window.$) !== "undefined")
	$('#search_AddMember').autocomplete({
		source: function(request,response) {
				  $.ajax({
					url: createURL('method=sourceForAutoCompleteMember&search_text='+request.term),
					dataType: "json",
					minChars: 2,
					success: function(data) {
					  response($.map(data, function(item) {
						return {
						  label: item.member_name,
						  value: item.member_name,
						  member_id:  item.member_id,
						  community_id: item.community_id,
						}
					  }));
					},
				  });
				},
    select: function(event, ui) {
      
      addMember(ui.item.member_id, ui.item.community_id);
      document.getElementById('search_AddMember').value = ui.item.value;
      //getTabView('Members');
      searchListings('AddMember',1);
          var defaultTab = "Members";
          loadListings(defaultTab,1,'');
    var tabNum = YAHOO.connections.tabs_meta[defaultTab].order;
    var tabView = getTabView(defaultTab);
    tabView.selectTab(tabNum);
      //loadListings(defaultTab,1,'');
      
      
    }
		
		});
}
function showCommunities() {
    //console.log(YAHOO.connections.communitiesTabView);
    if(typeof(YAHOO.connections.communitiesTabView) == 'undefined')
        loadTabGroup("communitiesTabView");

    var defaultTab = "MyCommunities";
    var tabNum = YAHOO.connections.tabs_meta[defaultTab].order;
    var tabView = getTabView(defaultTab);
    tabView.selectTab(tabNum);
    YAHOO.connections.communitiesPanel.show();
}

function addMembers() {
    //var communitiesEl = document.getElementById('connections_communities');
    //if(communitiesEl.innerHTML == "")
    //    loadCommunitiesTabs();
    //YAHOO.connections.communitiesPanel.show();
}

function showMemberProfile(member_id) {
    if(typeof(YAHOO.connections.memberProfile) == 'undefined')
        YAHOO.connections.memberProfile = new YAHOO.widget.Panel("profile_dialog");
    //YAHOO.connections.memberProfile.cfg.applyConfig({ width:"500px", visible:false, draggable:false, close:true, context: ["memberProfile","tl","tl",["windowResize"],[200,50]] }, false);
    YAHOO.connections.memberProfile.cfg.applyConfig({ width:"500px", visible:false, draggable:false, close:true, context: [member_id,"tl","tr",["windowResize"],[20,-10]] }, false);
    //console.log(YAHOO.connections.memberProfile);
    YAHOO.connections.memberProfile.setHeader(getLabel('LBL_LOADING'));
    YAHOO.connections.memberProfile.setBody("");
    //YAHOO.connections.memberProfile.setFooter("");
    YAHOO.connections.memberProfile.render("profile");

    var url = createURL('method=loadMemberProfileCard&member_id='+member_id);

    YAHOO.util.Connect.asyncRequest('POST', url, showMemberHandler);
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_LOADING'));
    YAHOO.connections.memberProfile.show();
    //return false;
}

var showMemberHandler = {
    success: function(data) {
        var response = JSON.parse(data.responseText);
        YAHOO.connections.memberProfile.setHeader(response.header);
        YAHOO.connections.memberProfile.setBody(response.body);
        SUGAR.ajaxStatusClass.prototype.hideStatus();
        //setTimeout("SemTagSvc.parseDom(null, 'memberProfile')", 1000 );
    },
    failure: function(data) {

    }
}

function searchListings(tab_name, page_number) {
    //YAHOO.connections.communitiesPanel.tab_name = tab_name;
    var el_id = "search_"+tab_name;
    var search_string = document.getElementById(el_id).value.replace(" ", "+");
    loadListings(tab_name, page_number, search_string);
}

function loadListings(tab_name, page_number, search_string) {
    //tab = YAHOO.connections.communitiesPanel.tab_name;
    YAHOO.connections.data.page_number = page_number;

    var tabNum = YAHOO.connections.tabs_meta[tab_name].order;
    var method = YAHOO.connections.tabs_meta[tab_name].method;
    var tabView = getTabView(tab_name);

    var tab = tabView.getTab(tabNum);
    tab.set('dataSrc', createURL('method='+method+'&search_text='+search_string+'&tab_name='+tab_name),true);
    tabView.selectTab(tabNum);
}

function selectCommunity(community_id) {
    if (community_id == '') 
    {
    	var text = getLabel('LBL_CONFIRM_DELETE');
    	if (!confirm(text)) return;
    }
    SUGAR.ajaxStatusClass.prototype.showStatus('Saving');

    var url = createURL('method=saveCommunitySelection&community_id='+community_id);
    YAHOO.util.Connect.asyncRequest('POST', url, selectCommunityHandler);
    return false;
}

var selectCommunityHandler = {
    success: function(data) {
        //console.log(data.responseText);
        SUGAR.ajaxStatusClass.prototype.hideStatus();
        setTimeout(function() {
            YAHOO.connections.communitiesPanel.cancel();
        }, 500);
        loadListings('Files',1,'');
    },
    failure: function(data) {

    }
}

function closeTab(tab_name) {
    var tabView = getTabView(tab_name);
    var tabNum = YAHOO.connections.tabs_meta[tab_name].order;
    var returnTab = YAHOO.connections.tabs_meta[tab_name].returnTab;

    if(typeof(returnTab) != 'undefined')
        loadListings(returnTab,1,'');

    tabView.removeTab(tabView.getTab(tabNum));
}

function getTabView(tab_name) {
    var tabViewName = YAHOO.connections.tabs_meta[tab_name].tabView;
    var tabView = YAHOO.connections[tabViewName];
    return tabView;
}

function saveNewCommunity() {
    var createCommunityForm = document.getElementById('CreateCommunity');

    publicCommunity = createCommunityForm.elements['public_community'].checked;

    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateCommunity');
    YAHOO.util.Connect.asyncRequest('POST', url, saveNewCommunityHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var saveNewCommunityHandler = {
    success: function(data) {
        closeTab('CreateCommunity');
        window.setTimeout('ajaxStatus.hideStatus()', 1000);

        if(publicCommunity)
            loadListings('PublicCommunities',1,'');
        else
            loadListings('MyCommunities',1,'');
    },
    failure: function(data) {

    }
}

function saveNewFile() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateFile', true);
    YAHOO.util.Connect.asyncRequest('POST', url, saveNewFileHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var saveNewFileHandler = {
    upload: function(data) {
        closeTab('CreateFile');
        window.setTimeout('ajaxStatus.hideStatus()', 2000);
        loadListings('Files',1,'');
    },
    success: function(data) {
		//console.log('success');
		//console.log(data);

    },
    failure: function(data) {
		//console.log('fail');
		//console.log(data);
    }
}

function addMember(member_id, community_id) {
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_ADD_MEMBER'));

    var url = createURL('method=addMember&community_id='+community_id+'&member_id='+member_id);
    YAHOO.util.Connect.asyncRequest('POST', url, selectMemberHandler);
    return false;
}

var selectMemberHandler = {
    success: function(data) {
        //console.log(data.responseText);
        // window.setTimeout('ajaxStatus.hideStatus()', 1000);
        SUGAR.ajaxStatusClass.prototype.hideStatus();
        //loadListings('Files',0,'');
    },
    failure: function(data) {

    }
}

/**
 *
 * @param additional_parts - additional URL parts string.
 */

function createURL(additional_parts) {
    var url = "index.php?module=Connectors&action=Connections";

    for(var name in YAHOO.connections.data) {
        url += "&"+name+"="+YAHOO.connections.data[name];
    }

    if(typeof(additional_parts) != "undefined" && additional_parts !== null) {
        url += "&"+additional_parts;
    }

    url += "&to_pdf=true";

    return url;
}

function getLabel(label_name) {
    return YAHOO.connections.language[label_name];
}

function showLoadingModal() {
    YAHOO.connections.wait =  new YAHOO.widget.Panel("wait",
        { width:"240px",
          fixedcenter:true,
          close:false,
          draggable:false,
          zindex:4,
          modal:true,
          visible:false
        }
    );

    YAHOO.connections.wait.setHeader("Loading, please wait...");
    YAHOO.connections.wait.setBody('<img src="http://l.yimg.com/a/i/us/per/gr/gp/rel_interstitial_loading.gif" />');
    YAHOO.connections.wait.render(document.body);
    YAHOO.connections.wait.show();
}

YAHOO.connections.communitiesPanel = new YAHOO.widget.Dialog("communities_dialog",
        { width : "65em",
          fixedcenter : true,
          visible : false,
          constraintoviewport : true,
          modal:true
        });

YAHOO.connections.communitiesPanel.render();
