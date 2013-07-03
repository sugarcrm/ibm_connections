<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('include/externalAPI/Base/ExternalAPIBase.php');
require_once('include/externalAPI/Base/WebDocument.php');

class ExtAPIConnections extends ExternalAPIBase implements WebDocument {

    protected $llMimeWhitelist = array(
        'application/msword',
        'application/pdf',
        'application/postscript',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.oasis.opendocument.formula',
        'application/vnd.oasis.opendocument.graphics',
        'application/vnd.oasis.opendocument.presentation',
        'application/vnd.oasis.opendocument.presentation-template',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.spreadsheet-template',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.text-master',
        'application/vnd.oasis.opendocument.text-template',
        'application/vnd.oasis.opendocument.text-web',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-word.document.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'application/vnd.ms-word.template.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel.sheet.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'application/vnd.ms-excel.template.macroEnabled.12',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'application/vnd.ms-excel.addin.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.presentationml.template',
        'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'application/vnd.ms-officetheme',
        'application/onenote',
        );

    //Not needed?  Just for LotusLive meetings?
    //public $authMethod = 'oauth';
    //public $supportMeetingPassword = false;
    //public $canInvite = false;
    //public $sendsInvites = false;
    //public $needsUrl = true;
    //public $sharingOptions = array('private'=>'LBL_SHARE_PRIVATE','company'=>'LBL_SHARE_COMPANY','public'=>'LBL_SHARE_PUBLIC');
    //public $hostURL;
    //protected $dateFormat = 'm/d/Y H:i:s';

    public $connector = "ext_eapm_connections";
    public $supportedModules = array(
    	'Documents',
    );
    public $authMethod = 'password';

    public $docSearch = false;
    public $restrictUploadsByExtension = false;

	public $needsUrl = false;
	public $client;
	public $url;
	public $userId;
	public $api;

    function __construct() {
		require('custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php');
		require_once 'custom/include/IBMConnections/Api/ConnectionsAPI.php';

	    $this->url = $config['properties']['company_url'];
		$this->api = new ConnectionsAPI();
    }

    public function loadEAPM($eapmBean)
    {
        parent::loadEAPM($eapmBean);
        if ( !empty($eapmBean->api_data) ) {
            $this->api_data = json_decode(base64_decode($eapmBean->api_data), true);
            if ( isset($this->api_data['subscriberID']) ) {
                $this->userId = $this->api_data['userId'];
            }
        }
    }

