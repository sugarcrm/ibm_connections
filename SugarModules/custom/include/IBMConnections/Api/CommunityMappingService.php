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



require_once 'custom/include/IBMConnections/Exceptions/IBMDBException.php';
require_once('custom/include/IBMCore/Logger/SFALogger.php');
require_once 'custom/include/Helpers/IBMHelper.php';

/**
 * Utility Methods for accessing ibm_*_communities database table.
 *
 * sid - sugar id
 * cid - community id
 *
 * @author Niall Ryan
 * @author Mario Casciaro
 */
class CommunityMappingService {
    const OPPORTUNITY_COMMUNITY_TBL = 'ibm_opportunities_communities';
    const ACCOUNT_COMMUNITY_TBL = 'ibm_accounts_communities';

    /**
     * @var SFALogger
     */
    private $LOGGER;

    public function __construct() {
        $this->LOGGER = SFALogger::getLog('connections');
    }

    /**
     * 
     * Enter description here ...
     * @param unknown_type $bean
     */
    public function emptyCommunityMapping($bean) {
        $db = IBMDBManagerWrapper::getInstance();
        try {
            $db->startTransaction();

            $cid = $this->getCommunityMapping($this->getMappingKey($bean));
            if ($cid == null) {
                $db->commit();
                return;
            }

            $tableName = $this->getTableName($bean);
            $this->removeCommunityMapping($tableName, $cid, $this->getMappingKey($bean));

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
        }
    }

    /**
     * checks local sugar db to see if a mapping exists
     * @param $sid of the opp or account
     */
    public function hasMappedCommunity($bean) {
        return $this->getMappedCommunity($bean) ? true : false;
    }

    
    /**
     * 
     * @param unknown_type $bean
     */
    public function getMappingKey($bean) {
    	//build the final id in case of site clients
    	if ($bean->module_name == 'Accounts') {
	    	if(strtoupper($bean->ccms_level) === "SC" || strtoupper($bean->ccms_level) === "DC") {
	    		$anchorId = IBMHelper::getClass('Accounts')->getAnchorSite($bean);
    		} else {
    			$anchorId = $bean->id;
    		}
    		
    		$level = strtoupper($bean->ccms_level);
    		$id = $anchorId . "-" . $level;
    	} else {
    		$id = $bean->id;
    	}
    	
    	return $id;
    }
    
    
    /**
     * -return the id of the community mapped to this sid
     * @param sid $
     */
    public function getMappedCommunity($bean) {
        $cid = null;
        $tableName = $this->getTableName($bean);
        if ($tableName) {
            $cid = $this->getConnectionsId($tableName, $this->getMappingKey($bean));
        }

        return $cid;
    }

    /**
     * 
     * Enter description here ...
     * @param $tablename
     * @param $connId
     * @param $sid
     */
    public function addMapping($bean, $connId) {
        $tablename = $this->getTableName($bean);
        $sid = $this->getMappingKey($bean);
        require_once('custom/metadata/' . $tablename . 'MetaData.php');
        global $dictionary;

        $date_modified = $GLOBALS['db']
                ->convert("'" . $GLOBALS['timedate']->nowDb() . "'", 'datetime');
        $query = 'INSERT into ' . $tablename . ' ( '
                . $dictionary[$tablename]['external_mappings']['connections']
                . ', ' . $dictionary[$tablename]['external_mappings']['sugar']
                . ', deleted, date_modified ) ' . 'VALUES (\'' . $connId
                . '\', \'' . $sid . '\', 0 , ' . $date_modified . ' )';

        $this->LOGGER->debug($query);
        $result = $GLOBALS['db']
                ->query($query, false,
                        'Creating a new mapping in table < ' . $tablename
                                . ' > ' . $query);
    }

    /**
     * Generic function to delete a mapping between a Connections Community ID and Sugar Module ID
     * @param $tablename
     * @param $connId - Connections Community ID
     * @param $sid - Sugar Module ID
     * @return boolean
     */
    protected function removeCommunityMapping($tablename, $connId, $sid) {
        require_once('custom/metadata/' . $tablename . 'MetaData.php');
        global $dictionary;

        if ($sid == null && $connId != null) {
            $query = 'delete from  ' . $tablename . ' where '
                    . $dictionary[$tablename]['external_mappings']['connections']
                    . ' = "' . $connId . '";';
        } else if ($connId == null && $sid != null) {
            $connId = $this->getConnectionsId($tablename, $sid);
            $query = 'delete from ' . $tablename . ' where '
                    . $dictionary[$tablename]['external_mappings']['connections']
                    . ' = "' . $connId . '" or '
                    . $dictionary[$tablename]['external_mappings']['sugar']
                    . ' =  "' . $sid . '";';
        } else {
            $query = 'delete from  ' . $tablename . ' where '
                    . $dictionary[$tablename]['external_mappings']['connections']
                    . ' = "' . $connId . '" or '
                    . $dictionary[$tablename]['external_mappings']['sugar']
                    . ' =  "' . $sid . '";';
        }

        $this->LOGGER->debug($query);
        $rs = $GLOBALS['db']->query($query) or die;
        $this->LOGGER->debug("hardDelete result: " . $rs);

        return $rs;
    }

