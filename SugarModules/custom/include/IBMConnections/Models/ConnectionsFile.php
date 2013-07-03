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
class ConnectionsFile extends ConnectionsModel
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
    	return "/files/";
    }
    
    /**
     *
     * Enter description here ...
     */
    public function getId() {
    	$id = $this->atom->getId();
		//echo $id. '  '. str_replace('urn:lsid:ibm.com:td:','', $id);
		return str_replace('urn:lsid:ibm.com:td:','', $id);//getNodeContent("td:uuid");
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
    public function getLabel() {
        return $this->atom->getNodeContent("td:label");
    }
    
      /**
     *
     * Enter description here ...
     */
    public function getLibraryId() {
        return $this->atom->getNodeContent("td:libraryId");
    }
    
	/**
	 * 
	 * Enter description here ...
	 */
	/*public function getId() {
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
	/*public function getCategory() {
		return $this->atom->getNodeContent("category");
	}
	*/
	public function getRanks() 
	{
		$nodeList = $this->atom->xquery("./snx:rank");
		$retArray = array();
    	for($j = 0; $j < $nodeList->length; $j++) 
    	{
    		$node = $nodeList->item($j);
    		$retArray[$node->attributes->item(0)->textContent] = $node->textContent;
    	}
    	return $retArray;
	}
	
	public function getRank($scheme)
	{
		$nodeList = $this->atom->xquery("./snx:rank[@scheme='".$scheme."']");
		return $nodeList->item(0)->textContent;
	}
	
	public function getRecomendationsCount() 
	{
		return $this->getRank(ConnectionsConstants::RANK_SCHEME_RECOMENDATIONS);
	}
	
	public function getCommentsCount() 
	{
		return $this->getRank(ConnectionsConstants::RANK_SCHEME_COMMENT);
	}
	
	public function getDownloadsCount() 
	{
		return $this->getRank(ConnectionsConstants::RANK_SCHEME_HIT);
	}
	
	public function getVersion() 
	{
		return $this->getRank("http://www.ibm.com/xmlns/prod/sn/versions");
	}
	
	public function getFormattedPublishedDate()
	{
		return $this->formatTimeDate($this->atom->getPublishedDate());
	}
	
	public function getFormattedUpdatedDate()
	{
		return $this->formatTimeDate($this->atom->getUpdatedDate());
	}
	
	public function getContent() 
	{
		return $this->atom->getContent();
	}
	
	public function getAuthor() {
		$contributor = $this->atom->getAuthor();
		//$contributor = CommunitiesIntegration::toSugarProfile($contributor);
		return $contributor;
	}
	
	public function getMimeType() {
    	$reply = $this->atom->getLink("enclosure");
    	return $reply['type']; 
    }
    
    public function getFileSize() {
    	$reply = $this->atom->getLink("enclosure");
    	return $reply['length']; 
    }
    
    public function getVisibility() {
        $vis = $this->atom->getNodeContent("./td:visibility");
        if (empty($vis)){
        	$bool = $this->atom->getNodeContent("./td:restrictedVisibility");
        	if ($bool == "false" ) $vis = "public";
        	else $vis = "shared";
        }
        
        return $vis;
    }
    
    public function getReplies() {
        //return $this->atom->getNodeContent("td:visibility");
    }
    
    public function getFileLink()
    {
     	$reply = $this->atom->getLink("edit-media");
    	return $reply['href']; 
    }
    
    public function getViewLink()
    {
     	$reply = $this->atom->getLink("alternate");
    	$link = $reply['href']; 
    	if (empty($link)){
    		$reply = $this->atom->getLink();
    		$link = $reply['href']; 
    	
    	}
    	return $link;
    }
     
     public function getTags() {
		$tags = $this->atom->getTags();
		if(!$tags) {
			return '';
		}
		
		$stringOfTags ='';
		for($i=0;$i < count($tags);$i++){
			$stringOfTags = $stringOfTags.$tags[$i].' ';
		}
		return $stringOfTags;
	}
     
   
	/**
	 * 65
	 * Enter description here ...
	 */
/*	public function getEntries() {
		$entries = $this->atom->getEntries();
		$blogEntries = array(); 
		foreach($entries as $entry) {
			$blogEntries[] = new ConnectionsBlogEntry($entry);
		}
		return $blogEntries;
	}
	*/
	
}