    public function quickCheckLogin() {
        $reply = parent::quickCheckLogin();
        $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Parent Reply: '.print_r($reply,true));
        if ( $reply['success'] ) {
            $reply = $this->makeRequest('vulcan/shindig/rest/people/@me/@self');

            $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): LL Reply: '.print_r($reply,true));
            if ( $reply['success'] == true ) {
                if ( !empty($reply['responseJSON']['entry']['objectId']) ) {
                    $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Has objectId: '.print_r($reply['responseJSON']['entry']['objectId'],true));
                    return $reply;
                } else {
                    $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): No objectId: '.print_r($reply['responseJSON']['entry']['objectId'],true));
                    $reply['success'] = false;
                    $reply['errorMessage'] = translate('LBL_ERR_NO_RESPONSE', 'EAPM')." #QK1";
                }
            }
        }
        $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Bad reply: '.print_r($reply,true));

        return $reply;
    }

    public function checkLogin($eapmBean = null)
    {
        $reply = parent::checkLogin($eapmBean);
        if ( $reply['success'] != true ) {
            // $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Bad reply: '.print_r($reply,true));
            return $reply;
        }
        try {
	        $reply = $this->makeRequest('profiles/atom/profileService.do','GET',false);
            //$reply = $this->makeRequest('files/basic/cmis/my/servicedoc','GET',false);
            if ( $reply['success'] !== true ) {
                $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Bad reply: '.print_r($reply,true));
                return $reply;
            }
            // get user details
            if ( $reply['success'] == true ) {
	            $response = new SimpleXMLElement($reply['rawResponse']);
	            $this->api_data['userId'] = (string) $response->workspace->collection->children('http://www.ibm.com/xmlns/prod/sn')->userid;
            } else {
                $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Bad reply: '.print_r($reply,true));
                return $reply;
            }
        } catch(Exception $e) {
            $reply['success'] = FALSE;
            $reply['errorMessage'] = $e->getMessage();
            $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Bad reply: '.print_r($reply,true));
            return $reply;
        }

        $this->eapmBean->api_data = base64_encode(json_encode($this->api_data));

        // $GLOBALS['log']->debug(__FILE__.'('.__LINE__.'): Good reply: '.print_r($reply,true));
        return $reply;
    }

    /**
     * Get HTTP client for communication with Connections
     *
     * Creates and setup the http client object, including authorization data if needed
     *
     * @return Zend_Http_Client
     */
    protected function getClient()
    {
	    $client = new Zend_Http_Client();
        return $client;
    }

    public function uploadDoc($bean, $fileToUpload, $docName, $mimeType)
    {
    	$fileObject = $this->uploadFile($fileToUpload, $docName, $mimeType, "public");
    	
    	if (is_object($fileObject) && $fileObject->docId) {
    		$bean->doc_id = $fileObject->docId;
    		$bean->doc_url = $fileObject->docUrl;
    		$bean->doc_direct_url = $fileObject->docUrl;
    		return array('success' => true);
    	}
    	return array('success' => false);
    }

    public function deleteFile($document_id)
    {
      	return $this->api->getFilesAPI()->deleteDoc($document_id);
    }
    
     public function deleteDoc($document_id)
    {
      	return $this->api->getFilesAPI()->deleteDoc($document_id);
    }


    public function downloadDoc($documentId, $documentFormat){}
    public function shareDoc($documentId, $emails){}

    public function loadDocCache($forceReload = false) {
        global $db, $current_user;
       
        $cacheKeyName = 'docCache_' . $current_user->id . '_LotusLiveDirect';
        
        if (!$forceReload && isset(SugarCache::instance()->$cacheKeyName)) {

            $docCache = SugarCache::instance()->$cacheKeyName;

            if (abs(time() - $docCache['loadTime']) < 3600) {
                return $docCache['results'];
            }
        }
        $requestUrl = '/files/basic/cmis/repository/p!'.$this->api_data['subscriberId'].'/folderc/snx:files';
        if ( $this->getVersion() == 1 ) {
            $requestUrl .= '!'.$this->api_data['subscriberId'];
        }
        $requestUrl .= '?maxItems=50';
        $reply = $this->makeRequest($requestUrl,'GET',false);

        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->strictErrorChecking = false;
        $xml->loadXML($reply['rawResponse']);
        if ( !is_object($xml) ) {
            $reply['success'] = false;
            // FIXME: Translate
            $reply['errorMessage'] = 'Bad response from the server: '.print_r(libxml_get_errors(),true);
            return;
        }

        $xp = new DOMXPath($xml);

        $results = array();

        $fileNodes = $xp->query('//atom:feed/atom:entry');
        foreach ( $fileNodes as $fileNode ) {
            $result = array();

            $idTmp = $xp->query('.//atom:id',$fileNode);
            list($dontcare,$result['id']) = explode("!",$idTmp->item(0)->textContent);

            $nameTmp = $xp->query('.//atom:title',$fileNode);
            $result['name'] = $nameTmp->item(0)->textContent;

            $timeTmp = $xp->query('.//atom:updated',$fileNode);
            $timeTmp2 = $timeTmp->item(0)->textContent;
            $result['date_modified'] = preg_replace('/^([^T]*)T([^.]*)\....Z$/','\1 \2',$timeTmp2);

            $result['url'] = $this->url.'files/filer2/home.do#files.do?subContent=fileDetails.do?fileId='.$result['id'];

            $results[] = $result;
        }


        $docCache['loadTime'] = time();
        $docCache['results'] = $results;
        
        SugarCache::instance()->$cacheKeyName = $docCache;     

        return $results;
    }
    public function searchDoc($keywords,$flushDocCache=false){
        $docList = $this->loadDocCache($flushDocCache);

        $results = array();

        $searchLen = strlen($keywords);

        foreach ( $docList as $doc ) {
            if ( empty($keywords) || strncasecmp($doc['name'],$keywords,strlen($keywords)) == 0 ) {
                // It matches
                $results[] = $doc;

                if ( count($results) > 15 ) {
                    // Only return the first 15 results
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Make request to a service
     * @param unknown_type $url
     * @param unknown_type $method
     * @param unknown_type $json
     */
    protected function makeRequest($urlReq, $method = 'GET', $json = true)
    {
        $client = $this->getClient();
        $url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");
        $GLOBALS['log']->debug("REQUEST: $url");
        $client->setUri($url);
	    $client->setAuth($this->account_name,$this->account_password);
	    $rawResponse = $client->request();
//echo $url;
        $reply = array('rawResponse' => $rawResponse->getBody());
        $GLOBALS['log']->debug("RESPONSE: ".var_export($rawResponse, true));

	    //$ns = $xml->attributes();
		//$xml->preserveWhiteSpace = false;
		//$xml->strictErrorChecking = false;
		//$xml->loadXML($reply['rawResponse']);

		if(!$rawResponse->isSuccessful() || empty($reply['rawResponse'])) {
			$reply['success'] = false;
			// FIXME: Translate
			$reply['errorMessage'] = 'Bad response from the server: '.$rawResponse->getMessage();
			return $reply;
		} else {
			$reply['success'] = true;
			$cookie_array = $rawResponse->getHeader('Set-cookie');
			$expires = strtotime($rawResponse->getHeader('Expires'));

			//$this->processLTPACookies($cookie_array, $expires);
			//var_dump($_COOKIE); die();
		}

		//$xp = new DOMXPath($xml);

        /*if($json) {
            $response = json_decode($reply['rawResponse'],true);
            $GLOBALS['log']->debug("RESPONSE-JSON: ".var_export($response, true));
            if ( empty($rawResponse) || !is_array($response) ) {
                $reply['success'] = FALSE;
                // FIXME: Translate
                $reply['errorMessage'] = 'Bad response from the server';
            } else {
                $reply['responseJSON'] = $response;
                $reply['success'] = TRUE;
            }
        } else {
            $reply['success'] = true;
        }*/

        return $reply;
    }

	/**
	 * Finds and sets LTPA cookies from authentication request
	 * @param $cookie_array
	 * @param $expires
	 */
	private function processLTPACookies($cookie_array, $expires) {
		ob_clean();
		//var_dump($cookie_array);

		for($i=0; $i<count($cookie_array); $i++) {
			if(substr_count($cookie_array[$i],"LtpaToken") > 0) {
			//if(substr($cookie_array[$i],0,strlen("LtpaToken")) === "LtpaToken") {
				$cookie_parts = explode(";",$cookie_array[$i]);
				if(substr_count($cookie_parts[0],"LtpaToken") > 0)
					$cookie_token = explode("=", $cookie_parts[0], 2);
				if(substr_count($cookie_parts[1],"Path") > 0)
					$cookie_path = explode("=", $cookie_parts[1], 2);
				if(substr_count($cookie_parts[2],"Domain") > 0)
					$cookie_domain = explode("=", $cookie_parts[2], 2);

				$cookie = $cookie_array[$i];
				//$test = $this->client->setHeaders("Set-Cookie",$cookie);
				//var_dump($test);
				//echo $cookie;
				//header("Set-Cookie: {$cookie}");
				//echo $cookie_array[$i];
				//var_dump($cookie_token); die();
				$cookie_name = $cookie_token[0];
				$cookie_value = $cookie_token[1];
				//header("Set-Cookie: {$cookie_name}={$cookie_value}; Domain={$cookie_domain}; Max-Age=0; Path={$cookie_path}");
				//echo "setcookie('{$cookie_name}','{$cookie_value}',{$expires},'{$cookie_path[1]}','{$cookie_domain[1]}')<br/>";
//die();
				//$cookie_status = setcookie('LtpaToken','w9sx2rFYg/Ez/lJrUfeBJ0QAnXYBWRDE9++hn2Cq5YDPKpNzUBlB8FZX6PpWsvV6BQsjHco5zMvFoQX/h/lS6ortCsmUlJpkXEf9vFS7VB5C0vL6niBkLLuuE8ACyuNGiwxyVySU+hCpm3vbUvhZKFZkrmcUWGawU1Y1DZOj3VDwNcjbdZ/MxAih9SdFhPNFdDhhFxOItTQ+ykrfx/JqV9Rc1NifOROkAts9E65Hec9UmZGZQzx4Zt1BC0ND1vnlzGlAANhGATePlMLgTOokGX9We3gr18jyMsXp9TVDlWC54FV4edNCYXl04GAO8F3fPypAFURX4psQIsStrmGeAqirgQqhOmx5YSVzDtzl16cHCwQSLsWCz7hmc/UdK0Ip35K+srZHguW4mMk6tTLg9w==',786297600,'/','.lotus.com');
				$cookie_status = setcookie($cookie_name,$cookie_value,0,$cookie_path[1],$cookie_domain[1]);
				//setcookie("TestCookie", "Test!", 0, "/", ".example.com", 1);
				//echo $cookie_name. " Status: ".$cookie_status."<br/>";
			}
		}
		ob_flush();
		//die();
	}

    /**
     * Check the API version
     */
    protected function getVersion()
    {
        static $cacheVersion;

        if ( isset($cacheVersion) ) {
            return $cacheVersion;
        }


        $defaultVersion = 2;
        return $defaultVersion;

        $reply = $this->makeRequest('/files/basic/api/nonce','GET',false);
        if ( !$reply['success'] ) {
            // Return the default version, not much else we can do except not cache this
            return $defaultVersion;
        }

        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->strictErrorChecking = false;
        $xml->loadXML($reply['rawResponse']);
        if ( !is_object($xml) ) {
            // Return the default version, not much else we can do except not cache this
            return $defaultVersion;
        }

        $xp = new DOMXPath($xml);

        $results = array();

        $versionNodes = $xp->query('//cmisra:repositoryInfo/cmis:productName');

        $versionLabel = $versionNodes->item(0)->textContent;

        switch ( $versionLabel ) {
            case 'LotusLive Files':
                $version = 1;
                break;
            case 'IBM Connections - Files':
                $version = 2;
                break;
            default:
                $GLOBALS['log']->error('Lotus Live API version could not be detected, the version label returned was: '.$versionLabel);
                $version = 2;
                break;
        }

        $cacheVersion = $version;
        return $version;
    }

	public function getCommunities($type, $page = 1, $searchText = null) {
		$searchText = str_ireplace(" ","+",$searchText);
		$api = $this->api->getCommunitiesAPI();
		if($type == 'MyCommunities') {
			$reply = $api->listMyCommunities($searchText, null, null, null, 5, $page);
		}

		if($type == 'PublicCommunities') {
			$reply = $api->listAllCommunities($searchText, null, null, null, 5, $page);
		}
		//var_dump($api->getLastResultMetadata());
		return $reply;

	}
	
	public function getActivitiesList($communityId, $page, $searchText)
	{
		$searchText = str_ireplace(" ","+",$searchText);
		return $this->api->getActivitiesAPI()->listCommunityActivities($communityId, $searchText, $page);
	}
	
	public function editCommunity($communityId, $title, $content, $type, $tags)
	{
		$this->api->getCommunitiesAPI()->editCommunity($communityId, $title, $content, $type, $tags);
	}
	
	public function markToDoCompeted($entryId, $completed = true)
	{
		return $this->api->getActivitiesAPI()->markToDoCompleted($entryId, $completed);
	}
	
	public function getActivity($activityId)
	{
		return $this->api->getActivitiesAPI()->getActivity($activityId);
	}
	public function getActivityNodes($activityId)
	{
		return $this->api->getActivitiesAPI()->listActivityNodes($activityId);
	}
	
	public function getNode($activityId, $nodeId)
	{
		return $this->api->getActivitiesAPI()->getNode($activityId, $nodeId);
	}

	
	public function createActivity($communityId, $activityName, $description, $tag, $due_date)     
	{
		return $this->api->getActivitiesAPI()->createCommunityActivity($communityId, $activityName, $description, $tag, $due_date);
	}
	
	public function postNote($communityId, $content)     
	{
		return $this->api->getUpdatesAPI()->postNote($communityId, $content);
	}
	
	public function createActivitySection($activityId, $name)     
	{
		$this->api->getActivitiesAPI()->createSection($activityId, $name);
	}
	public function createSectionToDo($activityId, $sectionId, $title, $description, $due_date,$tags,$assigned)     
	{
		$this->api->getActivitiesAPI()->createSectionToDo($activityId, $sectionId, $title, $description,$due_date, $tags,$assigned);
	}
	public function createSectionEntry($activityId, $sectionId, $title, $description, $tags)     
	{
		$this->api->getActivitiesAPI()->createSectionEntry($activityId, $sectionId, $title, $description, $tags);
	}
	public function createActivityToDo($activityId, $title, $description, $due_date,$tags,$assigned)     
	{
		$this->api->getActivitiesAPI()->createToDo($activityId, $title, $description,$due_date, $tags,$assigned);
	}
	
	public function createActivityEntry($activityId, $title, $description, $tags)     
	{
		$this->api->getActivitiesAPI()->createEntry($activityId, $title, $description, $tags);
	}

	public function deleteCommunity($communityId) {
		return $this->api->getCommunitiesAPI()->deleteCommunity($communityId);
	}
	
	public function getCommunityDiscussions($communityId, $page, $searchText) 
	{
		$searchText = str_ireplace(" ","+",$searchText);
		$forums = $this->api->getForumsAPI()->getCommunityForums($communityId,$page, $searchText);
		return  (empty($forums)) ? array() : $forums->getEntries();
	}

	public function getForumReplies($forumId)
	{
		return $this->api->getForumsAPI()->getForumReplies($forumId);
	}
	public function getCommunityBookmarks($communityId, $pageNumber = 1, $searchText = '') 
	{
		$searchText = str_ireplace(" ","+",$searchText);
		return $this->api->getBookmarksAPI()->getCommunityBookmarks($communityId, $pageNumber, $searchText);
	}
	public function createDiscussion($communityId, $title, $content, $tags, $isQuestion) 
	{
		return $this->api->getForumsAPI()->createTopic($communityId, $title, $content, $tags, $isQuestion);
	}
	
	public function getCommunityWiki($communityId, $page = 1, $searchText = '') 
	{
		$searchText = str_ireplace(" ","+",$searchText);
		return $this->api->getWikiAPI()->getCommunityWiki($communityId, $page, $searchText);
	}
	
	public function getCommunityWikiContent($communityId, $wikiId) 
	{
		return $this->api->getWikiAPI()->getCommunityWikiContent($communityId, $wikiId);
	}
	
	
	public function getCommunityBlog($communityId, $page, $searchText) 
	{
		$searchText = str_ireplace(" ","+",$searchText);
		return $this->api->getBlogAPI()->getCommunityBlog($communityId, $page, $searchText);
	}
	public function getBlogComments($communityId, $blogId) 
	{
		return $this->api->getBlogAPI()->getBlogComments($communityId, $blogId);
	}
	public function getFilesList($communityId, $page, $searchText) 
	{
		$searchText = str_ireplace(" ","+",$searchText);
		return $this->api->getFilesAPI()->getCommunityFiles($communityId, $page, $searchText);
	}
	public function getMembers($communityId="", $searchText="", $page = 1, $sortBy = 'name', $asc = true) {
		$searchText = str_ireplace(" ","+",$searchText);
		if(!empty($searchText) && empty($communityId)) {
			$reply = $this->makeRequest("profiles/atom/search.do?search={$searchText}&page={$page}&ps=5");
		}
		if(!empty($communityId)) {
		//require_once 'custom/include/IBMConnections/Api/CommunitiesAPI.php';
		//$communitiesApi = new CommunitiesAPI();
		//$reply = $communitiesApi->listMembers($community_id);
			$request = "communities/service/atom/community/members?communityUuid={$communityId}&page={$page}&ps=16&sortField={$sortBy}";
			if(!empty($asc) && $asc){
				$request .= "&asc=true";
			}
			else{
				$request .= "&desc=true";
			}
			if(!empty($searchText)){
				$request .= "&search=" . $searchText;
			}
			$reply = $this->makeRequest($request);
		}
		return $reply;
	}

	public function getCommunity($communityId) 
	{
		return $this->api->getCommunitiesAPI()->getCommunity($communityId);
	}

	public function downloadFile($document_id) 
	{
		return $this->api->getFilesAPI()->downloadFile($document_id);
	}
	
	public function commentFile($userId, $documentId, $comment) 
	{
		return $this->api->getFilesAPI()->commentFile($userId, $documentId, $comment);
	}
	public function replyDiscussion($topicId, $title, $comment) 
	{
		return $this->api->getForumsAPI()->replyToTopic($topicId, $title, $comment);
	}
	
	public function replyDiscussionReply($replyId, $title, $comment) 
	{
		return $this->api->getForumsAPI()->replyToReply($replyId, $title, $comment);
	}
	public function likeFile($userId, $documentId) 
	{
		return $this->api->getFilesAPI()->likeFile($userId, $documentId);
	}

	public function createCommunity($name, $description, $type, $tags, $logo) 
	{
		return $this->api->getCommunitiesAPI()->createCommunity($name, $description, $type, $tags, $logo);
	}

	public function addMemberToCommunity($member_id, $community_id, $role) 
	{
		$this->api->getCommunitiesAPI()->addMemberById($member_id, $community_id, $role);
	}
	
	public function removeMemberFromCommunity($member_id, $community_id) 
	{
		$this->api->getCommunitiesAPI()->removeMemberById($member_id, $community_id);
	}
	
	public function createBookmarkToCommunity($communityId, $title, $href, $description, $tags, $important) 
	{
		return $this->api->getBookmarksAPI()->createCommunityBookmark($communityId, $title, $href, $description, $tags, $important);
	}
	
	public function shareMyFileWithCommunity($cid, $myFileId) 
	{
		$this->api->getCommunitiesAPI()->shareMyFileWithCommunity($cid, $myFileId);
	}
	
	public function getUpdates($cid, $pageNum = 1, $searchText = '') 
	{
		return $this->api->getUpdatesAPI()->getCommunityUpdates($cid, $pageNum, $searchText);
	}
	
	public function unshareMyFileFromCommunity($cid, $myFileId) 
	{
		$this->api->getCommunitiesAPI()->unshareMyFileFromCommunity($cid, $myFileId);
	}
	
	public function uploadFile($fileToUpload, $docName, $mimeType = null, $visibility = "private") 
	{
		return $this->api->getFilesAPI()->uploadFile($docName, sugar_file_get_contents($fileToUpload),  $mimeType, $visibility);
    }
    
    public function getMyFolders() 
	{
		return $this->getUserFolders($this->api_data['userId']);
    }
    public function getUserFolders($userId) 
	{
		return $this->api->getFilesAPI()->getUserFolders($userId);
    }
    
    public function getSharedFolders() 
	{
		return $this->api->getFilesAPI()->getSharedFolders();
    }
    
    public function getPinnedFolders() 
	{
		return $this->api->getFilesAPI()->getPinnedFolders();
    }
    
    public function getPublicFolders() 
	{
		return $this->api->getFilesAPI()->getPublicFolders();
    }
    
    public function getFilesFromFolder($folderId, $page = 1) 
	{
		return $this->api->getFilesAPI()->getFilesFromFolder($folderId, $page);
    }
    
    public function getFileDetails($fileId, $libraryId = '') 
	{
		return $this->api->getFilesAPI()->getFileDetails($fileId, $libraryId);
    }
    
    public function getOverviewCounts($communityId)
    {
		$api = $this->api->getCommunitiesAPI();
		$reply = array();
		$reply['activity'] = $api->getActivityCount($communityId);
		$reply['file'] = $api->getFileCount($communityId);
		$reply['bookmark'] = $api->getBookmarkCount($communityId);
		$reply['wiki'] = $api->getWikiCount($communityId);
		$reply['update'] = $api->getUpdateCount($communityId);
		$reply['discussion'] = $api->getDiscussionCount($communityId);
		$reply['blog'] = $api->getBlogCount($communityId);
		return $reply;
    }

}