    /**
     * Helper method to retrieve table name based on the type of bean.
     * @param $bean
     * @return null|string
     */
    protected function getTableName($bean) {
        $tableName = null;
        switch (get_class($bean)) {
            case 'Account':
                $tableName = CommunityMappingService::ACCOUNT_COMMUNITY_TBL;
                break;
            case 'Opportunity':
                $tableName = CommunityMappingService::OPPORTUNITY_COMMUNITY_TBL;
                break;

            default:
                // no mapping table defined..
                // ignore
        }
        return $tableName;
    }

    /**
     * 
     * Enter description here ...
     * @param $tablename
     * @param $connId
     * @param $sid
     */
    protected function insert($tablename, $connId, $sid) {
        require_once('custom/metadata/' . $tablename . 'MetaData.php');
        global $dictionary;

        $date_modified = $GLOBALS['db']
                ->convert("'" . $GLOBALS['timedate']->nowDb() . "'", 'datetime');
        $query = 'INSERT into ' . $tablename . ' ( '
                . $dictionary[$tablename]['external_mappings']['connections']
                . ', ' . $dictionary[$tablename]['external_mappings']['sugar']
                . ', deleted, date_modified ) ' . 'VALUES (\'' . $connId
                . '\', \'' . $sid . '\', 0 , ' . $date_modified . ' )';

        $this->LOGGER->debug($query);
        $GLOBALS['db']
                ->query($query, false,
                        'Creating a new mapping in table < ' . $tablename
                                . ' > ' . $query);
    }

    /**
     * Generic method to retrieve a Connections ID based on a Sugar ID
     * @param $tablename
     * @param $sugar id
     * @return $connectionsId
     */
    protected function getConnectionsId($tablename, $sid) {
        require_once('custom/metadata/' . $tablename . 'MetaData.php');
        global $dictionary;

        $connId = null;
        $query = 'select '
                . $dictionary[$tablename]['external_mappings']['connections']
                . ' from  ' . $tablename . ' where '
                . $dictionary[$tablename]['external_mappings']['sugar']
                . ' = \'' . $sid . '\' and deleted = 0';

        $this->LOGGER->debug($query);
        $rs = $GLOBALS['db']->query($query);
        while ($row = $GLOBALS['db']->fetchByAssoc($rs)) {
            $connId = $row[$dictionary[$tablename]['external_mappings']['connections']];
            break;
        }
        return $connId;
    }

    /**
     * Generic method to insert a Connections ID mapped to a Sugar ID
     * @param $tablename
     * @param $sugar id
     * @param $connections id
     * @return boolean
     */
    protected function set($tablename, $connId, $sid) {
        require_once('custom/metadata/' . $tablename . 'MetaData.php');
        global $dictionary;

        $query = 'insert into' . $tablename . ' ('
                . $dictionary[$tablename]['external_mappings']['connections']
                . ', ' . $dictionary[$tablename]['external_mappings']['sugar']
                . ' values ( ' . $connId . ', ' . $sid . ' )';
        $rs = $GLOBALS['db']->query($query) or die;

        //if query true set rs = true, else false;
        /*while ($row = $GLOBALS['db']->fetchByAssoc($rs) ){
         $connId = $row[ $dictionary[$tablename]['external_mappings']['connections'] ];
        
        break;
        }*/
        return $rs;
    }

    /**
     * checks ibm_opportunities_communitites db table to see if a mapping exists
     * @param $sid of the opp or account
     */
    protected function getCommunityMapping($sid) {
        //$query = "SELECT community_id FROM ibm_opportunities_communities WHERE opportunity_id = '$sid';";
        //$result = $GLOBALS['db']->query($query);
        //$row = mysql_fetch_array($result, MYSQL_NUM);
        //$resultMapping = $row[0];

        //return $resultMapping;

        //TODO: check later if mapping is one to one or many to many. Assuming one to one based on comments. Code for many to many mappings below
        /*
        $result_array;
        $i=0;
        while($row = mysql_fetch_array($result,MYSQL_NUM)){
            $result_array[$i] = $row[0];
            $i++;
        }
        
        //$this->LOGGER->debug("getCommunityMapping result: ".$result_array);
        return $result_array;
         */

        return $this->getConnectionsId(CommunityMappingService::OPPORTUNITY_COMMUNITY_TBL, $sid);

    }
}
