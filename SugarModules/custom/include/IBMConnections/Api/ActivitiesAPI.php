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
require_once 'custom/include/IBMConnections/Api/CommunitiesAPI.php';
require_once 'custom/include/IBMConnections/FeedApi/IBMAtomFeed.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivities.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivity.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivityNode.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivityEntry.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivitySection.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivityTodo.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsActivityReply.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsHistoryEntry.php';

/**
 *
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ActivitiesAPI extends AbstractConnectionsAPI
{
    /**
     *
     * Enter description here ...
     */

    public function listCommunityActivities($communityId, $page, $searchText)
    {
        //      	$commApi = new CommunitiesAPI($this->getHttpClient());
//        	$commApi->activateActivities($communityId); 
        $feed = $this->getRemoteAppFeed($communityId);
        if (empty($feed)) {
            return array();
        }
        $entries = array();
        $entries = $feed->getEntries();
        for ($i = 0; $i < sizeof($entries); $i++) {
            $entry = $entries[$i];
            $retArray = $entry->getCategories("http://www.ibm.com/xmlns/prod/sn/type");
            for ($k = 0; $k < sizeof($retArray); $k++) {
                if (strcmp($retArray[$k]['term'], "Activities") == 0) {
                    $link = $entry->getLink("http://www.ibm.com/xmlns/prod/sn/remote-application/feed");
                    $path = $this->extractRelativeUrlPath($link['href']);
                }
            }
        }
        if (empty($path)) {
            return array('message' => 'widget_is_not_activated');
        }


        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet("commUuid", $communityId);
        $this->getHttpClient()->setParameterGet("page", $page);
        $this->getHttpClient()->setParameterGet("ps", '10');
        $this->getHttpClient()->setParameterGet("sortBy", 'title');
        $this->getHttpClient()->setParameterGet("sortOrder", 'asc');
        if (!empty($searchText)) {
            $this->getHttpClient()->setParameterGet("nodetype", 'community_activity');
            $this->getHttpClient()->setParameterGet("search", $searchText);

        }
        $this->getHttpClient()->setParameterGet("completed", "yes");

        //$this->getHttpClient()->setParameterGet("tunedout","yes");
        $result = $this->requestForPath("GET", "/activities/service/atom2/everything");
        if (empty($result) || !$this->checkResult($result)) {
            return array();
        }
        $activities = array();
        $feed = IBMAtomFeed::loadFromString($result->getBody());
        $entries = $feed->getEntries();
        foreach ($entries as $entry) {
            $activities[] = new ConnectionsActivity($entry);
        }
        return $activities;
    }

    /**
     *
     */
    public function getActivity($id)
    {
        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet("activityUuid", $id);
        $this->getHttpClient()->setParameterGet("sortBy", 'title');
        $this->getHttpClient()->setParameterGet("sortOrder", 'asc');
        $result = $this->requestForPath("GET", "/activities/service/atom2/activity");
        if (empty($result) || !$this->checkResult($result)) {
            return;
        }
        $feed = IBMAtomFeed::loadFromString($result->getBody());
        return new ConnectionsActivity($feed);
    }

    public function getActivityNode($id)
    {
        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet("activityNodeUuid", $id);
        $result = $this->requestForPath("GET", "/activities/service/atom2/activitynode");
        if (empty($result) || !$this->checkResult($result)) {
            return;
        }
        $entry = IBMAtomEntry::loadFromString($result->getBody());
        return new ConnectionsActivity($entry);
    }

    public function deleteActivityNode($id)
    {
        $this->getHttpClient()->resetParameters();
        //$this->getHttpClient()->setParameterGet("activityNodeUuid", $id);
        $result = $this->requestForPath("DELETE", "/activities/service/atom2/activitynode?activityNodeUuid=" . $id);
        return (!empty($result) && $this->checkResult($result));
    }

    public function getNode($activityId, $nodeId)
    {
        $feedLink = "/activities/service/atom2/activity?activityUuid=" . $activityId . "&ps=150";
        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet("sortBy", 'title');
        $this->getHttpClient()->setParameterGet("sortOrder", 'asc');
        $result = $this->requestForPath("GET", $feedLink);
        if (empty($result) || !$this->checkResult($result)) {
            return;
        }
        $feed = IBMAtomFeed::loadFromString($result->getBody());

        $nodeEntries = $feed->getEntries();
        $nodes = array();
        foreach ($nodeEntries as $nodeEntry) {
            if (substr(
                    $nodeEntry->getId(),
                    -1 * strlen($nodeId)
                ) == $nodeId
            ) {
                return ConnectionsActivity::createActivityNodeFromEntry($nodeEntry, $feed);
            }
        }
        return null;
    }

    /**
     *
     * Enter description here ...
     */
    public function listActivityNodes($activityId)
    {
        $feedLink = "/activities/service/atom2/activity?activityUuid=" . $activityId . "&ps=150"; //.AbstractConnectionsAPI::MAX_PAGE_SIZE;
        $this->getHttpClient()->setParameterGet("sortBy", 'title');
        $this->getHttpClient()->setParameterGet("sortOrder", 'asc');
        $this->getHttpClient()->resetParameters();
        $result = $this->requestForPath("GET", $feedLink);
        if (empty($result) || !$this->checkResult($result)) {
            return array();
        }
        $feed = IBMAtomFeed::loadFromString($result->getBody());

        $activityNodeEntries = $feed->getReplies();
        $activityNodes = array();
        foreach ($activityNodeEntries as $activityNodeEntry) {
            $activityNodes[] = ConnectionsActivity::createActivityNodeFromEntry($activityNodeEntry, $feed);
        }
        return $activityNodes;
    }

    /**
     *
     * Enter description here ...
     */
    public function getActivityHistory($activityId)
    {
        $link = "/activities/service/atom2/activity/history?activityUuid=" . $activityId;
        $this->getHttpClient()->resetParameters();
        $result = $this->requestForPath("GET", $link);
        if (empty($result) || !$this->checkResult($result)) {
            return array();
        }
        $historyFeed = IBMAtomFeed::loadFromString($result->getBody());
        $entries = $historyFeed->getEntries();
        $changeLog = array();
        for ($i = 0; ($i < count($entries)) && $i < 3; $i++) {
            $changeLog[] = new ConnectionsHistoryEntry($entries[$i]);
        }
        return $changeLog;
    }


    /**
     *
     * Description:
     * Method is responsible for creating an Activity using the
     * from a specified template uuid.
     *
     * Parmeters:
     * $communityId - Id of the community we wish to create the activity under.
     * $templateUuid - the uuid of the template. This template must already exist in
     * Connections
     */
    public function createActivityFromTemplate($communityId, $templateUuid, $activityName)
    {

        $nonce = $this->requestNonce();
        $client = $this->getHttpClient(true);
        $client->resetParameters();

        //$activityUrl = "/activities/service/html/post/activity";
        //$client->setRequestPath($activityUrl);

        $cvmHeaders = array(
            'Pragma' => 'WWW-Authenticate=XHR',
            'X-Requested-With' => 'XMLHttpRequest',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'X-Update-Nonce' => $nonce
        );

        $client->setHeaders($cvmHeaders);

        $client->setParameterPost("activityUuid", $templateUuid);
        $client->setParameterPost("pattern", "false");
        $client->setParameterPost("title", $activityName);
        $client->setParameterPost("startpage", "outline");
        $client->setParameterPost("assignToGroup", $templateUuid);

        $client->setParameterPost("commUuid", $communityId);
        $client->setParameterPost("explicit", "notifyAll");

        $response = $this->requestForPath('POST', '/activities/service/html/post/activity');

        // if( $response->getStatus() != 200) {
        //     throw new IBMConnectionsServiceException("Failed to Create the CVM Sellers Activity: "  . $response->getStatus(),
        //    	CONNECTIONS_TECH_GENERAL);
        // }
        return;
    }

    /**
     * Method to create a new activity
     * @param $name
     * @param $description
     * @param $type
     * @return mixed
     */
    public function createCommunityActivity($communityId, $title, $content = '', $tags = array(), $due_date = null)
    {

        $feed = $this->getRemoteAppFeed($communityId);
        if (empty($feed)) {
            return;
        }
        $path = "";
        //$entries = array();
        $entries = $feed->getEntries();
        for ($i = 0; $i < sizeof($entries); $i++) {
            $entry = $entries[$i];
            $retArray = $entry->getCategories("http://www.ibm.com/xmlns/prod/sn/type");
            for ($k = 0; $k < sizeof($retArray); $k++) {
                if (strcmp($retArray[$k]['term'], "Activities") == 0) {
                    $link = $entry->getLink("http://www.ibm.com/xmlns/prod/sn/remote-application/feed");
                    $path = $this->extractRelativeUrlPath($link['href']);
                }
            }
        }
        //echo "path1". $path;
        if (empty($path)) {
            $commApi = new CommunitiesAPI($this->getHttpClient());
            $commApi->activateActivities($communityId);
            $feed = $this->getRemoteAppFeed($communityId);
            if (empty($feed)) {
                return;
            }
            $path = "";
            $entries = array();
            $entries = $feed->getEntries();
            for ($i = 0; $i < sizeof($entries); $i++) {
                $entry = $entries[$i];
                $retArray = $entry->getCategories("http://www.ibm.com/xmlns/prod/sn/type");
                for ($k = 0; $k < sizeof($retArray); $k++) {
                    if (strcmp($retArray[$k]['term'], "Activities") == 0) {
                        $link = $entry->getLink("http://www.ibm.com/xmlns/prod/sn/remote-application/feed");
                        $path = $this->extractRelativeUrlPath($link['href']);
                    }
                }
            }
        }
        // echo "path2". $path;
        if (empty($path)) {
            return array('message' => 'widget_is_not_activated');
        }
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('activity', 'http://www.ibm.com/xmlns/prod/sn/type', 'Activity');
        $entry->addCategory('community_activity', 'http://www.ibm.com/xmlns/prod/sn/type', 'Community Activity');

        $entry->setTitle($title);
        $entry->setContent($content);

        if (!empty($due_date)) {
            $dueDateElt = $entry->getDom()->createElement('snx:duedate', $due_date);
            $entry->getEntryNode()->appendChild($dueDateElt);
        }
        foreach ($tags as $tag) {
            $entry->addTag($tag);
        }
        $this->httpClient = $this->getHttpClient();
        $this->httpClient->resetParameters();
        $this->httpClient->setParameterPost("commUuid", $communityId);
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
        return $response->getBody();
        //echo $response->getStatus();

    }

    public function createSection($activityId, $name)
    {
        $parent = $this->getActivity($activityId);
        $path = $this->extractRelativeUrlPath($parent->getCollectionLink());

        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('section', 'http://www.ibm.com/xmlns/prod/sn/type', 'Section');
        $entry->setTitle($name);
        $entry->setContent("");
        $this->httpClient = $this->getHttpClient();
        $this->httpClient->resetParameters();
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );

    }

    public function createEntry($activityId, $title, $description, $tags = array())
    {
        $parent = $this->getActivity($activityId);
        $path = $this->extractRelativeUrlPath($parent->getCollectionLink());

        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('entry', 'http://www.ibm.com/xmlns/prod/sn/type', 'Entry');
        $entry->setTitle($title);
        $entry->setContent($description);
        foreach ($tags as $tag) {
            if (empty($tag)) {
                continue;
            }
            $entry->addTag($tag);
        }
        $this->httpClient = $this->getHttpClient();
        $this->httpClient->resetParameters();
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
        return $response->getBody();

    }

    public function createSectionEntry($activityId, $sectionId, $title, $description, $tags = array())
    {
        $parent = $this->getActivity($activityId);
        $path = $this->extractRelativeUrlPath($parent->getCollectionLink());
        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('entry', 'http://www.ibm.com/xmlns/prod/sn/type', 'Entry');
        $entry->setTitle($title);
        $entry->setContent($description);
        foreach ($tags as $tag) {
            if (empty($tag)) {
                continue;
            }
            $entry->addTag($tag);
        }
        $elt = $entry->getDom()->createElement('thr:in-reply-to');
        $elt->setAttribute('ref', "urn:lsid:ibm.com:oa:" . $sectionId);
        $elt->setAttribute(
            'href',
            $this->url . "/activities/service/atom2/activitynode?activityNodeUuid=" . $sectionId
        );
        $elt->setAttribute('type', "application/atom+xml");
        $elt->setAttribute('source', "urn:lsid:ibm.com:oa:" . $sectionId);

        $entry->getEntryNode()->appendChild($elt);

        $this->httpClient = $this->getHttpClient();
        $this->httpClient->resetParameters();
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );

    }

    public function createToDo(
        $activityId,
        $title,
        $description,
        $due_date,
        $tags = array(),
        $assigned = array('name' => null, 'id' => null)
    ) {
        $parent = $this->getActivity($activityId);
        $path = $this->extractRelativeUrlPath($parent->getCollectionLink());

        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('todo', 'http://www.ibm.com/xmlns/prod/sn/type', 'To Do');
        $entry->setTitle($title);
        $entry->setContent($description);
        foreach ($tags as $tag) {
            if (empty($tag)) {
                continue;
            }
            $entry->addTag($tag);
        }
        if (!empty($due_date)) {
            $dueDateElt = $entry->getDom()->createElement('snx:duedate', $due_date);
            $entry->getEntryNode()->appendChild($dueDateElt);
        }
        //2008-04-23T04:00:00Z

        if (!empty($assigned) && !empty($assigned['id'])) {
            $assignedElt = $entry->getDom()->createElement('snx:assignedto');
            $assignedElt->setAttribute('name', $assigned['name']);
            $assignedElt->setAttribute('userid', $assigned['id']);
            $entry->getEntryNode()->appendChild($assignedElt);
        }
        //<snx:assignedto name="Max Jensen" userid="756647c0-a7a2-1031-94f8-a3db76a297cb"></snx:assignedto>

        $this->httpClient = $this->getHttpClient();
        $this->httpClient->resetParameters();
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );
        return $response->getBody();

    }

    public function createSectionToDo(
        $activityId,
        $sectionId,
        $title,
        $description,
        $due_date,
        $tags = array(),
        $assigned = array('name' => null, 'id' => null)
    ) {
        $parent = $this->getActivity($activityId);
        $path = $this->extractRelativeUrlPath($parent->getCollectionLink());

        $entry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $entry->addCategory('todo', 'http://www.ibm.com/xmlns/prod/sn/type', 'To Do');
        $entry->setTitle($title);
        $entry->setContent($description);
        foreach ($tags as $tag) {
            if (empty($tag)) {
                continue;
            }
            $entry->addTag($tag);
        }

        if (!empty($due_date)) {
            $dueDateElt = $entry->getDom()->createElement('snx:duedate', $due_date);;
            $entry->getEntryNode()->appendChild($dueDateElt);
        }
        //2008-04-23T04:00:00Z

        if (!empty($assigned) && !empty($assigned['id'])) {
            $assignedElt = $entry->getDom()->createElement('snx:assignedto');
            $assignedElt->setAttribute('name', $assigned['name']);
            $assignedElt->setAttribute('userid', $assigned['id']);
            $entry->getEntryNode()->appendChild($assignedElt);
        }

        $elt = $entry->getDom()->createElement('thr:in-reply-to');
        $elt->setAttribute('ref', "urn:lsid:ibm.com:oa:" . $sectionId);
        $elt->setAttribute(
            'href',
            $this->url . "/activities/service/atom2/activitynode?activityNodeUuid=" . $sectionId
        );
        $elt->setAttribute('type', "application/atom+xml");
        $elt->setAttribute('source', "urn:lsid:ibm.com:oa:" . $sectionId);

        $entry->getEntryNode()->appendChild($elt);

        $this->httpClient = $this->getHttpClient();
        $this->httpClient->resetParameters();
        $this->httpClient->setRawData($entry->getDomString());
        $response = $this->requestForPath(
            'POST',
            $path,
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US',
            )
        );

    }

    public function markToDoCompleted($id, $completed = true)
    {
        $this->httpClient = $this->getHttpClient();
        $newEntry = IBMEditableAtomEntry::createEmptyEditableEntry();
        $newEntry->addCategory('todo', 'http://www.ibm.com/xmlns/prod/sn/type', 'To Do');
        if ($completed) {
            $newEntry->addCategory('completed', 'http://www.ibm.com/xmlns/prod/sn/flags', 'Completed');
        }
        $this->httpClient->resetParameters();
        $this->httpClient->setRawData($newEntry->getDomString());
        $this->httpClient->setParameterPost("activityNodeUuid", $id);
        $result = $this->requestForPath(
            'PUT',
            "/activities/service/atom2/activitynode?activityNodeUuid={$id}",
            array(
                'Content-Type' => 'application/atom+xml',
                'Content-Language' => 'en-US'
            )
        );
        if (empty($result) || !$this->checkResult($result)) {
            return false;
        }
        switch ($result->getStatus()) {
            case '200':
            case '201':
                return true;
                break;
            default :
                return false;
        }
    }
}
