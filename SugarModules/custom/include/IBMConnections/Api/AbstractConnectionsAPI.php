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



define('CONNECTIONS_3', '3.0');
define('CONNECTIONS_4', '4.0');

//require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
//require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsSecurityException.php';
//require_once('custom/include/IBMConnections/utils/IBMGenericUtils.php');

//require_once 'custom/include/IBMConnections/utils/IBMHttpClient.php';
//require_once 'custom/include/IBMConnections/utils/IBMHttpClientFactory.php';
require_once 'custom/include/IBMConnections/Api/ConnectionsResultMetadata.php';
require_once 'custom/include/IBMConnections/Api/ConnectionsConstants.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomFeed.php';
require_once('include/externalAPI/Base/ExternalAPIBase.php');
require_once('include/externalAPI/Base/WebDocument.php');
/**
 *
 * Enter description here ...
 * @author mario
 *
 */
abstract class AbstractConnectionsAPI extends ExternalAPIBase// implements WebDocument 
{

    const MAX_PAGE_SIZE = 100;
    protected $httpClient;
    private $webLoggedIn;
    private $lastMetadata = null;

    // public function __construct(IBMHttpClient $httpClient)
    public function __construct($httpClient)
    {
        require('custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php');
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
    }

    private function getClient() {
        // return $this->getHttpClient();
        $client = new Zend_Http_Client();
        return $client;
    }

    public function loadEAPM($eapmBean)
    {
        parent::loadEAPM($eapmBean);

        //if($eapmBean->url) {
        //    $this->url = $eapmBean->url;
        //}

        if ( !empty($eapmBean->api_data) ) {
            $this->api_data = json_decode(base64_decode($eapmBean->api_data), true);
            if ( isset($this->api_data['subscriberID']) ) {
                $this->userId = $this->api_data['userId'];
            }
        }
    }


    /**
     *
     * Enter description here ...
     */
    public function getLastResultMetadata() {
        return $this->lastMetadata;
    }

    /**
     *
     * Enter description here ...
     */
    protected function setLastResultMetadata(ConnectionsResultMetadata $metadata) {
        $this->lastMetadata = $metadata;
    }

    /**
     * @return IBMHttpClient
     */
    public function getHttpClient($needWebLogin = false) {
        return $this->httpClient;
    }

    public function setHttpClient($client) {
        $this->httpClient = $client;
    }

    /**
     *
     */
    public function extractRelativeUrlPath($fullUrl) {
        return str_replace($this->url, "" , $fullUrl);
    }

    /**
     * Invoke this method to request a path from the baseUrl
     * @param $method
     * @param null $path
     * @param array $additionalHeaders
     * @return null|Zend_Http_Response
     * @throws IBMConnectionsTechnicalException
     */


    public function requestForPath($method, $path = null, array $additionalHeaders = array(), $resetParams = true)
    {
        $response = null;
        try{
            //$this->httpClient;
            //Allow using BaseUrl and requestPath if set
            if( isset($this->url) && isset($path) )
            {
                $url = rtrim($this->url,"/")."/".ltrim($path, "/");

                $this->httpClient->setUri($url);


            }
            if( isset($additionalHeaders) && is_array($additionalHeaders) )
            {
                $this->httpClient->setHeaders($additionalHeaders);
            }
            $this->httpClient->setAuth($this->account_name,$this->account_password);
            $response = $this->httpClient->request($method);

            //$this->LOGGER->fatal( 'Request == ' . $this->_client->getLastRequest() );
            //$this->LOGGER->debug( 'Response == ' . print_r($response, true));
            //$this->LOGGER->debug( 'Response Body == ' . print_r($response->getBody(), true));
            if($resetParams) {
                $this->httpClient->resetParameters(true);;
            }
        } catch(Exception $ex){
            if($resetParams) {
                $this->httpClient->resetParameters(true);
            }
            /* $wrappedEx = $this->handleException($ex);
             $this->LOGGER->fatal($wrappedEx->__toString(), $ex);
             throw $wrappedEx;*/
        }

        return $response;
    }

