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



require_once 'custom/include/IBMConnections/Models/ConnectionsActivity.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsActivities extends ConnectionsModel
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
    	return "/activities/service/";
    }
    
	
	/**
	 *
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getId();
		$idx = strpos( $origId , "/activities/service/atom2/activities?commUuid=");
		$size = strlen("/activities/service/atom2/activities?commUuid=");
		return substr($origId, $idx + $size, strlen($origId) - ($idx + $size));
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
		$activityEntries = array(); 
		foreach($entries as $entry) {
			$activityEntries[] = new ConnectionsActivity($entry);
		}
		return $activityEntries;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCreateActivityLink() {
		return $this->connectionsBaseUrl() . "/activities/service/html/mainpage?commUuid=" . 
			$this->getId() . "#communityActivityForm," . $this->getId();
	}
}
