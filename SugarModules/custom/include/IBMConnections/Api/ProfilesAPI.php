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


/**
 * @author Christophe Guillou
 */

//require_once('custom/include/IBMCore/Logger/SFALogger.php');
class ProfilesAPI extends AbstractConnectionsAPI{

    /**
     * @var SFALogger
     */
    private $LOGGER;

    private $whoAmI;

    protected static $emailToIds_Cache = array();

    function __construct($httpClient = null) {
        parent::__construct($httpClient);
       // $this->LOGGER = SFALogger::getLog('connections');
    }

    /**
     * Method retrieves UserId in connection based on Intranet ID (email)
     * @param $email
     * @return
     */
    public function getUserId( $email )
    {
        $emails = array();
        $emails[] = $email;
        $userIds = $this->getUserIds( $emails );
        return $userIds[strtolower($email)];
    }

    /**
     * Method to retrieve currently authenticated User UUID in Connections.
     * @throws IBMConnectionsApiException
     */
    public function whoAmI()
    {
        if( ! $this->whoAmI )
        {
            $response = $this->requestForPath('GET', '/profiles/atom/profileService.do');

           /* if ($response->getStatus() !== 200) {

                require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
                $connEx = new IBMConnectionsApiException(
                        'Unexpected Authentication Error During Retrieval of Authenticated User UUID in Connections', 
                        PROFILES_API_AUTH_UUID_RETRIEVAL_FAILED, $response);
                $this->LOGGER->error($connEx->__toString());
                throw $connEx;
            }*/

            $dom = new DOMDocument();
            $dom->loadXML( $response->getBody() );
            echo $response->getBody();
            $collections = $dom->getElementsByTagName('collection');

            foreach($collections->item(0)->childNodes as $collectionNode)
            {
                switch( $collectionNode->tagName )
                {
                    case 'snx:userid':
                    case 'userid':
                        $this->whoAmI = $collectionNode->textContent;
                        break;
                }
            }

        }

        return $this->whoAmI;
    }

    /**
     * Method to retrieve UserIds in Connections based on Intranet UserIds.
     * 
     * @throws IBMConnectionsApiException
     * @param array $emails
     * @return array|null
     */
    public function getUserIds( $emails = array() )
    {
        if( empty($emails) )
            return null;

        // Implement caching
        $cachedIds = array();
        $nonCachedIds = array();

        foreach($emails as $email)
        {
            if( isset(self::$emailToIds_Cache[strtolower($email)] ) )
            {
                $cachedIds[strtolower($email)] = self::$emailToIds_Cache[strtolower($email)];
            } else {
                $nonCachedIds[] = strtolower($email);
            }
        }

        if( count($cachedIds) == count($emails) )
        {
            return $cachedIds;
        } else{
            $emails = $nonCachedIds;
        }

        $emailParam = count($emails) > 1  ? implode(',', $emails) : $emails[0];

        $response = $this->getClient()->requestForPath('GET', '/profiles/atom/profile.do?email=' . $emailParam );

       /* if ($response->getStatus() !== 200) {

            require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            $connEx = new IBMConnectionsApiException(
                    'Unexpected Authentication Error During Retrieval of User IDs In Connections Based on Intranet Userids', 
                    PROFILES_API_AUTH_USER_IDS_RETRIEVAL_FAILED, $response);
            $this->LOGGER->error($connEx->__toString());
            throw $connEx;
        }
*/
        //$this->LOGGER->debug(  $response->getBody() );
        $dom = new DOMDocument();
        $dom->loadXML( $response->getBody() );

        $userIds = $cachedIds;
        $contributors = $dom->getElementsByTagName('contributor');
        //$this->LOGGER->debug( 'Length == ' . $contributors->length );
        for($i=0; $i<$contributors->length; $i++) {

            $email = null;
            $userId = null;
            $contribNodes = $contributors->item($i)->childNodes;
            foreach($contribNodes as $contribNode)
            {
               // $this->LOGGER->debug( $contribNode->tagName );
                switch( $contribNode->tagName )
                {
                    case 'snx:userid':
                    case 'userid':
                  //  $this->LOGGER->debug( 'USER ID == ' . $contribNode->textContent);
                        $userId = $contribNode->textContent;
                        break;
                    case 'email':
                      //  $this->LOGGER->debug( 'EMAIL == ' . $contribNode->textContent);
                        $email = $contribNode->textContent;
                        break;
                }

            }

            $userIds[ strtolower($email) ] = $userId;
        }

        foreach( $userIds as $email => $userId)
        {
            // cache the new Ids
            self::$emailToIds_Cache[$email] = $userId;
        }

        return $userIds;
    }


    /**
     * @return IBMHttpClient
     */
    private function getClient(){
        return $this->getHttpClient();
    }

    public function getProfile($userId)
    {
        $response = $this->requestForPath('GET', '/profiles/atom/profile.do?userid=' . $userId );
        $feed = IBMAtomFeed::loadFromString($response->getBody());
        $entries =  $feed->getEntries();
        $entry = $entries[0];

        $picture = $entry->getLink("http://www.ibm.com/xmlns/prod/sn/image", "image");

        $profileArr = array(
            'id' => $userId,
            'name' => $entry->getTitle(),
            'picture' => $picture['href']
        );

        return $profileArr;
    }
}
