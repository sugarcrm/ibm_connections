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
    public $supportedModules = array();
    public $authMethod = 'password';

    public $docSearch = false;
    public $restrictUploadsByExtension = false;

	public $needsUrl = false;
	public $client;
	public $url;
	public $userId;


    function __construct() {
		require('custom/include/externalAPI/Connections/ConnectionsXML.php');
		require('custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php');

	    $this->url = $config['properties']['company_url'];
		$this->create_community_xml = $create_community_xml;
	    $this->upload_document_xml = $upload_document_xml;
	    $this->community_member_xml = $community_member_xml;


	///Not needed?  Just for LotusLive meetings?
        //if ( isset($GLOBALS['sugar_config']['ll_base_url']) ) {
            //$this->url = $GLOBALS['sugar_config']['ll_base_url'];
        //}
    }

    public function loadEAPM($eapmBean)
    {
        parent::loadEAPM($eapmBean);

        //if($eapmBean->url) {
        //    $this->url = $eapmBean->url;
        //}

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

    public function uploadDoc($bean, $fileToUploadContents, $docName, $mimeType)
    {
        // Let's see if this is not on the whitelist of mimeTypes
        if ( empty($mimeType) || ! in_array($mimeType,$this->llMimeWhiteList) ) {
            // It's not whitelisted
            $mimeType = 'application/octet-stream';
        }

        $client = $this->getClient();
	    $url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");
        $url = $this->url."files/basic/cmis/repository/p!{$this->api_data['subscriberId']}/folderc/snx:files";
        if ( $this->getVersion() == 1 ) {
            $url .= "!{$this->api_data['subscriberId']}";
        }
        $GLOBALS['log']->debug("Connections REQUEST: $url");
        $rawResponse = $client->setUri($url)
            ->setRawData($fileToUploadContents, $mimeType)
            ->setHeaders("slug", $docName)
            ->request("POST");
        $reply = array('rawResponse' => $rawResponse->getBody());
//        $GLOBALS['log']->debug("REQUEST: ".var_export($client->getLastRequest(), true));
//        $GLOBALS['log']->debug("RESPONSE: ".var_export($rawResponse, true));
        if(!$rawResponse->isSuccessful() || empty($reply['rawResponse'])) {
            $reply['success'] = false;
            $reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'].': '.$rawResponse->getMessage();
            return;
        }

        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->strictErrorChecking = false;
        $xml->loadXML($reply['rawResponse']);
        if ( !is_object($xml) ) {
            $reply['success'] = false;
            $reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'].': '.print_r(libxml_get_errors(),true);
            return;
        }

        $xp = new DOMXPath($xml);
        $url = $xp->query('//atom:entry/atom:link[attribute::rel="alternate"]');
        $directUrl = $xp->query('//atom:entry/atom:link[attribute::rel="edit-media"]');
        $id = $xp->query('//atom:entry/cmisra:pathSegment');

        if ( !is_object($url) || !is_object($directUrl) || !is_object($id) ) {
            $reply['success'] = false;
            $reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'];
            return;
        }
        $bean->doc_url = $url->item(0)->getAttribute("href");
        $bean->doc_direct_url = $directUrl->item(0)->getAttribute("href");
        $bean->doc_id = $id->item(0)->textContent;

        // Refresh the document cache
        $this->loadDocCache(true);

        return array('success'=>TRUE);
    }

    public function deleteDoc($document)
    {
        $client = $this->getClient();
        $url = $this->url."files/basic/cmis/repository/p!{$this->api_data['subscriberId']}/object/snx:file!{$document->doc_id}";
        $GLOBALS['log']->debug("CONNECTIONS REQUEST: $url");
        $rawResponse = $client->setUri($url)
            ->request("DELETE");
        $reply = array('rawResponse' => $rawResponse->getBody());
        $GLOBALS['log']->debug("REQUEST: ".var_export($client->getLastRequest(), true));
        $GLOBALS['log']->debug("RESPONSE: ".var_export($rawResponse, true));

        // Refresh the document cache
        $this->loadDocCache(true);

        return array('success'=>TRUE);
    }

    public function downloadDoc($documentId, $documentFormat){}
    public function shareDoc($documentId, $emails){}

    public function loadDocCache($forceReload = false) {
        global $db, $current_user;

        $key = 'docCache_'.$current_user->id.'_ConnectionsDirect';
        if ( isset(SugarCache::instance()->$key) ) {
            return SugarCache::instance()->$key;
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
            $reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'].': '.print_r(libxml_get_errors(),true);
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

        SugarCache::instance()->$key = $results;
        
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

        $reply = array('rawResponse' => $rawResponse->getBody());
        $GLOBALS['log']->debug("RESPONSE: ".var_export($rawResponse, true));

	    //$ns = $xml->attributes();
		//$xml->preserveWhiteSpace = false;
		//$xml->strictErrorChecking = false;
		//$xml->loadXML($reply['rawResponse']);

		if(!$rawResponse->isSuccessful() || empty($reply['rawResponse'])) {
			$reply['success'] = false;
			$reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'].': '.$rawResponse->getMessage();
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
                $reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'];
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
                $GLOBALS['log']->error('API version could not be detected, the version label returned was: '.$versionLabel);
                $version = 2;
                break;
        }

        $cacheVersion = $version;
        return $version;
    }

	public function getCommunities($type, $page=1, $search_text='') {
		$search_text = str_ireplace(" ","+",$search_text);

		if($type == 'MyCommunities') {
			$reply = $this->makeRequest("communities/service/atom/communities/my?page={$page}&search={$search_text}&ps=5");
		}

		if($type == 'PublicCommunities') {
			$reply = $this->makeRequest("communities/service/atom/communities/all?page={$page}&search={$search_text}&ps=5");
		}

		return $reply;

	}

	public function getFiles($community_id, $page=1) {
		$reply = $this->makeRequest("files/basic/api/communitycollection/{$community_id}/feed?page={$page}&ps=5");
		return $reply;
	}

	public function getMembers($community_id="", $search_text="", $page=1) {
		if(!empty($search_text)) {
			$search_text = str_ireplace(" ","+",$search_text);

			$reply = $this->makeRequest("profiles/atom/search.do?search={$search_text}&page={$page}&ps=5");
		}
		if(!empty($community_id)) {
			$reply = $this->makeRequest("communities/service/atom/community/members?communityUuid={$community_id}&page={$page}");
		}

		return $reply;
	}
///communities/service/atom
//communities/service/atom/community/instance?communityUuid=d2fdec09-0221-47fe-a27b-2e55497caa22
	public function getCommunityInfo($community_id) {
		$reply = $this->makeRequest("communities/service/atom/community/instance?communityUuid={$community_id}");
		return $reply;
	}
	
	
	public function searchMembers($search_text, $page=1) {
		$search_text = str_ireplace(" ","+",$search_text);

		$reply = $this->makeRequest("profiles/atom/search.do?search={$search_text}&page={$page}&ps=5");
		return $reply;
	}

	public function downloadFile($document_id) {
		$reply = $this->makeRequest("files/basic/anonymous/api/document/{$document_id}/media");
		return $reply;
	}

	public function getFileDetails($document_id) {
		$reply = $this->makeRequest("files/basic/anonymous/api/myuserlibrary/document/{$document_id}/entry");
		return $reply;
	}

	public function createCommunity($data) {
		$client = $this->getClient();

		$urlReq = "communities/service/atom/communities/my";
		$url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");

		$client->setRawData($data,'application/atom+xml');
		$client->setUri($url);
		$client->setAuth($this->account_name,$this->account_password);
		$reply = $client->request("POST");
		parse_str(parse_url($reply->getHeader('Location'),PHP_URL_QUERY));
		$community_id = $communityUuid;

		//print_r($community_id);

		return $community_id;

	}

	public function addMemberToCommunity($member_id, $community_id) {
		$client = $this->getClient();

		$urlReq = "communities/service/atom/community/members?communityUuid={$community_id}";
		$url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");

		$member_xml = new SimpleXMLElement($this->community_member_xml);
		$member_xml->contributor->addChild('snx:userid',$member_id,'http://www.ibm.com/xmlns/prod/sn');
		//$role = $member_xml->addChild('snx:role','owner','http://www.ibm.com/xmlns/prod/sn');
		//$role->addAttribute('component','http://www.ibm.com/xmlns/prod/sn/communities');
		$data = $member_xml->asXML();
		$client->setRawData($data,'application/atom+xml');
		$client->setUri($url);
		$client->setAuth($this->account_name,$this->account_password);
		$reply = $client->request("POST");

		return $reply;
	}

	public function getMemberProfileInfo($member_id) {
		$profile_info = array();

		$profile_reply = $this->makeRequest("profiles/atom/profile.do?userid={$member_id}&format=full");
		if($profile_reply['success']) {
			$profile_response = new SimpleXMLElement($profile_reply['rawResponse']);
			//$profile_info['rawResponse'] = print_r($profile_response,true);

			$profile_info['member_name'] = (string) $profile_response->entry->contributor->name;

			//$profile_info['sn'] = print_r($servicedoc_response->workspace->children('http://www.w3.org/2005/Atom'),true);
			foreach($profile_response->entry->children('http://www.w3.org/2005/Atom')->link as $link) {
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/blogs")
					$profile_info['member_blogs'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/forums")
					$profile_info['member_forums'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/wikis")
					$profile_info['member_wikis'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/files")
					$profile_info['member_files'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/communities")
					$profile_info['member_communities'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/dogear")
					$profile_info['member_bookmarks'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/profiles")
					$profile_info['member_profiles'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/service/activities")
					$profile_info['member_activities'] = urldecode((string) $link->attributes()->href);
				if($link->attributes()->rel == "http://www.ibm.com/xmlns/prod/sn/image")
					//$profile_info['profile_image_url'] = substr((string) $link->attributes()->href,0,-1);
					$profile_info['profile_image_url'] = urldecode((string) $link->attributes()->href);
			}
		}

		return $profile_info;
	}

	public function uploadFile($fileToUploadContents, $docName, $mimeType, $comm_type) {
        // Let's see if this is not on the whitelist of mimeTypes
        if ( empty($mimeType) || ! in_array($mimeType,$this->llMimeWhitelist) ) {
            // It's not whitelisted
            $mimeType = 'application/octet-stream';
        }

		//$urlReq = "files/basic/api/myuserlibrary/feed?visibility=public";
		$urlReq = "files/basic/api/myuserlibrary/feed?visibility=".$comm_type;
		//$urlReq = "files/basic/api/community/a139f3b0-4cf7-40b6-af28-6cad51292aa9/libraries/feed";

        $client = $this->getClient();
	    $url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");

		//$xml = new SimpleXMLElement($this->upload_document_xml);
		//$data = $xml->asXML();
		//$client->setFileUpload($fileToUpload, $docName);
		$client->setRawData($fileToUploadContents, $mimeType);
		$client->setHeaders("slug", $docName);
		//$client->setParameterPost("visibility", "public");
		$client->setUri($url);
		$client->setAuth($this->account_name,$this->account_password);
		$rawResponse = $client->request("POST");
		//print_r($rawResponse);
		$rawResponseBody = $rawResponse->getBody();
        $reply = array('rawResponse' => $rawResponseBody);
       // $reply['errorMessage'] = $rawResponse->getMessage();
       
        
//        $GLOBALS['log']->debug("REQUEST: ".var_export($client->getLastRequest(), true));
//        $GLOBALS['log']->debug("RESPONSE: ".var_export($rawResponse, true));
        /*if(!$rawResponse->isSuccessful() || empty($reply['rawResponse'])) {
            $reply['success'] = false;
            $reply['errorMessage'] = $GLOBALS['app_strings']['ERR_BAD_RESPONSE_FROM_SERVER'].': '.$rawResponse->getMessage();
            return;
        }*/

        return $reply;
    }


public function updateFile($fileToUploadContents, $docName, $mimeType,$comm_type) {
        // Let's see if this is not on the whitelist of mimeTypes
        if ( empty($mimeType) || ! in_array($mimeType,$this->llMimeWhitelist) ) {
            // It's not whitelisted
            $mimeType = 'application/octet-stream';
        }
		$urlReq = "files/basic/api/myuserlibrary/document/{$docName}/entry?identifier=label";
        $client = $this->getClient();
	    $url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");
		$client->setAuth($this->account_name,$this->account_password);
		$rawResponse = $client->setUri($url)->request("DELETE");
        $reply = $this->uploadFile($fileToUploadContents, $docName, $mimeType,$comm_type);
        return $reply;
    }
	public function shareMyFileWithCommunity($document_id,$community_xml) {
		$client = $this->getClient();

		$urlReq = "files/basic/api/myuserlibrary/document/{$document_id}/feed";
		$url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");

		$client->setRawData($community_xml,'application/atom+xml');
		$client->setUri($url);
		$client->setAuth($this->account_name,$this->account_password);
		$reply = $client->request("POST");

		return $reply;
	}
}
