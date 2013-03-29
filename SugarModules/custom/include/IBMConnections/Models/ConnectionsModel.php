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



require_once 'include/TimeDate.php';


class ConnectionsDateTime extends TimeDate {
	public function isAlwaysDb()
    {
        return $this->always_db;
    }
    
    public function relativeTimeFormat($timestamp) { 
        $ts = time() - $timestamp + 240;//strtotime(str_replace("-","/",$d)); 
       // $ts = abs($ts);
        if ($ts > 31536000) {
        	$val = round($ts / 31536000, 0);
        	$unit ='YEAR';
        } 
        else if ($ts > 2419200) {
        		$val = round($ts / 2419200, 0);
        		$unit = 'MONTH';
        	}
        	else if ($ts > 604800) {
        		$val = round($ts / 604800, 0);
        		$unit = 'WEEK';
        	}
        		else if ($ts > 86400) {
        			$val = round($ts / 86400, 0);
        			$unit = 'DAY';
        		}
        	  		else if ($ts > 3600) {
        	  			$val = round($ts / 3600, 0);
        	  			$unit = 'HOUR';
        	  		} 
       			 		else if ($ts > 60) {
       			 			$val = round($ts / 60, 0);
       			 			$unit ='MINUTE';
       			 		} 
        					else { 
        						$val = $ts;
        						$unit = 'SECOND';
        					} 
        
       // if($val < 0) { 
       // 	$val = 0;
       // }
        
        if($val != 1) {
        	$unit .= 'S'; 
        }
        return array('value' => $val, 'unit' =>$unit); 
    } 
//Then I use it as follows; 
//    echo ucwords(ezDate('2006-09-07 18:42:00')).' Ago'; 
}

/**
 * 
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
abstract class ConnectionsModel
{   
	
	/**
	 * @var IBMAbstractAtomItem
	 */
	protected $atom = null;
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $httpClient
	 * @param IBMAtomEntry $atomEntry
	 */
    function __construct(IBMAbstractAtomItem $atom)
    {
    	$this->atom = $atom;
    }
	
    
    /**
     * 
     * Enter description here ...
     */
	abstract protected function serviceBaseUrl();
	
	
    /**
     * 
     * Enter description here ...
     * @param unknown_type $timestamp
     */
	protected function connectionsBaseUrl() {
	 	$linkSelf = $this->atom->getLink(IBMAtomConstants::ATTR_LINK_SELF);
	 	if($linkSelf == null) {
	 		return null;
	 	}
	 	$linkSelf = $linkSelf['href'];
	 	
		$pos = strpos($linkSelf, $this->serviceBaseUrl());
		if($pos == false) {
			return null;
		}
		return substr($linkSelf, 0, $pos);
	}
	
    /**
     * 
     * Enter description here ...
     * @param unknown_type $timestamp
     */
	protected function formatDate($timestamp) {
		$timedate = new ConnectionsDateTime();
		$rel_time = $timedate->relativeTimeFormat($timestamp);
		//return date($timedate->get_date_format(), $timestamp). "  -  ".$rel_time['value']." ".$rel_time['unit'];
		return $rel_time;//['value']." ".$rel_time['unit'];
	}
	
	protected function formatSugarDate($timestamp) {
		$timedate = new ConnectionsDateTime();
		return date($timedate->get_date_format(), $timestamp);
	}
	
    /**
     * 
     * Enter description here ...
     * @param unknown_type $timestamp
     */
	protected function formatTimeDate($timestamp) {
		$timedate = new ConnectionsDateTime();
$rel_time = $timedate->relativeTimeFormat($timestamp);
		//return date($timedate->get_date_time_format(), $timestamp). "  -  ".$rel_time['value']." ".$rel_time['unit'];
		return $rel_time;//['value']." ".$rel_time['unit'];
	}
	
	protected function userProfile($user_array) {
		return '<span class="vcard">
  				<a href="javascript:void(0);"class="fn url">' . $user_array['name'] . '</a>
  				<span class="x-lconn-userid" style="display: none;">' . $user_array['id'] . '</span>
			</span>
			
			';
		
	}
}
