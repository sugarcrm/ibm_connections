<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bkilgore
 * Date: 5/10/12
 * Time: 3:48 PM
 * To change this template use File | Settings | File Templates.
 */

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
		"order" => 3,
		"onload" => true,
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
);