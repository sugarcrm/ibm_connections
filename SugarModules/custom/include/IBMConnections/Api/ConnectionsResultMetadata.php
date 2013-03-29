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
 * @author Mario Casciaro
 *
 */
class ConnectionsResultMetadata
{
	public $sortBy;
	public $sortOrder;
	public $pageSize;
	public $pageNumber;
	public $totalResults;
	
	public function __construct($sortBy = null, $sortOrder = null, $pageSize = null, $pageNumber = null, $totalResults = null) {
		$this->sortBy = $sortBy;
		$this->sortOrder = $sortOrder;
		$this->pageSize = $pageSize;
		$this->pageNumber = $pageNumber;
		$this->totalResults = $totalResults;
	}
}
