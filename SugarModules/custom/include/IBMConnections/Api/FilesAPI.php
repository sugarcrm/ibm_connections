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


require_once 'custom/include/IBMConnections/FeedApi/IBMAtomEntry.php';
require_once('custom/include/IBMConnections/Api/AbstractConnectionsAPI.php');
require_once 'custom/include/IBMConnections/Models/ConnectionsFile.php';
//require_once 'custom/include/Helpers/IBMHelperTags.php';
//require_once('custom/include/IBMCore/Logger/SFALogger.php');

/**
 * This is the Files API for Connections allowing you to do basic
 * operations on the file
 *
 * @extends <b>AbstractConnectionsAPI</b>
 *
 * @author Christophe Guillou
 * @author Gearoid O'Treasaigh
 *
 */
class FilesAPI extends AbstractConnectionsAPI {

    private $LOGGER;
    var $profilesAPI;

    function __construct($httpClient = null) {
        parent::__construct($httpClient);
       // require_once('custom/include/IBMConnections/Api/ProfilesAPI.php');
       // $this->profilesAPI = new ProfilesAPI($this->getHttpClient());
       // $this->LOGGER = SFALogger::getLog('connections');
    }

    /**
     * Content-Type
     */
    const CONTENT_TYPE = 'application/atom+xml';
    const ATOM_NS = 'http://www.w3.org/2005/Atom';

