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



require_once 'custom/include/IBMConnections/Models/ConnectionsBlogEntry.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsBlog extends ConnectionsModel
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
    	return "/blogs/";
    }
    
	/**
	 * 
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getLink(IBMAtomConstants::ATTR_LINK_SELF);
		$origId = $origId['href'];
		
		if(!preg_match('#^(.+)/blogs/(.+)/feed/#', $origId, $matches)) {
			return null;
		}
		
		return $matches[2];
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
		$entries = $this->atom->getEntries();
		$blogEntries = array(); 
		foreach($entries as $entry) {
			$blogEntries[] = new ConnectionsBlogEntry($entry);
		}
		return $blogEntries;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCreateBlogEntryLink() {
		return $this->connectionsBaseUrl() . 
			"/blogs/roller-ui/authoring/weblog.do?method=create&weblog=" . $this->getId();
	}
}
