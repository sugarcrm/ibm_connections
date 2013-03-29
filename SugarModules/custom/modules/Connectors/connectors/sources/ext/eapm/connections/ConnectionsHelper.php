<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

class ConnectionsHelper 
{
	protected $apiClass;
	protected $community;
	protected $community_id;
	protected $tab_name;
	protected $page_number;
	protected $method;
	protected $search_text;
	protected $tabs_meta;
	protected $language;

	public function __construct() {
		require_once('include/connectors/utils/ConnectorUtils.php');
		require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/tabs.meta.php');
		$this->tabs_meta = $tabs_meta;

		$this->language = ConnectorUtils::getConnectorStrings('ext_eapm_connections');
		$this->community_id = $this->getCommunityId();

		foreach($_REQUEST as $key => $value) {
			
			if(!empty($value)) 
			{
				@safe_map($key, $this, true);
			}
		}
		$this->apiClass = self::getEAPM();
	}

	/**
	 * Exists to validate the passed in method to make sure it exists, along with getting around an annoying quirk of
	 * ModuleScanner that does allow variable functions.
	 *
	 * @param string $methodName
	 */
	public static function handleMethod($methodName) {
		// TODO: This seems to be breaking things..
		$helper = new ConnectionsHelper;
	    if ( $methodName == 'uploadNewFile' ) {
	        return $helper->uploadNewFile();
	    }
	    if ( $methodName == 'downloadFile' ) {
	        return $helper->downloadFile();
	    }
	    if ( $methodName == 'saveNewCommunity' ) {
	        return $helper->saveNewCommunity();
	    }
	    if ( $methodName == 'sourceForAutoCompleteMember' ) {
	        return $helper->sourceForAutoCompleteMember();
	    }
	    if ( $methodName == 'loadConnectionsCommunities' ) {
	        return $helper->loadConnectionsCommunities();
	    }
	    if ( $methodName == 'loadFilesList' ) {
	        return $helper->loadFilesList();
	    }
	    if ( $methodName == 'loadMembersList' ) {
	        return $helper->loadMembersList();
	    }
	    if ( $methodName == 'createNewCommunity' ) {
	        return $helper->createNewCommunity();
	    }
	    if ( $methodName == 'createNewFile' ) {
	        return $helper->createNewFile();
	    }
	    if ( $methodName == 'addCommunityMember' ) {
	        return $helper->addCommunityMember();
	    }
	    if ( $methodName == 'myAccount' ) {
	        return $helper->myAccount();
	    }
	    if ( $methodName == 'saveCommunitySelection' ) {
	        return $helper->saveCommunitySelection();
	    }
	    
	    throw new Exception("Method passed '$methodName' does not exist");        
	}
	
	public static function getEAPM() {
		require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
		$api_class = new ExtAPIConnections();
		$eapmBean = EAPM::getLoginInfo('Connections');
		if(!empty($eapmBean)) {
			$api_class->loadEAPM($eapmBean);
			return $api_class;
		}
		else {
		 	$GLOBALS['log']->error("Could not load external account. Please verify your IBM Connections login credentials.");
		}

	}

	private function getMembersListForAutocomplete($response) {
		$entries = $response->entry;
		$community_id = $this->getCommunityId();
		$res = array();
		if(!empty($entries)) {
			foreach($entries as $entry) {
				$member_name = (string) $entry->contributor->name;
				$member_id = (string) $entry->contributor->children('http://www.ibm.com/xmlns/prod/sn')->userid;
				$res [] = array('member_id' => $member_id, 'member_name' =>$member_name, 'community_id' => $community_id);
			}
		}
		return $res;
	}

	
	protected function sourceForAutoCompleteMember() {
		$search_text = $this->search_text;
		$reply = $this->apiClass->getMembers("", $search_text);
		$response = new SimpleXMLElement($reply['rawResponse']);
		$member_list = $this->getMembersListForAutocomplete($response);
		echo json_encode($member_list);
	}

