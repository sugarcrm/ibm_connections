<?php

class ConnectionsHelper 
{
	public $apiClass;
	public $community;
	public $communityId;
	public $tab_name;
	public $page_number;
	public $method;
	public $search_text;
	public $tabs_meta;
	public $language;
	private $view;

	public function __construct() 
	{
		require_once('include/connectors/utils/ConnectorUtils.php');
		require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/tabs.meta.php');
		require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsViewer.php');
		
		$this->tabs_meta = $tabs_meta;
		$this->language = ConnectorUtils::getConnectorStrings('ext_eapm_connections');
		$this->community_id = $this->getCommunityId();
		$this->img_url = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/images';
		
		foreach($_REQUEST as $key => $value) {
			if ($key == 'ibm_header') continue;
			if(!empty($value)) 
			{
				@safe_map($key, $this, true);
			}
		}
		$this->apiClass = self::getEAPM();
		
		$this->view = new ConnectionsViewer();
		$this->view->img_url = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/images';
		$this->view->language = $this->language;
		$this->view->connection_url = $this->apiClass->url;
	}

	static function getEAPM() 
	{
		require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
		$api_class = new ExtAPIConnections();
		$eapmBean = EAPM::getLoginInfo('Connections');
		
		if(!empty($eapmBean)) {
			$api_class->loadEAPM($eapmBean);
		}	
		return $api_class;
	}
	
	public function saveEAPM()
	{
		global $current_user;
		$eapm = new EAPM();
		$eapm->application = "Connections";
		$eapm->name = $_REQUEST['name'];
		$eapm->password = $_REQUEST['password'];
		$eapm->relate_to = "Users";
		$eapm->relate_id = $current_user->id;
		$eapm->assigned_user_id = $current_user->id;

        if(empty($eapm->id)){
            $eapmBean = EAPM::getLoginInfo($eapm->application,true);
            if($eapmBean){
                $eapm->id = $eapmBean->id;
            }
        }
        $eapm->validated = false;
        $eapm->save_cleanup();
        $this->apiClass->loadEAPM($eapm);
	
		$eapm->save();
		$reply = $this->apiClass->checkLogin();
        if ( !$reply['success'] ) {
        	if (array_key_exists('rawResponse', $reply)) {
        		ob_clean();
				echo json_encode(array('success' => false, 'problem' => 'auth'));
				return;
			}
			else {
				ob_clean();
				echo json_encode(array('success' => false, 'problem' => 'url'));
				return;
			}
        } else {
			
            $eapm->validated();
            ob_clean();
			echo json_encode(array('success' => true));			
        }
		
	}
	static function isConnectionsLoaded() {

	}
	public function getCommunityNavigation($communityId, $recordOwner)
	{
		$visibility = 'private';
		if (empty($communityId)){
			$head = ($recordOwner) ? $this->view->navWrapStart() . $this->view->selectCommunityMenu() . $this->view->navWrapEnd() : false;
		}
		else {
			$community = $this->apiClass->getCommunity($communityId);
			if (empty($community)) {
				$head = ($recordOwner) ? $this->view->navWrapStart() ."	<li>{$this->view->actionsEditCommunity('')}</li>". $this->view->selectCommunityMenu() . $this->view->navWrapEnd() : '';
				
				return array('head' => "<div id='community_name'>{$this->language['LBL_PRIVATE_COMMUNITY']}</div>{$head}", 'visibility' => 'denied', 'members' => array());
			}
			if ($community == "no_connection") {
				return array('head' => "<div id='community_name'>{$this->language['LBL_NO_CONNECTION']}</div>", 'visibility' => 'denied', 'members' => array());
			}
			$navigation = ($recordOwner) ? $this->view->navWrapStart()."	<li>{$this->view->actionsEditCommunity($communityId)}</li>". $this->view->selectCommunityMenu() .$this->view->navWrapEnd() :"";
			$head = "
			<div id='community_logo'>
							<img src='{$community->getLogoLink()}'/>
					</div>
					<div id='community_name'> 
						
						<a href='".urldecode((string) $community->getLink())."' target='_blank'>".$community->getTitle()."</a>
					</div>
					{$navigation}";
			$visibility = $community->getCommunityType();
		
		}
		return array('head' => $head, 'visibility' => $visibility, 'members' => array());
	}
	
	
	private function getPagination($data, $search_text='') 
	{
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
		$tab_butt = $this->getSearch($this->tab_name, $search_text);
		$buttons = (empty($tab_butt)) ? "": "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td>".$tab_butt."</td></tr></tbody></table>";
		$pagination .= "<tr class='pagination'><td align='left'>{$buttons}</td>";
		$pagination .= "<td align='right'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td align='right'>{$start_button} {$previous_button} {$pageCount} {$next_button} {$end_button}</td></tr></tbody></table></td></tr>";
		$pagination .= "</table>";

		return $pagination;
		
	
	}

	private function getTabButtons() 
	{
		switch ($this->tab_name) {
			case 'Discussions' : return '<div class="ibm_buttons">' . $this->view->button('LBL_NEW_DISCUSSION_BUTTON', 'createIBMElement("Discussion");'). "</div>"; break;
			case 'Activities' : return '<div class="ibm_buttons">' . $this->view->button('LBL_NEW_ACTIVITY_BUTTON', 'createIBMElement("Activity");', 'newActivity') . "</div>"; break;
			case 'Bookmarks' : return '<div class="ibm_buttons">' .  $this->view->button('LBL_NEW_BOOKMARK_BUTTON', 'createIBMElement("Bookmark");')."</div>";break;
			case 'Members' : return ($this->isCommunityOwner) ?'<div class="ibm_buttons">' .  $this->view->button('LBL_ADD_MEMBER_BUTTON', 'createIBMElement("Member");')."</div>" : ""; break;
		}
		return;	
	}

	

	private function formateDate($relArray)
	{
		if ($relArray['value'] == 1 && $relArray['unit'] == 'DAY') return $this->language['LBL_UNIT_YESTERDAY'];
		else return $relArray['value']. " " .$this->language['LBL_UNIT_' . $relArray['unit']] . " " .$this->language['LBL_AGO']; 
	}
	
	public function getCommunityId() 
	{
		if(isset($this->parent_id) && !empty($this->parent_id)) {
			$connections = new ibm_connections();
			$connections->retrieve_by_string_fields(array('parent_id' => $this->parent_id));

			return $connections->community_id;
		}
	}
	
