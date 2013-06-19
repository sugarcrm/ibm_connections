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
require_once 'custom/include/IBMConnections/Models/ConnectionsBlog.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class BlogAPI extends AbstractConnectionsAPI
{
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCommunityBlog($communityId, $page = 1, $searchText = "") {
		$this->getHttpClient()->resetParameters();
		--$page;
		if (!empty($searchText)){
			$this->getHttpClient()->setParameterGet("commUuid", $communityId);
			$this->getHttpClient()->setParameterGet("t", "entry");
			$this->getHttpClient()->setParameterGet("search",$searchText);
			$this->getHttpClient()->setParameterGet("sortby", "0");
			$this->getHttpClient()->setParameterGet("order","desc");
			
		}
		else {
			$this->getHttpClient()->setParameterGet("sortBy", "modified");
			$this->getHttpClient()->setParameterGet("sortOrder","desc");
			//$this->getHttpClient()->setParameterGet("page", $page);
		
		}
		//?search=social&t=entry&ps=5&page=0&sortby=0&order=desc&lang=en_us
		$this->getHttpClient()->setParameterGet("includeTags", "true");
		$this->getHttpClient()->setParameterGet("ps", 5);
		$this->getHttpClient()->setParameterGet("page", $page);
		$this->getHttpClient()->setParameterGet("lang", (!empty($GLOBALS['current_language']) ? $GLOBALS['current_language'] : 'en-US'));
		///atom/blogs?commUuid={resourceId}&blogType=communityblog
		//$result = $this->requestForPath("GET", "blogs/atom/blogs?commUuid={$communityId}&blogType=communityblog");
		$result = $this->requestForPath("GET", "blogs/roller-ui/rendering/feed/{$communityId}/entries/atom");
		if (empty($result) || !$this->checkResult($result)){
         	return array();
        }
		try{
			$feed = IBMAtomFeed::loadFromString($result->getBody());
			$blog = new ConnectionsBlog($feed);
			return $blog->getEntries();
		}
		catch(Exception $e)
		{
			return;
		}
	}
	
}


