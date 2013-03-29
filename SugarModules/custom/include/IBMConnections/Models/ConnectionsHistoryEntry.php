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



//require_once 'custom/include/IBMConnections/Services/CommunitiesIntegration.php';

/**
 * 
 * Enter description here ...
 * @author mario
 *
 */
class ConnectionsHistoryEntry extends ConnectionsModel
{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $httpClient
	 * @param IBMAtomEntry $atomEntry
	 */
    function __construct(IBMAtomEntry $atomEntry)
    {
    	parent::__construct($atomEntry);
    }
    
    
    protected function serviceBaseUrl() {
    	return "/activities/service/atom2/";
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
	public function getFormattedDescription() {
		$summary = $this->atom->getSummary();
		$summary = str_replace('class="actevent.entry"', "class='actevent.entry' target='_blank'", $summary);
		
		$search = "</span>";
		$pos = strripos($summary, $search);
		if($pos !== false) {
			return substr($summary, $pos + strlen($search), strlen($summary) - ($pos + strlen($search)));
		} else {
			$author = $this->getAuthor();
			return str_replace($author['name'], "", $summary);
		}
	}
	
	/**
     * 
     * Enter description here ...
     */
	public function getTimeDate() {
		return $this->formatTimeDate($this->atom->getUpdatedDate());
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getId();
		$idx = strpos( $origId , "urn:lsid:ibm.com:oa:");
		$size = strlen("urn:lsid:ibm.com:oa:");
		return substr($this->atom->getId(), $idx + $size, strlen($origId) - ($idx + $size));
	}
	
	
    /**
     * 
     * Enter description here ...
     */
	public function getAuthor() {
		$contributor = $this->atom->getAuthor();
		//$contributor = CommunitiesIntegration::toSugarProfile($contributor);
		return $contributor;
	}
}
