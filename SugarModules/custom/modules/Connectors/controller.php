<?php

require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
require_once('modules/Connectors/controller.php');
require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php');

class CustomConnectorsController extends ConnectorsController {

	public function action_Connections() {
		if(isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {
			$method = $_REQUEST['method'];
			$connectionsHelper = new ConnectionsHelper();
			
			switch ($method) {
				case 'saveEAPM':
					$connectionsHelper->saveEAPM();
					break;
				case 'getCommunityNavigation':
					$connectionsHelper->getCommunityNavigation();
					break;
				case 'getCommunityId':
					$connectionsHelper->getCommunityId();
					break;
				case 'sourceForAutoCompleteMember':
					$connectionsHelper->sourceForAutoCompleteMember();
					break;
				case 'loadConnectionsCommunities':
					$connectionsHelper->loadConnectionsCommunities();
					break;
				case 'createNewCommunity':
					$connectionsHelper->createNewCommunity();
					break;
				case 'saveBookmark':
					$connectionsHelper->saveBookmark();
					break;
				case 'postNote':
					$connectionsHelper->postNote();
					break;
				case 'saveActivity':
					$connectionsHelper->saveActivity();
					break;
				case 'saveActivitySection':
					$connectionsHelper->saveActivitySection();
					break;
				case 'saveActivityToDo':
					$connectionsHelper->saveActivityToDo();
					break;
				case 'saveActivityEntry':
					$connectionsHelper->saveActivityEntry();
					break;
				case 'saveSubcommunity':
					$connectionsHelper->saveSubcommunity();
					break;	
				case 'saveDiscussion':
					$connectionsHelper->saveDiscussion();
					break;
				case 'commentFile':
					$connectionsHelper->commentFile();
					break;
				case 'likeFile':
					$connectionsHelper->likeFile();
					break;
				case 'deleteFile':
					$connectionsHelper->deleteFile();
					break;
				case 'deleteCommunity':
					$connectionsHelper->deleteCommunity();
					break;
				case 'markToDoCompleted':
					$connectionsHelper->markToDoCompleted();
					break;
				case 'getCommunityBookmarks':
					$connectionsHelper->getCommunityBookmarks();
					break;
				case 'loadUpdatesList':
					$connectionsHelper->loadUpdatesList();
					break;
				case 'getCommunityDiscussions':
					$connectionsHelper->getCommunityDiscussions();
					break;
				case 'getCommunityActivities':
					$connectionsHelper->getCommunityActivities();
					break;
				case 'replyDiscussion':
					$connectionsHelper->replyDiscussion();
					break;
				case 'uploadNewFile':
					$connectionsHelper->uploadNewFile();
					break;
				case 'downloadFile':
					$connectionsHelper->downloadFile();
					break;
				case 'addMember':
					$connectionsHelper->addMember();
					break;
				case 'removeMember':
					$connectionsHelper->removeMember();
					break;
				case 'saveCommunitySelection':
					$connectionsHelper->saveCommunitySelection();
					break;
				case 'saveCommunity':
					$connectionsHelper->saveCommunity();
					break;
				case 'loadFilesList':
					$connectionsHelper->loadFilesList();
					break;
				case 'getCommunityWiki':
					$connectionsHelper->getCommunityWiki();
					break;
				case 'getCommunityWikiList':
					$connectionsHelper->getCommunityWikiList();
					break;
				case 'getCommunityWikiContent':
					$connectionsHelper->getCommunityWikiContent();
					break;
				case 'getCommunityBlog':
					$connectionsHelper->getCommunityBlog();
					break;
				case 'loadOverview':
					$connectionsHelper->loadOverview();
					break;
				case 'loadCreationBody':
					$connectionsHelper->loadCreationBody();
					break;
				case 'loadEditionBody':
					$connectionsHelper->loadEditionBody();
					break;
				case 'getUpdatesList':
					$connectionsHelper->getUpdatesList();
					break;
				case 'getBookmarksList':
					$connectionsHelper->getBookmarksList();
					break;
				case 'getDiscussionsList':
					$connectionsHelper->getDiscussionsList();
					break;
				case 'getCommunityList':
					$connectionsHelper->getCommunityList();
					break;
				case 'getActivityNode':
					$connectionsHelper->getActivityNode();
					break;
				case 'getDiscussion':
					$connectionsHelper->getDiscussion();
					break;
				case 'getActivity':
					$connectionsHelper->getActivity();
					break;
				case 'getDiscussionModal':
					$connectionsHelper->getDiscussionModal();
					break;
				case 'getFilesList':
					$connectionsHelper->getFilesList();
					break;
				case 'getActivityCompletion':
					$connectionsHelper->getActivityCompletion();
					break;
				case 'getActivitiesList':
					$connectionsHelper->getActivitiesList();
					break;
				case 'getCommunityBlogList':
					$connectionsHelper->getCommunityBlogList();
					break;
				case 'getCommunityMemberArray':
					$connectionsHelper->getCommunityMemberArray();
					break;
				case 'getCommunityMembers':
					$connectionsHelper->getCommunityMembers();
					break;
				case 'getCommunitySubcommunities':
					$connectionsHelper->getCommunitySubcommunities();
					break;
			}
			
		}
	}
}