	protected function loadConnectionsCommunities() {
		$this->community = $this->tab_name;
		$search_text = $this->search_text;
		$page_number = $this->page_number;

		try {
			$reply = $this->apiClass->getCommunities($this->community,$page_number,$search_text);

			$response = new SimpleXMLElement($reply['rawResponse']);
			$opensearch = $response->children('http://a9.com/-/spec/opensearch/1.1/');

			$pagination_data = array(
				'totalResults' => 0,
				'itemsPerPage' => 10,
				'startIndex' => 1
			);
			if(!empty($opensearch->totalResults))
				$pagination_data['totalResults'] = intval($opensearch->totalResults);
			if(!empty($opensearch->itemsPerPage))
				$pagination_data['itemsPerPage'] = intval($opensearch->itemsPerPage);
			if(!empty($opensearch->startIndex))
				$pagination_data['startIndex'] = intval($opensearch->startIndex);

			$pagination = $this->getPagination($pagination_data,$search_text);
			$community_list = $this->getCommunityList($response);
			$search = $this->getSearch($this->tab_name,$search_text);

			echo $pagination;
			echo $community_list;
			echo $search;
		}
		catch(Exception $e) {
			$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ConnectionsError.tpl';
			$smarty = new Sugar_Smarty();
			$smarty->assign('error_message', $e->getMessage());
			$smarty->assign('language', $this->language);
			$smarty->assign('javascript_onclick',"loadListings('{$this->community}','{$page_number}','')");

			echo $smarty->fetch($tplName);
		}
	}

	private function getSearch($tab_name,$search_string) {
		$searchboxname = "search_".$tab_name;
		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Search.tpl';
		$smarty = new Sugar_Smarty();
		$smarty->assign('community',$this->community);
		$smarty->assign('searchboxname',$searchboxname);
		$smarty->assign('search_text',$search_string);
		$smarty->assign('tab_name',$tab_name);
		$smarty->assign('language', $this->language);

		return $smarty->fetch($tplName);
	}

	// returns a list of all Connections members registered in Sugar instance
	private function getSugarMembers() {
		global $current_user;
		$eapm = new EAPM();

		//$where = $GLOBALS['db']->getWhereClause($eapm,$where_array);
		$eapm_data = $eapm->get_list("","eapm.assigned_user_id != '{$current_user->id}' AND eapm.application = 'Connections' AND eapm.deleted = '0' AND eapm.validated = '1'");

		$members_list = array();
		for($i=0; $i<count($eapm_data['list']); $i++) {
			$api_data = json_decode(base64_decode($eapm_data['list'][$i]->api_data), true);
			$members_list[$eapm_data['list'][$i]->assigned_user_id] = $api_data['userId'];
		}

		return $members_list;
	}

	//TODO: Combine into one funtion with createNewFile
	protected function createNewCommunity() {
		$members_list = $this->getSugarMembers();
		$members = array();
		$user = new User();

		foreach($members_list as $sugarId => $connectionsId) {
			$user->retrieve($sugarId);
			$userName = $user->full_name;
			$members[$connectionsId] = $userName;
		}

		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateCommunity.tpl';
		$smarty = new Sugar_Smarty();
		$smarty->assign('parent_type', $this->parent_type);
		$smarty->assign('parent_id', $this->parent_id);
		$smarty->assign('members', $members);
		$smarty->assign('language', $this->language);

		echo $smarty->fetch($tplName);
	}

	protected function createNewFile() {

		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateFile.tpl';
		$smarty = new Sugar_Smarty();
		$smarty->assign('parent_type', $this->parent_type);
		$smarty->assign('parent_id', $this->parent_id);
		$smarty->assign('language', $this->language);

		echo $smarty->fetch($tplName);
	}

