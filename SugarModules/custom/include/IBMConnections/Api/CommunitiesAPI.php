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
require_once 'custom/include/IBMConnections/Api/ProfilesAPI.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomFeed.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsCommunity.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsFile.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivity.php';
//require_once('custom/include/IBMCore/Logger/SFALogger.php');
//require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';

/**
 *
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class CommunitiesAPI extends AbstractConnectionsAPI
{
    /**
     * Content-Type
     */
    const CONTENT_TYPE = 'application/atom+xml';
    const ATOM_NS = 'http://www.w3.org/2005/Atom';

    protected $atomEntry;
    protected $profilesAPI;

    private $LOGGER;

    /**
     *
     * Enter description here ...
     * @param unknown_type $httpClient
     * @param IBMAtomEntry $atomEntry
     */
    function __construct($httpClient)
    {
        parent::__construct($httpClient);
        $this->profilesAPI = new ProfilesAPI($httpClient);
        // $this->LOGGER = SFALogger::getLog('connections');
    }

    /**
     * Method to delete a community
     * @param $communityId
     * @return status code
     */
    public function deleteCommunity($communityId)
    {
        $path = '/communities/service/atom/community/instance?communityUuid='
            . $communityId;
        $response = $this->requestForPath('DELETE', $path);
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }
        if ($response->getStatus() != 200) {
            $message = 'Unexpected httpResponse code  = '
                . $response->getStatus()
                . ' while attempting to delete community [' . $communityId
                . ']';
            //  echo $message;
            return false;
            // $this->LOGGER->fatal($message);
            /* require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
             throw new IBMConnectionsApiException($message,
                     CONNECTIONS_SERVICE_GENERIC, $response);
                     */
        }
        //echo "deleted";
        return true;
    }

    /**
     * Method to create a new community
     * @param $name
     * @param $description
     * @param $type
     * @return mixed
     */
    public function createCommunity($title, $content, $type, $tags = array(), $logo = '')
    {
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();

        $entry->addCategory('community', 'http://www.ibm.com/xmlns/prod/sn/type');
        $entry->setTitle($title);
        $entry->setContent($content);

        // $communityTypeElt = $entry->getDom()->createElementNS('http://www.ibm.com/xmlns/prod/sn','communityType', 'private' );
        $communityTypeElt = $entry->getDom()->createElementNS(
            'http://www.ibm.com/xmlns/prod/sn',
            'communityType',
            $type
        );
        $entry->getEntryNode()->appendChild($communityTypeElt);

        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                $entry->addTag($tag);
            }
        }
        if (!empty($logo)) {
            $logoElt = $entry->getDom()->createElement('link');
            $logoElt->setAttribute('rel', "http://www.ibm.com/xmlns/prod/sn/logo");
            $logoElt->setAttribute('href', $logo);
            $entry->getEntryNode()->appendChild($logoElt);
        }

        $this->getHttpClient()->setRawData($entry->getDomString());

        $response = $this->requestForPath(
            'POST',
            '/communities/service/atom/communities/my',
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }

        $loc = $response->getHeader('Location');
        $parsedURL = parse_url($loc);
        $query = explode('=', $parsedURL['query']);
        return $query[1];
    }

    public function editCommunity($communityId, $title = null, $content = null, $type = null, $tags = array())
    {
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();

        $entry->addCategory('community', 'http://www.ibm.com/xmlns/prod/sn/type');
        $entry->setTitle($title);
        $entry->setContent($content);

        // $communityTypeElt = $entry->getDom()->createElementNS('http://www.ibm.com/xmlns/prod/sn','communityType', 'private' );
        // $communityTypeElt = $entry->getDom()->createElementNS('http://www.ibm.com/xmlns/prod/sn','communityType', $type);
        //$entry->getEntryNode()->appendChild($communityTypeElt);
        if (!empty($type)) {
            $typeElt = $entry->getDom()->createElementNS('http://www.ibm.com/xmlns/prod/sn', 'communityType', $type);
            $nodelist = $entry->getDom()->getElementsByTagName('communityType');
            if ($nodelist->length > 0) {
                $oldnode = $nodelist->item(0);
                $newnode = $entry->getDom()->importNode($typeElt, true);
                $oldnode->parentNode->replaceChild($newnode, $oldnode);
            } else {
                $entry->getEntryNode()->appendChild($typeElt);
            }
        }
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                $entry->addTag($tag);
            }
        }

        // $client = $this->getHttpClient();
        $this->getHttpClient()->setRawData($entry->getDomString());

        $response = $this->requestForPath(
            'PUT',
            '/communities/service/atom/community/instance?communityUuid=' . $communityId,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
    }

    public function createSubcommunity($communityId, $title, $content, $type, $tags = array(), $logo = '')
    {
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry
            ->addCategory(
                'community',
                'http://www.ibm.com/xmlns/prod/sn/type'
            );
        $entry->setTitle($title);
        $entry->setContent($content);

        $communityTypeElt = $entry->getDom()->createElementNS(
            'http://www.ibm.com/xmlns/prod/sn',
            'communityType',
            $type
        );
        $entry->getEntryNode()->appendChild($communityTypeElt);

        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                $entry->addTag($tag);
            }
        }
        if (!empty($logo)) {
            $logoElt = $entry->getDom()->createElement('link');
            $logoElt->setAttribute('rel', "http://www.ibm.com/xmlns/prod/sn/logo");
            $logoElt->setAttribute('href', $logo);
            $entry->getEntryNode()->appendChild($logoElt);
        }

        $parentElt = $entry->getDom()->createElement('link');
        $parentElt->setAttribute('rel', "http://www.ibm.com/xmlns/prod/sn/parentcommunity");
        $parentElt->setAttribute(
            'href',
            $this->url . '/communities/service/atom/community/instance?communityUuid=' . $communityId
        );
        $entry->getEntryNode()->appendChild($parentElt);

        $this->getHttpClient()->setRawData($entry->getDomString());

        $response = $this->requestForPath(
            'POST',
            '/communities/service/atom/communities/my',
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }

        $loc = $response->getHeader('Location');
        $query = explode('communityUuid=', $loc);
        return $query[1];
    }

    /**
     *
     */
    public function getCommunity($id)
    {

        $remoteAppFeed = $this->getRemoteAppFeed($id);
        if (empty($remoteAppFeed)) {
            return "no_connection";
        }
        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet("communityUuid", $id);
        $result = $this->requestForPath("GET", "/communities/service/atom/community/instance");
        if (empty($result) || !$this->checkResult($result)) {
            return;
        }

        $entry = IBMAtomEntry::loadFromString($result->getBody());

        return new ConnectionsCommunity($entry, $remoteAppFeed);
    }


    /**
     * Function to return the communities the user is a member of
     * @param string $filterType - filter the communities the user is a member
     *     or an owner of, default is no filtering
     * @param integer $pageSize - specify the number of communities to return,
     *     default is to return 10
     * @return multitype:ConnectionsCommunity
     */
    public function listMyCommunities(
        $search = null,
        $filterType = null,
        $sortBy = null,
        $sortOrder = null,
        $pageSize = null,
        $pageNumber = null
    ) {
        $this->getHttpClient()->resetParameters();
        if ($filterType != null) {
            $this->getHttpClient()->setParameterGet("filterType", $filterType);
        }
        if ($search != null) {
            $this->getHttpClient()->setParameterGet("search", $search);
        } elseif ( is_null($sortBy) ) {
            $sortBy = "title";
        }

        if ($pageSize != null) {

            $this->getHttpClient()->setParameterGet("ps", $pageSize);
        }

        if ($pageNumber != null) {
            $this->getHttpClient()->setParameterGet("page", $pageNumber);
        }

        if ($sortBy != null) {
            $this->getHttpClient()->setParameterGet("sortField", $sortBy);
        }

        if ($sortOrder != null) {
            if ($sortOrder == "asc") {
                $this->getHttpClient()->setParameterGet("asc", "true");
            } else {
                $this->getHttpClient()->setParameterGet("asc", "false");
            }
        }

        $result = $this->requestForPath("GET", '/communities/service/atom/communities/my');
        if (empty($result) || !$this->checkResult($result)) {
            return array('communities' => array(), 'metadata' => array());
        }

        $communities = array();
        $feed = IBMAtomFeed::loadFromString($result->getBody());
        $entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $communities[] = new ConnectionsCommunity($entry, null);
        }

        $retrievedPageNumber = ceil($feed->getStartIndex() / $feed->getItemsPerPage());
        $this->setLastResultMetadata(
            new ConnectionsResultMetadata($sortBy, $sortOrder, $feed->getItemsPerPage(
            ), $retrievedPageNumber, $feed->getTotalResults())
        );
        return array('communities' => $communities, 'metadata' => $this->getLastResultMetadata());
    }

    /**
     * Function to return all communities
     * @param string $filterType - filter the communities the user is a member
     *     or an owner of, default is no filtering
     * @param integer $pageSize - specify the number of communities to return,
     *     default is to return 10
     * @return multitype:ConnectionsCommunity
     */
    public function listAllCommunities(
        $search = null,
        $filterType = null,
        $sortBy = null,
        $sortOrder = null,
        $pageSize = null,
        $pageNumber = null
    ) {
        $this->getHttpClient()->resetParameters();
        if ($filterType != null) {
            $this->getHttpClient()->setParameterGet("filterType", $filterType);
        }
        if ($search != null) {
            $this->getHttpClient()->setParameterGet("search", $search);
        } else {
            $sortBy = "lastmod";
        }

        if ($pageSize != null) {
            $this->getHttpClient()->setParameterGet("ps", $pageSize);
        }

        if ($pageNumber != null) {
            $this->getHttpClient()->setParameterGet("page", $pageNumber);
        }

        if ($sortBy != null) {
            $this->getHttpClient()->setParameterGet("sortField", $sortBy);
        }

        if ($sortOrder != null) {
            if ($sortOrder == "asc") {
                $this->getHttpClient()->setParameterGet("asc", "true");
            } else {
                $this->getHttpClient()->setParameterGet("asc", "false");
            }
        }

        $result = $this->requestForPath("GET", '/communities/service/atom/communities/all');
        if (empty($result) || !$this->checkResult($result)) {
            return array('communities' => array(), 'metadata' => array());
        }

        $communities = array();
        $feed = IBMAtomFeed::loadFromString($result->getBody());
        $entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $communities[] = new ConnectionsCommunity($entry, null);
        }

        $retrievedPageNumber = ceil($feed->getStartIndex() / $feed->getItemsPerPage());
        $this->setLastResultMetadata(
            new ConnectionsResultMetadata($sortBy, $sortOrder, $feed->getItemsPerPage(
            ), $retrievedPageNumber, $feed->getTotalResults())
        );
        return array('communities' => $communities, 'metadata' => $this->getLastResultMetadata());
    }

    public function getSubCommunities(
        $communityId,
        $search = null,
        $filterType = null,
        $sortBy = null,
        $sortOrder = null,
        $pageSize = null,
        $pageNumber = null
    ) {
        $this->getHttpClient()->resetParameters();
        if ($filterType != null) {
            $this->getHttpClient()->setParameterGet("filterType", $filterType);
        }
        if ($search != null) {
            $this->getHttpClient()->setParameterGet("search", $search);
        } else {
            $sortBy = "lastmod";
        }

        if ($pageSize != null) {
            $this->getHttpClient()->setParameterGet("ps", $pageSize);
        }

        if ($pageNumber != null) {
            $this->getHttpClient()->setParameterGet("page", $pageNumber);
        }

        if ($sortBy != null) {
            $this->getHttpClient()->setParameterGet("sortField", $sortBy);
        }

        if ($sortOrder != null) {
            if ($sortOrder == "asc") {
                $this->getHttpClient()->setParameterGet("asc", "true");
            } else {
                $this->getHttpClient()->setParameterGet("asc", "false");
            }
        }

        $result = $this->requestForPath(
            "GET",
            "/communities/service/atom/community/subcommunities?communityUuid={$communityId}"
        );
        if (empty($result) || !$this->checkResult($result)) {
            return array('communities' => array(), 'metadata' => array());
        }

        $communities = array();
        $feed = IBMAtomFeed::loadFromString($result->getBody());
        $entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $communities[] = new ConnectionsCommunity($entry, null);
        }
        return $communities;
    }

    /**
     *
     * Description:
     * Method is responsible for logging into Connections Activites and
     * the activate the Activites widget. This widget is not active
     * by default.
     *
     * Parmeters: None
     * Returns: Widget activation response code.
     */
    public function activateActivities($communityId)
    {
        return $this->activateAppWidget($communityId, "Activities");
    }

    /**
     *
     * Description:
     * Method is responsible to activate the a Connections app widget.
     */
    public function activateAppWidget($communityId, $appName)
    {
        $client = $this->getHttpClient(true);

        $this->getHttpClient()->resetParameters();
        $raw = '<?xml version="1.0" encoding="UTF-8"?>';
        $raw .= '<entry xmlns:snx="http://www.ibm.com/xmlns/prod/sn" xmlns="http://www.w3.org/2005/Atom">';
        $raw .= '<title type="text">' . $appName . '</title>';
        $raw .= '<category term="widget" scheme="http://www.ibm.com/xmlns/prod/sn/type" />';
        $raw .= '<snx:widgetDefId>' . $appName . '</snx:widgetDefId>';
        $raw .= '<snx:location>col2</snx:location>';
        $raw .= '</entry>';
        $client->setRawData($raw);

        $client->setHeaders(array('Content-Type' => 'text/plain'));
        $result = $this->requestForPath('POST', '/communities/service/atom/community/widgets?communityUuid=' . $communityId);

        //return array('message' => 'widget_is_not_activated');
        return ($result->getStatus() == 201);
    }

    /**
     * Method to add a list a members (using email addresses) to a community
     * @param $cid
     * @param array $members - tuple containing email address and isMembershipModifier
     */
    function addMembers($cid, $members = array())
    {
        global $sugar_config;

        $path = '/communities/service/atom/community/members?communityUuid='
            . $cid;
        $client = $this->getHttpClient();
        $client->resetParameters();
        $response = $this->requestForPath('GET', $path);
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        if ($members && !empty($members)) {
            // retrieve entry tag
            $entryElt = $dom->getElementsByTagName('entry')->item(0);

            // trim out Feed - Keep only Entry
            $entryDom = new DOMDocument('1.0', 'UTF-8');
            $entryDom->appendChild($entryDom->importNode($entryElt, true));
            $dom = & $entryDom;
            $entryElt = $dom->getElementsByTagName('entry')->item(0);

            // trim out the owner..
            $existingContributor = $dom->getElementsByTagName('contributor')
                ->item(0);
            $ownerEmail = $existingContributor->ownerDocument
                ->getElementsByTagName('email')->item(0)->textContent;
            $entryElt->removeChild($existingContributor);

            // trim out role for next iteration
            $existingRole = $dom
                ->getElementsByTagNameNS(
                    'http://www.ibm.com/xmlns/prod/sn',
                    'role'
                )
                ->item(0);
            $entryElt->removeChild($existingRole);

            foreach ($members as $member) {
                foreach ($member as $key => $value) {
                    if ($key === 'email') {
                        $emails[] = $value;
                    }
                }
            }

            // Retrieve User Ids..
            $userIdsMap = $this->profilesAPI->getUserIds($emails);
            foreach ($members as $member) {
                $userId = $userIdsMap[strtolower($member['email'])];

                // Email doesnt map to any valid user ID in connections
                if (!$userId) {
                    continue;
                }

                // Do not attempt to add the owner again.


                if (strcasecmp($ownerEmail, $member['email']) == 0) {
                    continue;
                }

                //  $this->LOGGER->info('adding userId == ' . $userId . '. Admin: ' . $member['isMembershipModifiers']);
                $contributorElt = $dom->createElement('contributor');
                $emailElt = $dom->createElement('email');
                $emailElt
                    ->appendChild($dom->createTextNode($member['email']));
                $contributorElt->appendChild($emailElt);
                $entryElt->appendChild($contributorElt);

                //Add Profile API to retrieve UserID
                $uuidElt = $dom
                    ->createElementNS(
                        'http://www.ibm.com/xmlns/prod/sn',
                        'snx:userid',
                        $userId
                    );
                $contributorElt->appendChild($uuidElt);

                // New role.
                $roleElt = $dom
                    ->createElementNS(
                        'http://www.ibm.com/xmlns/prod/sn',
                        'snx:role',
                        $member['isMembershipModifiers'] ? 'owner'
                            : 'member'
                    );
                $roleElt
                    ->setAttribute(
                        'component',
                        'http://www.ibm.com/xmlns/prod/sn/communities'
                    );
                $entryElt->appendChild($roleElt);

                //Send Post request everytime
                $client->setRawData($dom->saveXML());

                $client->setIgnoreResponseContentLength();
                $response = $this
                    ->requestForPath(
                        'POST',
                        $path,
                        array(
                            'X-HTTP-Method-Override' => 'PUT',
                            'Content-Type' => self::CONTENT_TYPE,
                            'Content-Language' => 'en-US',
                        )
                    );
                if (empty($response) || !$this->checkResult($response)) {
                    return;
                }
                switch ($response->getStatus()) {
                    case 201: /*Created*/
                    case 204: /*No Content*/
                        break;

                    default:
                        $message = 'Unexpected httpResponse code  = '
                            . $response->getStatus()
                            . ' while attempting to add member ['
                            . $member['email'] . ']';
                    //  $this->LOGGER->fatal($message);
                }

                // trim out contributor for next iteration
                $existingContributor = $dom
                    ->getElementsByTagName('contributor')->item(0);
                $entryElt->removeChild($existingContributor);
            }
        }
    }

    /**
     * Method single member, throws exception
     * @param $cid
     * @param array $members - tuple containing email address and isMembershipModifier
     */
    function addMember($cid, $member)
    {
        global $sugar_config;

        $path = '/communities/service/atom/community/members?communityUuid=' . $cid;
        $client = $this->getHttpClient();
        $client->resetParameters();
        $response = $this->requestForPath('GET', $path);
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        if (!$member || empty($member)) {
            //$this->LOGGER->warn(__METHOD__ . ' Empty member list provided!');
        }

        // retrieve entry tag
        $entryElt = $dom->getElementsByTagName('entry')->item(0);

        // trim out Feed - Keep only Entry
        $entryDom = new DOMDocument('1.0', 'UTF-8');
        $entryDom->appendChild($entryDom->importNode($entryElt, true));
        $dom = & $entryDom;
        $entryElt = $dom->getElementsByTagName('entry')->item(0);

        // trim out the owner..
        $existingContributor = $dom->getElementsByTagName('contributor')->item(0);
        $ownerEmail = $existingContributor->ownerDocument->getElementsByTagName('email')->item(0)->textContent;
        $entryElt->removeChild($existingContributor);

        // trim out role for next iteration
        $existingRole = $dom->getElementsByTagNameNS('http://www.ibm.com/xmlns/prod/sn', 'role')->item(0);
        $entryElt->removeChild($existingRole);

        // Retrieve User Ids..
        $userIdsMap = $this->profilesAPI->getUserIds(array($member['email']));
        if (!array_key_exists(strtolower($member['email']), $userIdsMap)) {
            //	$this->LOGGER->warn(__METHOD__ . ' Cannot find member profile: ' . $member['email']);
            /*require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            throw new IBMConnectionsApiException("Email doesn't map to any valid user ID in Connections",
                    CONNECTIONS_SERVICE_GENERIC);*/
        }
        $userId = $userIdsMap[strtolower($member['email'])];

        // Email doesnt map to any valid user ID in connections
        if (!$userId) {
            /*	require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
                throw new IBMConnectionsApiException("Email doesn't map to any valid user ID in Connections (2)",
                        CONNECTIONS_SERVICE_GENERIC);
                        */
        }

        // Do not attempt to add the owner again.
        if (strcasecmp($ownerEmail, $member['email']) == 0) {
            return;
        }

        //$this->LOGGER->info(__METHOD__ . 'adding userId == ' . $userId . '. Admin: ' . $member['isMembershipModifiers']);
        $contributorElt = $dom->createElement('contributor');
        $emailElt = $dom->createElement('email');
        $emailElt->appendChild($dom->createTextNode($member['email']));
        $contributorElt->appendChild($emailElt);
        $entryElt->appendChild($contributorElt);

        //Add Profile API to retrieve UserID
        $uuidElt = $dom->createElementNS('http://www.ibm.com/xmlns/prod/sn', 'snx:userid', $userId);
        $contributorElt->appendChild($uuidElt);

        // New role.
        $roleElt = $dom->createElementNS(
            'http://www.ibm.com/xmlns/prod/sn',
            'snx:role',
            $member['isMembershipModifiers'] ? 'owner' : 'member'
        );
        $roleElt->setAttribute('component', 'http://www.ibm.com/xmlns/prod/sn/communities');
        $entryElt->appendChild($roleElt);

        //Send Post request everytime
        $client->setRawData($dom->saveXML());

        $client->setIgnoreResponseContentLength();
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'X-HTTP-Method-Override' => 'PUT',
                'Content-Type' => self::CONTENT_TYPE,
                'Content-Language' => 'en-US',
            )
        );
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }

        switch ($response->getStatus()) {
            case 201: /*Created*/
            case 409: /*already added*/
            case 204: /*No Content*/
                break;

            default:
                $message = 'Unexpected httpResponse code  = '
                    . $response->getStatus()
                    . ' while attempting to add member ['
                    . $member['email'] . ']';


            /*require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            throw new IBMConnectionsApiException($message,
                CONNECTIONS_SERVICE_GENERIC, $response);
            */

        }

        // trim out contributor for next iteration
        $existingContributor = $dom->getElementsByTagName('contributor')->item(0);
        $entryElt->removeChild($existingContributor);
    }

    public function AddMemberById($member_id, $community_id, $role)
    {
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $elt = $entry->getDom()->createElement('contributor');

        $uuidElt = $entry->getDom()->createElementNS('http://www.ibm.com/xmlns/prod/sn', 'snx:userid', $member_id);
        $elt->appendChild($uuidElt);
        $entry->getEntryNode()->appendChild($elt);

        $roleEl = $entry->getDom()->createElementNS('http://www.ibm.com/xmlns/prod/sn', 'snx:role', $role);
        $roleEl->setAttribute('component', 'http://www.ibm.com/xmlns/prod/sn/communities');
        $entry->getEntryNode()->appendChild($roleEl);

        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setRawData($entry->getDomString());
        $result = $this->requestForPath(
            'POST',
            "communities/service/atom/community/members?communityUuid={$community_id}",
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
    }


    /**
     * Function attempts to remove 1 member from a Community
     * @param $cid = Community Id
     * @param $email
     * @throws IBMConnectionsApiException
     */
    public function removeMember($cid, $email)
    {
        $path = '/communities/service/atom/community/members?communityUuid=' . $cid . '&email=' . $email;
        $response = $this->requestForPath('DELETE', $path);
        switch ($response->getStatus()) {
            case 200: //Member was successfully removed with wrong error code
            case 204: //Member was successfully removed
            case 404: //Member already removed from community
                break;
            default:
                $message = 'Unexpected httpResponse code  = '
                    . $response->getStatus()
                    . ' while attempting to remove member [' . $email
                    . ']';

            /*	require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
                throw new IBMConnectionsApiException($message,
                            CONNECTIONS_SERVICE_GENERIC, $response);
                        */
        }
    }

    public function removeMemberById($id, $cid)
    {
        $path = '/communities/service/atom/community/members?communityUuid=' . $cid . '&userid=' . $id;
        $response = $this->requestForPath('DELETE', $path);
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }
        switch ($response->getStatus()) {
            case 200: //Member was successfully removed with wrong error code
            case 204: //Member was successfully removed
            case 404: //Member already removed from community
                break;
            default:
                $message = 'Unexpected httpResponse code  = '
                    . $response->getStatus()
                    . ' while attempting to remove member [' . $id
                    . ']';
            //echo $message;
            /*	require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';

                throw new IBMConnectionsApiException($message,
                            CONNECTIONS_SERVICE_GENERIC, $response);
                        */
        }
    }


    /**
     * Function attempts to remove members from a Community
     * @param $cid = Community Id
     * @param $emails = Array of email adresses of the members
     * that will be removed from the Community
     * @throws IBMConnectionsApiException
     */
    public function removeMembers($cid, $emails = array())
    {
        foreach ($emails as $email) {
            $path = '/communities/service/atom/community/members?communityUuid='
                . $cid . '&email=' . $email;
            $response = $this->requestForPath('DELETE', $path);
            if (empty($response) || !$this->checkResult($response)) {
                return;
            }
            switch ($response->getStatus()) {
                case 200:
                    //Member was successfully removed with wrong error code
                    break;
                case 204:
                    //Member was successfully removed
                    break;
                case 404:
                    //Member already removed from community
                    break;
                default:
                    $message = 'Unexpected httpResponse code  = '
                        . $response->getStatus()
                        . ' while attempting to remove member [' . $email
                        . ']';
                //$this->LOGGER->fatal($message);
            }
        }
    }

    /**
     * Function attempts to share an Existing File in Connections MyFiles
     * With a community
     * @param $cid = Community Id
     * @param $myFileId = File Id in connections
     * @return bool
     *      TRUE : File has been sucessfully shared
     *      FALSE: File is already been shared
     * @throws IBMConnectionsApiException
     */
    public function shareMyFileWithCommunity($cid, $myFileId)
    {
        $path = '/communities/service/atom/community/instance?communityUuid=' . $cid;
        $response = $this->requestForPath('GET', $path);
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }
        if ($response->getStatus() == 404) {
            // No access to the community
            $message = 'Received HTTP code 404 while attempting to share File [' . $myFileId . ']  with community [' . $cid . '].'
                . ' The community appears to have been deleted.';
            /*require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            $connEx = new IBMConnectionsApiException($message,
                ERR_COMMUNITIES_SERVICE_NO_MAPPED_COMMUNITY, $response);
            //$this->LOGGER->fatal($connEx->__toString());
            throw $connEx;
            */
            //    echo $message;
        }
        unset($response);

        $dom = new DOMDocument();
        $feedElt = $dom->createElementNS(self::ATOM_NS, 'feed');
        $dom->appendChild($feedElt);

        $entryElt = $dom->createElement('entry');

        $categoryElt = $dom->createElement('category');
        $categoryElt->setAttribute('term', 'community');
        $categoryElt->setAttribute('label', 'community');
        $categoryElt->setAttribute('scheme', 'tag:ibm.com,2006:td/type');
        $entryElt->appendChild($categoryElt);

        $itemIdElt = $dom->createElementNS('urn:ibm.com/td', 'itemId', $cid);

        $entryElt->appendChild($itemIdElt);
        $feedElt->appendChild($entryElt);

        $client = $this->getHttpClient();
        $client->setRawData($dom->saveXML());
        //error_log( $dom->saveXML() );
        $response = $this
            ->requestForPath(
                'POST',
                '/files/basic/api/myuserlibrary/document/' . $myFileId . '/feed',
                array(
                    'Content-Type' => self::CONTENT_TYPE,
                    'Content-Language' => 'en-US'
                )
            );

        //error_log( $response->getBody() );
