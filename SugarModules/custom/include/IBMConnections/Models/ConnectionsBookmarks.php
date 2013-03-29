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
require_once 'custom/include/IBMConnections/Models/ConnectionsBookmark.php';

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsBookmarks extends ConnectionsModel
{
	protected $sortBy;
	protected $sortOrder;
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $httpClient
	 * @param IBMAtomEntry $atomEntry
	 */
    function __construct(IBMAbstractAtomItem $atom, $sortBy, $sortOrder, $pageSize, $pageNumber)
    {
    	parent::__construct($atom);
    	$this->sortBy = $sortBy;
    	$this->sortOrder = $sortOrder;
    	$this->pageSize = $pageSize;
    	$this->pageNumber = $pageNumber;
    }
    
    
    protected function serviceBaseUrl() {
    	return "/communities/service/atom/community/bookmarks";
    }
    
	/**
	 * 
	 * Enter description here ...
	 */
	public function getId() {
		$origId = $this->atom->getLink(IBMAtomConstants::ATTR_LINK_SELF);
		$origId = $origId['href'];
		
		if(!preg_match('#^(.+)communityUuid=(.+)\&?#', $origId, $matches)) {
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
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public function sort_updated_asc($a, $b) {
		if($a->getUpdatedDate() == $b->getUpdatedDate()) {
			return 0;
		} 
		return ($a->getUpdatedDate() < $b->getUpdatedDate()) ? -1 : 1;
	}
	
	
	public function sort_updated_desc($a, $b) {
		return $this->sort_updated_asc($b, $a);
	}
	
	/**
	 * 
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public function sort_contributor_asc($a, $b) {
		$contrA = $a->getContributor();
		$contrB = $b->getContributor();
		return strcasecmp($contrA['name'] , $contrB['name']);
	}
	
	public function sort_contributor_desc($a, $b) {
		return $this->sort_contributor_asc($b, $a);
	}
	
	
	/**
	 *
	 * @param unknown_type $a
	 * @param unknown_type $b
	 */
	public function sort_title_asc($a, $b) {
		return strcasecmp($a->getTitle() , $b->getTitle());
	}
	
	public function sort_title_desc($a, $b) {
		return $this->sort_title_asc($b, $a);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getEntries() {
		$entries = $this->atom->getEntries();
		$blogEntries = array(); 
		$sort_func = "sort_".$this->sortBy."_".$this->sortOrder;
		//sorting
		//usort($entries, array($this, $sort_func));
		//pagination
		$pageNumber = $this->pageNumber;
		$pageSize = $this->pageSize;
		if(!$pageNumber ) {
		   foreach($entries as $entry) {
			  $blogEntries[] = new ConnectionsBookmark($entry);
		   }
			return $blogEntries;
		}
		else {
		   $firstElement = ($pageNumber-1) * $pageSize;
		   while($firstElement < ($pageNumber * $pageSize) && $firstElement < count($entries)){
		   		$blogEntries[] = new ConnectionsBookmark($entries[$firstElement]);
		   		$firstElement++;
		   }
		   return $blogEntries;	
		}		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getAddBookmarkLink() {
		return $this->connectionsBaseUrl() . 
			"/communities/service/html/community/bookmarks?contextAction=createEntry&communityUuid=" . $this->getId();
	}
	
	public function getNumberOfBookmarks(){
		$numberOfBookmarks = $this->atom->xquery("./opensearch:totalResults");
		return $numberOfBookmarks->item(0)->textContent;
	}
}
