/**
 * Created by JetBrains PhpStorm.
 * User: bkilgore
 * Date: 3/9/12
 * Time: 10:12 AM
 * To change this template use File | Settings | File Templates.
 */

YAHOO.namespace("connections");

YAHOO.connections.data = {
    parent_type : "",
    parent_id : "",
    page_number : 1
};

function getFileImage(fileType)
{
	
	var type_image = new Array();
	type_image['web'] = " axs css htc htm html java js jsp php sct stm wml xml xsl ";
	type_image['pdf'] = " ai eps ich ich6 iwp pdf ps ";
	type_image['txt'] = " 323 asc bas c etx h jw log mcw msg ow pfs qa rtf sow text tsv tw txt uls vw3 ws xy ";
	type_image['img'] = " jpg jpeg png bmp cgm cmx cod drw dxf eshr gif hgs ico ief img jfif pbm pcx pgl pgm pnm pntg ppm psd psp6 ras rgb sdw svg svgz tga wbmp wpg xbm xpm xwd ";
	type_image['doc'] = " doc docm docx dot dotm dotx en4 enw leg lwp lwp7 manu msw mwp mwp2 mwpf odc odg odt ort otg oth odm ott pages pcl rtx std stw sxd sxq sxw w6 w97 wm wp5 wp6 wpf ";
	type_image['pres'] = " flw key mass odp ope otc otp pot potm potx pp2 pp97 ppam pps ppsm ppsx ppt pptm pptx prz shw3 sti sxi ";
	type_image['spread'] = " 123 12m acs csv dbs dez ens fcd fcs mwkd mwks odf ods oss otf ots qad smd sms stc sxc sxm wg2 wk4 wk6 xla xlam xlc xlm xls xlsb xlsm xlsx xlt xltm xltx xlw ";
	type_image['archive'] = " cab dmg gtar gz jar rar sit sqx tar taz tgz z zip ";
	type_image['sound'] = " aac aif aifc aiff au kar m3u m4a mid midi mp3 mpga ra ram rm rmi rpm snd wav wma ";
	
	
	var icon_name = "undefined";
	if (fileType != '') {
		for (key in type_image){
			if ( type_image[key].indexOf(' ' + fileType + ' ') != -1){
				icon_name = key;
				break;
			}
		}
	}
	document.getElementById('file_dropbox').innerHTML = "<img style='width:40px;height:50px;'src='custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/file_types/" + icon_name + ".PNG' />";
	
}
function fixMenu(){
$('.ibm-actions-nav').mouseover( 
	function(){
		$(this).find('ul li:first ul').css('display','block');
		$(this).find('ul li:first ul').css('z-index','99999');
	});
$('.ibm-actions-nav').mouseout( 
	function(){
		$(this).find('ul li:first ul').css('display','none');
	});
}


