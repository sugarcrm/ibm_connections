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
 
$tabs_meta = array(
	"Files" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadFilesList",
		"label" => "LBL_FILES_TAB",
		"order" => 0,
		"active" => true,
		"onload" => true,
		"buttons" => array(
			"selectcommunity",
			"newfile"
		)
	),
	"Members" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadMembersList",
		"label" => "LBL_MEMBERS_TAB",
		"order" => 1,
		"active" => false,
		"onload" => true,
		"buttons" => array(
			"selectcommunity",
			"addmember"
		)
	),
	"MyCommunities" => array(
		"tabView" => "communitiesTabView",
		"method" => "loadConnectionsCommunities",
		"label" => "LBL_MY_COMMUNITIES_TAB",
		"order" => 0,
		"active" => false,
		"onload" => true,
		"buttons" => array(
			"newcommunity"
		)
	),
	"PublicCommunities" => array(
		"tabView" => "communitiesTabView",
		"method" => "loadConnectionsCommunities",
		"label" => "LBL_PUBLIC_COMMUNITIES_TAB",
		"order" => 1,
		"active" => false,
		"onload" => true,
		"buttons" => array(
			"newcommunity"
		)
	),
	"CreateCommunity" => array(
		"tabView" => "communitiesTabView",
		"method" => "createNewCommunity",
		"label" => "LBL_CREATE_COMMUNITY",
		"order" => -1,
		"onload" => false,
		"buttons" => array()
	),
	"CreateFile" => array(
		"tabView" => "connectionsTabView",
		"method" => "createNewFile",
		"label" => "LBL_CREATE_FILE",
		"returnTab" => "Files",
		"order" => -1,
		"onload" => false,
		"buttons" => array()
	),
	"AddMember" => array(
		"tabView" => "connectionsTabView",
		"method" => "addCommunityMember",
		"label" => "LBL_ADD_MEMBER",
		"returnTab" => "Members",
		"order" => -1,
		"onload" => false,
		"buttons" => array()
	),
	"MyAccount" => array(
		"tabView" => "connectionsTabView",
		"method" => "myAccount",
		"label" => "LBL_MY_ACCOUNT_TAB",
		"order" => 0,
		"active" => false,
		"onload" => true,
		"buttons" => array(
			//"myaccount"
		)
	),
);
