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
require_once 'custom/include/IBMConnections/Models/ConnectionsUpdates.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class UpdatesAPI extends AbstractConnectionsAPI
{
	/**
	 * 
	 * Enter description here ...
	 * @return ConnectionsUpdates[]
	 */
	public function getCommunityUpdates($communityId, $pageNumber = 1, $searchText = '') {
		$this->getHttpClient()->resetParameters();		
		$this->getHttpClient()->setParameterGet("communityUUID", $communityId);
		//$this->getHttpClient()->setParameterGet("filter","status");
       	//if (empty($pageSize)) $pageSize = AbstractConnectionsAPI::MAX_PAGE_SIZE;
        $this->getHttpClient()->setParameterGet("ps", 10);
        $this->getHttpClient()->setParameterGet("page", $pageNumber);
        $result = $this->requestForPath("GET", "/news/atom/stories/community");
        // $result = $this->requestForPath("GET", "/news/atom/stories/statusupdates?source=all");
        // echo $result->getBody();
		$this->checkResult($result);
		$feed = IBMAtomFeed::loadFromString($result->getBody());
		
		//$retrievedPageNumber = ceil($feed->getStartIndex() / $feed->getItemsPerPage());
		//$this->setLastResultMetadata(
       // 	new ConnectionsResultMetadata($sortBy, $sortOrder, $feed->getItemsPerPage(), $retrievedPageNumber , $feed->getTotalResults())
       // );
		$entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $updates[] = new ConnectionsUpdates($entry, null);
        }
		//return $updates;
		 return array('list' => $updates,'metadata' => array());//$this->getLastResultMetadata());
	}
	
	public function postNote($communityId, $content) {
	/*
		//$parent = $this->getActivity($activityId);
        //$path = $this->extractRelativeUrlPath($parent->getCollectionLink());
        $path = "/connections/opensocial/rest/ublog/urn:lsid:lconn.ibm.com:communities.community:{$communityId}/@all";
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('note', 'http://www.ibm.com/xmlns/prod/sn/type');
        //$entry->setTitle($name);
        $entry->setContent($content);
 		$this->httpClient = $this->getHttpClient();
       	$this->httpClient->resetParameters();
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath('POST',$path,
                        array('Content-Type' => 'application/atom+xml',
                                'Content-Language' => 'en-US',));
                                
        echo $response->getStatus();
        print_r($response->getBody());
        
        
        boardId: "urn:lsid:lconn.ibm.com:communities.community:ac71e701-1f90-4679-965d-5c5755be2c77"

entry: {summary:rurururururururur, replies:{totalItems:0,…}, content:rurururururururur, objectType:note,…}
author: {connections:{state:active}, objectType:person,…}
connections: {state:active}
state: "active"
displayName: "Max Jensen"
id: "urn:lsid:lconn.ibm.com:profiles.person:756647c0-a7a2-1031-94f8-a3db76a297cb"
objectType: "person"
content: "rurururururururur"
id: "urn:lsid:lconn.ibm.com:communities.note:77f501ec-7507-4826-8347-7c9f4149b787"
likes: {totalItems:0,…}
totalItems: 0
url: "https://greenhouse.lotus.com/connections/opensocial/rest/ublog/@all/@all/urn:lsid:lconn.ibm.com:communities.note:77f501ec-7507-4826-8347-7c9f4149b787/likes"
objectType: "note"
published: "2013-02-26T11:20:53.729Z"
replies: {totalItems:0,…}
totalItems: 0
url: "https://greenhouse.lotus.com/connections/opensocial/rest/ublog/@all/@all/urn:lsid:lconn.ibm.com:communities.note:77f501ec-7507-4826-8347-7c9f4149b787/comments"
summary: "rurururururururur"
url: "https://greenhouse.lotus.com/communities/service/html/community/updates?communityUuid=ac71e701-1f90-4679-965d-5c5755be2c77&entryId=77f501ec-7507-4826-8347-7c9f4149b787"



Request URL:https://greenhouse.lotus.com/connections/opensocial/rest/ublog/urn:lsid:lconn.ibm.com:communities.community:ac71e701-1f90-4679-965d-5c5755be2c77/@all
Request Method:POST
Status Code:200 OK
Request Payload
{"content":"rurururururururur"}
        */
	}
	
	
	public function getCommunityStatusUpdates($communityId, $sortBy = "updated", $sortOrder = "desc",  $pageSize = null, $pageNumber = null) {
	    
		$this->getHttpClient()->resetParameters();		
		$this->getHttpClient()->setParameterGet("communityUUID", $communityId);
		//$this->getHttpClient()->setParameterGet("sortBy", $sortBy);
        //$this->getHttpClient()->setParameterGet("sortOrder", $sortOrder);
        $this->getHttpClient()->setParameterGet("ps", AbstractConnectionsAPI::MAX_PAGE_SIZE);
         $result = $this->requestForPath("GET", "/atom/stories/statusupdates");
		//$this->checkResult($result);
		$feed = IBMAtomFeed::loadFromString($result->getBody());
		$entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $updates[] = new ConnectionsUpdates($entry, null);
        }
		return $updates;
	}
	
	
}