	protected function addCommunityMember() {
		$this->community = $this->tab_name;
		$search_text = $this->search_text;
		$page_number = $this->page_number;

		try {
			if(!empty($search_text)) {
				$reply = $this->apiClass->getMembers("", $search_text, $page_number);

				$response = new SimpleXMLElement($reply['rawResponse']);
				$opensearch = $response->children('http://a9.com/-/spec/opensearch/1.1/');
				
				$pagination_data = array(
					'totalResults' => 0,
					'itemsPerPage' => 10,
					'startIndex' => 1
				);
				if(!empty($opensearch->totalResults))
					$pagination_data['totalResults'] = intval($opensearch->totalResults);
				if(!empty($opensearch->itemsPerPage))
					$pagination_data['itemsPerPage'] = intval($opensearch->itemsPerPage);
				if(!empty($opensearch->startIndex))
					$pagination_data['startIndex'] = intval($opensearch->startIndex);

				$pagination = $this->getPagination($pagination_data,$search_text);
				$member_list = $this->getMembersList($response, true);
			}
			else {
				$pagination = "";
				$member_list = "";
				echo "{$this->language['LBL_ENTER_SEARCH_PARAMETER']}<br/><br/>";
			}

			$search = $this->getSearch($this->tab_name,$search_text);

			echo $pagination;
			echo $member_list;
			echo $search;
			echo "<input type='button' value='Cancel' onclick='closeTab(\"AddMember\");' />";
		}
		catch(Exception $e) {
			$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ConnectionsError.tpl';
			$smarty = new Sugar_Smarty();
			$smarty->assign('error_message', $e->getMessage());
			$smarty->assign('language', $this->language);
			$smarty->assign('javascript_onclick',"loadListings('{$this->community}','{$page_number}','')");

			echo $smarty->fetch($tplName);
		}
	}

	protected function uploadNewFile() {
	    $uploadFile = new UploadFile('userfile');
	    if ( !$uploadFile->confirm_upload() ) {
	        echo $this->language['LBL_FILE_UPLOADED_NOT_SHARED'];
	        return;
	    }
		$fileToUploadContents = $uploadFile->get_file_contents();
		$docName = $uploadFile->get_uploaded_file_name();
		$mimeType = $uploadFile->get_mime_type();
		$fileShared = false;
		$comm_info  = $this->apiClass->getCommunityInfo($this->getCommunityId());
		$response_info = new SimpleXMLElement($comm_info['rawResponse']);
		$comm_type = (string)$response_info->children("http://www.ibm.com/xmlns/prod/sn")->communityType;
		if ($comm_type != "public" && $comm_type != "private") $comm_type = "private";
		$reply = $this->apiClass->uploadFile($fileToUploadContents, $docName, $mimeType,$comm_type);
		$response = new SimpleXMLElement($reply['rawResponse']);
		$document_id = $response->children('urn:ibm.com/td')->uuid;
		
		if (empty($document_id) || false)
		{
			$reply = $this->apiClass->updateFile($fileToUpload, $docName, $mimeType, $comm_type);
			$response = new SimpleXMLElement($reply['rawResponse']);
			$document_id = $response->children('urn:ibm.com/td')->uuid;
		}
		
		if(!empty($document_id)) {
			$xml = new SimpleXMLElement($this->apiClass->upload_document_xml);
			$xml->entry->addChild('itemId',$this->getCommunityId(),'urn:ibm.com/td');
			$fileShared = false;
			$count = 0;
			while(!$fileShared) {
				$doc_reply = $this->apiClass->shareMyFileWithCommunity($document_id,$xml->asXML());
				if($doc_reply->isSuccessful()) $fileShared = true;
				if($count == 3 && !$fileShared) {
					$GLOBALS['log']->fatal("Unable to share file [{$document_id}] with community. Error code: {$doc_reply->getStatus()}");
					break;
				}
				$count++;
			}
		}

		if($fileShared) echo $this->language['LBL_FILE_UPLOADED_SHARED'];
		else echo $this->language['LBL_FILE_UPLOADED_NOT_SHARED'];
	}

	private function addMember() {
		$this->apiClass->addMemberToCommunity($this->member_id, $this->community_id);
	}

	protected function saveCommunitySelection() {
		$connections = new ibm_connections();

		$connections->retrieve_by_string_fields(array('parent_id' => $this->parent_id));

		$connections->parent_type = $this->parent_type;
		$connections->parent_id = $this->parent_id;
		$connections->community_id = $this->community_id;
		$connections->date_modified = $GLOBALS['timedate']->nowDb();
		if (empty($this->community_id)) $connections->deleted = 1;
		else $connections->deleted = 0;
		$connections->save();

		echo $this->language['LBL_COMMUNITY_SELECTION_SAVED'];
	}

