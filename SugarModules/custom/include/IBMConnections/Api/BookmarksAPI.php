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
require_once 'custom/include/IBMConnections/Models/ConnectionsBookmarks.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class BookmarksAPI extends AbstractConnectionsAPI
{
	/**
	 * 
	 * Enter description here ...
	 * @return ConnectionsBookmark[]
	 */
	public function getCommunityBookmarks($communityId, $pageNumber = 1, $searchText = '' ) 
	{
		$path = "/communities/service/atom/community/bookmarks";
		$client = $this->getHttpClient();
		$client->resetParameters();
		$client->setParameterGet("includeTags", "true");
		$client->setParameterGet("ps", 15);
		$client->setParameterGet("page", $pageNumber);
		$client->setParameterGet("communityUuid", $communityId);
		if (!empty($searchText)) {
			$client->setParameterGet("search", $searchText);
		
		}
		$this->setHttpClient($client);
		$rawResponse = $this->requestForPath("GET", $path);
		if (empty($rawResponse) || !$this->checkResult($rawResponse)){
         	return array();
        }
        $reply = array('rawResponse' => $rawResponse->getBody());
		
		$feed = IBMAtomFeed::loadFromString($rawResponse->getBody());
		$entries = $feed->getEntries();
        foreach ($entries as $entry) {
             	$bookmarks[] = new ConnectionsBookmark($entry);
        }
		return $bookmarks;
	}
	
	
	/**
	 * Method to create a new community
	 * @param $name
	 * @param $description
	 * @param $type
	 * @return mixed
	 */
	public function createCommunityBookmark($communityId, $title, $href, $description, $tag = null, $important = false)
	{
		$entry = IBMEditableAtomEntry::createEmptyEditableEntry();
		$entry->addCategory('bookmark', 'http://www.ibm.com/xmlns/prod/sn/type');
		echo $href."  ";
		$entry->setTitle($title);
		$entry->setContent($description);
		if($tag){
			if(is_array($tag)){
				foreach($tag as $indTag){
					$entry->addTag($indTag);
				}
			}
			else{
				$entry->addTag($tag);
			}
		}
		if($important) {
			$entry->addCategory("important", "http://www.ibm.com/xmlns/prod/sn/flags");
		}
		
		$link = $entry->getDom()->createElement('link');
		$link->setAttribute('href', $href);
		$entry->getEntryNode()->appendChild($link);
		
		$this->getHttpClient()->setParameterGet("communityUuid", $communityId);
		$this->getHttpClient()->setRawData($entry->getDomString());
	
		$result = $this->requestForPath('POST', '/communities/service/atom/community/bookmarks',
			array(
				'Content-Type' => 'application/atom+xml',
				'Content-Language' => 'en-US',
			)
		);
	}
	
	public function deleteCommunityBookmark($bookmarkId) {
		$path = strstr($bookmarkId, 'service' );
		$path = '/communities/'.$path;
		
		$response = $this->requestForPath('DELETE', $path);
       /* if($response->getStatus() != 200) {
			$message = 'Unexpected httpResponse code  = ' . $response->getStatus() ;
            require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            throw new IBMConnectionsApiException( $message, CONNECTIONS_SERVICE_GENERIC, $response);
        }
*/
     }
}