    /**
     * Upload a file to Connections My Files with visibility set to private
     * @param string $fileName
     * @param string $content - Content of the file
     * @param string $description - XML encoded description
     * @param string $mimeType
     * @return Zend_Http_Response $response - Response from the file upload
     *         to Connections
     * @throws IBMConnectionsApiException
     *
     * returns the response of the file upload to Connections with the
     * Connections file ID and URL added to response
     */
     
     
     /**
     * Method retrieving a list of connections files shared with the community.
     * @param $cid
     * @return array of ConnectionsFile
     * @throws IBMConnectionsApiException
     */
    public function getCommunityFiles($cid, $page = 1, $search = '') {
        $path = '/files/basic/api/communitycollection/' . $cid . '/feed';
        $client = $this->getHttpClient();
        $client->resetParameters();
        $client->setParameterGet('sK', 'added');
        $client->setParameterGet('sO', 'dsc');
        $client->setParameterGet('acls', 'true');
        $client->setParameterGet('collectionAcls', 'true');
        $client->setParameterGet('pageSize',  5);//AbstractConnectionsAPI::MAX_PAGE_SIZE);
         $client->setParameterGet('page',  $page);
        $client->setParameterGet('includeTags', 'true');
     //   echo $search;
        if (!empty($search)) {
			$client->setParameterGet("component", 'files');
			$client->setParameterGet("query", $search);
			$path = "/search/atom/mysearch/results";
			//$client->setParameterGet("search", $search);
		
		}
        $this->setHttpClient($client);
        $response = $this
                ->requestForPath('GET', $path,
                        array('Content-Type' => self::CONTENT_TYPE,
                                'Content-Language' => 'en-US'));

        if (empty($result) || !$this->checkResult($result)){
         	return array();
        }
		$files = array();
        $feed = IBMAtomFeed::loadFromString($response->getBody());
        $entries = $feed->getEntries();
        foreach ($entries as $entry) {
             	$files[] = new ConnectionsFile($entry);
        }
        switch ($response->getStatus()) {
            case 200:
            //Files retrieved.
               
                break;

            default:
                $message = 'Unexpected httpResponse code  = '
                        . $response->getStatus()
                        . ' while attempting to list Files from community ['
                        . $cid . ']';
                //$this->LOGGER->fatal($message);
               /* require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
                throw new IBMConnectionsApiException($message,
                        CONNECTIONS_SERVICE_GENERIC, $response);
                        */
               echo $message;
        }
		return $files;
    }
    public function uploadFile($fileName, $content, $mimeType, $visibility) {
        $path = '/files/basic/api/myuserlibrary/feed';
        $nonce = $this->requestNonce();
        $client = $this->getClient();
        $client->setParameterPost('visibility', $visibility);
        $client->setParameterPost('label', basename($fileName));
        $client->setFileUpload($fileName, 'file', $content, $mimeType);
        //$client->setIgnoreResponseContentLength();
        $curlOpts = array(
                                    CURLOPT_SSL_VERIFYHOST => false,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_VERBOSE => true, // Display communication with server
                                    CURLOPT_RETURNTRANSFER => true, // Return data instead of display to std out
                                    CURLOPT_HEADER => true, // Display headers
                                    CURLOPT_USERPWD => $this->account_name. ":" . $this->account_password,//$client->username . ":" . $client->password,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0
                                    
                                );
                                
       $config = array(
                        'persistent'   => true,
                        'adapter'   => 'Zend_Http_Client_Adapter_Curl',
                        'curloptions' => $curlOpts,
                        'ssltransport' => 'sslv3',
                        'strictredirects' => true,
                        'maxredirects' => 0
                    );
       
         $client->setConfig( $config );
		$this->setHttpClient($client);
        $response = $this
                ->requestForPath('POST', $path,
                        array('Content-Type' => 'multipart/form-data',
                                'Content-Language' => 'en-US',
                                'X-Update-Nonce' => $nonce,));

        switch ($response->getStatus()) {
            case 200:
                if (strstr($response->getStatus(),
                        '<meta name="status" content="409"/>') === false) {
                    //File has been uploaded
                    break;
                }
            // Error with response and falling through to the default error message.
            default:
                $message = 'The file - ' . $fileName . ' - couldn\'t be'
                        . ' successfully uploaded at this time, the response returned'
                        . ' with an unexpected HTTP error code.';
              //  require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
             //  $connEx = new IBMConnectionsApiException(
              //          'The file upload to Connections failed',
              ///          FILES_API_UPLOAD_FILENAME_ALREADY_EXISTS, $response);
              //  $this->LOGGER->error($connEx->__toString());
              //  throw $connEx;
        }

        if (strstr($response->getBody(), '<body class="X-LConn-API-Response">')
                != false) {
            // removing the html wrapping the json
            $json = $this->decodeXml(substr($response->getBody(), 134, -16));

            $jsonResponse = json_decode($json);

            $fullId = $jsonResponse->{'id'};
            preg_match(
                    '/[a-zA-Z|0-9]{8}-[a-zA-Z|0-9]{4}-[a-zA-Z|0-9]{4}-[a-zA-Z|0-9]{4}-[a-zA-Z|0-9]{12}/',
                    $fullId, $id);
            $response->docId = $id[0];
            //$response->docUrl = $this->updateIebUrl($jsonResponse->{'links'}[3]->{'href'});
            $response->docUrl = $jsonResponse->{'links'}[3]->{'href'};
            $response->fileSize = $jsonResponse->{'links'}[4]->{'length'};
        }

        if (!empty($response)) {
            preg_match('/<meta name="status" content="409"\/>/',
                    $response->getBody(), $conflict);
            if (sizeof($conflict) >= 1) {
                $message = 'The file - ' . $fileName . ' - couldn\'t be'
                        . ' successfully uploaded at this time, a file with the same'
                        . ' name already exists in Connections.';
                        echo $message;
                //require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
              //  $connEx = new IBMConnectionsApiException($message,
              //          FILES_API_UPLOAD_FILENAME_ALREADY_EXISTS, $response);
               // $this->LOGGER->error($connEx->__toString());
                //throw $connEx;
            }

            if (empty($response->docId) || empty($response->docUrl)
                    || empty($response->docUrl) || !isset($response->fileSize)) {
                $message = 'The file - ' . $fileName . ' - couldn\'t be'
                        . ' successfully uploaded at this time, the Connections'
                        . ' ID, url, and filesize could not be set.';
                        echo $message;
               // require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
              //  $connEx = new IBMConnectionsApiException($message,
               //         FILES_API_UPLOAD_FILENAME_ALREADY_EXISTS, $response);
               // $this->LOGGER->error($connEx->__toString());
              //  throw $connEx;
            }
            $bean->doc_id = $response->docId;
            $bean->doc_url = $response->docUrl;
            $bean->doc_direct_url = $response->docUrl;
            $bean->file_size = $response->fileSize;
        }

        return $response;
    }

