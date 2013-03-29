<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bkilgore
 * Date: 5/10/12
 * Time: 3:48 PM
 * To change this template use File | Settings | File Templates.
 */

$tabs_meta = array(
	"Overview" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadOverview",
		"label" => "LBL_OVERVIEW_TAB",
		"order" => 0,
		"active" => true,
		"onload" => false,//true,
		"buttons" => array(
			"selectcommunity",
		)
	),
	"Updates" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadUpdatesList",
		"label" => "LBL_UPDATES_TAB",
		"order" => 1,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
		)
	),
	"Activities" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityActivities",
		"label" => "LBL_ACTIVITIES_TAB",
		"order" => 2,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
			"createactivity",
		)
	),
	"Files" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadFilesList",
		"label" => "LBL_FILES_TAB",
		"order" => 3,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
		)
	),
	"Discussions" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityDiscussions",
		"label" => "LBL_DISCUSSIONS_TAB",
		"order" => 4,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
			"creatediscussion",
		)
	),
	"Bookmarks" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityBookmarks",
		"label" => "LBL_BOOKMARKS_TAB",
		"order" => 5,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
			"createbookmark",
		)
	),
	"Blog" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityBlog",
		"label" => "LBL_BLOG_TAB",
		"order" => 6,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
		)
	),
	"Wiki" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityWiki",
		"label" => "LBL_WIKI_TAB",
		"order" => 7,
		"active" => false,
		"onload" => false,//true,
		"buttons" => array(
		)
	),
/*	"Files" => array(
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
	),*/
	"CreateCommunity" => array(
		"tabView" => "communitiesTabView",
		"method" => "createNewCommunity",
		"label" => "LBL_CREATE_COMMUNITY",
		"order" => 0,
		"active" => true,
		"onload" => true,
		"buttons" => array()
	),
	
	"MyCommunities" => array(
		"tabView" => "communitiesTabView",
		"method" => "loadConnectionsCommunities",
		"label" => "LBL_MY_COMMUNITIES_TAB",
		"order" => 1,
		"active" => false,
		"onload" => true,
		"buttons" => array(
		//	"newcommunity"
		)
	),
	"PublicCommunities" => array(
		"tabView" => "communitiesTabView",
		"method" => "loadConnectionsCommunities",
		"label" => "LBL_PUBLIC_COMMUNITIES_TAB",
		"order" => 2,
		"active" => false,
		"onload" => true,
		"buttons" => array(
		//	"newcommunity"
		)
	),
	
	/*"CreateFile" => array(
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
/*	"MyAccount" => array(
		"tabView" => "connectionsTabView",
		"method" => "myAccount",
		"label" => "LBL_MY_ACCOUNT_TAB",
		"order" => 2,
		"active" => false,
		"onload" => true,
		"buttons" => array(
			//"myaccount"
		)
	),
	*/
	
);
