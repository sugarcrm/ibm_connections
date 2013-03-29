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



require_once 'custom/include/IBMConnections/Api/AbstractConnectionsAPI.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomFeed.php';
require_once 'custom/include/IBMConnections/Api/CommunitiesAPI.php';
require_once 'custom/include/IBMConnections/Api/ActivitiesAPI.php';
require_once('custom/include/IBMConnections/Api/FilesAPI.php');
require_once('custom/include/IBMConnections/Api/ProfilesAPI.php');
require_once('custom/include/IBMConnections/Api/WikiAPI.php');
require_once('custom/include/IBMConnections/Api/BlogAPI.php');
require_once('custom/include/IBMConnections/Api/BookmarksAPI.php');
require_once('custom/include/IBMConnections/Api/ForumsAPI.php');
require_once('custom/include/IBMConnections/Api/UpdatesAPI.php');

/**
 *
 * Enter description here ...
 * @author mario
 *
 */
class ConnectionsAPI extends AbstractConnectionsAPI
{
	static protected $instance = null;
	private $communities = null;
	private $activities = null;
    private $files = null;
    private $profiles = null;
    private $wiki = null;
    private $blog = null;
    private $bookmarks = null;
    private $forums = null;


 public function __construct($httpClient = null)
    {
    /*	require('custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php');
	    $this->url = $config['properties']['company_url'];
	    $eapmBean = EAPM::getLoginInfo('Connections');
	    $this->loadEAPM($eapmBean);
	    if (empty($httpClient)) 
	    {
	    	$httpClient = $this->getClient(); 
	    }
	    //$httpClient->setUri($url);
	    $httpClient->setAuth($this->account_name,$this->account_password);
	    $this->httpClient = $httpClient;
	    */
    }
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $connector
	 * @return ConnectionsAPI
	 */
	static public function getInstance() {
		if(self::$instance == null) {
			self::$instance = new self(IBMHttpClientFactory::createConnectionsClient());
		}
		return self::$instance;
	}

	/**
	 *  @return CommunitiesAPI
	 */
	public function getCommunitiesAPI() {
		if(!$this->communities) {
			$this->communities = new CommunitiesAPI($this->getHttpClient());
		}
		return $this->communities;
	}


	/**
	 * @var ActivitiesAPI
	 */
	public function getActivitiesAPI() {
		if(!$this->activities) {
			$this->activities = new ActivitiesAPI($this->getHttpClient());
		}
		return $this->activities;
	}

    /**
     *  @var FilesAPI
     */
    public function getFilesAPI() {
        if(!$this->files) {
            $this->files = new FilesAPI($this->getHttpClient());
        }
        return $this->files;
    }


    /**
     * @var ProfilesAPI
     */
    public function getProfilesAPI() {
        if(!$this->profiles) {
            $this->profiles = new ProfilesAPI($this->getHttpClient());
        }
        return $this->profiles;
    }

    /**
     * @return WikiAPI
     */
    public function getWikiAPI() {
        if(!$this->wiki) {
            $this->wiki = new WikiAPI($this->getHttpClient());
        }
        return $this->wiki;
    }

    /**
     * @return BlogAPI
     */
    public function getBlogAPI() {
        if(!$this->blog) {
            $this->blog = new BlogAPI($this->getHttpClient());
        }
        return $this->blog;
    }

    /**
     * @return BookmarksAPI
     */
    public function getBookmarksAPI() {
    	if(!$this->bookmarks) {
    		$this->bookmarks = new BookmarksAPI($this->getHttpClient());
    	}
    	return $this->bookmarks;
    }
    
    /**
     * @return ForumsAPI
     */
    public function getForumsAPI() {
        if(!$this->forums) {
            $this->forums = new ForumsAPI($this->getHttpClient());
        }
        return $this->forums;
    } 
 	/**
     * @return UpdatesAPI
     */
    public function getUpdatesAPI() {
        if(!$this->updates) {
            $this->updates= new UpdatesAPI($this->getHttpClient());
        }
        return $this->updates;
    } 

	/**
	 *
	 */
	public function rawGet($url) {
		$this->getHttpClient()->resetParameters();
		$url = $this->extractRelativeUrlPath($url);

//		$result = $this->getHttpClient(true)->requestForPath("GET", $url);
		$result = $this->requestForPath("GET", $url);
		return $result;
	}
}
