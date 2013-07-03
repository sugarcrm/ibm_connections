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



require_once 'custom/include/IBMConnections/FeedApi/IBMAbstractAtomItem.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomException.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomConstants.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomEntry.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMEditableAtomEntry.php';

/**
 * 
 * Enter description here ...
 * @author mario
 *
 */
class IBMAtomFeed extends IBMAbstractAtomItem {
    /**
     * 
     * Enter description here ...
     * @param unknown_type $xmlString
     */
    public static function loadFromString($xmlString) {
    	$dom = new DOMDocument();
        @$dom->loadXML($xmlString);
        $xpath = new DOMXPath($dom);
        $xpath->registerNameSpace("default", IBMAtomConstants::NS_ATOM);
		$nodeList = $xpath->query("/default:feed");
		if($nodeList->length > 0) {
			return new self($dom, $nodeList->item(0));
		}
		throw new IBMAtomException("The string does not contain any feed.");
    }
    
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $xmlString
     */
    public static function loadFromDOM($dom, $contextNode = null) {
    	return new self($dom, $contextNode);
    }
    
	/**
     * 
     * Enter description here ...
     */
    public function getTotalResults() {
    	$total = $this->getNodeContent("./opensearch:totalResults");
    	if(empty($total) && $total !== "0" && $total !== 0) $total = $this->getNodeContent("./os:totalResults");
    	if(empty($total) && $total !== "0" && $total !== 0) $total = $this->getNodeContent("./openSearch:totalResults");
    	return $total;
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getStartIndex() {
    	return $this->getNodeContent("./opensearch:startIndex");
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getItemsPerPage() {
    	return $this->getNodeContent("./opensearch:itemsPerPage");
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getEntries() {
		$nodeList = $this->xquery("default:entry");
    	
    	$retArray = array();
    	for($i = 0; $i < $nodeList->length; $i++) {
    		$retArray[] = new IBMAtomEntry($this->dom, $nodeList->item($i));
    	}
    	return $retArray;
    }
    
	/**
	* 
	*/
	public function getReplies(){
		return $this->getRepliesTo($this->getId());
	}
	
	//try to sort the entries based on the SNX position attribute
	private function entryPositionCmp($e1, $e2) {
		$position1 = intval($e1->getSNXPosition());
		$position2 = intval($e2->getSNXPosition());
		if ($position1 == $position2) {
	        return 0;
	    }
	    return ($position1 < $position2) ? -1 : 1;
	}
		
	/**
	 * 
	 */
	public function getRepliesTo($id){
		$nodeList = $this->xquery("./default:entry[./thr:in-reply-to[@ref='".$id."']]");
		$retArray = array();
		for($i = 0; $i < $nodeList->length; $i++) {
			$retArray[$i] = new IBMAtomEntry($this->dom, $nodeList->item($i));
		}
		
		$this->sortRepliesToReturnArray($retArray);
		
		return $retArray;
	}
	
	private function sortRepliesToReturnArray(&$retArray)
	{
		for ($i = 0; $i < count($retArray) - 1; $i++) {
			for ($j = $i + 1; $j < count($retArray); $j++) {
				if ($this->entryPositionCmp($retArray[$i], $retArray[$j]) == 1) {
					$tmp = $retArray[$i];
					$retArray[$i] = $retArray[$j];
					$retArray[$j] = $tmp;			
				}
			}
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
    public function getEditableEntries() {
    	$dom =  $this->dom;
    	$entryEltsList = $this->dom->getElementsByTagName('entry');
    	for($i=0;$i<$entryEltsList->length;$i++){
    		$entryElt = $entryEltsList->item($i);
    		$entryDom = new DOMDocument('1.0', 'UTF-8');
        	$entryDom->appendChild($entryDom->importNode($entryElt, true));
        	$dom = $entryDom;
        	$entryElt = $dom->getElementsByTagName('entry')->item(0);

        	$retArray[$i]= new IBMEditableAtomEntry($dom, $entryElt);
    		//Replace the entry in the feed with this either here or at the end of editing
    	}
    	return $retArray;
    }
}