function connectionsLogin()
{
	var url = 'index.php';
    YAHOO.util.Connect.setForm('loginConnections');
    YAHOO.util.Connect.asyncRequest('POST', url, connectionsLoginHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}
var connectionsLoginHandler = {
    success: function(data) {
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        var response = JSON.parse(data.responseText);
        if (response.success == false) {
        	document.getElementById("eapm_notice_div").innerHTML = getLabel('LBL_STATUS') + ' <span class="error">' + getLabel('LBL_CONNECT_PROBLEM_' + response.problem.toUpperCase()) + '</span>';
        	if (response.problem == 'url'){
        		document.getElementById("connection_password").disabled = true;
        		document.getElementById("connection_user_name").disabled = true;
        	}
        }
        else {
        	document.getElementById("eapm_notice_div").innerHTML = getLabel('LBL_STATUS') + ' ' + getLabel('LBL_CONNECTED');
        }
    },
    failure: function(data) {

    }
}

function checkAccess(allow_mod) {
	if (allow_mod == 'yes') {
		document.getElementById('ibm_name'). disabled = false;
      	document.getElementById('ibm_asset'). disabled = false;
      	var tabs = document.getElementsByClassName('ibm_tabs');
      	for (var i = 0; i < tabs.length; i++ ) {
      		tabs[i].disabled = false;
      	}
		
    }
    else  {
    document.getElementById('ibm_name'). disabled = true;
      	document.getElementById('ibm_asset'). disabled = true;
      	var tabs = document.getElementsByClassName('ibm_tabs');
      	for (var i = 0; i < tabs.length; i++ ) {
      		tabs[i].disabled = true;
      	}
		
    }
	
} 

function loadTabGroup(tab_group) {
    if(typeof(tab_group) == 'undefined' || tab_group == 'DOMReady')
        tab_group = 'connectionsTabView';

    YAHOO.connections[tab_group] = new YAHOO.widget.TabView();

    for(tab_name in YAHOO.connections.tabs_meta) {
        if(YAHOO.connections.tabs_meta[tab_name].tabView == tab_group && YAHOO.connections.tabs_meta[tab_name].onload) {
            newTab(tab_name, tab_group);
        }
    }

   // if(tab_group == 'connectionsTabView')
    //    YAHOO.connections[tab_group].appendTo('subpanel_connections');//ibm_tabs');
    if(tab_group == 'communitiesTabView')
    {
        YAHOO.connections[tab_group].appendTo('connections_communities');
    }
}

function newTab(tab_name, tab_group) {
    var method = YAHOO.connections.tabs_meta[tab_name].method;
    var tabActive = (YAHOO.connections.tabs_meta[tab_name].active == true)?true:false;
    var url = createURL('method='+method+'&tab_name='+tab_name);
    var label = getLabel(YAHOO.connections.tabs_meta[tab_name].label);
    var tab = new YAHOO.widget.Tab({
							label: label,
							dataSrc: url,
							cacheData: false,
							active: tabActive});
    if (tab_group == "communitiesTabView" )
    {
        tab.loadHandler =  {
            success: function(o) {
               this.set("content", o.responseText);
                if(typeof(YAHOO.connections.communitiesPanel) != 'undefined') 
                {
               		 YAHOO.connections.communitiesPanel.center();
                }
            },
            failure: function(o) {
            }
        };
     }
    YAHOO.connections[tab_group].addTab(tab);
}


function createNewConnectionsObject(object_name) {
    var tabView = getTabView(object_name);
    var label = getLabel(YAHOO.connections.tabs_meta[object_name].label);

    var method = YAHOO.connections.tabs_meta[object_name].method;
    var url = "method="+method+"&tab_name="+object_name;

    tabView.addTab( new YAHOO.widget.Tab({
        label: label,
        dataSrc: createURL(url),
        cacheData: false,
        active: true
    }));

}

function addMemberAutocomplete()
{
if( typeof(window.$) !== "undefined")
$('#member_name').autocomplete({
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
					error: function(data) {
						}
				  });
				},
    select: function(event, ui) {
    
      var role = $('#ibm_member_role option:selected').val();
      if ($('#'+role+'_list option:selected[value="'+ui.item.member_id+'"]').length > 0) return false;
      $('#member_list_div').append('<span style=" border: 1px solid #999999;background-color: #fafafa;padding: 1px 0 1px 5px;display:inline;" id="'+ui.item.member_id+'_'+role+'"> <span  style="border-right-style: 1px solid #999999;" onclick="removeMemberFromList(\''+ui.item.member_id+'\', \''+role+'\')";> X </span> '+ui.item.value+' ('+role+')</span>&nbsp;');
      if (role == 'member'){
      	$('#member_list').append('<option value="'+ui.item.member_id+'" selected></option>');
      }
      else{
      	$('#owner_list').append('<option value="'+ui.item.member_id+'" selected></option>');
      }
      $('#member_name').val('');
    }
		
		});
	$('#member_name').autocomplete("option", "appendTo", "#CreateCommunity");
}

function addMemberMembersTabAutocomplete()
{
if( typeof(window.$) !== "undefined")
$('#mt_member_name').autocomplete({
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
					error: function(data) {
						}
				  });
				},
    select: function(event, ui) {
    
      var role = $('#mt_member_role option:selected').val();
      $('#mt_member_id').val(ui.item.member_id);
      console.log(role + '  ' +$('#mt_member_id').val());
      //addMember(ui.item.member_id, ui.item.community_id);
      //document.getElementById('member_name').value = ui.item.value;
      //getTabView('Members');
      //searchListings('AddMember',1);
    //      var defaultTab = "Members";
   //       loadListings(defaultTab,1,'');
  //  var tabNum = YAHOO.connections.tabs_meta[defaultTab].order;
   // var tabView = getTabView(defaultTab);
 //   tabView.selectTab(tabNum);
      //loadListings(defaultTab,1,'');
      
      
    }
		
		});
		
		$('#mt_member_name').autocomplete("option", "appendTo", "#AddMemberForm");
}


function removeMemberFromList(member_id, role)
{
  $('#'+role+'_list option:selected[value="'+member_id+'"]').remove();//'<option value="'+member_id+'" selected></option>');
  $('#'+member_id+'_'+role).remove();
}

function removeMember(member_id)
{
  var text = getLabel('LBL_CONFIRM_REMOVE_MEMBER');
  if (!confirm(text)) return false;
  var url = createURL('method=removeMember&member_id='+member_id);
  $('#ibm_member_'+member_id).hide();
  YAHOO.util.Connect.asyncRequest('POST', url);
  return false;
}


function showCommunities() {
    if(typeof(YAHOO.connections.communitiesPanel) == 'undefined') {
      YAHOO.connections.communitiesPanel = new YAHOO.widget.Dialog("communities_dialog",
              { width : "40em",
                fixedcenter : true,
                visible : false,
                constraintoviewport : true,
                modal:true
              });
      YAHOO.connections.communitiesPanel.render();
    }
    if(typeof(YAHOO.connections.communitiesTabView) == 'undefined')
        loadTabGroup("communitiesTabView");

    var defaultTab = "CreateCommunity";
    var tabNum = YAHOO.connections.tabs_meta[defaultTab].order;
    var tabView = getTabView(defaultTab);
    tabView.selectTab(tabNum);
    YAHOO.connections.communitiesPanel.show();
   // YAHOO.connections.communitiesPanel.center();
}

