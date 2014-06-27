<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
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
 * by SugarCRM are Copyright (C) 2004-2014 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


$fields = array(
    'id' => array(
        'name' => 'id',
        'type' => 'id',
        'source' => 'non-db',
    ),
    'name' => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => true,
        'required' => true,
        'vname' => 'LBL_NAME',
    ),
    'author_id' => array(
        'name' => 'id',
        'type' => 'id',
        'source' => 'non-db',
    ),
    "author_name" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "author_email" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "author_status" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "uploaded" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "fileVisibility" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "commentsCount" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "recomendationsCount" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "url" => array(
        'name' => 'url',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "view_link" => array(
        'name' => 'view_link',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),    
    "downloadsCount" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "version" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "fileSize" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    "picture" => array(
        'name' => 'name',
        'type' => 'varchar',
        'source' => 'non-db',
        'unified_search' => false,
    ),
    'community_id' => array(
        'name' => 'community_id',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'content_type' => array(
        'name' => 'content_type',
        'type' => 'varchar',
        'source' => 'non-db',
    ),    
    'filename' => array (
        'name' => 'filename',
        'vname' => 'File',
        'type' => 'file',
        'source' => 'non-db',
        'required' => true,
),

);

$dictionary['ibm_connectionsFiles'] =
    array(
        'fields' => $fields,
        'mergedFields' => array('id', 'name'),
        'activity_enabled' => false,
        'unified_search' => false,
        'favorites' => false,
    );

VardefManager::createVardef('ibm_connectionsFiles', 'ibm_connectionsFiles');
