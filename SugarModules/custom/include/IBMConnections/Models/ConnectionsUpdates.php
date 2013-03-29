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



//require_once 'custom/include/IBMConnections/Models/ConnectionsForumsTopic.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsModel.php';
/**
 *
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsUpdates extends ConnectionsModel
{
    
     var $forumId = null;
     
    /**
     *
     * Enter description here ...
     * @param unknown_type $httpClient
     * @param IBMAtomEntry $atomEntry
     */
    function __construct(IBMAbstractAtomItem $atom)
    {
        parent::__construct($atom);
    }


    protected function serviceBaseUrl() {
        return "/news/atom/stories/top";
    }


    /**
     *
     * Enter description here ...
     */
    public function getTitle() {
        return $this->atom->getTitle();
    }
    
	public function getFormattedUpdatedDate() {
		return $this->formatTimeDate($this->atom->getUpdatedDate());
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
	public function getContent() {
		return $this->atom->getContent();
	}
    /**
     *
     * Enter description here ...
     */
   /* public function getEntries() {
        $topics = $this->atom->getEntries();
        $forumTopics = array();
        foreach($topics as $entry) {
            $forumTopics[] = new ConnectionsForumsTopic($entry);
        }
        return $forumTopics;
    }

/*
    /**
     *
     * Enter description here ...
     */
    public function getCommunityId() {
        $id =  $this->atom->getNodeContent("snx:relcommid");
       
        return $id;
    }
public function getAuthor() {
		$contributor = $this->atom->getAuthor();
		$test = $this->userProfile($contributor);
		//$contributor = CommunitiesIntegration::toSugarProfile($contributor);
		//return $test;//$contributor;
		return $contributor;
	}

     /**
     *
     * Enter description here ...
     */
    public function getId() {    
         
         $origId = $this->atom->getId();
		$idx = strpos( $origId , "urn:lsid:ibm.com:news:story");
		$size = strlen("urn:lsid:ibm.com:news:story");
		return substr($this->atom->getId(), $idx + $size + 1, strlen($origId) - ($idx + $size));
    }
	
	public function getLink() {    
         
        $reply = $this->atom->getLink('alternate');
		return $reply['href'];
    }
}