    /**
     * Method to update File visibility in MyFiles
     *
     * @param $myFileId
     * @param bool $isPublic
     * @return bool
     * @throws IBMConnectionsApiException
     */
    public function updateMyFileVisibility($myFileId, $isPublic = false) {
        $path = '/files/basic/api/myuserlibrary/document/' . $myFileId
                . '/feed';
       $path .= ($isPublic ? '?visibility=public' : '?visibility=private');
        $nonce = $this->requestNonce();
       // $this->LOGGER->info($path);
        $client = $this->getHttpClient();
        $client
                ->setParameterPost('visibility',
                        ($isPublic ? 'public' : 'private'));
        $client
                ->setRawData(
                        '<feed xmlns="http://www.w3.org/2005/Atom"></feed>');
        $this->setHttpClient($client);
        $response = $this
                ->requestForPath('POST', $path,
                        array('Content-Type' => self::CONTENT_TYPE,
                                'Content-Language' => 'en-US',
                                'X-Update-Nonce' => $nonce));

        //print_r( $response );
       // echo $response->getStatus();
        switch ($response->getStatus()) {
            case 204:
            //File visibility Updated
                return true;
                break;

            default:
                $message = 'The Connections file with id - ' . $myFileId
                        . 'failed to update' . ' it\'s visibility to '
                        . $isPublic ? 'public' : 'private';
                        echo $message;
               // require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
              //  $connEx = new IBMConnectionsApiException($message,
                 //       FILES_API_UPDATE_VISIBILITY_FAILED, $response);
               // $this->LOGGER->error($connEx->__toString());
               // throw $connEx;
        }
    }
    
     /**
     * Method to update File visibility in MyFiles

     *
     * @param $myFileId
     * @param bool $isPublic
     * @return bool

     * @throws IBMConnectionsApiException
     */
    public function updateMyFileName($myFileId, $newFilename) {
        $path = '/files/basic/api/myuserlibrary/document/' . $myFileId
                . '/media'; 
       $path .= "?identifier={$newFilename}";
        $nonce = $this->requestNonce();
       // $this->LOGGER->info($path);
        $client = $this->getHttpClient();
        $client->resetParameters();
        $client->setParameterPost('identifier', $newFilename);
        //$client->setRawData('<feed xmlns="http://www.w3.org/2005/Atom"></feed>');
        $this->setHttpClient($client);
        $response = $this
                ->requestForPath('POST', $path,
                        array(//'Content-Type' => self::CONTENT_TYPE,
                                //'Content-Language' => 'en-US',
                                'X-Update-Nonce' => $nonce,
                                'X-Method-Override' => 'PUT',));

        //print_r( $response );
        echo $response->getStatus();
        //print_r($response->getBody());
    }

    /**
     * Method to share a File uploaded to MyFile with Individuals.
     * @param $emails
     * @param $myFileId
     *
     * returns true if File sucessfully shared - else throws Sharing Exception
     */
     
     
    public function getFileDetails($documentId) {
    	$this->httpClient = $this->getHttpClient();
		$result = $this->requestForPath("GET","files/basic/api/myuserlibrary/document/{$documentId}/entry");
		try	{
			$entry = IBMAtomEntry::loadFromString($result->getBody());
        	return new ConnectionsFile($entry);
        }
        catch (Exception $e)
        {return;}
		
	}
	
	 public function downloadFile($documentId) {
    	$this->httpClient = $this->getHttpClient();
		$result = $this->requestForPath("GET","/files/basic/anonymous/api/document/{$documentId}/media");
		echo $result->getStatus();
		return $result->getBody();
	}
	
