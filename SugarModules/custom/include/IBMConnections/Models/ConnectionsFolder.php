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
class ConnectionsFolder extends ConnectionsModel
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
		return $this->atom->getNodeContent("td:uuid");
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
		return $this->atom->getAuthor();
	}

    
    public function getFileLink()
    {
     	$reply = $this->atom->getLink("files");
    	return $reply['href']; 
    }
    
    public function getRank($scheme) {
		$nodeList = $this->atom->xquery("./snx:rank[@scheme='".$scheme."']");
		return $nodeList->item(0)->textContent;
	}
	
	public function getItemsCount(){
		return $this->getRank("http://www.ibm.com/xmlns/prod/sn/item");
	}
	
}