function closeCommunityPanel()
{
	if(typeof(YAHOO.connections.communitiesPanel) != 'undefined') {
		YAHOO.connections.communitiesPanel.hide();
		return false;
	}
	
}

function closeCreationWindow()
{
	if(typeof(YAHOO.connections.createElementPanel) != 'undefined') {
		//YAHOO.connections.createElementPanel.hide();
		YAHOO.connections.createElementPanel.cancel();
		YAHOO.connections.createElementPanel.hide();
		return false;
	}
}
function createIBMElement(element, additional_params) {
	 if(typeof(YAHOO.connections.createElementPanel) == 'undefined')
	 {
	 	YAHOO.connections.createElementPanel = new YAHOO.widget.Dialog("creation_dialog", 
		{ 
			width:"500px", 
			fixedcenter : true,  
			visible : false,  
			constraintoviewport : true , 
			modal:true
		});
	}
    YAHOO.connections.createElementPanel.setHeader(getLabel('LBL_LOADING'));
    YAHOO.connections.createElementPanel.setBody("");
    YAHOO.connections.createElementPanel.render(document.body);
    var params_string = "";
    if(typeof(additional_params) != "undefined" && additional_params !== null) {
        params_string = "&"+additional_params;
    }
   var url = createURL('method=loadCreationBody&element=' + element + params_string);
    YAHOO.util.Connect.asyncRequest('POST', url, createElementHandler);
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_LOADING'));
    YAHOO.connections.createElementPanel.show();
}

var createElementHandler = {
    success: function(data) {
        var response = JSON.parse(data.responseText);
        YAHOO.connections.createElementPanel.setHeader(response.header);
        YAHOO.connections.createElementPanel.setBody(response.body);
        YAHOO.connections.createElementPanel.center();
        initCal();
        SUGAR.ajaxStatusClass.prototype.hideStatus();
    },
    failure: function(data) {

    }
}

function editIBMElement(element, id, additional_params) {
	 if(typeof(YAHOO.connections.createElementPanel) == 'undefined')
	 {
	 	YAHOO.connections.createElementPanel = new YAHOO.widget.Dialog("creation_dialog", 
		{ 
			width:"500px", 
			fixedcenter : true,  
			visible : false,  
			constraintoviewport : true , 
			modal:true
		});
	}
    YAHOO.connections.createElementPanel.setHeader(getLabel('LBL_LOADING'));
    YAHOO.connections.createElementPanel.setBody("");
    YAHOO.connections.createElementPanel.render(document.body);
    var params_string = "";
    if(typeof(additional_params) != "undefined" && additional_params !== null) {
        params_string = "&"+additional_params;
    }
   var url = createURL('method=loadEditionBody&element=' + element +'&ibm_id=' + id + params_string);
    YAHOO.util.Connect.asyncRequest('POST', url, editElementHandler);
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_LOADING'));
    YAHOO.connections.createElementPanel.show();
}

var editElementHandler = {
    success: function(data) {
        var response = JSON.parse(data.responseText);
        YAHOO.connections.createElementPanel.setHeader(response.header);
        YAHOO.connections.createElementPanel.setBody(response.body);
        YAHOO.connections.createElementPanel.center();
        SUGAR.ajaxStatusClass.prototype.hideStatus();
    },
    failure: function(data) {

    }
}



function showMemberProfile(member_id) {
    if(typeof(YAHOO.connections.memberProfile) == 'undefined')
        YAHOO.connections.memberProfile = new YAHOO.widget.Panel("profile_dialog");
    //YAHOO.connections.memberProfile.cfg.applyConfig({ width:"500px", visible:false, draggable:false, close:true, context: ["memberProfile","tl","tl",["windowResize"],[200,50]] }, false);
    YAHOO.connections.memberProfile.cfg.applyConfig({ width:"500px", visible:false, draggable:false, close:true, context: [member_id,"tl","tr",["windowResize"],[20,-10]] }, false);
    YAHOO.connections.memberProfile.setHeader(getLabel('LBL_LOADING'));
    YAHOO.connections.memberProfile.setBody("");
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
	//setTimeout("SemTagSvc.parseDom(null, 'subpanel_connections')", 2000 );
}

function viewDiscussion(forumId)
{
	if(typeof(YAHOO.connections.modalViewPanel) == 'undefined')
	 {
	 	YAHOO.connections.modalViewPanel = new YAHOO.widget.Dialog("view_modal", 
		{ 
			width:"500px", 
			fixedcenter : true,  
			visible : false,  
			constraintoviewport : true , 
			modal:true
		});
	}
    YAHOO.connections.modalViewPanel.setHeader(getLabel('LBL_LOADING'));
    YAHOO.connections.modalViewPanel.setBody("");
    YAHOO.connections.modalViewPanel.render(document.body);
   var url = createURL('method=getDiscussionModal&forum_id=' + forumId);
    YAHOO.util.Connect.asyncRequest('POST', url, viewDiscussionHandler);
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_LOADING'));
    YAHOO.connections.modalViewPanel.show();
	return false;
}

