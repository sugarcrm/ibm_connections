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


class IBMAtomException extends Exception
{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param Exception $previous
	 */
    public function __construct($description, Exception $previous = null) {
        parent::__construct ('IBMAtomException: '.$description, 0, $previous);
    }
}
