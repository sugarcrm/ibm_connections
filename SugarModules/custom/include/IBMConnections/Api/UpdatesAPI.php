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
        $this->getHttpClient()->setParameterGet("lang", (!empty($GLOBALS['current_language']) ? $GLOBALS['current_language'] : 'en-US'));
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
	
	private function timestamp() {
  		// Gives us a timestamp in a format that Connections will use
  		$time = time();
  		return date("Y-m-d", $time) . 'T' . date("H:i:s", $time) .'.000Z';
  }
	
	public function postNote($communityId, $content) {
	  $communityUID = "urn:lsid:lconn.ibm.com:communities.community:". $communityId;
	  $path         = "/connections/opensocial/basic/rest/activitystreams/" . $communityUID . "/@all";
	  $json = array(
			"actor"   => array("id" => "@me"),
			"verb"	  => "post",
			"title"	  => "\${Actor} " . $content,
			"updated" => $this->timestamp(),
			"object"  => array(
      	"summary"	    => $content,
			  "replies"     => array("totalItems" => 0),
      	"objectType"  => "note",
			  "id"          => uniqid(),
      ),
      "target"  => array(
        "objectType"  => "community",
        "id"          => $communityUID,
        "url"         => $this->url . $path,
      ),
		);
		$headers = array(
		  'Content-Type' => 'application/json; charset=UTF-8',
		  'Accept'       => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		);
	  $this->getHttpClient()->resetParameters();
	  $this->getHttpClient()->setRawData(json_encode($json));
	  $response = $this->requestForPath("POST", $path, $headers);
	  //$GLOBALS['log']->fatal("POST to " . $this->url . $path . "\n" . json_encode($json) . "\nResponse: " . $response->getStatus());
	  return $response->getBody();
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