    protected function makeRequest($urlReq, $method = 'GET', $json = true)
    {

        $client = $this->httpClient;
        $url = rtrim($this->url,"/")."/".ltrim($urlReq, "/");
        $GLOBALS['log']->debug("REQUEST: $url");
        $client->setUri($url);
        $client->setAuth($this->account_name,$this->account_password);
        $rawResponse = $client->request();
        $reply = array('rawResponse' => $rawResponse->getBody());
        $GLOBALS['log']->debug("RESPONSE: ".var_export($rawResponse, true));
        if(!$rawResponse->isSuccessful() || empty($reply['rawResponse'])) {
            $reply['success'] = false;
            // FIXME: Translate
            $reply['errorMessage'] = 'Bad response from the server: '.$rawResponse->getMessage();
            return $reply;
        } else {
            $reply['success'] = true;
            $cookie_array = $rawResponse->getHeader('Set-cookie');
            $expires = strtotime($rawResponse->getHeader('Expires'));
        }
        return $reply;
    }



    /**
     *
     * Enter description here ...
     */
    protected function checkResult(Zend_Http_Response $result) {
        if($result->isError()) {
            //if($result->getStatus() == 403) {
            //throw new IBMConnectionsSecurityException("HTTP Error: ".$result->getStatus() ."\n" .$result->getBody(), CONNECTIONS_TECH_SECURITY_403);
            $GLOBALS['log']->fatal("HTTP Error: ".$result->getStatus() ."\n" .$result->getBody());
            return false;
            //}
            //throw new IBMConnectionsApiException("HTTP Error: ".$result->getStatus(), ERR_COMMUNITIES_API_HTTP_GENERIC_ERROR, $result);
        }
        return true;
    }

    /**
     *
     * Description:
     * Obtain the nonce value.
     *
     * Parmeters: None
     * Return: nonce value
     */

    public function requestNonce() {
        // $this->getHttpClient()->setEnctype(); uznaty sho tse
        $response = $this->requestForPath('GET',  "/files/basic/api/nonce");
        return $response->getBody();
    }


    public function getRemoteAppFeed($id) {

        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet("communityUuid", $id);
        $this->getHttpClient()->setParameterGet("ps", AbstractConnectionsAPI::MAX_PAGE_SIZE);
        $result = $this->requestForPath("GET", "/communities/service/atom/community/remoteApplications");
        if (empty($result) || $result->getStatus() != 200) {
            return null;
        }

        $remoteAppFeed = IBMAtomFeed::loadFromString($result->getBody());
        return  $remoteAppFeed;
    }

    /**
     * Method to get the IBM Connections server version.
     * @return string | CONNECTIONS_3 | CONNECTIONS_4
     * @throws IBMConnectionsServiceException
     */
    public function getConnectionsVersion()
    {
        $version = '';

        $this->getHttpClient()->resetParameters();
        $response = $this->requestForPath('GET', '/profiles/serviceconfigs');

        if ($response->getStatus() == 200) {

            $dom = new DOMDocument();
            $dom->loadXML($response->getBody());

            $collections = $dom->getElementsByTagName('generator');

            if( version_compare( $collections->item(0)->getAttribute('version'), CONNECTIONS_4 , "<") ){
                $version = CONNECTIONS_3;
            } else{
                $version = CONNECTIONS_4;
            }
        }

        return $version;
    }

    /**
     * Method to get retrieve Connections back-end server baseURL
     * this is used to verify security settings
     * @return string Https URL
     * @throws IBMConnectionsServiceException
     */
    public function getConnectionsBackend()
    {
        $baseUrl = '';

        $this->getHttpClient()->resetParameters();
        $response = $this->requestForPath('GET', '/profiles/serviceconfigs');

        if ($response->getStatus() == 200) {

            $dom = new DOMDocument();
            $dom->loadXML($response->getBody());

            $collections = $dom->getElementsByTagName('entry');
            $firstEntry = $collections->item(0);
            foreach( $firstEntry->childNodes as $entryNode){
                switch ($entryNode->tagName) {

                    case 'link':
                        if ($entryNode->getAttribute('rel') == 'http://www.ibm.com/xmlns/prod/sn/alternate-ssl') {
                            // Replacing with IebUrl.
                            $baseUrl = $entryNode->getAttribute('href');
                            $parsedURL = parse_url($baseUrl);
                            $baseUrl = str_replace($parsedURL['path'], '', $baseUrl);
                        }

                    default:
                        // dont map data.
                }
            }

        }

        return $baseUrl;
    }

    protected function  getItemList(IBMAtomFeed $feed, $class)
    {
        $result = array(
            'entries' => array(),
            'total' =>  $feed->getTotalResults(),
        );
        $entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $result['entries'][] = new $class($entry);
        }
        return $result;
    }

}