var viewDiscussionHandler = {
    success: function(data) {
       var response = JSON.parse(data.responseText);
       YAHOO.connections.modalViewPanel.setHeader(response.header);
       YAHOO.connections.modalViewPanel.setBody(response.body);
       YAHOO.connections.modalViewPanel.center();
       SUGAR.ajaxStatusClass.prototype.hideStatus();
    },
    failure: function(data) {

    }
}

function modalActivityNode(activity_id, node_id)
{
	if(typeof(YAHOO.connections.modalViewPanel) == 'undefined')
	 {
	 	YAHOO.connections.modalViewPanel = new YAHOO.widget.Dialog("view_modal", 
		{ 
			width:"500px", 
			fixedcenter : true,  
			visible : false,  
			constraintoviewport : true , 
			modal:true
		});
	}
    YAHOO.connections.modalViewPanel.setHeader(getLabel('LBL_LOADING'));
    YAHOO.connections.modalViewPanel.setBody("");
    YAHOO.connections.modalViewPanel.render(document.body);
   var url = createURL('method=getActivityNode&ibm_activity_id=' + activity_id+'&ibm_node_id='+node_id);
    YAHOO.util.Connect.asyncRequest('POST', url, modalActivityNodeHandler);
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_LOADING'));
    YAHOO.connections.modalViewPanel.show();
	return false;
	
}

var modalActivityNodeHandler = {
    success: function(data) {
        var response = JSON.parse(data.responseText);
        YAHOO.connections.modalViewPanel.setHeader(response.header);
        YAHOO.connections.modalViewPanel.setBody(response.body);
        SUGAR.ajaxStatusClass.prototype.hideStatus();
    },
    failure: function(data) {

    }
}

function selectCommunity() {
	if (document.getElementById('community_id_selection') == null || document.getElementById('community_id_selection').value == '')  {
		community_id = '';
    	var text = getLabel('LBL_CONFIRM_DELETE');
    	if (!confirm(text)) return false;
    }
    else community_id = document.getElementById('community_id_selection').value;
    SUGAR.ajaxStatusClass.prototype.showStatus('Saving');
    var url = createURL('method=saveCommunitySelection&community_id='+community_id);
    YAHOO.util.Connect.asyncRequest('POST', url, selectCommunityHandler);
    return false;
}

