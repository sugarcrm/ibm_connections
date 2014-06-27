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


//require_once 'custom/include/IBMConnections/Services/CommunitiesIntegration.php';
require_once 'custom/include/IBMConnections/Models/ConnectionsModel.php';
/**
 *
 * Enter description here ...
 * @author mario
 *
 */
class ConnectionsActivity extends ConnectionsModel
{
    /**
     *
     * Enter description here ...
     * @param unknown_type $httpClient
     * @param IBMAtomEntry $atomEntry
     */

    protected $totalToDos = null;
    protected $completedToDos = null;

    function __construct(IBMAbstractAtomItem $atom)
    {
        parent::__construct($atom);
    }


    protected function serviceBaseUrl()
    {
        return "/activities/service/atom2/";
    }

    /**
     *
     * Enter description here ...
     */
    public function getTitle()
    {
        return $this->atom->getTitle();
    }

    /**
     *
     * Enter description here ...
     */
    public function getId()
    {
        $origId = $this->atom->getId();
        $idx = strpos($origId, "urn:lsid:ibm.com:oa:");
        $size = strlen("urn:lsid:ibm.com:oa:");
        return substr($this->atom->getId(), $idx + $size, strlen($origId) - ($idx + $size));
    }


    /**
     *
     * Enter description here ...
     */
    public function getLinkAlt()
    {
        //The line below returns a link to the recent updates entries of the activity
        // use 	getWebEditUrl() if you need the proper web url of the activity
        $link = $this->atom->getLink(IBMAtomConstants::ATTR_LINK_ALT, IBMAtomConstants::ATTR_LINK_TYPE_HTML);
        return $link['href'];
    }


    /**
     *
     * Enter description here ...
     */
    public function getWebEditUrl()
    {
        return $this->connectionsBaseUrl() . "/activities/service/html/mainpage#activitypage," . $this->getId();
    }

    /**
     *
     * Enter description here ...
     */
    public function getFormattedUpdatedDate()
    {
        return $this->formatTimeDate($this->atom->getUpdatedDate());
    }

    /**
     *
     * Enter description here ...
     */
    public function getContributor()
    {
        $contributor = $this->atom->getContributor();
        //	$contributor = CommunitiesIntegration::toSugarProfile($contributor);
        return $contributor;
    }

    /**
     *
     * Enter description here ...
     */
    public function getTags()
    {
        $tags = $this->atom->getTags();
        if (!$tags) {
            return '';
        }

        $stringOfTags = '';
        for ($i = 0; $i < count($tags); $i++) {
            $stringOfTags = $stringOfTags . $tags[$i] . ' ';
        }
        $stringOfTags = trim($stringOfTags);
        $stringOfTags = str_replace(' ', ', ', $stringOfTags);

        return $stringOfTags;
    }

    /**
     *
     * Enter description here ...
     */
    public function getSummary()
    {
        return $this->atom->getSummary();
    }

    /**
     *
     * Enter description here ...
     */
    public function getContent()
    {
        return $this->atom->getContent();
    }

    public function getCollectionLink()
    {
        $link = $this->atom->xquery("./app:collection"); //$this->atom->xquery("./td:type");
        return $link->item(0)->attributes->getNamedItem("href")->textContent;
        //return htmlspecialchars_decode($link->item(0)->attributes->getNamedItem("href")->textContent);
    }

    /**
     *
     * Enter description here ...
     */
    public function getExtendedContentUrl()
    {
        $link = $this->atom->getLink("enclosure");
        return $link['href'];
    }

    /**
     *
     * Enter description here ...
     */
    public function getPriority()
    {
        return $this->atom->getCategory(ConnectionsConstants::CATEGORY_SCHEME_PRIORITY);
    }

    /**
     *
     * Enter description here ...
     */
    public function getDueDate()
    {
        $dueDate = $this->atom->getNodeContent("./snx:duedate");
        if ($dueDate) {
            $dueDate = strtotime($dueDate);
            return $this->formatSugarDate($dueDate);
        }
        return null;
    }

    public function getCommentsCount()
    {
        $entries = $this->atom->getEntries();
        $res = 0;
        foreach ($entries as $entry) {
            $type = $entry->getCategory(ConnectionsConstants::CATEGORY_SCHEME_TYPE);
            if ($type['term'] == 'reply') {
                $res++;
            }
        }
        return $res;
    }

    public function getCompletion()
    {
        $entries = $this->atom->getEntries();
        $this->totalToDos = 0;
        $this->completedToDos = 0;
        foreach ($entries as $entry) {
            $type = $entry->getCategory(ConnectionsConstants::CATEGORY_SCHEME_TYPE);
            if ($type['term'] == 'todo') {
                $this->totalToDos++;
            }
            $type = $entry->getCategory(ConnectionsConstants::CATEGORY_SCHEME_FLAGS);
            if ($type['term'] == 'completed') {
                $this->completedToDos++;
            }
        }
        if ($this->totalToDos == 0) {
            return 0;
        }
        return round($this->completedToDos / $this->totalToDos * 100);
    }

    public function getToDosCount()
    {
        if (is_null($this->totalToDos) || is_null($this->completedToDos)) {
            $this->getCompletion();
        }
        return array(
            'total' => $this->totalToDos,
            'completed' => $this->completedToDos
        );
    }

    public function isCompleted()
    {
        $type = $this->atom->getCategory(ConnectionsConstants::CATEGORY_SCHEME_FLAGS);
        //return print_r('1'.$type,true);
        return (!empty($type) && $type['term'] == 'completed') ? true : false;

    }

    /**
     *
     * Enter description here ...
     */
    public static function createActivityNodeFromEntry(IBMAtomEntry $atomEntry, IBMAtomFeed $parentFeed)
    {
        $type = $atomEntry->getCategory(ConnectionsConstants::CATEGORY_SCHEME_TYPE);
        //$GLOBALS['log']->fatal("Unrecognized Activity node type: " . $type['term']);
        switch ($type['term']) {
            case "section":
                return new ConnectionsActivitySection($atomEntry, $parentFeed);
                break;
            case "todo":
                return new ConnectionsActivityTodo($atomEntry, $parentFeed);
                break;
            case "entry":
                return new ConnectionsActivityEntry($atomEntry, $parentFeed);
                break;
            case "reply":
                return new ConnectionsActivityReply($atomEntry, $parentFeed);
                break;
            default :
                $GLOBALS['log']->fatal("Unrecognized Activity node type: " . $type['term']);
        }
    }

    public function getAtom()
    {
        return $this->atom;
    }
}
