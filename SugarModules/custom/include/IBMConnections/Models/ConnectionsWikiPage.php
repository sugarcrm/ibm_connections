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
class ConnectionsWikiPage extends ConnectionsModel
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
	public function getTitle() {
		return $this->atom->getTitle();
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
	
	public function getContentSrc() {
		$nodeList = $this->atom->xquery("./default:content");
		 $full_path = $nodeList->item(0)->attributes->getNamedItem("src")->textContent;
		$size = strlen("urn:lsid:ibm.com:td:");
		return substr($full_path, strpos( $full_path , "/wikis/"));
	}
	
	public function getSummary() {
		$cont = $this->atom->getSummary();
		return $cont;
	}
    /**
     * 
     * Enter description here ...
     */
	public function getModified() {
		return $this->formatTimeDate($this->atom->getUpdatedDate());
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getModifier() {
		$contributor = $this->atom->xquery("./td:modifier");
		if($contributor->length > 0) {
			$person = $this->atom->toPerson($contributor->item(0));
			//$person = CommunitiesIntegration::toSugarProfile($person);
			return $person;
		}
		return null;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getHitCount() {
		return $this->atom->getNodeContent("./snx:rank[@scheme='http://www.ibm.com/xmlns/prod/sn/hit']");
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getRecommendationsCount() {
		return $this->atom->getNodeContent("./snx:rank[@scheme='http://www.ibm.com/xmlns/prod/sn/recommendations']");
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCommentsCount() {
		return $this->atom->getNodeContent("./snx:rank[@scheme='http://www.ibm.com/xmlns/prod/sn/comment']");
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
	
    /**
     * 
     * Enter description here ...
     */
	public function getExtendedContentUrl() {
		$link = $this->atom->getLink("enclosure");
		return $link['href'];
	}
}
