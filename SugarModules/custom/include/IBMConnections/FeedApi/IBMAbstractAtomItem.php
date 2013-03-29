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



/**
 * 
 * Enter description here ...
 * @author mario
 *
 */
abstract class IBMAbstractAtomItem {
	protected $dom;
	protected $domXpath;
	protected $contextNode;
	
	const DEFAULT_NS = "default";
	
	/**
	 * 
	 * Enter description here ...
	 */
	protected function __construct($dom, $contextNode) {
		$this->dom = $dom;
		$this->contextNode = $contextNode;
    }
    
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $query
     * @param unknown_type $contextNode
     */
	public function xquery($query, $contextNode = NULL) {
		if(!$this->domXpath) {
			$this->domXpath = new DomXPath($this->dom);
			$this->domXpath->registerNameSpace('default', IBMAtomConstants::NS_ATOM);
		}
		$result = @$this->domXpath->query($query, $contextNode ? $contextNode : $this->contextNode);
		return $result;
	}
    
    
    public function getDom() {
		return $this->dom;
	}
	
	public function getEntryNode() {
		return $this->contextNode;
	}
    /**
     * 
     * Enter description here ...
     * @param unknown_type $xpath
     * @param unknown_type $returnList
     */
	public function getNodeContent($xpath, $contextNode = NULL) {
		$nodeList = $this->xquery($xpath, $contextNode);
		if($nodeList->length > 0 ) {
			$node = $nodeList->item(0);
			$content = $node->textContent;
			
		    $type = $node->attributes->getNamedItem("type");
            if ( $type ) {
                $type = $type->textContent;
            }
            
			if($type && $type == "html") {
				return trim(html_entity_decode_utf8($content));
			}
			
			return trim($content);
		}
		return null;
	}
    
    /**
     * 
     * Enter description here ...
     */
    public function getId() {
    	return trim($this->getNodeContent("./default:id"));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getTitle() {
    	return $this->getNodeContent("./default:title");
    }
    
	/**
	* 
	*/
	public function getCategory($scheme){
		$nodeList = $this->xquery("./default:category[@scheme='".$scheme."']");
		$retArray = null;
		if($nodeList->length > 0) {
			$retArray = array(
				"term" => $nodeList->item(0)->attributes->getNamedItem("term")->textContent,
				"label" => $nodeList->item(0)->attributes->getNamedItem("label")->textContent,
			);
		}
		return $retArray;
	}
	
	/**
	 *
	 */
	public function getCategories($scheme){
		$nodeList = $this->xquery("./default:category[@scheme='".$scheme."']");
		$retArray = array();
		
		for($i = 0; $i < $nodeList->length; $i++) {                           
                if ($nodeList->item($i)->attributes->getNamedItem("term") != null) {
                    $retArray[] = array(
                        "term" => $nodeList->item($i)->attributes->getNamedItem("term")->textContent,
                      );                  
                }
                if ($nodeList->item($i)->attributes->getNamedItem("label") != null) { // May not always be present. 
                    if (sizeof($retArray) == 0) {
                         $retArray[] = array(
                        "label" => $nodeList->item($i)->attributes->getNamedItem("label")->textContent,
                      );                  
                    }  else {   //append to array.      
                        $retArray["label"] = $nodeList->item($i)->attributes->getNamedItem("label")->textContent;
                    }
                }                               
        }
		return $retArray;
	}
	
	/**
	 * 
	 * @param unknown_type $scheme
	 * @param unknown_type $term
	 */
	public function hasCategory($scheme, $term){
		foreach($this->getCategories($scheme) as $cat) {
			if($cat['term'] == $term) {
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * 
	 */
	public function getTags(){
		$nodeList = $this->xquery('./default:category');
		$retArray = array();
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->item($i)->attributes->getNamedItem("scheme")==null){
				$retArray[] = $nodeList->item($i)->attributes->getNamedItem("term")->textContent;
			}
		}
		return $retArray;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getLinks($rel, $type = null) {
    	$nodeList = array();
    	if($type == null) {
    		$nodeList = $this->xquery("./default:link[contains(@rel,'".$rel."')]");
    	} else {
    		$nodeList = $this->xquery("./default:link[contains(@rel,'".$rel."')][contains(@type,'".$type."')]");
    	}
    	$retArray = array();
    	for($j = 0; $j < $nodeList->length; $j++) {
    		$retArray[$j] = array();
    		$node = $nodeList->item($j);
    		for($i = 0; $i < $node->attributes->length; $i++) {
    			$retArray[$j][$node->attributes->item($i)->localName] = $node->attributes->item($i)->textContent;
    		}
    	}
    	return $retArray;
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getLink($rel, $type = null) {
    	$links = $this->getLinks($rel, $type);
    	if(empty($links)) {
    		return null;
    	}
    	
    	return $links[0];
    }

    /**
     * 
     * Enter description here ...
     */
    public function getPublishedDate(){
    	return strtotime($this->getNodeContent("./default:published"));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getUpdatedDate(){
    	return strtotime($this->getNodeContent("./default:updated"));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getIcon(){
    	return $this->getNodeContent("./default:icon");
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $contextNode
     */
    public function toPerson($contextNode) {
    	$name = $this->getNodeContent("./default:name", $contextNode);
		$email = $this->getNodeContent("./default:email", $contextNode);
		$id = $this->getNodeContent("./snx:userid", $contextNode);
		$status = $this->getNodeContent("./snx:userState", $contextNode);
		$retArray = array(
    			"name" => trim($name),
    			"email" => trim($email),
				"status" => $status,
				"id" => $id
    		);
    	return $retArray;
    }
    
	/**
	 * Method which returns the Authors Name and Email address
	 */
	public function getAuthor(){
		$authorInfo =$this->xquery("./default:author");
		if($authorInfo->length > 0) {
			return $this->toPerson($authorInfo->item(0));
		}
		return null;
	}
	
	/**
	 * Method which returns the Contributors Name and Email address
	 *
	 */
	public function getContributor(){
		$contributorNode =$this->xquery("./default:contributor");
		if($contributorNode->length > 0) {
			return $this->toPerson($contributorNode->item(0));
		}
		return null;
	}
	
	/**
	 * Method which returns the Entry summary
	 *
	 */
	public function getSummary(){
		return $this->getNodeContent("./default:summary");
	}
	
	/**
	 * 
	 */
	public function getContent(){
		return $this->getNodeContent("./default:content");
	}
	
    public function getLogo(){
    	return $this->getNodeContent("./default:logo");
    }
    
}
