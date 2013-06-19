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



require_once 'custom/include/IBMConnections/Api/AbstractConnectionsAPI.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsForums.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ForumsAPI extends AbstractConnectionsAPI
{
	/**
	 * 
	 * Enter description here ...
	 * @return ConnectionsForums[]
	 */
	public function getCommunityForums($communityId, $pageNumber = 1, $searchText = '') 
	{
	    $feed = $this->getRemoteAppFeed($communityId);
	    if (empty($feed)) return;
      	$sortBy = "created";
      	$sortOrder = "desc"; 
      	$pageSize = 10;
      	
        $entries = array();    
        $entries = $feed->getEntries();
       
	    for($i = 0; $i < sizeof($entries); $i++) {             
            $entry = $entries[$i];    //Array of IBMAtomEntries
            $retArray = $entry->getCategories("http://www.ibm.com/xmlns/prod/sn/type"); //Get category with this scheme
            for($k = 0; $k < sizeof($retArray); $k++) {
               if(strcmp($retArray[$k]['term'],"Forum") == 0) {                  
                     $forumWidgetIdLongForm = $entry->getId();                 
                     $forumWidgetId = strrev(strtok(strrev($forumWidgetIdLongForm), '-')); //Retrieve string after '-'
                   
               } 
            }                   
        }
    
    	$path = "/forums/atom/topics";
		$this->getHttpClient()->resetParameters();		

		if (!empty($searchText)) {
			$this->getHttpClient()->setParameterGet("search", $searchText);
		}
		
		$this->getHttpClient()->setParameterGet("communityUuid", $communityId);
		$this->getHttpClient()->setParameterGet("sortBy", $sortBy);
        $this->getHttpClient()->setParameterGet("sortOrder", $sortOrder);
        $this->getHttpClient()->setParameterGet('includeTags', 'true');
        $this->getHttpClient()->setParameterGet("ps", $pageSize);
        $this->getHttpClient()->setParameterGet("page", $pageNumber);
        $result = $this->requestForPath("GET", $path);
		if (empty($result) || !$this->checkResult($result)){
         	return ;
        }
		
		$feed = IBMAtomFeed::loadFromString($result->getBody());
		
		$this->setLastResultMetadata(new ConnectionsResultMetadata($sortBy, $sortOrder, $pageSize, $pageNumber));
		
		return new ConnectionsForums($feed, $forumWidgetId, $sortBy, $sortOrder, $pageSize, $pageNumber);
	}
	
	public function getForumReplies($id) {
	    
	    $this->getHttpClient()->resetParameters();		

		$this->getHttpClient()->setParameterGet("topicUuid", $id);
        $this->getHttpClient()->setParameterGet("ps", 150);
        $this->getHttpClient()->setParameterGet("sortBy", "lastmod");
        $this->getHttpClient()->setParameterGet("sortOrder", "asc");
        $result = $this->requestForPath("GET", "/forums/atom/replies");
        	
		if (empty($result) || !$this->checkResult($result)){
         	return array();
        }
		$feed = IBMAtomFeed::loadFromString($result->getBody());
		$all =  new ConnectionsForums($feed, '');
		$topics = $all->getReplies();
        return $topics;
	}
	public function replyToTopic($topicId, $title, $content)
	{
		$this->getHttpClient()->resetParameters();
		
		$entry = IBMEditableAtomEntry::createEmptyEditableEntry();
		$entry->addCategory('forum-reply', 'http://www.ibm.com/xmlns/prod/sn/type');
		//$entry->addCategory('answer', 'http://www.ibm.com/xmlns/prod/sn/flags');
		
		$entry->setTitle($title);
		$entry->setContent($content);
		
		$elt =$entry->getDom()->createElement('thr:in-reply-to');
		$elt->setAttribute('ref', "urn:lsid:ibm.com:forum:" . $topicId);
		$elt->setAttribute('href', $this->url . "/forums/atom/topic?topicUuid=" . $topicId);
		$elt->setAttribute('type' , "application/atom+xml");
		$elt->setAttribute('xmlns:thr', "http://purl.org/syndication/thread/1.0");
		
		$entry->getEntryNode()->appendChild($elt);	
		$this->getHttpClient()->setParameterPost("topicUuid", $topicId);
		$this->getHttpClient()->setRawData($entry->getDomString());
		$result = $this->requestForPath('POST', '/forums/atom/replies?topicUuid='.$topicId,
			array(
				'Content-Type' => 'application/atom+xml',
				'Content-Language' => 'en-US',
			));
	
	}
	//href="https://greenhouse.lotus.com/forums/atom/replies?replyUuid=97bd0a6e-1a9f-4ca1-9173-2054902053ab" 
	
	public function replyToReply($replyId, $title, $content)
	{
		$this->getHttpClient()->resetParameters();
		
		$entry = IBMEditableAtomEntry::createEmptyEditableEntry();
		$entry->addCategory('forum-reply', 'http://www.ibm.com/xmlns/prod/sn/type');
		//$entry->addCategory('answer', 'http://www.ibm.com/xmlns/prod/sn/flags');
		
		$entry->setTitle($title);
		$entry->setContent($content);
		
		$elt =$entry->getDom()->createElement('thr:in-reply-to');
		$elt->setAttribute('ref', "urn:lsid:ibm.com:forum:" . $replyId);
		$elt->setAttribute('href', $this->url . "/forums/atom/replies?replyUuid=" . $replyId);
		$elt->setAttribute('type' , "application/atom+xml");
		$elt->setAttribute('xmlns:thr', "http://purl.org/syndication/thread/1.0");
		
		$entry->getEntryNode()->appendChild($elt);	
		$this->getHttpClient()->setParameterPost("replyUuid", $replyId);
		$this->getHttpClient()->setRawData($entry->getDomString());
		$result = $this->requestForPath('POST', '/forums/atom/reply?replyUuid='.$replyId,
			array(
				'Content-Type' => 'application/atom+xml',
				'Content-Language' => 'en-US',
			));
	}
	
	public function createTopic($communityId, $title, $content, $tags = array(), $isQuestion = false)
	{
		
		$entry = IBMEditableAtomEntry::createEmptyEditableEntry();
		$entry->addCategory('forum-topic', 'http://www.ibm.com/xmlns/prod/sn/type');
		if($isQuestion)	$entry->addCategory('question', 'http://www.ibm.com/xmlns/prod/sn/flags');
		$entry->setTitle($title);
		$entry->setContent($content);
		foreach($tags as $tag) {
			if (!empty($tag)) 	$entry->addTag($tag);
        }
		$this->getHttpClient()->resetParameters();		
		$this->getHttpClient()->setParameterPost("communityUuid", $communityId);
		$this->getHttpClient()->setRawData($entry->getDomString());
		$result = $this->requestForPath('POST', '/forums/atom/topics?communityUuid='.$communityId,
			array(
				'Content-Type' => 'application/atom+xml',
				'Content-Language' => 'en-US',
			));
	
	}
}
