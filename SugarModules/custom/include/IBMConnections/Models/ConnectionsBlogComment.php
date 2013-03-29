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


require_once 'custom/include/IBMConnections/Models/ConnectionsModel.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsBlogComment extends ConnectionsModel
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
	public function getTitle() {
		return $this->atom->getTitle();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getId();
		$pattern = "urn:lsid:ibm.com:blogs:comment-";
		$idx = strpos( $origId , $pattern);
		$size = strlen($pattern);
		return substr($this->atom->getId(), $idx + $size, strlen($origId) - ($idx + $size));
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
	public function getPublishedDate() {
		return $this->formatTimeDate($this->atom->getPublishedDate());
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getAuthor() {
		$person = $this->atom->getAuthor();
		//$person = CommunitiesIntegration::toSugarProfile($person);
		return $person;
	}
	

	/**
	 * 
	 * Enter description here ...
	 */
	public function getLinkAlt() {
		return $this->atom->getLink(IBMAtomConstants::ATTR_LINK_ALT, IBMAtomConstants::ATTR_LINK_TYPE_HTML);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getTags() {
		return $this->atom->getTags();
	}
	public function getContent() {
		return $this->atom->getContent();
	}
}
