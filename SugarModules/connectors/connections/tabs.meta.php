<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bkilgore
 * Date: 5/10/12
 * Time: 3:48 PM
 * To change this template use File | Settings | File Templates.
 */

$tabs_meta = array(
	"Members" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityMembers",
		"label" => "LBL_MEMBERS_TAB",
		"order" => 0,
		"active" => false,
		"onload" => false,
	),
	"Updates" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadUpdatesList",
		"label" => "LBL_UPDATES_TAB",
		"order" => 1,
		"active" => false,
		"onload" => false,
	),
	"Activities" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityActivities",
		"label" => "LBL_ACTIVITIES_TAB",
		"order" => 2,
		"active" => false,
		"onload" => false,
	),
	"Files" => array(
		"tabView" => "connectionsTabView",
		"method" => "loadFilesList",
		"label" => "LBL_FILES_TAB",
		"order" => 3,
		"active" => false,
		"onload" => false,
	),
	"Discussions" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityDiscussions",
		"label" => "LBL_DISCUSSIONS_TAB",
		"order" => 4,
		"active" => false,
		"onload" => false,
	),
	"Bookmarks" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityBookmarks",
		"label" => "LBL_BOOKMARKS_TAB",
		"order" => 5,
		"active" => false,
		"onload" => false,
	),
	"Blog" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityBlog",
		"label" => "LBL_BLOG_TAB",
		"order" => 6,
		"active" => false,
		"onload" => false,
	),
	"Wiki" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunityWiki",
		"label" => "LBL_WIKI_TAB",
		"order" => 7,
		"active" => false,
		"onload" => false,
	),
	"Subcommunities" => array(
		"tabView" => "connectionsTabView",
		"method" => "getCommunitySubcommunities",
		"label" => "LBL_SUBCOMMUNITIES_TAB",
		"order" => 8,
		"active" => false,
		"onload" => false,
	),
	"CreateCommunity" => array(
		"tabView" => "communitiesTabView",
		"method" => "createNewCommunity",
		"label" => "LBL_CREATE_COMMUNITY",
		"order" => 0,
		"active" => true,
		"onload" => true,
	),
	
	"MyCommunities" => array(
		"tabView" => "communitiesTabView",
		"method" => "loadConnectionsCommunities",
		"label" => "LBL_MY_COMMUNITIES_TAB",
		"order" => 1,
		"active" => false,
		"onload" => true,
	),
	"PublicCommunities" => array(
		"tabView" => "communitiesTabView",
		"method" => "loadConnectionsCommunities",
		"label" => "LBL_PUBLIC_COMMUNITIES_TAB",
		"order" => 2,
		"active" => false,
		"onload" => true,
	),
);
