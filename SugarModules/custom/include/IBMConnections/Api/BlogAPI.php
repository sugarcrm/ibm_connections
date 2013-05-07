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
		$this->checkResult($result);
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
	
	
	public function getBlogComments($communityId, $blogId) {
	//echo "blogs/{$communityId}/api/entrycomments/{$blogId}";
		$this->getHttpClient()->resetParameters();
		$this->getHttpClient(true)->setParameterGet("ps", 150);
		$this->getHttpClient()->setParameterGet("lang", (!empty($GLOBALS['current_language']) ? $GLOBALS['current_language'] : 'en-US'));
		$result = $this->requestForPath("GET", "blogs/roller-ui/rendering/df62d3f7-1837-4f7b-900e-af7b2cdf937c/feed/entrycomments/una_piattaforma_che_%25C3%25A8_un_vero_e_proprio_social_network_molto_filtrato/atom?lang=en_us&authenticate=no");
		//$result = $this->requestForPath("GET", "blogs/{$communityId}/api/entrycomments/{$blogId}");
		return $result->getBody();
		echo "<pre>";
		print_r($result->getBody);
		print_r($result);
		echo "</pre>";
		$this->checkResult($result);
		try{
			$feed = IBMAtomFeed::loadFromString($result->getBody());
			echo "<pre>";
		print_r($feed);
		echo "</pre>";
	        $entries = $feed->getEntries();
    	    $comments = array();
    	    foreach ($entries as $entry) {
    	        $comments[] = new ConnectionsBlogComments($entry);
    	    }
			return $comments;
		}
		catch(Exception $e)
		{
			return;
		}
	}
}


