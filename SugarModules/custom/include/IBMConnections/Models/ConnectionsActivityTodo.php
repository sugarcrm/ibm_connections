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
class ConnectionsActivityTodo extends ConnectionsActivityNode
{
	/**
	 * 
	 * Enter description here ...
	 */
	public function getCompleted() {
		$category = $this->atom->getCategory(ConnectionsConstants::CATEGORY_SCHEME_FLAGS);
		if($category && $category['term'] == "completed") {
			return true;
		} 
		
		return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getDueDate() {
		$dueDate = $this->atom->getNodeContent("./snx:duedate");
		if($dueDate) {
			$dueDate = strtotime($dueDate);
			return $this->formatSugarDate($dueDate);
		}
		return null;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getAssignee() {
		$nodeList = $this->atom->xquery("./snx:assignedto");
		if($nodeList->length > 0) {
			$node = $nodeList->item(0);
			$person = array(
				"name" => $node->attributes->getNamedItem("name")->textContent,
				"id" => $node->attributes->getNamedItem("userid")->textContent,
				"email" => $node->textContent
			);
			return $person;//null;// CommunitiesIntegration::toSugarProfile($person);
		}
		return null;
	}
}
