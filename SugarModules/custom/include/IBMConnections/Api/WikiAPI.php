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
require_once 'custom/include/IBMConnections/Models/ConnectionsWiki.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class WikiAPI extends AbstractConnectionsAPI
{
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCommunityWiki($communityId, $page = 1, $searchText = '') {
		$this->getHttpClient()->resetParameters();
		$this->getHttpClient()->setParameterGet("sortBy", "modified");
		$this->getHttpClient()->setParameterGet("sortOrder", "desc");
		$this->getHttpClient()->setParameterGet("includeTags", "true");
		$this->getHttpClient()->setParameterGet("ps", 10);
		$this->getHttpClient()->setParameterGet("page", $page);
		if (!empty($searchText)){
			$this->getHttpClient()->setParameterGet("search", $searchText);
		}
		
		$result = $this->requestForPath("GET", "/wikis/basic/api/communitywiki/". $communityId."/feed");
		if (! $this->checkResult($result)) return;
		$wikiPages = array();
		$feed = IBMAtomFeed::loadFromString($result->getBody());
		
		$this->setLastResultMetadata(new ConnectionsResultMetadata($sortBy, $sortOrder));
		$wikis = new ConnectionsWiki($feed);
		return $wikis->getPages();
	}
	
	public function getCommunityWikiContent($communityId, $wikiId) {
		$this->getHttpClient()->resetParameters();
		$result = $this->requestForPath("GET", "/wikis/basic/api/wiki/{$communityId}/page/{$wikiId}/media");
		
		if (! $this->checkResult($result)) return ;
		return $result->getBody();
	}
}
//https://greenhouse.lotus.com/wikis/basic/api/wiki/40729c8d-8458-4274-b87c-39cedce3043c/page/e34d8d24-8db8-410d-b32b-7a62a4162a88/media

