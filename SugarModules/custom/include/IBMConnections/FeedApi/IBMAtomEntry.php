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
 require_once('custom/include/IBMConnections/FeedApi/IBMAbstractAtomItem.php');
class IBMAtomEntry extends IBMAbstractAtomItem{
	
	/**
     * 
     * Enter description here ...
     * @param unknown_type $xmlString
     */
    public static function loadFromString($xmlString) {
    	$dom = new DOMDocument();
        $dom->loadXML($xmlString);
        $xpath = new DOMXPath($dom);
        $xpath->registerNameSpace("default", IBMAtomConstants::NS_ATOM);
		$nodeList = $xpath->query("/default:entry");
		if($nodeList->length > 0) {
			return new self($dom, $nodeList->item(0));
		}
		throw new IBMAtomException("The string does not contain any entry.");
    }
	
	public function getSNXPosition(){
		return $this->getNodeContent("./snx:position");
	}
}