//echo $response->getStatus();
        switch ($response->getStatus()) {
            case 204:
                //File has been shared
                return true;
                break;

            case 409:
                //Conflict - File already shared
                return false;
                break;

            default:
                $message = 'Unexpected httpResponse code  = '
                    . $response->getStatus()
                    . ' while attempting to share File [' . $myFileId
                    . ']  with community [' . $cid . ']';
            //            echo $message;
            // $this->LOGGER->fatal($message);
            /*require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            $connEx = IBMConnectionsApiException($message,
                    CONNECTIONS_SERVICE_GENERIC, $response);
           // $this->LOGGER->fatal($connEx->__toString());
            throw new $connEx;
            */

        }

    }

    /**
     *
     * Enter description here ...
     * @param $cid
     * @param $myFileId
     */
    public function unshareMyFileFromCommunity($cid, $myFileId)
    {
        $nonce = $this->requestNonce();
        $client = $this->getHttpClient();
        $client->setParameterPost('itemId', $myFileId);
        $response = $this
            ->requestForPath(
                'POST',
                '/files/basic/api/communitycollection/' . $cid
                . '/feed',
                array(
                    'Content-Type' => self::CONTENT_TYPE,
                    'Content-Language' => 'en-US',
                    'X-Method-Override' => 'DELETE',
                    'X-Update-Nonce' => $nonce
                )
            );

        //print_r( $response->getBody() );
//echo $response->getStatus();
        switch ($response->getStatus()) {
            case 204:
                //File has been unshared
                return true;
                break;

            case 404:
                //File already unshared.
                return false;
                break;

            default:
                $message = 'Unexpected httpResponse code  = '
                    . $response->getStatus()
                    . ' while attempting to unshare File [' . $myFileId
                    . ']  from community [' . $cid . ']';
            // $this->LOGGER->fatal($message);
            /*require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            throw new IBMConnectionsApiException($message,
                    CONNECTIONS_SERVICE_GENERIC, $response);
                */
        }
        return false;
    }


    /**
     * Method to update a member's community membership type (using email addresses)
     * @param $cid
     * @param $typeMembership ( 'owner' or 'member' )
     * @param $emails
     */
    public function updateCommunityMembership($cid, $typeMembership, $email)
    {
        $myUserId = $this->profilesAPI->getUserId($email);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $entryElt = $dom->createElementNS(self::ATOM_NS, 'entry');
        $dom->appendChild($entryElt);

        $path = '/communities/service/atom/community/members?communityUuid='
            . $cid;
        $client = $this->getHttpClient();
        $client->setParameterGet('userid', $myUserId);

        $roleElt = $dom
            ->createElementNS(
                'http://www.ibm.com/xmlns/prod/sn',
                'snx:role',
                $typeMembership
            );
        $roleElt
            ->setAttribute(
                'component',
                'http://www.ibm.com/xmlns/prod/sn/communities'
            );
        $entryElt->appendChild($roleElt);

        $contributorElt = $dom->createElement('contributor');
        $entryElt->appendChild($contributorElt);

        //Add Profile API to retrieve UserID
        $uuidElt = $dom
            ->createElementNS(
                'http://www.ibm.com/xmlns/prod/sn',
                'snx:userid',
                $myUserId
            );
        $contributorElt->appendChild($uuidElt);

        //Send Post request everytime
        $client->setRawData($dom->saveXML());
        $client->setIgnoreResponseContentLength();
        $response = $this
            ->requestForPath(
                'PUT',
                $path,
                array(
                    'Content-Type' => self::CONTENT_TYPE,
                    'Content-Language' => 'en-US'
                )
            );
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }
        switch ($response->getStatus()) {
            case 200:
                //Membership updated.
                return true;

            default:
                $message = 'Unexpected httpResponse code  = '
                    . $response->getStatus()
                    . ' while attempting to update user [' . $email
                    . ']  membership type in community [' . $cid
                    . '] to membership type [' . $typeMembership . ']';
            //$this->LOGGER->fatal($message);
            /*
            require_once 'custom/include/IBMConnections/Exceptions/IBMConnectionsApiException.php';
            throw new IBMConnectionsApiException($message,
                    CONNECTIONS_SERVICE_GENERIC, $response);*/
        }

    }

    /**
     * Method retrieve members emails and membership in the community.
     * Key represents email in Connections, and value represents Community membership:
     * 'owner' | 'member'
     *
     * @param $cid
     * @return array( array( 'emailowner1' => 'owner', 'emailmember2' => 'member'));
     * @return array( array( 'id1' => 'owner', 'id2' => 'member'));
     */
    public function listMembers($cid, $page = 1, $sortField = 'created', $asc = 'true')
    {
        $result = array();

        // Retrieve Members Email for this community..
        $path = '/communities/service/atom/community/members?communityUuid=' . $cid;

        $this->httpClient = $this->getHttpClient();
        if ($sortField == 'created' || $sortField == 'name') {
            $this->httpClient->setParameterGet("sortField", $sortField);
        }
        $this->httpClient->setParameterGet("page", $page);
        $this->httpClient->setParameterGet("ps", 32);
        $this->httpClient->setParameterGet("asc", $asc);
        $response = $this->requestForPath('GET', $path);
        if (empty($response) || !$this->checkResult($response)) {
            return;
        }
        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        for ($i = 0; $i < $dom->getElementsByTagName('entry')->length; $i++) {
            $entryNodes = $dom->getElementsByTagName('entry')->item($i)
                ->childNodes;
            $email = null;
            $membership = null;
            foreach ($entryNodes as $entryNode) {
                switch ($entryNode->tagName) {
                    case 'snx:role':
                    case 'role':
                        $membership = $entryNode->textContent;
                        break;
                    case 'contributor':
                        foreach ($entryNode->childNodes as $contribNode) {
                            //if ($contribNode->tagName == 'email') {
                            if ($contribNode->tagName == 'snx:userid' || $contribNode->tagName == 'userid') {
                                $email = $contribNode->textContent;
                                break;
                            }
                        }
                        break;
                    default:
                        // dont map data.
                }
                if (isset($email) && isset($membership)) {
                    $result[strtolower($email)] = $membership;
                    $email = null;
                    $membership = null;
                }
            }
        }
        return $result;

    }

    public function getActivityCount($communityId)
    {
        return $this->getCount("/activities/service/atom2/everything?commUuid=" . $communityId . '&completed=yes');
    }

    public function getFileCount($communityId)
    {
        return $this->getCount("/files/basic/api/communitycollection/" . $communityId . "/feed");
    }

    public function getBookmarkCount($communityId)
    {
        return $this->getCount("/communities/service/atom/community/bookmarks?communityUuid=" . $communityId);
    }

    public function getDiscussionCount($communityId)
    {
        return $this->getCount("/forums/atom/topics?communityUuid=" . $communityId);
    }

    public function getUpdateCount($communityId)
    {
        return $this->getCount("/news/atom/stories/community?communityUUID=" . $communityId);
    }

    public function getWikiCount($communityId)
    {
        return $this->getCount("/wikis/basic/api/communitywiki/" . $communityId . "/feed");
    }

    ////
    public function getBlogCount($communityId)
    {
        return $this->getCount("/blogs/" . $communityId . "/feed");
    }

    private function getCount($path)
    {
        $this->getHttpClient()->resetParameters();
        $result = $this->requestForPath("GET", $path);
        try {
            $feed = IBMAtomFeed::loadFromString($result->getBody());
            return $feed->getTotalResults();
        } catch (Exception $c) {
            return 0;
        }
    }

    public function isWidgetActivated($communityId, $widget)
    {
        $feed = $this->getRemoteAppFeed($communityId);
        if (empty($feed)) {
            return false;
        }
        $entries = $feed->getEntries();
        for ($i = 0; $i < sizeof($entries); $i++) {
            $entry = $entries[$i];
            $retArray = $entry->getCategories("http://www.ibm.com/xmlns/prod/sn/type");
            for ($k = 0; $k < sizeof($retArray); $k++) {
                if (strcmp($retArray[$k]['term'], $widget) == 0) {
                    $link = $entry->getLink("http://www.ibm.com/xmlns/prod/sn/remote-application/feed");
                    $path = $this->extractRelativeUrlPath($link['href']);
                }
            }
        }
        return !empty($path);
    }

}
