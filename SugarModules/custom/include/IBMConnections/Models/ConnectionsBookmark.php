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




/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsBookmark extends ConnectionsModel
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
    	return "/communities/service/atom/community/bookmarks";
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
	public function getSummary() {
		return $this->atom->getSummary();
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getUpdatedDate() {
		return $this->formatTimeDate($this->atom->getUpdatedDate());
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getContributor() {
		$person = $this->atom->getContributor();
		//$person = CommunitiesIntegration::toSugarProfile($person);
		return $person;
	}
	public function getAuthor() {
		$person = $this->atom->getAuthor();
		//$person = CommunitiesIntegration::toSugarProfile($person);
		return $person;
	}
	
	/**
	 * 
	 * Returns the id of the bookmark
	 */
	public function getId(){
		return $this->atom->getId();
	}
	/**
	 * 
	 * Enter description here ...
	 */
	public function getLink() {
		$nodeList = $this->atom->xquery('./default:link');
		$retArray = array();
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->item($i)->attributes->getNamedItem("rel") == null && 
					$nodeList->item($i)->attributes->getNamedItem("scheme") == null){
				return $nodeList->item($i)->attributes->getNamedItem("href")->textContent;
			}
		}
		return null;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getTags() {
		return $this->atom->getTags();
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function isImportant() {
		return $this->atom->hasCategory("http://www.ibm.com/xmlns/prod/sn/flags", "important");
	}
	
}
