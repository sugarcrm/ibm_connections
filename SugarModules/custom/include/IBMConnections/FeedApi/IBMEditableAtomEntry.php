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




class IBMEditableAtomEntry extends IBMAtomEntry {
	
	/**
	 * Method to create an empty entry
	 * 
	 */
    public static function createEmptyEditableEntry(){
    	$dom = new DOMDocument('1.0', 'UTF-8');
        
    	$entryElt = $dom->createElement('entry');
        $dom->appendChild($entryElt);
        
        $domAttribute = $dom->createAttribute('xmlns');
		$domAttribute->value = 'http://www.w3.org/2005/Atom';
		$entryElt->appendChild($domAttribute);
		
		$domAttribute2 = $dom->createAttribute('xmlns:app');
		$domAttribute2->value = 'http://www.w3.org/2007/app';
		$entryElt->appendChild($domAttribute2);
		
		$domAttribute3 = $dom->createAttribute('xmlns:opensearch');
		$domAttribute3->value = 'http://a9.com/-/spec/opensearch/1.1/';
		$entryElt->appendChild($domAttribute3);
		
    	$domAttribute4 = $dom->createAttribute('xmlns:snx');
		$domAttribute4->value = 'http://www.ibm.com/xmlns/prod/sn';
		$entryElt->appendChild($domAttribute4);
		
        
		$domAttribute5 = $dom->createAttribute('xmlns:xhtml');
		$domAttribute5->value = 'http://www.w3.org/1999/xhtml';
		$entryElt->appendChild($domAttribute5);
		
		$domAttribute6 = $dom->createAttribute('xmlns:thr');
		$domAttribute6->value = 'http://purl.org/syndication/thread/1.0';
		$entryElt->appendChild($domAttribute6);

		$entryElt = $dom->getElementsByTagName('entry')->item(0);
        
		return new self($dom, $entryElt);
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $xpath
     * @param unknown_type $returnList
     */
	public function getNodeContent($node) {
		if(!$node) {
			return null;
		}
		if($node instanceof DOMNodeList) {
			if($node->length > 0) {
				$node = $node->item(0);
			} else {
				return null;
			}
		}
		
		$content = $node->textContent;
		if(($node->attributes != null) && ($node->attributes->getNamedItem("type") != null)) {
    		$type = $node->attributes->getNamedItem("type")->textContent;
    		if($type && $type == "html") {
    			return trim(html_entity_decode_utf8($content));
    		}
		}
		
		return trim($content);
	}
    
	/**
	 * (non-PHPdoc)
	 * @see IBMAbstractAtomItem::getTitle()
	 */
 	public function getTitle() {
		return $this->dom->getElementsByTagName('title')->item(0)->textContent;
     }
     
    /**
     * 
     * @param unknown_type $titleText
     */
	public function setTitle($titleText){
		$titleElt = $this->dom->createElement('title', $titleText);
		$titleElt->setAttribute('type', 'text');
		$nodelist = $this->dom->getElementsByTagName('title');
		if($nodelist->length > 0){
			$oldnode = $nodelist->item(0);
			$newnode = $this->dom->importNode($titleElt, true);
			$oldnode->parentNode->replaceChild($newnode, $oldnode);
		}
		else{
			$this->contextNode->appendChild($titleElt);
		}
	}
	
	/**
	 * 
	 */
	public function getDom() {
		return $this->dom;
	}
	
	
	/**
	 *
	 */
	public function getEntryNode() {
		return $this->contextNode;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IBMAbstractAtomItem::getLinks()
	 */
	public function getLinks() {
    	$nodeList = $this->dom->getElementsByTagName('link');
    	$retArray []= array();
    	for($i=0;$i<$nodeList->length;$i++){
    		$retArray[$i]["href"] = $nodeList->item($i)->attributes->getNamedItem("href")->textContent;
    		$retArray[$i]["rel"] = $nodeList->item($i)->attributes->getNamedItem("rel")->textContent;
    	}
    	return $retArray;
    }
    
    
    /**
     * 
     * @param unknown_type $categoryTerm
     * @param unknown_type $categoryScheme
     * @param unknown_type $categoryLabel
     * @param unknown_type $textContent
     */
	public function addCategory($categoryTerm, $categoryScheme, $categoryLabel=null, $textContent = null ){
		$categoryElt =$this->dom->createElement('category');
		$categoryElt->setAttribute('term', $categoryTerm);
		$categoryElt->setAttribute('scheme', $categoryScheme);
		if($categoryLabel){
			$categoryElt->setAttribute('label', $categoryLabel);
		}
        if( $textContent )
        {
            $categoryElt->textContent = $textContent;
        }
		$this->contextNode->appendChild($categoryElt);	
	}
	

	/**
     * Method which returns an array of categories
     */
    public function getCategories(){
    	$nodeList = $this->dom->getElementsByTagName('category');
    	$retArray [] = array();
		for($i = 0; $i < $nodeList->length; $i++) {
    		$retArray[$i]['term'] = $nodeList->item($i)->attributes->getNamedItem('term')->textContent;
    		if($nodeList->item($i)->attributes->getNamedItem("scheme"))
    		$retArray[$i]['scheme']= $nodeList->item($i)->attributes->getNamedItem('scheme')->textContent;
    		if($nodeList->item($i)->attributes->getNamedItem("label"))
    		$retArray[$i]['label'] =$nodeList->item($i)->attributes->getNamedItem('label')->textContent;
    	}
    	return $retArray;
    }
    
	/**
	 * (non-PHPdoc)
	 * @see IBMAbstractAtomItem::getAuthor()
	 */
	public function getAuthor(){
		$authorInfo =$this->dom->getElementsByTagName('author')->item(0);
		$authorName = $authorInfo->getElementsByTagName('name')->item(0)->textContent;
		$authorEmail = $authorInfo->getElementsByTagName('email')->item(0)->textContent;
		$retArray = array(
    			"name" => $authorName,
    			"email" => $authorEmail,
    		);
    	return $retArray;
	}

	/**
	 * Method which returns the Contributors Name and Email address
	 *
	 */
	public function getContributor(){
		$contributorNode =$this->dom->getElementsByTagName('contributor')->item(0);
		$contributorName = $contributorNode->getElementsByTagName('name')->item(0)->textContent;
		$contributorEmail = $contributorNode->getElementsByTagName('email')->item(0)->textContent;
		
		$retArray = array(
    			"name" => $contributorName,
    			"email" => $contributorEmail,
    		);
    	return $retArray;
	}
	
	public function getContent(){
		return $this->getNodeContent($this->dom->getElementsByTagName('content'));
	}
	
	public function setContent($content){
		$cdata = $this->dom->createCDATASection($content);
		$contentElt = $this->dom->createElement('content');
        $contentElt->setAttribute('type', 'html');
        $contentElt->appendChild($cdata);
        $this->contextNode->appendChild($contentElt);
	}

	public function getDomString(){
		return $this->dom->saveXML();
	}
	
	/**
	 * Method which returns the Entry summary
	 *
	 */
	public function getSummary(){
		return $this->getNodeContent($this->dom->getElementsByTagName('summary'));
	}
	
	public function setSummary($summaryText){
		$summaryElt = $this->dom->createElement('summary', $summaryText);
		$nodelist = $this->dom->getElementsByTagName('summary');
		if($nodelist->length>0){
			$oldnode = $nodelist->item(0);
			$newnode = $this->dom->importNode($summaryElt, true);
			$oldnode->parentNode->replaceChild($newnode, $oldnode);
		}
		else{
			$this->contextNode->appendChild($summaryElt);
		}

	}
	public function getPublishedDate(){
		$date = strtotime($this->dom->getElementsByTagName('published')->item(0)->textContent);
		return $date;
    }
	
    public function addTag($tag){
    	$this->addCategory($tag, null);
    	
    }
    	
}

