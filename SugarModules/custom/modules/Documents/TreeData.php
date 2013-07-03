<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('include/ytree/Node.php');
require_once('include/externalAPI/ExternalAPIFactory.php');

function getAPI()
{
	$apiName = 'Connections';
	$eapmBean = EAPM::getLoginInfo($apiName,true);
    $api = ExternalAPIFactory::loadAPI($apiName,true);
    $validSession = true;

    if(!empty($eapmBean))
    {
    	try {
    	    $api->loadEAPM($eapmBean);
    	    $loginCheck = $api->checkLogin($eapmBean);
    		if(isset($loginCheck['success']) && !$loginCheck['success']){
    	    	$validSession = false;
        	}
    	} catch(Exception $ex) {
            $validSession = false;
            $GLOBALS['log']->error(string_format($mod_strings['ERR_INVALID_EXTERNAL_API_LOGIN'], array($apiName)));
        }
    }
    if ($validSession){
    	return $api;
   	}
   	return ;
}

 function get_main_folders()
 {
    global $mod_strings, $app_list_strings;
    $api = getAPI();
    if (empty($api)) return;
    $groups = array('pinned', 'private', 'shared', 'public');
    $nodes=array();
    foreach ($groups as $folderType) {
    	switch ($folderType){
    		case 'pinned': $folderName = $mod_strings['LBL_CONNECTIONS_PINNED_FOLDERS']; break;
    	 	case 'private': $folderName = $mod_strings['LBL_CONNECTIONS_MY_FOLDERS']; break;
    	 	case 'shared':  $folderName = $mod_strings['LBL_CONNECTIONS_SHARED_FOLDERS']; break;
    	 	case 'public':  $folderName = $mod_strings['LBL_CONNECTIONS_PUBLIC_FOLDERS']; break;
    	 	default: $folders = array();break;
    	}
		$cat_node = new Node($folderType, $folderName);
		$cat_node->set_property("href", '');
		$cat_node->expanded = false;
		$cat_node->dynamic_load = true;
		$nodes[]=$cat_node;
  	}
    return $nodes;
 }

function get_connections_documents($cat_id, $subcat_id,$href=true) {
    $nodes=array();
    $href_string = "javascript:select_document_in_tree('ibmdoctree')";
	
    $files = getAPI()->getFilesFromFolder($subcat_id);
    foreach($files as $file) {
        $node = new Node($file->getId(), $file->getTitle());
        $node->set_property("href", $href_string);
        $node->expanded = true;
        $node->dynamic_load = false;
        
        $nodes[]=$node;
    }
    return $nodes;
}

function get_subfolders($folderType = 'private'){
	require_once('include/ytree/Node.php');
	$api = getApi();
	switch ($folderType){
    		case 'pinned': $folders = $api->getPinnedFolders();  break;
    	 	case 'private': $folders = $api->getMyFolders(); break;
    	 	case 'shared': $folders = $api->getSharedFolders(); break;
    	 	case 'public': $folders = $api->getPublicFolders(); break;
    	 	default: $folders = array();break;
    	}
    $ret=array();
    foreach ($folders as $folder) {
		$id=$folder->getId();
		$name= $folder->getTitle(). " (". $folder->getItemsCount() .")";
		$node = new Node($id, $name);
		$href_string = "javascript:getDocumentList('1')";
		$node->set_property("href", $href_string);
		$node->expanded = false;
		$node->dynamic_load = false;
		$ret['nodes'][]=$node->get_definition();
	}
	$json = new JSON(JSON_LOOSE_TYPE);
	$str=$json->encode($ret);
	return $str;
}
?>