	private function getMembersArray($response)
	{
		$entries = $response->entry;
		$communityId = $this->getCommunityId();
		$res = array();
		if(!empty($entries)) {
			foreach($entries as $entry) {
				$member_name = (string) $entry->contributor->name;
				$member_id = (string) $entry->contributor->children('http://www.ibm.com/xmlns/prod/sn')->userid;
				$member_role = (string) $entry->children('http://www.ibm.com/xmlns/prod/sn')->role;
				$res [] = array(
					'member_id' => $member_id, 
					'member_name' => $member_name, 
					'member_email' => $member_email,
					'member_role' => $member_role, 
					'community_id' => $communityId);
			}
		}
		return $res;
	}

	
	public function sourceForAutoCompleteMember() 
	{
		$search_text = $this->search_text;
		$reply = $this->apiClass->getMembers("", $search_text);
		$response = new SimpleXMLElement($reply['rawResponse']);
		$member_list = $this->getMembersArray($response);
		ob_clean();
		echo json_encode($member_list);
	}

	public function loadConnectionsCommunities() 
	{
		$this->community = $this->tab_name;
		$search_text = $this->search_text;
		$page_number = $this->page_number;

		try {
			$community_list = $this->getCommunityList($this->community,$page_number,$search_text);
			echo $community_list;
			
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

	private function getSearch($tab_name,$search_string) 
	{
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
	
	public function createNewCommunity() 
	{
		global $beanList, $current_user;
		$members = array();
		$bean = new $beanList[$this->parent_type];
		$bean->retrieve($this->parent_id);
		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateCommunity.tpl';
		$smarty = new Sugar_Smarty();
		$smarty->assign('parent_type', $this->parent_type);
		$smarty->assign('parent_id', $this->parent_id);
		$smarty->assign('community_members', $this->getTeamMembers());
		$smarty->assign('language', $this->language);
		$smarty->assign('name', $bean->name);
		echo $smarty->fetch($tplName);
	}

	public function saveBookmark()
	{
		$tag_str = str_replace(' ', ',' ,$this->bookmark_tags);
		if (!empty($tag_str)) {
			$tag  = explode(',', $tag_str);
		}
		else {
			$tag = array();
		}
		 
		$href = $this->bookmark_path;
		if (strpos($href,"://") === false) {
			$href = "http://". $href;
		}
		$this->apiClass->createBookmarkToCommunity($this->getCommunityId(), $this->bookmark_name, $href, $this->bookmark_description, $tag, $this->bookmark_important);
		
	}
	
	public function postNote() {
	  return $this->apiClass->postNote($this->getCommunityId(), $this->content);
	}

	public function saveActivity()
	{
		global $timedate;
		if (!empty($this->due_date)){
			$this->due_date = $timedate->asDbDate($timedate->fromUserDate($this->due_date));///$timedate->convert_to_gmt_datetime($this->due_date);
		}
		$tag_str = str_replace(' ',',',$this->activity_tags);
		if (!empty($tag_str)) {
			$tag  = explode(',', $tag_str);
		}
		else {
			$tag = array();
		}
		ob_clean();
		return json_encode( $this->apiClass->createActivity($this->getCommunityId(), $this->activity_name, $this->activity_goal, $tag, $this->due_date));
	}
	
	public function saveActivitySection()
	{
		$this->apiClass->createActivitySection($this->ibm_parent_id, $this->name);
		ob_clean();
		echo json_encode(array('id'=> $this->ibm_parent_id));
	}
	
	public function saveActivityToDo()
	{
		global $timedate;
		if (!empty($this->due_date)){
			$this->due_date =  $timedate->asDbDate($timedate->fromUserDate($this->due_date));
		}
		$tag_str = str_replace(' ',',',$this->tags);
		if (!empty($tag_str)) {
			$tag  = explode(',', $tag_str);
		}
		else $tag = array();
		if (!empty($this->ibm_section_id)) {
			$this->apiClass->createSectionToDo($this->ibm_parent_id, $this->ibm_section_id,$this->title, $this->description, $this->due_date, $tag, array('name' => $this->assigned_name, 'id' => $this->assigned_id));
		}
		else {
			$this->apiClass->createActivityToDo($this->ibm_parent_id, $this->title, $this->description, $this->due_date, $tag, array('name' => $this->assigned_name, 'id' => $this->assigned_id));
		}
		ob_clean();
		echo json_encode(array('id'=> $this->ibm_parent_id));
		
	}
	
	public function saveActivityEntry()
	{
		$tag_str = str_replace(' ',',',$this->tags);
		if (!empty($tag_str)) {
			$tag  = explode(',', $tag_str);
		}
		else {
			$tag = array();
		}
		if (!empty($this->ibm_section_id)) {
			$this->apiClass->createSectionEntry($this->ibm_parent_id, $this->ibm_section_id, $this->title, $this->description,$tag);
		}
		else {
			$this->apiClass->createActivityEntry($this->ibm_parent_id, $this->title, $this->description,$tag);
		}
		ob_clean();
		echo json_encode(array('id'=> $this->ibm_parent_id));
	}
	
	public function saveDiscussion()
	{
		$tag_str = str_replace(' ',',',$this->discussion_tags);
		if (!empty($tag_str)) $tag  = explode(',', $tag_str);
		else $tag = array();
		$this->apiClass->createDiscussion($this->getCommunityId(),$this->discussion_name,  $this->discussion_content_form, $tag, $this->discussion_is_question);
	}
	
	public function commentFile()
	{
		$result = $this->apiClass->commentFile($this->ibm_user_id, $this->ibm_doc_id, $this->comment_content);
		ob_clean();
		echo json_encode(array('id' => $this->ibm_doc_id,'result'=> $result));
	}
	
	public function likeFile()
	{
		$result = $this->apiClass->likeFile($this->user_id, $this->document_id);
		ob_clean();
		echo json_encode(array('id' => $this->document_id, 'result'=> $result));
	}
	
	public function deleteFile()
	{
		$this->apiClass->deleteFile($this->document_id);
	}
	
	public function deleteCommunity()
	{
		$res = $this->apiClass->deleteCommunity($this->community_id);
		ob_clean();
		echo json_encode(array('deleted' => $res));
		
	}
	
	public function markToDoCompleted()
	{
		$result = $this->apiClass->markToDoCompeted($this->todo_id, $this->completed);
		ob_clean();
		echo json_encode(array('result' => $result, 'id' => $this->activity_id));
		
	}

	public function getCommunityBookmarks()
	{
		$list = $this->getBookmarksList($this->getCommunityId(), $this->page_number, $this->search_text);
		$tab = 'bookmark';
		$reply = $this->display($tab, 1,'');
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => $tab .'_list'));
	}
	
	public function loadUpdatesList()
	{
		$reply = $this->view->button('LBL_UPDATES_ALL','showUpdates("all");return false;');
		$reply .= $this->view->button('LBL_UPDATES_STATUS','showUpdates("status");return false;');
		$list = $this->getUpdatesList($this->getCommunityId(), $this->page_number);
		$reply .= $this->display('update', 1,'');
		ob_clean();	
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => 'update_list', 'page' => $this->page_number));
	}
	
	public function getCommunityDiscussions()
	{
		$list = $this->getDiscussionsList($this->getCommunityId(),$this->page_number, $this->search_text);
		$reply = $this->display('discussion', 2, '' );
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => 'discussion_list'));
	}
	
	public function getCommunityActivities()
	{
		$search_text = $this->search_text; 
		$page = $this->page_number;
		global $beanList, $current_user;
		$communityId = $this->getCommunityId();

		$list = $this->getActivitiesList($communityId, $search_text, $page);
		$tab = 'activity';
		$reply = $this->display($tab, 2,'', '');
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => $tab .'_list'));
	}
	
	private function display($tab, $col_count, $col1 = "", $col2 = "")
	{
		if ($col_count == 1){
			$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/OneCol.tpl';
		}
		else if ($col_count == 2){
			$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/TwoCol.tpl';
		}
		else return;
		$smarty = new Sugar_Smarty();
		$smarty->assign('ibm_col1', $col1);
		$smarty->assign('ibm_col2', $col2);
		$smarty->assign('ibm_header', $this->getTabButtons());
		$smarty->assign('ibm_tab',$tab);
		return $smarty->fetch($tplName);
	}
	
	public function replyDiscussion()
	{
		if ($this->reply_to == 'reply'){
			$this->apiClass->replyDiscussionReply($this->ibm_parent_id, $this->reply_title, $this->reply_content);
			$topic_id = $this->ibm_discussion_id;
			ob_clean();
			echo json_encode(array('topic_id' => $topic_id));
		}
		else{
			$this->apiClass->replyDiscussion($this->ibm_parent_id, $this->reply_title, $this->reply_content);
			ob_clean();
			echo json_encode(array('topic_id' =>$this->ibm_parent_id));
		}
		
	}
	
	

	public function uploadNewFile() 
	{
		if (empty($_FILES['file_el']) || $_FILES['file_el']['error'] > 0) return;
		$fileToUpload = $_FILES['file_el']['tmp_name'];
		$mimeType = $_FILES['file_el']['type'];
		
		$reply = $this->apiClass->uploadFile($fileToUpload, $this->file_name, $mimeType,$this->visibility);	
		if (!empty($reply->docId)){
			$this->apiClass->shareMyFileWithCommunity($this->getCommunityId(), $reply->docId);
		}
	}
	
	public function downloadFile() 
	{
		$document_id = $this->documentId;
		$document_name = htmlspecialchars_decode($this->documentName);
		$content = $this->apiClass->downloadFile($document_id);
		header("Pragma: public");
		header("Cache-Control: maxage=1, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".$document_name."\";");
        header("Content-Length: " . $this->documentSize);
		header("X-Content-Type-Options: nosniff");
		header("Expires: 0");
		set_time_limit(0);
		ob_clean();
		echo $content;
	}

	public function addMember() 
	{
		$communityId = $this->getCommunityId();
		$this->apiClass->addMemberToCommunity($this->member_id, $communityId, $this->member_role);
	}
	
	public function removeMember() 
	{
		$communityId = $this->getCommunityId();
		$this->apiClass->removeMemberFromCommunity($this->member_id, $communityId);
	}

	public function saveCommunitySelection() 
	{
		$connections = new ibm_connections();

		$connections->retrieve_by_string_fields(array('parent_id' => $this->parent_id));

		$connections->parent_type = $this->parent_type;
		$connections->parent_id = $this->parent_id;
		$connections->community_id = $this->community_id;
		$connections->date_modified = $GLOBALS['timedate']->nowDb();
		if (empty($this->community_id)) $connections->deleted = 1;
		else $connections->deleted = 0;
		$connections->save();
		$navig = $this->getCommunityNavigation($this->community_id, true);
		$header = $navig['head'];
		ob_clean();
		echo json_encode(array('content' => $header , 'community_id' => $this->community_id));
	}

	public function saveCommunity() 
	{
		global $current_user;
		$tags = explode(",", $this->community_tags);
		if (!empty($this->ibm_id)) {
			$this->apiClass->editCommunity($this->ibm_id,$this->community_name, $this->description, $this->access, $tags, $this->logo);
			$communityId = $this->ibm_id;
		}
		else {
			$communityId = $this->apiClass->createCommunity($this->community_name, $this->description, $this->access, $tags, $this->logo);
		}
		if(!empty($communityId)) {
			$members = $this->getCommunityMemberArray();

			foreach($members as $member)
			{
				if ($member['member_role'] == 'owner') {
					if (in_array($member['member_id'], $this->owner_list)) {
						$key = array_search($member['member_id'], $this->owner_list);	
						$this->owner_list[$key] = '';
					}
					else {
						$this->apiClass->removeMemberFromCommunity($member['member_id'], $communityId);
					}
				}
				else if ($member['member_role'] == 'member') {
					if (in_array($member['member_id'], $this->member_list)) {
						$key = array_search($member['member_id'], $this->member_list);	
						$this->member_list[$key] = '';
					}
					else {
						$this->apiClass->removeMemberFromCommunity($member['member_id'], $communityId);
					}
				}
				
			}
			
			for($i = 0; $i < count($this->member_list); $i++) {
				$member_id = $this->member_list[$i];
				if (empty($member_id)) {
					continue;
				}
				$this->apiClass->addMemberToCommunity($member_id, $communityId, 'member');
			}
			for($i = 0; $i < count($this->owner_list); $i++) {
				$member_id = $this->owner_list[$i];
				if (empty($member_id)) {
					continue;
				}
				$this->apiClass->addMemberToCommunity($member_id, $communityId, 'owner');
			}
		}
	}

	

	public function loadFilesList() 
	{
		global $beanList, $current_user;
		$communityId = $this->getCommunityId();
		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateFile.tpl';
		$smarty = new Sugar_Smarty();
		$smarty->assign('language', $this->language);
		$smarty->assign('parent_type', $this->parent_type);
		$smarty->assign('parent_id', $this->parent_id);
		$profile_body = $smarty->fetch($tplName);
		
		
		$list = $this->getFilesList($communityId, $this->page_number, $this->search_text);
		$tab = 'file';
		$reply = $this->display($tab, 2,'', $profile_body);
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => $tab .'_list'));
			
	}
	
	public function getCommunityWiki()
	{
		global $beanList, $current_user;
		$communityId = $this->getCommunityId();
		if(empty($communityId)) {
			return;
		}
		$list = $this->getCommunityWikiList($this->getCommunityId(), $this->page_number, $this->search_text);
		
		$tab = 'wiki';
		$reply = $this->display($tab, 2,'', '');
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => $tab .'_list'));
	}
	
	function getCommunityWikiList($communityId, $page, $searchText)
	{
		$entries  = $this->apiClass->getCommunityWiki($communityId, $page, $searchText);
		$list = '';

		if(!empty($entries)) {
			foreach($entries as $entry) {
				$arr = array();
				$arr['id'] = $entry->getId();
				$arr['title'] = $entry->getTitle();
				$arr['content'] = $entry->getContentSrc();
				$list .= $this->view->wiki($arr);
			}
		}
		return $list;
	}
	
	function getCommunityWikiContent()
	{
		$reply  = $this->apiClass->getCommunityWikiContent($this->comm_id, $this->rec_id);
		if (strpos($reply, '>]>') < 200){
			$reply = substr($reply, strpos($reply, '>]>') + 3);
		}
		ob_clean();
		echo json_encode(array('content' => $reply));
		
	}
	
	
	function getCommunityBlog()
	{
		$list = $this->getCommunityBlogList($this->getCommunityId(),$this->page_number, $this_search_text);
		$tab = 'blog';
		$reply = $this->display($tab, 2,'', '');
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => $tab .'_list'));
	}
	
	function loadOverview()
	{
		global $beanList,$current_user;
		$communityId = $this->getCommunityId();
		$recordOwner = false;
		$bean = new $beanList[$this->parent_type];
		$bean->retrieve($this->parent_id);
		$recordOwner = $bean->isOwner($current_user->id);
		
		$list = $this->getUpdatesList($communityId);
		$community = $this->apiClass->getCommunity($communityId);
		$counts = $this->apiClass->getOverviewCounts($communityId);
		if (!empty($community)) $member_count = $community->getMemberCount();
		

		$html = "<table cellpadding='0' cellspacing='0' width='100%' border='0' class='pagination'>";
		$html .= "<tr><td align='left'></td></tr>";
		$html .= "<tr><td width='100%' >";
		$html .= '<div id="Overview_list" style="float:left;width:49%;height:250px;overflow:scroll;overflow-y:auto;overflow-x:hidden;display:inline;">';
		$html .= $list;
		$html .= '</div>';
		$html .= '<div id="Count_list" style="float:right;width:49%;height:250px;display:inline;">';
		$html .= "<table width='100%'><tr><td>";
		$html .= "<a href='' onclick='loadTabData(\"Updates\" ,1, \"\"); return false;' >{$counts['update']} {$this->language['LBL_UPDATES_TAB']}</a><br>";
		$html .= "<br><a href='#' onclick='loadTabData(\"Activities\",1, \"\");  return false;' >{$counts['activity']} {$this->language['LBL_ACTIVITIES_TAB']}</a><br>";
		$html .= "<br><a href='#' onclick='loadTabData(\"Files\",1, \"\"); return false;' >{$counts['file']} {$this->language['LBL_FILES_TAB']}</a><br>";
		$html .= "<br><a href='#' onclick='loadTabData(\"Discussions\",1, \"\"); return false;' >{$counts['discussion']} {$this->language['LBL_DISCUSSIONS_TAB']}</a><br>";
		$html .= "</td><td>";
		$html .= "<a href='#' onclick='loadTabData(\"Bookmarks\",1, \"\"); return false;' >{$counts['bookmark']} {$this->language['LBL_BOOKMARKS_TAB']}</a><br>";
		$html .= "<br><a href='#' onclick='loadTabData(\"Blog\",1, \"\"); return false;' >{$counts['blog']} {$this->language['LBL_BLOG_TAB']}</a><br>";
		$html .= "<br><a href='#' onclick='loadTabData(\"Wiki\",1, \"\"); return false;' >{$counts['wiki']} {$this->language['LBL_WIKI_TAB']}</a><br>";
		$html .= "<br><a href='#' onclick='return false;'>{$member_count} {$this->language['LBL_MEMBERS']}</span>";
		$html .= "</td></tr></table>";
		$html .= '</div>';
		$html .= "</td></tr>";
		$html .= "</table>";
		ob_clean();
		echo json_encode(array('frame' => $html , 'content' => '', 'container_id'=> ''));
	}
	
	public function loadCreationBody() 
	{
		global $timedate;
		$element = $this->element;
		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Create' . $element . '.tpl';
		$community = $this->apiClass->getCommunity($this->getCommunityId());
		if ($community == "no_connection"){
			$model_content = array(
				"header"=> $this->language['LBL_TITLE_NEW_' . strtoupper($element)],
				"body"=>$this->language['LBL_NO_CONNECTION']
			);

			ob_clean();
			echo json_encode($model_content);
			return;
		}
		$smarty = new Sugar_Smarty();
		$smarty->assign('parent_type', $this->parent_type);
		$smarty->assign('parent_id', $this->parent_id);
		$smarty->assign('language', $this->language);
		$smarty->assign('community_members', $this->getCommunityMemberArray());
		$smarty->assign('cal_dateformat', $timedate->get_cal_date_format());
		$smarty->assign('jscalendarImage', SugarThemeRegistry::current()->getImageURL('jscalendar.gif'));
		
		if (isset($this->ibm_user_id) && !empty($this->ibm_user_id)) $smarty->assign('user_id', $this->ibm_user_id);
		if (isset($this->ibm_doc_id) && !empty($this->ibm_doc_id)) $smarty->assign('document_id', $this->ibm_doc_id);
		//if (isset($this->ibm_topic_id) && !empty($this->ibm_topic_id)) $smarty->assign('topic_id', $this->ibm_topic_id);
		$smarty->assign('type_description', $this->language["LBL_PUBLIC_ACCESS_DESCRIPTION"]);
		if (isset($this->community_id) && !empty($this->community_id)) $smarty->assign('community_id', $this->community_id);
		if (isset($this->reply_to) && !empty($this->reply_to)) $smarty->assign('reply_to', $this->reply_to);
		if (isset($this->ibm_parent_id) && !empty($this->ibm_parent_id)) $smarty->assign('ibm_parent_id', $this->ibm_parent_id);
		if (isset($this->ibm_section_id) && !empty($this->ibm_section_id)) $smarty->assign('ibm_section_id', $this->ibm_section_id);
		if (isset($this->ibm_discussion_id) && !empty($this->ibm_discussion_id)) $smarty->assign('ibm_discussion_id', $this->ibm_discussion_id);
		$body = $smarty->fetch($tplName);
		$model_content = array(
			"header"=> $this->language['LBL_TITLE_NEW_' . strtoupper($element)],
			"body"=> $body,
		);
		ob_clean();
		echo json_encode($model_content);
	}
	
	private function getTeamMembers()
	{
		global $beanList;
		require_once('modules/Teams/Team.php');
		$bean = new $beanList[$this->parent_type];
		$bean->retrieve($this->parent_id);
		if(empty($bean->team_id)) {
			return array();
		}
		 
		$team = new Team();
		$team->retrieve($bean->team_id);
		$users = $team->get_team_members();
		$eapmBean = new EAPM();
		$ids_array = array();
		foreach($users as $user){
			$role = ($bean->assigned_user_id == $user->id) ? 'owner': 'member';
		 	$queryArray = array('assigned_user_id' => $user->id, 'application' => "Connections", 'deleted' => 0 );
           	$eapm = $eapmBean->retrieve_by_string_fields($queryArray);
		   	if (!empty($eapm->api_data)) {
				$api_data = json_decode(base64_decode($eapm->api_data), true);
				if ( isset($api_data['userId']) ) {
					$ids_array[] = array('member_id' => $api_data['userId'], 'member_name' => $user->full_name, 'member_role' => $role);
				}
			}
		}
		return $ids_array;
	}
	
	public function loadEditionBody() 
	{
		$element = $this->element;
		$id = $this->ibm_id;
		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Create' . $element . '.tpl';
		
		$smarty = new Sugar_Smarty();
		$smarty->assign('parent_type', $this->parent_type);
		
		$smarty->assign('parent_id', $this->parent_id);
		$smarty->assign('language', $this->language);
		
		$community = $this->apiClass->getCommunity($id);
		if ($community == "no_connection"){
			$model_content = array(
				"header"=> $this->language['LBL_EDITION_' . strtoupper($element)],
				"body"=>$this->language['LBL_NO_CONNECTION']
			);

			ob_clean();
			echo json_encode($model_content);
			return;
		}
		$smarty->assign('name', $community->getTitle());
		$smarty->assign('community_id', $community->getId());
		$smarty->assign('type', $community->getCommunityType());
		$smarty->assign('type_description', $this->language["LBL_". strtoupper($community->getCommunityType()) . "_ACCESS_DESCRIPTION"]);
		
		$smarty->assign('description', $community->getDescription());
		
		$tags = $community->getTags();
		
		if (count($tags) > 0) $tag_str =  implode(', ', $tags);
		$smarty->assign('tags', $tag_str);
		$smarty->assign('community_members', $this->getCommunityMemberArray()); 
		$body = $smarty->fetch($tplName);

		$model_content = array(
			"header"=> $this->language['LBL_EDITION_' . strtoupper($element)],
			"body"=>$body
		);

		ob_clean();
		echo json_encode($model_content);
	}

	function getUpdatesList($communityId, $pNum = 1 )
	{
		$response = $this->apiClass->getUpdates($communityId, $pNum);
		$entries  = $response['list'];
		$reply = "";
		if(!empty($entries)) {
			foreach($entries as $entry) {
				$arr = array();
				$arr['content'] = str_replace("href=","target='_blank' href=",$entry->getContent());
				$arr['type'] = ( strpos($entry->getLink(), 'community/updates') ) ? 'status_update' : 'recent_update'; 
				$arr['contributor'] = $entry->getAuthor();
				$arr['updated'] = $this->formateDate($entry->getFormattedUpdatedDate());
				$reply .= $this->view->update($arr);
			}
		}
		return $reply;
	}
	
	function getBookmarksList($communityId, $page, $search_text)
	{
		$entries  = $this->apiClass->getCommunityBookmarks($communityId, $page, $search_text);

		$reply = '';

		if(!empty($entries)) {
			$counter = 0;
			$reply .= "<tr>";
			foreach($entries as $entry) {
				if ($counter == 3)
				{
					$reply .= "</tr><tr><td></td></tr><tr>";
					$counter = 0;
					
				}
				$arr  = array();
				$arr['contributor'] = $entry->getAuthor();
				$arr['link'] = $entry->getLink();
				$arr['title'] = $entry->getTitle();
				$arr['updated'] = $this->formateDate($entry->getUpdatedDate());
				$reply .= $this->view->bookmark($arr);
				
				$counter++;
			}
			$reply .= "</tr>";
		}
		else {
			return '';
		}

		return $reply;
	}
	
	function getDiscussionsList($communityId, $page = 1, $searchText = '')
	{
		$entries  = $this->apiClass->getCommunityDiscussions($communityId, $page, $searchText);
		$reply ='';
		if(!empty($entries)) {
			
			foreach($entries as $entry) {
				$arr = array();
				$arr['id'] = $entry->getId();
				$arr['title'] = $entry->getTitle();
				$arr['tags'] = $entry->getTags();
				$arr['repliesCount'] = $entry->getRepliesCount();
				$arr['contributor'] = $entry->getLastPostBy();
				$arr['topicType'] = $entry->getTopicType();
				$arr['content'] = $entry->getContent();
				$arr['updated'] = $this->formateDate($entry->getUpdatedDate());
				$arr['author'] = $entry->getAuthor();
				$arr['published'] = $this->formateDate($entry->getPublishedDate());
				$arr['hiddenView'] = $this->view->discussionTitle($arr);
				$reply .= $this->view->discussion($arr);
				
			}
		}
		return $reply;
		
	}
	
	
	function getCommunityList($community, $page_number, $search_text) 
	{
		$result = $this->apiClass->getCommunities($community,$page_number,$search_text);
		$entries = $result['communities'];
		$metadata = $result['metadata'];
		$pagination_data = array(
			'totalResults' => 0,
			'itemsPerPage' => 10,
			'startIndex' => 1
		);
		if(!empty($metadata->totalResults))
			$pagination_data['totalResults'] = $metadata->totalResults;
		if(!empty($metadata->pageSize))
		{	$pagination_data['itemsPerPage'] = $metadata->pageSize;
			$pagination_data['startIndex'] = $metadata->pageSize * ($page_number-1) +1;
		}
		$reply = $this->getPagination($pagination_data,$search_text);
		$reply .= $this->view->table_start;
		if(!empty($entries)) {
			foreach($entries as $entry) {
				$communityId = urlencode($this->getCommunityId());
				$arr['id'] = $entry->getId();
				$arr['checked'] = ($entry->getId() == $communityId) ? "checked" : "";
				$arr['author'] = $entry->getAuthor();
				$arr['member_count'] = $entry->getMemberCount();
				$arr['tags'] = $entry->getTags();
				$arr['title'] = $entry->getTitle();
				$arr['logo'] = $entry->getLogoLink();
				$arr['link'] = $entry->getLink();
				$arr['updated'] = $this->formateDate($entry->getUpdatedDate());
				$arr['type'] = $entry->getCommunityType();
				
				$reply .= $this->view->community($arr);
			}
		}
		else {
			$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}
		$reply .= $this->view->table_end;
		$reply .= "<table><tr><td>
						<input class='button' type='button' value='Select' onclick='selectCommunity();' />
						<input class='button' type='button' value='Cancel' onclick='closeCommunityPanel();' />
						<input type='hidden' name='community_id_selection' id='community_id_selection' value='{$communityId}'>
						</td></tr></table>";
		return $reply;
	}
	
	
	function getActivityNode()
	{
		$activityId = $this->ibm_activity_id;
		$nodeId = $this->ibm_node_id;
		$entry  = $this->apiClass->getNode($activityId, $nodeId);
		$reply = $this->view->scrollable_div_start;
		$reply .= $this->view->table_start;

		if(!empty($entry)) {
			$arrs = array();
			$type = $entry->getType();
				switch ($type)
				{
					case 'todo': $reply .= $this->getToDoViewModal($entry); break;
					case 'entry': $reply .= $this->getEntryViewModal($entry); break;
					case 'section': $reply .= $this->getSectionView($entry); break;
				}
			$reply .= "<tr><td>";
			$attach_arr = $entry->getAttachments();
				for($i = 0; $i < count($attach_arr); $i++){
					$reply .= "<br>"."<a href='{$attach_arr[$i]['href']}'>{$attach_arr[$i]['fileName']}</a>";
					
				}
			$reply .= "</td></tr>";
			$nodes = $entry->listNodes();
			foreach($nodes as $node) {
				switch ($node->getType())
				{
					case 'todo': $reply .= $this->getToDoViewModal($node); break;
					case 'reply': 
									$arr = array(
										'contributor' => $node->getContributor(),
										'content' => $node->getContent(),
										'updated' => $this->formateDate($node->getFormattedUpdatedDate())
									);
									$reply .= $this->view->activityReply($arr);
									break;
				}
				
			}
			
		}
		else {
			$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}
		$reply .= $this->view->table_end;
		$reply .= $this->view->scrollable_div_end;
		$content = array(
			"header"=> $this->language['LBL_ACTIVITY_NODE'],
			"body"=> $reply
		);
		ob_clean();
		echo json_encode($content);
	}
	
	
	function getDiscussion()
	{
		$forumId = $this->forum_id;
		$entries  = $this->apiClass->getForumReplies($forumId);
		
		$reply = $this->view->button('LBL_REPLY', 'createIBMElement("DiscussionReply","&ibm_parent_id='.$forumId.'&reply_to=topic");' );
		$reply .= "<ul class='discussionTread'>";
		if(!empty($entries)) {
			foreach($entries as $entry) {
				$id = $entry->getId();
				
				if (empty($arrs[$id])) $arrs[$id] = array(); 
				$parent_arr[$id] = $entry->getInReplyTo();
				$arrs[$id]['id'] = $id;
				$arrs[$id]['title'] = $entry->getTitle();
				$arrs[$id]['author'] = $entry->getAuthor();
				$arrs[$id]['content'] = $entry->getContent();
				$arrs[$id]['parent_id'] = $entry->getInReplyTo();
				$arrs[$id]['topic_type'] = $entry->getTopicType();
				$arrs[$id]['updated'] = $this->formateDate($entry->getUpdatedDate());
				$arrs[$id]['ordered'] = false;
				$arrs[$id]['forum_id'] = $forumId;
				$arrs[$id]['attachments'] = $entry->getAttachments();
			}
			
			$arr = $this->getChildForParent($parent_arr,$forumId);

			$valid = true; 
			while($valid) 
			{ 
				   $oldCount  = count($arr,COUNT_RECURSIVE); 
				   
				   $this->applyTreeFunctionToArray($arr, $parent_arr);
				   
				   $newCount = count($arr,COUNT_RECURSIVE); 
				   if ($oldCount == $newCount)  // if there is no change, exit... 
							   $valid = false;     
			} 
			$order = array();
			$order += $this->showTreeDiscussion($arr, 0);
			
			foreach($order as $ord => $deep){
				$arr = $arrs[$ord];
				$arr['deep'] = $deep;
				$reply .= $this->view->discussionReply($arr);
			}
		}
		else {
			//$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}
		$reply .= "</ul>";
		//$reply .= $this->view->table_end;
		$content = array("content"=> $reply,);
		ob_clean();
		echo json_encode($content);
	}
	
	
	public function getActivity()
	{
		$id = $this->activity_id;
		$entries  = $this->apiClass->getActivityNodes($id);

		$reply = $this->view->table_start;
		$reply .= '<div class="ibm_buttons">' ;
		$reply .= $this->view->button('LBL_ADD_SECTION', 'createIBMElement("ActivitySection","&ibm_parent_id='.$id.'");', 'add');
		$reply .= $this->view->button('LBL_ADD_TODO', 'createIBMElement("ActivityToDo","&ibm_parent_id='.$id.'");', 'add');
		$reply .= $this->view->button('LBL_ADD_ENTRY', 'createIBMElement("ActivityEntry","&ibm_parent_id='.$id.'");' ,'add');
		$reply .= "</div>";
		if(!empty($entries)) {
			foreach($entries as $entry) {
				$type = $entry->getType();
				switch ($type)
				{
					case 'todo': $reply .= $this->getToDoView($entry); break;
					case 'entry': $reply .= $this->getEntryView($entry); break;
					case 'section': $reply .= $this->getSectionView($entry); break;
				}
			}
		}
			
		else {
			$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}
		$reply .= $this->view->table_end;
		$content = array("content" => $reply);
		ob_clean();
		echo json_encode($content);
	}
	
	private function getToDoView($entry)
	{
		$arr = array();
		$arr['id'] = $entry->getId();
		$arr['title'] = $entry->getTitle();
		$arr['parent_id'] = $this->activity_id;
		$arr['dueDate'] = $entry->getDueDate();
		$nodes = $entry->listNodes();
		$has_todo = false;
		$has_comment = false;
		foreach($nodes as $el){
			if ($el->getType() == "reply") $has_comment = true;
			if ($el->getType() == "todo") $has_todo = true;
			if ($has_todo && $has_comment) break;
		}
		
		$arr['hasAttachments'] = (count($entry->getAttachments()) > 0) ? true : false;		
		$arr['hasTodo'] = $has_todo;
		$arr['hasComment'] = $has_comment;
		$arr['contributor'] = $entry->getContributor();
		$arr['updated'] = $this->formateDate($entry->getFormattedUpdatedDate());
		$arr['completed'] = $entry->getCompleted();
		$arr['assignedTo'] = $entry->getAssignee();
						
		return $this->view->activityToDo($arr);
	}
	
	private function getToDoViewModal($entry)
	{
		$arr = array();
		$arr['id'] = $entry->getId();
		$arr['title'] = $entry->getTitle();
		$arr['parent_id'] = $this->activity_id;
		$arr['dueDate'] = $entry->getDueDate();
		$nodes = $entry->listNodes();
		$has_todo = false;
		$has_comment = false;
		foreach($nodes as $el){
			if ($el->getType() == "reply") $has_comment = true;
			if ($el->getType() == "todo") $has_todo = true;
			if ($has_todo && $has_comment) break;
		}
		
		$arr['hasAttachments'] = (count($entry->getAttachments()) > 0) ? true : false;		
		$arr['hasTodo'] = $has_todo;
		$arr['hasComment'] = $has_comment;
		$arr['contributor'] = $entry->getContributor();
		$arr['updated'] = $this->formateDate($entry->getFormattedUpdatedDate());
		$arr['completed'] = $entry->getCompleted();
		$arr['assignedTo'] = $entry->getAssignee();
						
		return $this->view->activityToDoModal($arr);
	}
	
	private function getEntryView($entry)
	{
		$arr = array();
		$arr['id'] = $entry->getId();
		$arr['title'] = $entry->getTitle();
		$arr['parent_id'] = $this->activity_id;
		$nodes = $entry->listNodes();
		$has_todo = false;
		$has_comment = false;
		foreach($nodes as $el){
			if ($el->getType() == "reply") $has_comment = true;
			if ($el->getType() == "todo") $has_todo = true;
			if ($has_todo && $has_comment) break;
		}
		
		$arr['hasAttachments'] = (count($entry->getAttachments()) > 0) ? true : false;		
		$arr['hasTodo'] = $has_todo;
		$arr['hasComment'] = $has_comment;
		$arr['contributor'] = $entry->getContributor();
		$arr['updated'] = $this->formateDate($entry->getFormattedUpdatedDate());
						
		return $this->view->activityEntry($arr);
		
	}
	private function getEntryViewModal($entry)
	{
		$arr = array();
		$arr['id'] = $entry->getId();
		$arr['title'] = $entry->getTitle();
		$arr['parent_id'] = $this->activity_id;
		$nodes = $entry->listNodes();
		$has_todo = false;
		$has_comment = false;
		foreach($nodes as $el){
			if ($el->getType() == "reply") $has_comment = true;
			if ($el->getType() == "todo") $has_todo = true;
			if ($has_todo && $has_comment) break;
		}
		
		$arr['hasAttachments'] = (count($entry->getAttachments()) > 0) ? true : false;		
		$arr['hasTodo'] = $has_todo;
		$arr['hasComment'] = $has_comment;
		$arr['contributor'] = $entry->getContributor();
		$arr['updated'] = $this->formateDate($entry->getFormattedUpdatedDate());
						
		return $this->view->activityEntryModal($arr);
		
	}
	
	private function getSectionView($entry)
	{
		$arr = array();
		$arr['id'] = $entry->getId();
		$arr['title'] = $entry->getTitle();
		$arr['parent_id'] = $this->activity_id;
		$arr['content'] = '';		
		$nodes = $entry->listNodes();
		
		foreach ($nodes as $node)
		{
			$node_type = $node->getType();
			if ($node_type == "todo"){
				$arr['content'] .= $this->getToDoView($node);
			}
			else if ($node_type == "entry"){
				$arr['content'] .= $this->getEntryView($node);
			}
		}				
		return $this->view->activitySection($arr);
	}
	
	private function getChildForParent($arr,$id)
	{
		$res = array();
		foreach($arr as $key => $elem){
			if($elem == $id)
			$res[$key] = '';
		}
		return $res;
	}
	
	private function treeFunction(&$item, $key, $arrs) 
	{ 
		$res = $this->getChildForParent($arrs, $key); 
		if (!empty($res)) { 
			$item = $res;
		}
	}
	
	private function showTreeDiscussion($array, $l){
		$res = array();
		$l ++;
		foreach($array as $key=>$value){
			$res[$key] = $l;
			if(!empty($value)){
				$res += $this->showTreeDiscussion($value, $l);
			}
		}
		return $res;
	}
	
	private function applyTreeFunctionToArray(&$input, $userdata = '')
	{
		if (!is_array($input)) {
			return false;
		}
		foreach ($input as $key => $value) {
			if (is_array($input[$key])) {			
				$this->applyTreeFunctionToArray($input[$key], $userdata);
			} else {
				$saved_value = $value;
				$this->treeFunction($value, $key, $userdata);		

				if ($value != $saved_value) {
					$input[$key] = $value;
				}
			}
		}
		return true;
	}
	
	
	public function getDiscussionModal()
	{
		$forumId = $this->forum_id;
		$entries  = $this->apiClass->getForumReplies($forumId);

		if(!empty($entries)) {
			$arrs = array();
			$parent_arr = array();
			foreach($entries as $entry) {
				if ($entry->isDeleted()) continue;
				$id = $entry->getId();
				
				if (empty($arrs[$id])) $arrs[$id] = array(); 
				$parent_arr[$id] = $entry->getInReplyTo();
				$arrs[$id]['id'] = $id;
				$arrs[$id]['title'] = $entry->getTitle();
				$arrs[$id]['author'] = $entry->getAuthor();
				$arrs[$id]['content'] = $entry->getContent();
				$arrs[$id]['parent_id'] = $entry->getInReplyTo();
				$arrs[$id]['topic_type'] = $entry->getTopicType();
				$arrs[$id]['updated'] = $this->formateDate($entry->getUpdatedDate());
				$arrs[$id]['ordered'] = false;
				$arrs[$id]['attachments'] = $entry->getAttachments();
				
			}
			
			$arr = $this->getChildForParent($parent_arr,$forumId);

			$valid = true; 
			while($valid) 
			{ 
				   $oldCount  = count($arr,COUNT_RECURSIVE); 
				   
				   $this->applyTreeFunctionToArray($arr, $parent_arr);
				   
				   $newCount = count($arr,COUNT_RECURSIVE); 
				   if ($oldCount == $newCount)  // if there is no change, exit... 
							   $valid = false;     
			} 
			$order = array();
			$order += $this->showTreeDiscussion($arr, 0);
			
			$reply = "<div id='discussion_modal' class='div-modal'>";
			$reply .= $this->view->table_start;
		
			foreach($order as $ord => $deep){
				$arr = $arrs[$ord];
				$arr['deep'] = $deep;
				$reply .= $this->view->discussionModal($arr);
			}
			$reply .= $this->view->table_end;
			$reply .= "</div>";
			
		}
		else {
			$reply .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
		}
		
		$content = array(
			"header"=> $this->language['LBL_DISCUSSION'],
			"body"=>$reply
		);
		ob_clean();
		echo json_encode($content);
	}
	public function getFilesList($communityId, $page = 1, $searchText = "")
	{
		$entries = $this->apiClass->getFilesList($communityId, $page, $searchText);
		$file_list = '';
		if(!empty($entries)) {
			foreach($entries as $entry) {
			
				$arr = array();
					
				$arr['author'] = $entry->getAuthor();
				$arr['uploaded'] = $this->formateDate($entry->getFormattedUpdatedDate());
				$arr['id'] = $entry->getId();
				$arr['title'] = $entry->getTitle();
				$arr['tags'] = $entry->getTags();
				$arr['visibility'] = $entry->getVisibility();
				$arr['commentsCount'] = $entry->getCommentsCount();
				$arr['recomendationsCount'] = $entry->getRecomendationsCount();
				$arr['viewLink'] = $entry->getViewLink();
				$arr['downloadsCount'] = $entry->getDownloadsCount();
				$arr['version'] = $entry->getVersion();
				$arr['fileSize'] = $entry->getFileSize();
				
				$file_list .= $this->view->fileSection($arr);
					
			}
		}
		return $file_list;
	}
	public function getActivityCompletion()
	{
		$this_activity = $this->apiClass->getActivity($this->activityId);
		ob_clean();
		echo json_encode(array( 'id' => $this->activityId, 'completion' => $this_activity->getCompletion()));
	}
	public function getActivitiesList($communityId, $searchText, $page)
	{
		$entries  = $this->apiClass->getActivitiesList($communityId, $searchText, $page);
		$list = '';

			if(!empty($entries) && empty($entries['message'])) {
				foreach($entries as $entry) {
					$this_activity = $this->apiClass->getActivity($entry->getId());
					
					$arr = array();
					
					$arr['contributor'] = $entry->getContributor();
					$arr['last_updated'] = $this->formateDate($entry->getFormattedUpdatedDate());
					$arr['id'] = $entry->getId();
					$arr['title'] = $entry->getTitle();
					$arr['content'] = $entry->getContent();
					$arr['commentsCount'] = $this_activity->getCommentsCount();
					$arr['webEditUrl'] = $entry->getWebEditUrl();
					$arr['dueDate'] = $entry->getDueDate();
					$arr['completion'] = $this_activity->getCompletion();
					$arr['completed'] = $entry->isCompleted();
					$list .= $this->view->activity($arr);
				}
			}
			else if  (!empty($entries['message'])){
				switch ($entries['message']){
					case 'widget_is_not_activated': $list .= "<tr><td>{$this->language['LBL_WIDGET_IS_NOT_ACTIVATED']}</td></tr>";break;
				}
			}
			
			
			else{
				return '';//$list .= "<tr><td>{$this->language['LBL_NO_DATA']}</td></tr>";
			}

		return $list;
	}
	
	
	public function getCommunityBlogList($communityId, $page = 1, $searchText = '')
	{
		$entries  = $this->apiClass->getCommunityBlog($communityId, $page, $searchText);
		$list = '';

			if(!empty($entries)) {
				foreach($entries as $entry) {
					$arr = array();
					
					$arr['contributor'] = $entry->getAuthor();
					$arr['last_updated'] = $this->formateDate($entry->getPublishedDate());
					$arr['id'] = $entry->getId();
					$arr['title'] = $entry->getTitle();
					$arr['content'] = $entry->getContent();
					$arr['commentsCount'] = $entry->getCommentsCount;
					
					$list .= $this->view->blog($arr);
				}
			}
			else {
				return '';
			}

		return $list;
	}
	
	public function getCommunityMemberArray($comm_id = '')
	{
		if (empty($comm_id)) $comm_id = $this->getCommunityId();
		if (empty($comm_id)) return array();
		$sortBy = (empty($this->sortBy)) ? 'name' : $this->sortBy;
		$asc = (empty($this->asc)) ? true : $this->asc;
		$page = (empty($this->page_number)) ? 1 : $this->page_number;
		$reply = $this->apiClass->getMembers($comm_id, $this->search_text, $page, $sortBy, $asc);
		$response = new SimpleXMLElement($reply['rawResponse']);
		return $this->getMembersArray($response);
	}
	
	public function getCommunityMembers()
	{
		global $beanList, $current_user;
		$communityId = $this->getCommunityId();
		$members_list = $this->getCommunityMemberArray($communityId);
		$this->isCommunityOwner = false;
		foreach($members_list as $member){
			if($this->apiClass->api_data['userId'] == $member['member_id']){
				if($member['member_role'] == 'owner'){
					$this->isCommunityOwner = true;
				}
				break;
			} 
		}
		$list = '';
		foreach($members_list as $member){
			$list .= $this->view->member($member, $this->isCommunityOwner);
		}
		$tab = 'members';
		$reply = $this->display($tab, 1,'');
		ob_clean();
		echo json_encode(array('frame' => $reply, 'content' => $list, 'container_id' => $tab .'_list', 'page' => $this->page_number));
	}
	

	
}
