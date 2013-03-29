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



require_once 'custom/include/IBMConnections/Models/ConnectionsWikiPage.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsWiki extends ConnectionsModel
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
    	return "/wikis/basic/api";
    }
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getId();
		$idx = strpos( $origId , "urn:lsid:ibm.com:td:");
		$size = strlen("urn:lsid:ibm.com:td:");
		return substr($this->atom->getId(), $idx + $size, strlen($origId) - ($idx + $size));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getName() {
		$name = $this->atom->getNodeContent("./td:label");
		
		//This method is for Connections 4
		if(!$name) {
			$link = $this->atom->getLink(IBMAtomConstants::ATTR_LINK_ALT);
			if(!preg_match('$^(.+)/wikis/home/wiki/(.+)(\?|#)?$', $link['href'], $matches)) {
				return null;
			}
			$name = $matches[2];
		}
			
		return $name;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getPages() {
		$entries = $this->atom->getEntries();
		foreach($entries as $entry) {
			$wikiPages[] = new ConnectionsWikiPage($entry);
		}
		return $wikiPages;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCreateWikiPageLink() {
		return $this->connectionsBaseUrl() . 
			"/wikis/home#/wiki/" . $this->getName() . "/pages/create?rel=root";
	}
}
