<?php
/* ***************************************************************** */
/*                                                                   */
/* Licensed Materials - Property of IBM                              */
/*                                                                   */
/* Sugar Sample Code Q4 2012                                         */
/*                                                                   */
/* Copyright IBM Corp. 2012  All Rights Reserved.                    */
/*                                                                   */
/* US Government Users Restricted Rights - Use, duplication or       */
/* disclosure restricted by GSA ADP Schedule Contract with           */
/* IBM Corp.                                                         */
/*                                                                   */
/* ***************************************************************** */

 

require_once 'custom/include/IBMConnections/Models/ConnectionsModel.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsCommunity extends ConnectionsModel
{
     var $remoteAppFeed = null;
     
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $httpClient
	 * @param IBMAtomEntry $atomEntry
	 */
    function __construct(IBMAtomEntry $atomEntry, $remoteAppFeed = null)
    {
    	parent::__construct($atomEntry);
    	$this->remoteAppFeed = $remoteAppFeed;
    }
    
    
    protected function serviceBaseUrl() {
    	return "/communities/service/atom/";
    }
    
    /**
     * 
     * Enter description here ...
     */
	public function getSummary() {
		return $this->atom->getSummary();
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getTitle() {
		return $this->atom->getTitle();
	}
	
	
	public function getLogoLink()
	{
		$link = $this->atom->getLink('http://www.ibm.com/xmlns/prod/sn/logo'); 
		return $link['href'];
	}
	
	public function getLink()
	{
		$link = $this->atom->getLink('alternate'); 
		return $link['href'];
	}
	/**
	 *
	 * Enter description here ...
	 */
	public function getDescription() {
		return $this->atom->getContent();
	}
	public function getCommunityType(){
		$type = $this->atom->xquery("./snx:communityType");//$this->atom->xquery("./td:type");
		return $type->item(0)->textContent;
	}
	
	public function getMemberCount(){
		$type = $this->atom->xquery("./snx:membercount");
		return $type->item(0)->textContent;
	}
	
	public function getAuthor() {
		$contributor = $this->atom->getAuthor();
		//$contributor = CommunitiesIntegration::toSugarProfile($contributor);
		return $contributor;
	}
	/**
	 * 
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getId();
		$idx = strpos( $origId , "/service/atom/community/instance?communityUuid=");
		$size = strlen("/service/atom/community/instance?communityUuid=");
		return substr($origId, $idx + $size, strlen($origId) - ($idx + $size));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCreateActivityLink() {
		return $this->connectionsBaseUrl() . "/activities/service/html/mainpage?commUuid=" . 
			$this->getId() . "#communityActivityForm," . $this->getId();
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCreateBlogEntryLink() {
		return $this->connectionsBaseUrl() . 
			"/blogs/roller-ui/authoring/weblog.do?method=create&weblog=" . $this->getId();
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function getTags() {
		return $this->atom->getTags();
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function getUpdatedDate() {
		return $this->formatTimeDate($this->atom->getUpdatedDate());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function getAddBookmarkLink() {
		return $this->connectionsBaseUrl() .
		"/communities/service/html/community/bookmarks?contextAction=createEntry&communityUuid=" . $this->getId();
	}
	
	public function getWebUrl() {
		return $this->connectionsBaseUrl()."/communities/service/html/communityview?communityUuid=".$this->getId();
	}
	
    /**
     *
     * Enter description here ...
     */
    
	public function getForumId() {    
		if(!$this->remoteAppFeed) {
			return null;
		}
		
        $entries = array();   
        $entries = $this->remoteAppFeed->getEntries();
          
        for($i = 0; $i < sizeof($entries); $i++) {             
            $entry = $entries[$i];    //Array of IBMAtomEntries
            $retArray = $entry->getCategories("http://www.ibm.com/xmlns/prod/sn/type"); //Get category with this scheme
            for($k = 0; $k < sizeof($retArray); $k++) {
               if(strcmp($retArray[$k]['term'],"Forum") == 0) {                  
                     $forumWidgetIdFullForm = $entry->getId();                 
                     $forumWidgetId = strrev(strtok(strrev($forumWidgetIdFullForm), '-')); //Retrieve string after '-'   
                     break;               
               } 
            }                   
        }
        return $forumWidgetId;
    }
	
    /**
     *
     * Enter description here ...
     */
    public function getCreateForumsLink() {
        return $this->connectionsBaseUrl() . "/communities/service/html/communityview?communityUuid=" .  $this->getId() . "#fullpageWidgetId=" .  $this->getForumId() . "&startTopic=true";

    }
}