    public function shareMyFileWithIndividuals($emails = array(), $myFileId) {

        // Retrieve User Ids..
     /*   $userIdsMap = $this->profilesAPI->getUserIds($emails);
        $authenticatedUser = $this->profilesAPI->whoAmI();

        if (empty($emails)
                || (count($emails) == 1
                        && strcasecmp($userIdsMap[strtolower($emails[0])],
                                $authenticatedUser) == 0))
            return false;

        $dom = new DOMDocument();
        $feedElt = $dom->createElementNS(self::ATOM_NS, 'feed');
        $dom->appendChild($feedElt);

        $entryElt = $dom->createElement('entry');

        $categoryElt = $dom->createElement('category');
        $categoryElt->setAttribute('term', 'share');
        $categoryElt->setAttribute('label', 'share');
        $categoryElt->setAttribute('scheme', 'tag:ibm.com,2006:td/type');
        $entryElt->appendChild($categoryElt);

        $sharedWhatElt = $dom
                ->createElementNS('urn:ibm.com/td', 'shareWhat', $myFileId);
        $entryElt->appendChild($sharedWhatElt);

        $sharedWithElt = $dom->createElementNS('urn:ibm.com/td', 'sharedWith');
        $entryElt->appendChild($sharedWithElt);

        $sharePermissionElt = $dom->createElement('sharePermission', 'Edit'); //TODO: Reader/ Writer privilege ?
        $sharedWithElt->appendChild($sharePermissionElt);

        foreach ($emails as $email) {
            $userId = $userIdsMap[strtolower($email)];

            // Email doesnt map to any valid user ID in connections
            if (!$userId)
                continue;

            // Do not attempt to share with self.
            if (strcasecmp($userId, $authenticatedUser) == 0)
                continue;

            $userElt = $dom->createElement('user');
            $userIdElt = $dom
                    ->createElementNS('http://www.ibm.com/xmlns/prod/sn',
                            'userid', $userId);
            $userElt->appendChild($userIdElt);
            $sharedWithElt->appendChild($userElt);
        }

        $feedElt->appendChild($entryElt);
        $client = $this->getClient();
        $client->setRawData($dom->saveXML());
        echo ($dom->saveXML());
        $response = $client
                ->requestForPath('POST',
                        '/files/basic/api/myuserlibrary/document/' . $myFileId
                                . '/feed',
                        array('Content-Type' => self::CONTENT_TYPE,
                                'Content-Language' => 'en-US',));

        //$this->LOGGER->debug( $response->getBody() );
        //$this->LOGGER->debug( $client->getUri(true) );

        switch ($response->getStatus()) {
            case 204:
            //File has been shared
                return true;
                break;

            default:
                $message = 'Unable to share the Connections file with ID - '
                        . $myFileId . ' with the following users - '
                        . implode(', ', $emails);
                //require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
                //$connEx = new IBMConnectionsApiException($message,
               //         FILES_API_SHARE_WITH_USERS_FAILED, $response);
               // $this->LOGGER->error($connEx->__toString());
              //  throw $connEx;
        }
*/
    }

    /**
     * This Method unshares the file from All the individuals altogether.
     * @param $myFileId
     * @return bool
     * @throws IBMConnectionsApiException
     *
     * returns true if File sucessfully unshared - false, if File is not shared to anyone (already unshared)
     */
    public function unshareMyFileFromAllIndividuals($myFileId) {

        $response = $this->requestForPath('DELETE',
                        '/files/basic/api/shares/feed?sharedWhat=' . $myFileId,
                        array('Content-Type' => self::CONTENT_TYPE,
                                'Content-Language' => 'en-US',));

        switch ($response->getStatus()) {
            case 204:
            //File has been shared
                return true;
                break;

            case 404:
            //File already unshared.
               // $this->LOGGER->warn('File is already unshared.');
                return false;
                break;

            default:
                $message = 'The unsharing of the file with Connections ID - '
                        . $myFileId . ', failed';
               // require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
               // $connEx = new IBMConnectionsApiException($message,
               //         FILES_API_UNSHARE_WITH_ALL_USERS_FAILED, $response);
                //$this->LOGGER->error($connEx->__toString());
                //throw $connEx;
        }
    }

