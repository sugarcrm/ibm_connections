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



require_once 'custom/include/IBMConnections/Models/ConnectionsForumsTopic.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsForumsTopicReplies.php';

/**
 *
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsForums extends ConnectionsModel
{
    
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
        return "/forums/atom/topics";
    }


    /**
     *
     * Enter description here ...
     */
    public function getTitle() {
        return $this->atom->getTitle();
    }

    /**
     *
     * Enter description here ...
     */
    public function getEntries() {
        $topics = $this->atom->getEntries();
        $forumTopics = array();
        foreach($topics as $entry) {
            $forumTopics[] = new ConnectionsForumsTopic($entry);
        }
        return $forumTopics;
    }

 public function getReplies() {
        $topics = $this->atom->getEntries();
        $forumTopics = array();
        foreach($topics as $entry) {
            $forumTopics[] = new ConnectionsForumsTopicReplies($entry);
        }
        return $forumTopics;
    }

    /**
     *
     * Enter description here ...
     */
    public function getName() {
        return $this->atom->getNodeContent("./td:label");
    }

    /**
     *
     * Enter description here ...
     */
    public function getCommunityId() {
        $id =  $this->atom->getNodeContent("snx:communityUuid");
        return $id;
    }


     /**
     *
     * Enter description here ...
     */
    public function getForumId() {    
        return $this->forumId;
    }

    
    /**
     *
     * Enter description here ...
     */
    public function getCreateForumsLink() {
            
        return $this->connectionsBaseUrl() . "/communities/service/html/communityview?communityUuid=" .  $this->getCommunityId() . "#fullpageWidgetId=" .  $this->getForumId() . "&startTopic=true";

    }
    
    public function getNumberOfForums(){
		$number = $this->atom->xquery("./opensearch:totalResults");
		return $number->item(0)->textContent;
	}
}
