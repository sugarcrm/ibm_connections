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
abstract class ConnectionsActivityNode extends ConnectionsModel
{
	protected $parentFeed;
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $httpClient
	 * @param IBMAtomEntry $atomEntry
	 */
    function __construct(IBMAtomEntry $atomEntry, IBMAtomFeed $parentFeed)
    {
    	parent::__construct($atomEntry);
    	$this->parentFeed = $parentFeed;
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
	public function getAttachments() {
		$retArray = array();
		$fields = $this->atom->xquery('./snx:field[@type="file"]');
		for($i = 0; $i < $fields->length; $i++) {
			$enclosure = $this->atom->xquery("./default:link[@rel='enclosure']", $fields->item($i));
			if($enclosure->length <= 0) {
				continue;
			}
			$enclosure = $enclosure->item(0);
			$file = array();
			$file['id'] = $fields->item($i)->attributes->getNamedItem('fid')->textContent;
			$file['title'] = $fields->item($i)->attributes->getNamedItem('name')->textContent;
			$file['href'] = $enclosure->attributes->getNamedItem('href')->textContent;
			$file['length'] = $enclosure->attributes->getNamedItem('length')->textContent;
			$file['size'] = $enclosure->attributes->getNamedItem('size')->textContent;
			
			$pos = strripos($file['href'], "/");
			$file["fileName"] = substr($file['href'], $pos + 1, strlen($file['href']) - ($pos + 1));
			$file["fileName"] = urldecode($file["fileName"]);
			
			$pos = strripos($file['href'], ".");
			$file["fileExtension"] = substr($file['href'], $pos + 1, strlen($file['href']) - ($pos + 1));
			
			$retArray[] = $file;
		}
		return $retArray;
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
	public function getDueDate() {
		$dueDate = $this->atom->getNodeContent("./snx:duedate");
		if($dueDate) {
			$dueDate = strtotime($dueDate);
			return $this->formatDate($dueDate);
		}
		return null;
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getFormattedUpdatedDate() {
		return $this->formatTimeDate($this->atom->getUpdatedDate());
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getContributor() {
		$contributor = $this->atom->getContributor();
		//$contributor = CommunitiesIntegration::toSugarProfile($contributor);
		return $contributor;
	}
	
	public function getTags(){
		$tags = $this->atom->getTags();
		$stringOfTags ='';
		for($i=0;$i<count($tags);$i++){
			$stringOfTags = $stringOfTags.$tags[$i].' ';
		}
		return $stringOfTags;
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getIcon() {
		return $this->atom->getIcon();
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
	public function getContent() {
		return $this->atom->getContent();
	}
	
    /**
     * 
     * Enter description here ...
     */
	public function getExtendedContentUrl() {
		$link = $this->atom->getLink("enclosure");
		return $link['href'];
	}
	
	/**
     * 
     * Enter description here ...
     */
	public function getType() {
		$type_arr = $this->atom->getCategory(ConnectionsConstants::CATEGORY_SCHEME_TYPE);
		return $type_arr['term'];
	}
	
	/**
     * 
     * Enter description here ...
     */
	public function listNodes() {
		$entries = $this->parentFeed->getRepliesTo($this->atom->getId());
		//$entries = $this->parentFeed->getEntries();
		$retArray = array();
		foreach($entries as $entry) {
			$retArray[] = ConnectionsActivity::createActivityNodeFromEntry($entry, $this->parentFeed);
		}
		return $retArray;
	}
}