    /**
     * Deletes the Connections file
     * @param $documentId - Connections File ID
     * @return bool - returns true if successful
     * @throws IBMConnectionsApiException
     *
     * returns true if the Connections File is successfully deleted
     */
    public function deleteDoc($documentId) {
        $path = '/files/basic/api/myuserlibrary/document/' . $documentId
                . '/entry';
        $client = $this->getHttpClient();
        //$this->LOGGER->info($path);
        $nonce = $this->requestNonce();
        $response = $this
                ->requestForPath('DELETE', $path,
                        array('X-Update-Nonce' => $nonce));
        //$this->LOGGER->info($response->getStatus());

        switch ($response->getStatus()) {
            case 204:
            //File has been deleted
                break;
            default:
                $message = 'Error when deleting the Connections file with id - '
                        . $documentId;
              //  require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
               // $connEx = new IBMConnectionsApiException($message,
               //         FILES_API_DELETE_FILE_FAILED, $response);
               // $this->LOGGER->error($connEx->__toString());
         //       throw $connEx;
        }
        return TRUE;
    }
	public function commentFile($userId, $documentId, $content)
	{
		$this->getHttpClient()->resetParameters();
		
		$entry = IBMEditableAtomEntry::createEmptyEditableEntry();
		$entry->addCategory('comment', 'tag:ibm.com,2006:td/type');
		$cdata = $entry->getDom()->createCDATASection($content);
		$contentElt = $entry->getDom()->createElement('content');
        $contentElt->setAttribute('type', 'text');
        $contentElt->appendChild($cdata);
        $entry->getEntryNode()->appendChild($contentElt);
		
		$this->getHttpClient()->setParameterPost("identifier", $documentId);
		$this->getHttpClient()->setRawData($entry->getDomString());
		$result = $this->requestForPath('POST', "files/basic/api/userlibrary/{$userId}/document/{$documentId}/feed",
			array(
				'Content-Type' => 'application/atom+xml',
				'Content-Language' => 'en-US',
			));
		//echo $result->getStatus();
		//echo $result->getBody();
		if ($result->getStatus() == 201) return true;
		return false;
	
	}
	
	public function likeFile($userId, $documentId)
	{
		$this->getHttpClient()->resetParameters();
		
		$entry = IBMEditableAtomEntry::createEmptyEditableEntry();
		
		$entry->addCategory('recommendation', 'tag:ibm.com,2006:td/type');
		
		$this->getHttpClient()->setParameterPost("identifier", $documentId);
		$this->getHttpClient()->setRawData($entry->getDomString());
		$result = $this->requestForPath('POST', "files/basic/api/userlibrary/{$userId}/document/{$documentId}/feed",
			array(
				'Content-Type' => 'application/atom+xml',
				'Content-Language' => 'en-US',
			));
		if ($result->getStatus() == 201) return true;
		return false;	
	}

