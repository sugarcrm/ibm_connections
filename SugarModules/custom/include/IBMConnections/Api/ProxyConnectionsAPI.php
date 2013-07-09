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


require_once('custom/include/IBMConnections/Api/ConnectionsAPI.php');
require_once('custom/include/IBMCore/Logger/SFALogger.php');

/**
 *  Proxy Class for the Connections API
 *  @author Christophe Guillou (IBM)
 */
class ProxyConnectionsAPI
{
    private $LOGGER;

    private $apiInstance = null;
    private $ibmHttpClient;

    function __construct(IBMHttpClient $ibmHttpClient = null){
      if( $ibmHttpClient == null )
      {
          throw new IBMConnectionsApiException('Illegal Argument: ibmHttpClient cannot be null', CONNECTIONS_TECH_GENERAL, null, null);
      }
      $this->ibmHttpClient = $ibmHttpClient;
      $this->LOGGER = SFALogger::getLog('connections');
    }

    function __call($methodName, $args){
        $this->LOGGER->info('Entered ProxyConnectionsAPI->' . $methodName);
        if($this->apiInstance == null) {
            $this->apiInstance = new ConnectionsAPI($this->ibmHttpClient);
            $this->LOGGER->info('ProxyConnectionsAPI : new ConnectionsAPI instance created.');
        }
        // calling the method on the API instance
        
        switch ($methodName) {
            case 'getCommunitiesAPI':
                return $this->apiInstance->getCommunitiesAPI($args);
            case 'getActivitiesAPI':
                return $this->apiInstance->getActivitiesAPI($args);
            case 'getFilesAPI':
                return $this->apiInstance->getFilesAPI($args);
            case 'getProfilesAPI':
                return $this->apiInstance->getProfilesAPI($args);
            case 'getWikiAPI':
                return $this->apiInstance->getWikiAPI($args);
            case 'getBlogAPI':
                return $this->apiInstance->getBlogAPI($args);
            case 'getBookmarksAPI':
                return $this->apiInstance->getBookmarksAPI($args);
            case 'getForumsAPI':
                return $this->apiInstance->getForumsAPI($args);
            case 'getUpdatesAPI':
                return $this->apiInstance->getUpdatesAPI($args);
        }
    }

}