	protected function saveNewCommunity() {
		global $current_user;
		//echo "Save new community";
		$community_xml = new SimpleXMLElement($this->apiClass->create_community_xml);
		$community_xml->title = $this->community_name;
		$community_xml->summary = $this->description;

		$tags = explode(",", $this->community_tags);
		for($i=0; $i<count($tags); $i++) {
			$community_xml->addChild('category')->addAttribute('term',$tags[$i]);
		}

		if($this->public_community == 'on')
			$community_xml->addChild('snx:communityType','public','http://www.ibm.com/xmlns/prod/sn');
		else
			$community_xml->addChild('snx:communityType','private','http://www.ibm.com/xmlns/prod/sn');

		try {
			$community_id = $this->apiClass->createCommunity($community_xml->asXML());
			//print_r($community_xml->asXML());
			if(!empty($community_id)) {
				for($i=0; $i<count($this->members); $i++) {
					$member_id = $this->members[$i];
					$this->apiClass->addMemberToCommunity($member_id, $community_id);
				}
			}
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	private function getPagination($data,$search_text='') {
		$tab_name = $this->tab_name;
		$search_text = str_ireplace(" ","+",$search_text);

		if(!empty($data['totalResults']))
			$totalResults = $data['totalResults'];
		else $totalResults = 0;
		if(!empty($data['itemsPerPage']))
			$itemsPerPage = $data['itemsPerPage'];
		else $itemsPerPage = 10;
		if(!empty($data['startIndex']))
			$startIndex = $data['startIndex'];
		else $startIndex = 1;

		$totalPages = ceil($totalResults / $itemsPerPage);
		
		$start_button_onclick = "";
		$prev_button_onclick = "";
		$next_button_onclick = "";
		$end_button_onclick = "";
		
		$start_button_disabled = "";
		$prev_button_disabled = "";
		$next_button_disabled = "";
		$end_button_disabled = "";
		
		if($totalPages == 0)
			$current_page = 0;
		elseif($totalPages > 1)
			$current_page = ceil($startIndex / $itemsPerPage);
		else
			$current_page = 1;
		$endIndex = $startIndex + $itemsPerPage - 1;
		if ($endIndex > $totalResults) $endIndex = $totalResults;
		if ($endIndex < 0) $endIndex = 0;
		if ($totalResults == 0) $startIndex = $totalResults;
		//$pageCount = "<span class='pageNumbers'>(Page {$current_page} of {$totalPages})</span>";
		$pageCount = "<span class='pageNumbers'>({$startIndex} - {$endIndex} of {$totalResults})</span>";
		if($current_page > 1) {
			$start_button_src = SugarThemeRegistry::current()->getImageURL('start.png');
			$start_page = 1;
			$start_button_onclick = "onclick=loadListings('{$tab_name}','{$start_page}','{$search_text}');";
		}
		else {
			$start_button_src = SugarThemeRegistry::current()->getImageURL('start_off.png');
			$start_button_disabled = "disabled='disabled'";
		}

		if($current_page > 1) {
			$previous_button_src = SugarThemeRegistry::current()->getImageURL('previous.png');
			$previous_page = $current_page - 1;
			$prev_button_onclick = "onclick=loadListings('{$tab_name}','{$previous_page}','{$search_text}');";
		}
		else {
			$previous_button_src = SugarThemeRegistry::current()->getImageURL('previous_off.png');
			$prev_button_disabled = "disabled='disabled'";
		}

		if($current_page < $totalPages) {
			$next_button_src = SugarThemeRegistry::current()->getImageURL('next.png');
			$next_page = $current_page + 1;
			$next_button_onclick = "onclick=loadListings('{$tab_name}','{$next_page}','{$search_text}');";
		}
		else {
			$next_button_src = SugarThemeRegistry::current()->getImageURL('next_off.png');
			$next_button_disabled = "disabled='disabled'";
		}

		if($current_page < $totalPages) {
			$end_button_src = SugarThemeRegistry::current()->getImageURL('end.png');
			$end_page = $totalPages;
			$end_button_onclick = "onclick=loadListings('{$tab_name}','{$end_page}','{$search_text}');";
		}
		else {
			$end_button_src = SugarThemeRegistry::current()->getImageURL('end_off.png');
			$end_button_disabled = "disabled='disabled'";
		}

		$start_button = "<button type='button' id='' name='listViewStartButton' title='Start' class='button' {$start_button_disabled} {$start_button_onclick}><img src='{$start_button_src}' align='absmiddle' /></button>";
		$previous_button = "<button type='button' name='listViewPrevButton' title='Previous' class='button' {$prev_button_disabled} {$prev_button_onclick}><img src='{$previous_button_src}' align='absmiddle' /></button>";
		$next_button = "<button type='button' name='listViewNextButton' title='Next' class='button' {$next_button_disabled} {$next_button_onclick}><img src='{$next_button_src}' align='absmiddle' /></button>";
		$end_button = "<button type='button' name='listViewEndButton' title='End' class='button' {$end_button_disabled} {$end_button_onclick}><img src='{$end_button_src}' align='absmiddle' /></button>";

		$pagination = "<table cellpadding='0' cellspacing='0' width='100%' border='0' class='list view'>";
		$tab_butt = $this->getTabButtons();
		$buttons = (empty($tab_butt)) ? "": "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td>".$tab_butt."</td></tr></tbody></table>";
		$pagination .= "<tr class='pagination'><td align='left'>{$buttons}</td>";
		$pagination .= "<td align='right'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td align='right'>{$start_button} {$previous_button} {$pageCount} {$next_button} {$end_button}</td></tr></tbody></table></td></tr>";
		$pagination .= "</table>";

		return $pagination;
	}

	private function getTabButtons() {
		global $beanList, $current_user;
		if(empty($this->community_id)) $this->community_id = $this->getCommunityId();

		$tabName = $this->tab_name;
		$recordOwner = false;
		if(!empty($this->parent_type) && !empty($this->parent_id)) {
			$bean = new $beanList[$this->parent_type];
			$bean->retrieve($this->parent_id);

			$recordOwner = $bean->isOwner($current_user->id);
		}

		$buttons = "";
		for($i=0; $i<count($this->tabs_meta[$tabName]['buttons']); $i++) {
			if($this->tabs_meta[$tabName]['buttons'][$i] == "newcommunity") {
				$disabled = (!$recordOwner)?"disabled":"";
				$button_label = $this->language['LBL_NEW_COMMUNITY_BUTTON'];
				$buttons .= "<input id='create_new_community' type='button' class='button' value='{$button_label}' onclick='createNewConnectionsObject(\"CreateCommunity\");' {$disabled} />";
			}
			if($this->tabs_meta[$tabName]['buttons'][$i] == "selectcommunity") {
				$disabled = (!$recordOwner)?"disabled":"";
				$button_label = $this->language['LBL_SELECT_COMMUNITY_BUTTON'];
				$buttons .= "<input type='button' class='button' id='selectCommunity' value='{$button_label}' onclick='showCommunities();' {$disabled} />";
			}
			if($this->tabs_meta[$tabName]['buttons'][$i] == "addmember") {
				$disabled = (empty($this->community_id) && !$recordOwner)?"disabled":"";
				$button_label = $this->language['LBL_ADD_MEMBER_BUTTON'];
				$buttons .= "<input type='button' class='button' id='addMember' value='{$button_label}' onclick='createNewConnectionsObject(\"AddMember\");' {$disabled} />";
			}
			if($this->tabs_meta[$tabName]['buttons'][$i] == "newfile") {
				$disabled = (empty($this->community_id))?"disabled":"";
				$button_label = $this->language['LBL_NEW_FILE_BUTTON'];
				$buttons .= "<input id='create_new_file' type='button' class='button' value='{$button_label}' onclick='createNewConnectionsObject(\"CreateFile\");' {$disabled} />";
			}
			
		}
		if (empty($this->community_id) && !$recordOwner) $buttons .= "<br>".$this->language['LBL_ONLY_OWNER_CAN_ASSOCIATE'];
		return $buttons;
	}

	private function getCommunityList($response) {
		$entries = $response->entry;
		$reply = "<br/><table cellspacing='0' cellpadding='0' border='0' width='100%' style='background: rgb(255, 255, 255); background: rgba(255, 255, 255, 0.7);' class='list view'>";

		if(!empty($entries)) {
			foreach($entries as $entry) {
				parse_str(parse_url($entry->id,PHP_URL_QUERY));
				$entry_id = $communityUuid;
				$community_id = urlencode($this->getCommunityId());

				$checked = "";
				if($entry_id == $community_id) $checked = "checked";
				$last_updated = date($GLOBALS['timedate']->get_date_time_format(),strtotime($entry->updated));
				$reply .= "<tr><td style='padding-left: 15px;'><br/>";
				$reply .= "<input type='radio' name='community' value='{$entry->title}' onchange='selectCommunity(\"{$entry_id}\");' {$checked}> <b>{$entry->title}</b></input><br/>";
				$reply .= "{$entry->author->name} | Last Updated: $last_updated";
				$reply .= "<br/><br/></td></tr>";
			}
		}
		else {
			$reply .= "<tr><td>No Data</td></tr>";
		}

		$reply .= "</table>";

		return $reply;
	}

	private function getCommunityId() {
		if(isset($this->parent_id) && !empty($this->parent_id)) {
			$connections = new ibm_connections();
			$connections->retrieve_by_string_fields(array('parent_id' => $this->parent_id));

			return $connections->community_id;
		}
	}

	protected function loadFilesList() {
		global $beanList, $current_user;
		$community_id = $this->getCommunityId();
		if(empty($community_id)) {
			$pagination = "<table cellpadding='0' cellspacing='0' width='100%' border='0' class='pagination'>";
			$pagination .= "<tr><td align='left'>".$this->getTabButtons()."</td></tr>";
			$pagination .= "</table>";
			echo $pagination;
			
			return;
		}
		$page_number = $this->page_number;

		try {
			$reply = $this->apiClass->getFiles($community_id,$page_number);

			$response = new SimpleXMLElement($reply['rawResponse']);
			$opensearch = $response->children('http://a9.com/-/spec/opensearch/1.1/');

			$pagination_data = array(
				'totalResults' => 0,
				'itemsPerPage' => 5,
				'startIndex' => 1
			);
			if(!empty($opensearch->totalResults))
				$pagination_data['totalResults'] = intval($opensearch->totalResults);
			if(!empty($opensearch->itemsPerPage))
				$pagination_data['itemsPerPage'] = intval($opensearch->itemsPerPage);
			if(!empty($opensearch->startIndex))
				$pagination_data['startIndex'] = intval($opensearch->startIndex);
			else
				$pagination_data['startIndex'] = ($pagination_data['itemsPerPage']*$page_number)-($pagination_data['itemsPerPage']-1);

			$pagination = $this->getPagination($pagination_data);
			$file_list = $this->getFilesList($response);

			echo $pagination;
			//isOwner
				$recordOwner = false;
				$bean = new $beanList[$this->parent_type];
				$bean->retrieve($this->parent_id);
				$recordOwner = $bean->isOwner($current_user->id);
			$button_X = ($recordOwner) ? "<button class='button' value='X' onclick='selectCommunity(\"\");'>X</button>" : ""; 	
			echo "<h5>{$button_X}<a href='".urldecode((string) $response->link[2]->attributes()->href)."' target='_blank'>".$response->title."</a></h5>";
			echo $file_list;
			
		}
		catch(Exception $e) {

			$message = "<div align='center'>";
			$message .= "{$this->language['LBL_PROBLEM_LOADING_PAGE']}:<br/>{$e->getMessage()}<br/>";
			$message .= "<br/>";
			$message .= "<input type='button' class='button' value='Retry' onclick='loadListings(\"Files\",\"{$page_number}\",\"\");'/>";
			$message .= "</div>";

			echo $message;
		}
	}
	
	protected function myAccount() {
			$pagination = "<table cellpadding='0' cellspacing='0' width='100%' border='0' class='pagination'>";
			$pagination .= "<tr><td align='left'><a href='".$this->apiClass->url."/profiles/html/myProfileView.do' target='_blank'>".$this->language['LBL_MY_ACCOUNT']."</a></td></tr>";
			$pagination .="</table>";
			echo $pagination;
	}
	
	protected function loadMembersList() {
		global $beanList,$current_user;
		$community_id = $this->getCommunityId();

		if(empty($community_id)) {
			
			$pagination = "<table cellpadding='0' cellspacing='0' width='100%' border='0' class='pagination'>";
			$pagination .= "<tr><td align='left'>".$this->getTabButtons()."</td></tr>";
			$pagination .= "</table>";
			echo $pagination;
			return;
		}
		
		$page_number = $this->page_number;

		try {
			$reply = $this->apiClass->getMembers($community_id,"",$page_number);

			$response = new SimpleXMLElement($reply['rawResponse']);
			$opensearch = $response->children('http://a9.com/-/spec/opensearch/1.1/');

			$pagination_data = array(
				'totalResults' => 0,
				'itemsPerPage' => 5,
				'startIndex' => 1
			);
			if(!empty($opensearch->totalResults))
				$pagination_data['totalResults'] = intval($opensearch->totalResults);
			if(!empty($opensearch->itemsPerPage))
				$pagination_data['itemsPerPage'] = intval($opensearch->itemsPerPage);
			if(!empty($opensearch->startIndex))
				$pagination_data['startIndex'] = intval($opensearch->startIndex);

			$pagination = $this->getPagination($pagination_data);
			$member_list = $this->getMembersList($response);

			echo $pagination;
			$url = rtrim($this->apiClass->url,"/")."/communities/service/html/communityview?communityUuid=".$this->community_id;
							$recordOwner = false;
				$bean = new $beanList[$this->parent_type];
				$bean->retrieve($this->parent_id);
				$recordOwner = $bean->isOwner($current_user->id);
			$button_X = ($recordOwner) ? "<button class='button' value='X' onclick='selectCommunity(\"\");'>X</button>" : ""; 	
			echo "<h5>{$button_X}<a href='".urldecode((string) $url)."' target='_blank'>".$response->title."</a></h5>";
			echo $member_list;

		}
		catch(Exception $e) {

			$message = "<div align='center'>";
			$message .= $this->language['LBL_PROBLEM_LOADING_PAGE']."<br/>".$e->getMessage()."<br/>";
			$message .= "<br/>";
			$message .= "<input type='button' class='button' value='Retry' onclick='loadListings(\"Members\",\"{$page_number}\",\"\");'/>";
			$message .= "</div>";

			echo $message;
		}
	}

	protected function getFilesList($response) {
		$entries = $response->entry;

		$reply = "<br/><table cellspacing='0' cellpadding='0' border='0' width='100%' style='background: rgb(255, 255, 255); background: rgba(255, 255, 255, 0.7);' class='list view'>";

		if(!empty($entries)) {
			foreach($entries as $entry) {
				//$entry_id = parse_str(parse_url($entry->id, PHP_URL_QUERY));
				$entry_id = $entry->children('urn:ibm.com/td')->uuid;
				$entry_name = $entry->children('urn:ibm.com/td')->label;
				//$community_id = urlencode($this->getCommunityId());

				//$checked = "";
				//if($entry_id == $community_id) $checked = "checked";

				//echo $entry_id."<br/>";
				$download_link = "index.php?module=Connectors&action=Connections&method=downloadFile&documentId={$entry_id}&documentName={$entry_name}";
				$last_updated = date($GLOBALS['timedate']->get_date_time_format(),strtotime($entry->updated));
				$reply .= "<tr><td style='padding-left: 15px;'><br/>";
				$reply .= "<b><span><a href='{$download_link}' target='_new'>{$entry->title}</a></span></b><br/>";
				$reply .= "{$entry->author->name} | {$this->language['LBL_LAST_UPDATED']} {$last_updated}";
				$reply .= "<br/></td></tr>";
			}
		}
		else {
			$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}

		$reply .= "</table>";

		return $reply;
	}

	protected function getMembersList($response, $add_member=false) {
		$entries = $response->entry;
		$community_id = $this->getCommunityId();
		
		$reply = "<br/><table cellspacing='0' cellpadding='0' border='0' width='100%' id='memberProfile' style='background: rgb(255, 255, 255); background: rgba(255, 255, 255, 0.7);' class='list view'>";

		if(!empty($entries)) {
			foreach($entries as $entry) {
				$member_name = $entry->contributor->name;
				$member_id = $entry->contributor->children('http://www.ibm.com/xmlns/prod/sn')->userid;
				//$member_state = $entry->contributor->children('http://www.ibm.com/xmlns/prod/sn')->userState;
				$member_role = $entry->children('http://www.ibm.com/xmlns/prod/sn')->role;

				$last_updated = date($GLOBALS['timedate']->get_date_time_format(),strtotime($entry->updated));

				$reply .= "<tr><td style='padding-left: 15px;'><br/>";
				if($add_member) {
					//$add_member_image = getImagePath("add.gif");
					$image_label = $this->language['LBL_ADD_MEMBER'];
					$image_attributes = "alt='{$image_label}' title='{$image_label}' onclick='addMember(\"{$member_id}\",\"{$community_id}\");'";

					$add_member_image = SugarThemeRegistry::current()->getImage("plus_inline",$image_attributes,"","",".png");
					//echo $add_member_image;
					//var_dump($GLOBALS);

					$reply .= "{$add_member_image} <span id='{$member_id}' style='cursor: pointer; color: #1970B0;' onclick=showMemberProfile('{$member_id}');><b>{$member_name}</b></span><br/>";
					$reply .= "{$this->language['LBL_LAST_UPDATED']} {$last_updated}";
				}
				else {
					$reply .= "<span id='{$member_id}' style='cursor: pointer; color: #1970B0;' onclick=showMemberProfile('{$member_id}');><b>{$member_name}</b></span> ({$member_role})<br/>";
					$reply .= "{$this->language['LBL_LAST_UPDATED']} {$last_updated}";
				}
				$reply .= "<br/></td></tr>";
			}
		}
		else {
			$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}

		$reply .= "</table>";

		return $reply;
	}

	protected function loadMemberProfileCard() {
		$body = "";
		$member_id = $this->member_id;
		$member_profile_response = $this->apiClass->getMemberProfileInfo($member_id);
		$profile_image_url = $member_profile_response['profile_image_url'];

		//$body .= print_r($member_profile_response,true);

		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.tpl';
		$smarty = new Sugar_Smarty();
		$smarty->assign('profile_image_url', $profile_image_url);
		$smarty->assign('profile', $member_profile_response);
		$smarty->assign('member_id', $member_id);
		$smarty->assign('body',$body);
		$smarty->assign('language', $this->language);
		$profile_body = $smarty->fetch($tplName);

		$member_profile = array(
			"header"=>$member_profile_response['member_name'],
			"member_name"=>$member_profile_response['member_name'],
			"member_id"=>$member_id,
			"body"=>$profile_body
		);

		echo json_encode($member_profile);
	}

	protected function downloadFile() {
		$document_id = $this->documentId;
		$document_name = $this->documentName;
		//$document_info = $this->apiClass->getFileDetails($document_id);
		$document = $this->apiClass->downloadFile($document_id);
		//var_dump($document['rawResponse']);

		header("Pragma: public");
		header("Cache-Control: maxage=1, post-check=0, pre-check=0");
		if(isset($this->isTempFile) && ($this->type=="SugarFieldImage")) {
		    //$mime = getimagesize($download_location);
		    if(!empty($mime)) {
			    //header("Content-Type: {$mime['mime']}");
		    } else {
		        header("Content-Type: image/png");
		    }
		} else {
            header("Content-Type: application/force-download");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"".$document_name."\";");
		}
		// disable content type sniffing in MSIE
		header("X-Content-Type-Options: nosniff");
		//header("Content-Length: " . filesize($local_location));
		header("Expires: 0");
		set_time_limit(0);

		@ob_clean();
		echo $document['rawResponse'];
		@ob_flush();
	}
}