    /**
     * Update the metadata (tags and description) of the Connections document,
     * and save the file size of the document in Sugar to the Document bean
     * using the response retrieved
     *
     * @param DocumentDecorator $docDecorator
     * @throws IBMConnectionsApiException
     */
    public function updateMetadata(DocumentDecorator $docDecorator) {
        $bean = $docDecorator->getBean();
        $path = '/files/basic/api/myuserlibrary/document/' . $bean->doc_id
                . '/entry';
        $nonce = $this->requestNonce();
        $client = $this->getClient();

        /*
         * The tags to be removed are added to the path as they
         * cannot be part of the atom entry, and the setParameterPost
         * cannot be used with atom or even be used for parameters
         * that are repeated
         */
        $isFirstParam = true;
        $tagsRemoved = str_replace(' ', '_', $docDecorator->tagsRemoved());
        if (isset($tagsRemoved) && !empty($tagsRemoved)) {
            foreach ($tagsRemoved as $removeTag) {
                if ($isFirstParam) {
                    $path .= '?removeTag=' . urlencode($removeTag);
                } else {
                    $path .= '&removeTag=' . urlencode($removeTag);
                }
                $isFirstParam = false;
            }
        }

        $dom = new DOMDocument();
        $entryElt = $dom->createElementNS(self::ATOM_NS, 'entry');
        $dom->appendChild($entryElt);

        $categoryElt = $dom->createElement('category');
        $categoryElt->setAttribute('term', 'document');
        $categoryElt->setAttribute('label', 'document');
        $categoryElt->setAttribute('scheme', 'tag:ibm.com,2006:td/type');
        $entryElt->appendChild($categoryElt);

        $tagsAdded = str_replace(' ', '_', $docDecorator->tagsAdded());
        if (isset($tagsAdded) && !empty($tagsAdded)) {
            foreach ($tagsAdded as $addTag) {
                $tagElt = $dom->createElement('category');
                $tagElt->setAttribute('term', $addTag);
                $tagElt->setAttribute('label', $addTag);
                $entryElt->appendChild($tagElt);
            }
        }

        if (!empty($bean->description)) {
            $descriptionElt = $dom
                    ->createElement('summary',
                            $this
                                    ->encodeXml(
                                            $this
                                                    ->decodeXml(
                                                            $bean->description)));
            $descriptionElt->setAttribute('type', 'text');
            $entryElt->appendChild($descriptionElt);
        }

        $client->setRawData($dom->saveXML());

        // $this->LOGGER->info($path);
        $response = $this
                ->requestForPath('POST', $path,
                        array('Content-Type' => self::CONTENT_TYPE,
                                'Content-Language' => 'en-US',
                                'X-Update-Nonce' => $nonce,
                                'X-Method-Override' => 'PUT',));

        switch ($response->getStatus()) {
            case 200:
            //File metadata has been updated
                break;
            default:
                $message = 'The file metadata update to the Connections file - '
                        . $bean->doc_id . ' failed.  It tried to removed the'
                        . ' following tags - ' . implode(',', $tagsRemoved)
                        . '.' . ' It tried to add the following tags - '
                        . implode(',', $tagsAdded) . '.'
                        . ' It tried to add the' . ' following description - '
                        . $bean->description;
            //    require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            // //   $connEx = new IBMConnectionsApiException($message,
            //            FILES_API_METADATA_UPDATE_FAILED, $response);
             //   $this->LOGGER->error($connEx->__toString());
             //   throw $connEx;
        }
        // $this->LOGGER->debug($response->getBody());

        $ibmAtomEntry = IBMAtomEntry::loadFromString($response->getBody());
        $enclosure = $ibmAtomEntry->getLink('enclosure');
        $bean->file_size = $enclosure['length'];
        $docDecorator->save();
    }

    protected function updateIebUrl($oldLink) {
        $newLink = $oldLink;
        if ($this->getClient()->getAuthMethod() == AuthMethod::WEB_SSO) {
            //$this->LOGGER->debug("updateIebUrl: oldLink=" . $oldLink);
            $end = strpos($oldLink, "files");
            $conn_url = substr($oldLink, 0, $end - 1);
            if (strcmp($conn_url, $this->getClient()->getBaseURL()) != 0) {
                $newLink = str_replace($conn_url,
                        $this->getClient()->getBaseURL(), $oldLink);
              //  $this->LOGGER->debug("updateIebUrl: newLink=" . $newLink);
            }
        }
        return $newLink;
    }

    /**
     * Decode the XML passed in
     *
     * @param $txt - XML encoded text
     * @return decoded text
     */
    private function decodeXml($txt) {
        $txt = str_replace('&amp;', '&', $txt);
        $txt = str_replace('&lt;', '<', $txt);
        $txt = str_replace('&gt;', '>', $txt);
        $txt = str_replace('&apos;', "'", $txt);
        $txt = str_replace('&#039;', "'", $txt);
        $txt = str_replace('&quot;', '"', $txt);
        return $txt;
    }

    /**
     * Encode the text passed in
     *
     * @param $txt - plain text
     * @return XML encoded text
     */
    private function encodeXml($txt) {
        $txt = str_replace('&', '&amp;', $txt);
        $txt = str_replace('<', '&lt;', $txt);
        $txt = str_replace('>', '&gt;', $txt);
        $txt = str_replace("'", '&apos;', $txt);
        $txt = str_replace('"', '&quot;', $txt);
        return $txt;
    }

    /**
     * @return IBMHttpClient
     */
    private function getClient() {
        return $this->getHttpClient();
       //$client = new Zend_Http_Client();
       // return $client;
    }
}