var selectCommunityHandler = {
    success: function(data) {
        SUGAR.ajaxStatusClass.prototype.hideStatus();
        setTimeout(function() {
            YAHOO.connections.communitiesPanel.cancel();
        }, 500);
         var response = JSON.parse(data.responseText);
        var elems = $('.ibm_tabs');
		for(var i = 0; i < elems.length; i++) {
			$(elems[i]).removeClass('activeTab');
		}
        document.getElementById('ibm_connection_body').innerHTML = '';
        document.getElementById('ibm_community_navigation').innerHTML = response.content;
        if (response.community_id != '' && response.community_id != null) {
        	checkAccess('yes');
        }
        else {
        	checkAccess('no');
        }
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
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateCommunity');
    YAHOO.util.Connect.asyncRequest('POST', url, saveNewCommunityHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var saveNewCommunityHandler = {
    success: function(data) {
       // closeTab('CreateCommunity');
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
		//YAHOO.connections.createElementPanel.hide();
	//	closeCommunityPanel();
		closeCreationWindow();
		//loadListings('MyCommunities',1,'');
       /* if(publicCommunity)
            loadListings('PublicCommunities',1,'');
        else
            loadListings('MyCommunities',1,'');*/
    },
    failure: function(data) {
    }
}

function createBookmark() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateBookmarkForm');
    YAHOO.util.Connect.asyncRequest('POST', url, createBookmarkHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var createBookmarkHandler = {
    success: function(data) {
        YAHOO.connections.createElementPanel.hide();
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        //loadListings('Bookmarks',1,'');
        loadTabData('Bookmarks',1,'');
    },
    failure: function(data) {

    }
}

function createDiscussion() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateDiscussionForm');
    YAHOO.util.Connect.asyncRequest('POST', url, createDiscussionHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var createDiscussionHandler = {
    success: function(data) {
        YAHOO.connections.createElementPanel.hide();
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        //loadListings('Discussions',1,'');
        loadTabData('Discussions',1,'');
    },
    failure: function(data) {

    }
}

function commentFile() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CommentFileForm');
    YAHOO.util.Connect.asyncRequest('POST', url, commentFileHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var commentFileHandler = {
    success: function(data) {
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
         YAHOO.connections.createElementPanel.hide();
         var response = JSON.parse(data.responseText);
        var el = document.getElementById('comment_'+response.id);
        if (response.result == true)
        {	
        	
        	el.innerHTML = parseInt(el.innerHTML) + 1;
        }
    },
    failure: function(data) {

    }
}

function replyDiscussion() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('ReplyDiscussionForm');
    YAHOO.util.Connect.asyncRequest('POST', url, replyDiscussionHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var replyDiscussionHandler = {
    success: function(data) {
        YAHOO.connections.createElementPanel.hide();
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        var response = JSON.parse(data.responseText);
        onloadDiscusionReplies(response.topic_id);
		refreshReplyCount(response.topic_id);
    },
    failure: function(data) {

    }
}

function refreshReplyCount(id)
{
	var val = document.getElementById('reply_count_'+id).innerHTML;
	document.getElementById('reply_count_'+id).innerHTML = parseInt(val) +1;
}
function createActivity() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateActivityForm');
    YAHOO.util.Connect.asyncRequest('POST', url, createActivityHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var createActivityHandler = {
    success: function(data) {
        YAHOO.connections.createElementPanel.hide();
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        //loadListings('Activities',1,'');
        loadTabData('Activities',1,'');
    },
    failure: function(data) {

    }
}

function communityValidation(){
	addToValidate('CreateCommunity', 'community_name', '', true, "");
	addToValidate('CreateCommunity', 'access', '', true, 'no access');
	return check_form('CreateCommunity');
}

function activityValidation(){
	addToValidate('CreateActivityForm', 'activity_name', '', true, "");
	//addToValidateIsValidDate('CreateActivityForm', 'due_date', 'date', '', '');
	//addToValidate('CreateActivityForm', 'access', '', true, 'no access');
	return check_form('CreateActivityForm');
}

function activitySectionValidation(){
	addToValidate('CreateActivitySection', 'name', '', true, "");
	//addToValidate('CreateActivityForm', 'access', '', true, 'no access');
	return check_form('CreateActivitySection');
}

function activityEntryValidation(){
	addToValidate('CreateActivityEntry', 'title', '', true, "");
	//addToValidate('CreateActivityForm', 'access', '', true, 'no access');
	return check_form('CreateActivityEntry');
}

function activityToDoValidation(){
	addToValidate('CreateActivityToDo', 'title', '', true, "");
	// addToValidateIsValidDate('CreateActivityToDo', 'due_date', 'date', '', '');
	//addToValidate('CreateActivityForm', 'access', '', true, 'no access');
	return check_form('CreateActivityToDo');
}

function replyDiscussionValidation(){
	addToValidate('ReplyDiscussionForm', 'reply_content', '', true, "");
	return check_form('ReplyDiscussionForm');
}

function discussionValidation(){
	addToValidate('CreateDiscussionForm', 'discussion_name', '', true, "");
	return check_form('CreateDiscussionForm');
}
function bookmarkValidation(){
	addToValidate('CreateBookmarkForm', 'bookmark_path', '', true, "");
	addToValidate('CreateBookmarkForm', 'bookmark_name', '', true, "");
	return check_form('CreateBookmarkForm');
}

function commentFileValidation(){
	addToValidate('CommentFileForm', 'comment_content', '', true, "");
	return check_form('CommentFileForm');
}




function createActivitySection() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateActivitySection');
    YAHOO.util.Connect.asyncRequest('POST', url, createActivityNodeHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

function createActivityToDo() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateActivityToDo');
    YAHOO.util.Connect.asyncRequest('POST', url, createActivityNodeHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

function createActivityEntry() {
    var url = 'index.php';
    YAHOO.util.Connect.setForm('CreateActivityEntry');
    YAHOO.util.Connect.asyncRequest('POST', url, createActivityNodeHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var createActivityNodeHandler = {
    success: function(data) {
        
        YAHOO.connections.createElementPanel.hide();
        var response = JSON.parse(data.responseText);
        onloadActivityView(response.id);
        recalculateCompletion(response.id);
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        
       
        //loadListings('Activities',1,'');
    },
    failure: function(data) {

    }
}

function recalculateCompletion(id)
{
	var url = createURL('method=getActivityCompletion&activityId='+id);
	ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
	YAHOO.util.Connect.asyncRequest('POST', url, recalculateCompletionHandler);
   	
}
var recalculateCompletionHandler = {
	success: function(data) {
        var response = JSON.parse(data.responseText);
        document.getElementById('activity_completion_'+response.id).innerHTML = response.completion;
        window.setTimeout('ajaxStatus.hideStatus()', 100);
    },
    failure: function(data) {

    }

}
function selectQuickCreateElement(val)
{
	var file_el = document.getElementById('file_el');
	if (val != 'file') {
		 file_el.style.display = 'none'; 
	}
	else {
		 file_el.style.display = ''; 
	}
}


function quickCreateIBM(event)
{
	if (event.keyCode == 13) {
		var select_el = document.getElementById('ibm_asset');
		var asset_type = select_el.options[select_el.selectedIndex].value;
		var ibm_name = document.getElementById('ibm_name').value;
		if (ibm_name == '' ) return false;
		var post_data="";
		switch (asset_type)
		{
			case 'update': var url = createURL('method=postNote&content='+ibm_name);break;
			case 'activity': var url = createURL('method=saveActivity&activity_name='+ibm_name);break;
			case 'question': var url = createURL('method=saveDiscussion&discussion_name='+ibm_name+'&discussion_is_question=1');break;
			case 'file': YAHOO.util.Connect.setForm('QuickpostFile', true); var url = createURL(); post_data = 'method=uploadNewFile&file_name='+ibm_name+'&visibility=private&quickpost=1';break;
			case 'bookmark': var url = createURL('method=saveBookmark&bookmark_path='+ibm_name+'&bookmark_name='+ibm_name.substr(0,30));break;
			default : return false;
			
		}	
		
		YAHOO.util.Connect.asyncRequest('POST', url, quickPostHandler, post_data);
    	ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
		
		
    }
     return false;
	
}

var quickPostHandler = {
	upload: function(data) {
      document.getElementById('ibm_name').value = '';
      document.getElementById('file_el').value = '';
        window.setTimeout('ajaxStatus.hideStatus()', 100);
    },
    success: function(data) {
        document.getElementById('ibm_name').value = '';
        window.setTimeout('ajaxStatus.hideStatus()', 100);
    },
    failure: function(data) {
    }
}


function likeFile(user_id, doc_id) {
	var url = createURL('method=likeFile&user_id='+user_id+'&document_id='+doc_id);
    YAHOO.util.Connect.asyncRequest('POST', url, likeFileHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var likeFileHandler = {
    success: function(data) {
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
         var response = JSON.parse(data.responseText);
        var el = document.getElementById('like_'+response.id);
        if (response.result == true)
        {	
        	
        	el.innerHTML = parseInt(el.innerHTML) + 1;
        }
    },
    failure: function(data) {

    }
}

function deleteFile(doc_id) {
	var text = getLabel('LBL_CONFIRM_DELETE_FILE');
    if (!confirm(text)) return false;
    
	var url = createURL('method=deleteFile&document_id='+doc_id);
    YAHOO.util.Connect.asyncRequest('POST', url, FileHandler);
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}

var FileHandler = {
    success: function(data) {
        window.setTimeout('ajaxStatus.hideStatus()', 1000);
        loadTabData('Files',1,'');
    },
    failure: function(data) {

    }
}

function saveNewFile() {
	var url = 'index.php';
	var xhr = new XMLHttpRequest();
	var fd = new FormData(document.getElementById('CreateFile'));
	ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING')); 
	xhr.open("POST", url, true);
	xhr.onreadystatechange = function() {
	if (xhr.readyState == 4 && xhr.status == 200) {
		window.setTimeout('ajaxStatus.hideStatus()', 2000);
		loadTabData('Files',1,'');
		}
	};
	fd.append('file_el', tmp_file);
	xhr.send(fd);
    return false;
}


function addMember() {
	if ($('#mt_member_id').val() == '') return false;
	//var community_id = $('#ibm_id').val();
	var member_id = $('#mt_member_id').val();
	var member_role = $('#mt_member_role').val();
    SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_ADD_MEMBER'));
    var url = createURL('method=addMember&member_id='+member_id+'&member_role='+member_role);
    YAHOO.util.Connect.asyncRequest('POST', url, selectMemberHandler);
    return false;
}


var selectMemberHandler = {
    success: function(data) {
        SUGAR.ajaxStatusClass.prototype.hideStatus();
        loadTabData(YAHOO.connections.data.tab_name, 1, '');
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


function collapseSection(id)
{
	$('#section_'+id).toggle();
	$('#expanded_icon_'+id).toggle();
	$('#collapsed_icon_'+id).toggle();
}

function todoAssign()
{
	$('#assign_member').toggle();
	$('#assigned_id').val('');
	$('#assigned_name').val('');
}
function selectAssign(u_id, u_name)
{
	$('#assigned_id').val(u_id);
	$('#assigned_name').val(u_name);
}
function showUpdates(updates_type)
{
	//setTimeout("SemTagSvc.parseDom(null, 'update_list')", 500 );
	if (updates_type == 'all')	$('.recent_update').show();
	if (updates_type == 'status')	$('.recent_update').hide();
}

function onloadWikiContent(id, wikiSrc) {
	var all_elements = $('.elements');
	for(var i = 0; i < all_elements.length; i++) {
		$(all_elements[i]).removeClass('activeIBMElement');
	}
	$('#wiki_'+id).addClass('activeIBMElement');
	document.getElementById('wiki_content').innerHTML = '';

	var id_1 = wikiSrc.substr(-42,36);
	var id_2 = wikiSrc.substr(-84,36);
   var url = createURL('method=getCommunityWikiContent&comm_id=' +id_2+'&rec_id='+id_1);
  	YAHOO.util.Connect.asyncRequest('POST', url, wikiContentHandler);
   // ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    return false;
}


var wikiContentHandler = {
    success: function(data) {
    var response = JSON.parse(data.responseText);
    var el = document.getElementById('wiki_content');
    el.innerHTML = response.content;
    },
    failure: function(data) {

    }
}

 function loadTabData(tab_name, page, search_text)//method)
 {
 	//YAHOO.connections.data.page_number = page_number;
    var tabNum = YAHOO.connections.tabs_meta[tab_name].order;
    var method = YAHOO.connections.tabs_meta[tab_name].method;
    YAHOO.connections.data.tab_name=tab_name;
    YAHOO.connections.data.page_number=page;
    if (YAHOO.connections.data.page_number < 1) YAHOO.connections.data.page_number = 1;
    
     if (YAHOO.connections.data.page_number == 1) document.getElementById('ibm_connection_body').innerHTML='';
   	var url = createURL('method='+method);
	if (search_text != '' && search_text != 'undefined') {
		url += "&search_text=" + search_text;
	}
   	//showLoadingModal();
   	var elems = $('.ibm_tabs');
	for(var i = 0; i < elems.length; i++) {
    	$(elems[i]).removeClass('activeTab');
	}
   $('#ibm_'+ tab_name +'_tab').addClass('activeTab');
   	
   	switch(tab_name)
   	{
   		case 'Updates' : document.getElementById('ibm_asset').selectedIndex = 0; selectQuickCreateElement();break;
   		case 'Activities' : document.getElementById('ibm_asset').selectedIndex = 1; selectQuickCreateElement();break;
   		case 'Discussions' : document.getElementById('ibm_asset').selectedIndex = 2; selectQuickCreateElement();break;
   		case 'Files' : document.getElementById('ibm_asset').selectedIndex = 3; selectQuickCreateElement('file');break;
   		case 'Bookmarks' : document.getElementById('ibm_asset').selectedIndex = 4; selectQuickCreateElement();break;
   	}
   	SUGAR.ajaxStatusClass.prototype.showStatus(getLabel('LBL_LOADING'));
  	YAHOO.util.Connect.asyncRequest('POST', url, loadTabDataHandler);
    return false;
}


var loadTabDataHandler = {
    success: function(data) {
    SUGAR.ajaxStatusClass.prototype.hideStatus();
	if (data.responseText == '') {
		YAHOO.connections.data.page_number -= 1;
		if (YAHOO.connections.data.page_number < 1) YAHOO.connections.data.page_number = 1;
		return false;
	}
    var response = JSON.parse(data.responseText);
     
    var el = document.getElementById('ibm_connection_body');
    if (YAHOO.connections.data.page_number == 1){
    	el.innerHTML = response.frame;
    	if (response.container_id == '') {
    		setTimeout("SemTagSvc.parseDom(null, 'ibm_connection_body')", 500 );
    		return false;
    	}
    	var container = document.getElementById(response.container_id);
    	var container_table = document.getElementById(response.container_id+'_table');
    	
    	if (container != null || container != 'undefined') {
			 container_table.innerHTML = '';
			$(container).scroll(function () {
			var el_height = $(container).css('height');
			  if((container.scrollTop + parseInt(el_height)) >= container.scrollHeight - 10 ){
			  		var search_el = document.getElementById('ibm_search_field');
			  		loadTabData(YAHOO.connections.data.tab_name, YAHOO.connections.data.page_number + 1, search_el.value);
			  }
			});
		}
    }
    
    document.getElementById(response.container_id+'_table').innerHTML += response.content;
    setTimeout("SemTagSvc.parseDom(null, 'ibm_connection_body')", 500 );
    if (response.content == '' && YAHOO.connections.data.page_number != 1) YAHOO.connections.data.page_number -= 1;
    if (YAHOO.connections.data.tab_name == 'Files') testDD();
    },
    failure: function(data) {

    }
}


function onloadBlogComments(id) {
	var all_elements = $('.elements');
	for(var i = 0; i < all_elements.length; i++) {
		$(all_elements[i]).removeClass('activeIBMElement');
	}
	$('#blog_'+id).addClass('activeIBMElement');
	document.getElementById('blog_content').innerHTML = document.getElementById('blog_content_'+id).innerHTML ;
    return false;
}


function onloadDiscusionReplies(forum_id) {
	
	var all_elements = $('.elements');
	for(var i = 0; i < all_elements.length; i++) {
		$(all_elements[i]).removeClass('activeIBMElement');
	}
	$('#discussion_'+forum_id).addClass('activeIBMElement');
	var visibleTitle = $('.visible-title').first();
	var tempId = $(visibleTitle).attr('id');
	if (tempId != null && tempId != 'undefined' && tempId != '')
	{
		var id = tempId.substr(-36);
		$('#discussionTitle_'+id).css('display','none');
		$('#discussionTitle_'+id).appendTo( $('#discussion_'+id) );
		$('#discussionTitle_'+id).removeClass('visible-title');
    	
		
	}
   var url = createURL('method=getDiscussion&forum_id=' +forum_id);
   document.getElementById('discussion_content').innerHTML = '';
    //document.getElementById('discussion_content').innerHTML = $('#discussion_title_'+forum_id).val();
    $('#discussionTitle_'+forum_id).appendTo( $('#discussion_content') );
    $('#discussionTitle_'+forum_id).css('display','block');
    $('#discussionTitle_'+forum_id).addClass('visible-title');
    setTimeout("SemTagSvc.parseDom(null, 'discussion_content')", 1500 );
    YAHOO.util.Connect.asyncRequest('POST', url, discusionRepliesHandler);
    return false;
}


var discusionRepliesHandler = {
    success: function(data) {
    var response = JSON.parse(data.responseText);
    document.getElementById('discussion_content').innerHTML += response.content;
     //setTimeout("SemTagSvc.parseDom(null, 'discussion_content')", 1000 );
    },
    failure: function(data) {

    }
}

function onloadActivityView(id) {
   
   var all_elements = $('.elements');
	for(var i = 0; i < all_elements.length; i++) {
		$(all_elements[i]).removeClass('activeIBMElement');
	}
	$('#activity_'+id).addClass('activeIBMElement');
   document.getElementById('activity_content').innerHTML = '';
	var url = createURL('method=getActivity&activity_id=' +id);
  	YAHOO.util.Connect.asyncRequest('POST', url, activityViewHandler);
    return false;
}


var activityViewHandler = {
    success: function(data) {
    var response = JSON.parse(data.responseText);
   
    var el = document.getElementById('activity_content');
    el.innerHTML = response.content;
    setTimeout("SemTagSvc.parseDom(null, 'ibm_connection_body')", 1000 );
    },
    failure: function(data) {

    }
}

function markCompleted(id, parent_id){
	var el=document.getElementById('todo_'+id);//.disabled = true;
	if ( $(el).hasClass('completed')) return false;
	el.src = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/new/ico_checkedBlueBig.png';
	$(el).addClass('completed');
	var url = createURL('method=markToDoCompleted&todo_id=' +id+'&activity_id='+parent_id);
  	YAHOO.util.Connect.asyncRequest('POST', url, markCompletedHandler);
    return false;
}

var markCompletedHandler = {
    success: function(data) {
    var response = JSON.parse(data.responseText);
    recalculateCompletion(response.id);
    },
    failure: function(data) {

    }
}

function deleteCommunity(id){
    var text = getLabel('LBL_CONFIRM_DELETE_COMMUNITY');
    if (!confirm(text)) return false;
	var url = createURL('method=deleteCommunity&community_id=' +id);
  	YAHOO.util.Connect.asyncRequest('POST', url, deleteCommunityHandler);
    return false;
}

var deleteCommunityHandler = {
    success: function(data) {
    var response = JSON.parse(data.responseText);
    if (response.deleted) {
    	var url = createURL('method=saveCommunitySelection&community_id=');
    	YAHOO.util.Connect.asyncRequest('POST', url, selectCommunityHandler);
    }
    else {
    	alert('access denied');
    }
    },
    failure: function(data) {

    }
}


function testDD(){
	var dropbox;
	 
	dropbox = document.getElementById("file_dropbox");
	dropbox.addEventListener("dragenter", dragenter, false);
	dropbox.addEventListener("dragover", dragover, false);
	dropbox.addEventListener("drop", drop, false);
	document.getElementById("new_file").addEventListener("change", selectFile, false);
return dropbox;
}
function dragenter(e) {
  e.stopPropagation();
  e.preventDefault();
}
 
function dragover(e) {
  e.stopPropagation();
  e.preventDefault();
} 
var tmp_file;
function drop(e) {
  e.stopPropagation();
  e.preventDefault();
 
  var dt = e.dataTransfer;
  var files = dt.files;
  tmp_file = files[0];
  var fileName = files[0].name;
  document.getElementById('file_path').innerHTML = fileName;
  document.getElementById('file_name').value = fileName;//this.value.replace(/^.*[\\\/]/, '');
  var fileType = fileName.substr(fileName.lastIndexOf('.') + 1);
  getFileImage(fileType);
}

function selectFile(e) {
  e.stopPropagation();
  e.preventDefault();
  var dt = e.target;
  var files = dt.files;
	tmp_file = files[0];
  var fileName = files[0].name;
  var path;
  if (files[0].mozFullPath != 'undefined' && files[0].mozFullPath != null && files[0].mozFullPath != ''){
  	path = files[0].mozFullPath;
  }
  else {
  	path = fileName;
  }
  document.getElementById('file_path').innerHTML = path;
  document.getElementById('file_name').value = fileName.replace(/^.*[\\\/]/, '');
  var fileType = fileName.substr(fileName.lastIndexOf('.') + 1);
  getFileImage(fileType);
}
function changeAccessDesc(val)
{
	var label = "LBL_"+ val.toUpperCase() +"_ACCESS_DESCRIPTION";
	document.getElementById("access_desription").innerHTML = getLabel(label);;
}

function startSearch(searchboxname , tab_name , event)
{
if (event.keyCode == 13) {
        var tb = document.getElementById(searchboxname);
       if (tab_name == "MyCommunities" ||  tab_name == "PublicCommunities" || tab_name == "CreateCommunity") searchListings(tab_name,1,tb.value);
       else loadTabData(tab_name, 1, tb.value);
    }
     return false;
}
function startSearchInTab(event)
{
	if (YAHOO.connections.data.tab_name != "undefined" && event.keyCode == 13) {
        var tb = document.getElementById('ibm_search_field');
        loadTabData(YAHOO.connections.data.tab_name, 1, tb.value);
    }
     return false;
}

function initCal()
{
Calendar.setup (
{ 	inputField : "due_date", 
	ifFormat : "%m\/%d\/%Y", 
	showsTime : false, 
	button : "due_date_trigger", 
	singleClick : true, 
	step : 1, 
	weekNumbers:false
	}
);
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



