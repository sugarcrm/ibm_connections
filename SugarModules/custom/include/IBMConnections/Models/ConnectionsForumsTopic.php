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


require_once 'custom/include/IBMConnections/Models/ConnectionsModel.php';

/**
 *
 * Enter description here ...
 * @author Mario Casciaro
 *
 */
class ConnectionsForumsTopic extends ConnectionsModel
{
    /**
     *
     * Enter description here ...
     * @param unknown_type $httpClient
     * @param IBMAtomEntry $atomEntry
     */
    function __construct(IBMAbstractAtomItem $atom)
    {
        parent::__construct($atom);
    }


    protected function serviceBaseUrl() {
        return "/forums/atom/topics";
    }

    /**
     *
     * Enter description here ...
     */
    public function getTitle() {
        return $this->atom->getTitle();
    }
  public function getId() {    
         
         $origId = $this->atom->getId();
		$idx = strpos( $origId , "urn:lsid:ibm.com:forum");
		$size = strlen("urn:lsid:ibm.com:forum");
		return str_replace( "urn:lsid:ibm.com:forum:", '',$origId);//substr($this->atom->getId(), $idx + $size + 1, strlen($origId) - ($idx + $size));
    }
     
    /**
     *
     * Enter description here ...
     */
    public function getAuthor() {
         
        $person = $this->atom->getAuthor();
       // $person = CommunitiesIntegration::toSugarProfile($person);
        return $person;
    }


    /**
     *
     * Enter description here ...
     */
    public function getUpdatedDate() {
       // $updatedDate = $this->formatTimeDate($this->atom->getUpdatedDate());
        return $this->formatTimeDate($this->atom->getUpdatedDate());
    }

    /**
     *
     * Retrieve the name of the person who last contributed to the Topic.
     * Note that if this person is the orivginal author, there ia no 'contributor'
     * element present in the feed.
     */
    public function getLastPostBy() {

        $person = $this->atom->getContributor();
        if(!is_null($person)) {
           // $person = CommunitiesIntegration::toSugarProfile($person);
        } else {
            $person = $this->atom->getAuthor();
          //  $person = CommunitiesIntegration::toSugarProfile($person);
        }
        return $person;
    }


    /**
     *
     * Enter description here ...
     */
    public function getLinkAlt() {
        return $this->atom->getLink(IBMAtomConstants::ATTR_LINK_ALT, IBMAtomConstants::ATTR_LINK_TYPE_HTML);
    }
    
    public function getTopicType() {
		$categories = $this->atom->getCategories(ConnectionsConstants::CATEGORY_SCHEME_FLAGS);
		$terms = array('question' => 0, 'answered' => 0);
		foreach($categories as $cat)
		{
			$terms[$cat['term']] = 1;
		}
		if ($terms['answered'] && $terms['question']) return "answered";
			else if ($terms['question']) return "question";
				else return "topic";
	}

	public function getTags() {
		$tags = $this->atom->getTags();
		if(!$tags) {
			return '';
		}
		
		$stringOfTags ='';
		for($i=0;$i < count($tags);$i++){
			$stringOfTags = $stringOfTags.$tags[$i].' ';
		}
		return $stringOfTags;
	}
    /*
     * Method which returns the reply count from a link
     *  with rel="replies"
     */
    public function getReplies(){
        $nodeList = $this->atom->xquery("./default:link[contains(@rel,'replies')]");
         
        $retArray = array();
        $replies = 0;
        for($j = 0; $j < $nodeList->length; $j++) {
            $retArray[$j] = array();
            $node = $nodeList->item($j);
            for($i = 0; $i < $node->attributes->length; $i++) {
                $retArray[$j][$node->attributes->item($i)->localName] = $node->attributes->item($i)->textContent;
                if (strcmp($node->attributes->item($i)->localName, "count") == 0) {
                    $replies = $node->attributes->item($i)->textContent;
                }
            }
        }
        return $retArray[0];

    }
    
        /*
     * Method which returns the reply count from a link
     *  with rel="replies"
     */
    public function getRepliesCount(){
        $cnt = $this->getReplies();
        return $cnt['count'];

    }
    
    

    /**
     *
     * Enter description here ...
     */
    public function getLink() {

        $nodeList = $this->atom->xquery("./default:link[contains(@rel,'alternate')]");
        $retArray = array();
         
        //link href="https://devconnections.rtp.raleigh.ibm.com:9444/forums/html/topic?id=3a875aaf-cb17-47ec-8784-e6509aaf17d5" rel="alternate" type="text/html"></link>
         
        for($i = 0; $i < $nodeList->length; $i++){
            if($nodeList->item($i)->attributes->getNamedItem("rel") != null &&
            $nodeList->item($i)->attributes->getNamedItem("type") != null){
                if(strcmp($nodeList->item($i)->attributes->getNamedItem("rel")->textContent,"alternate") &&
                strcmp($nodeList->item($i)->attributes->getNamedItem("type")->textContent,"text/html"))
                return $nodeList->item($i)->attributes->getNamedItem("href")->textContent;
            }
        }

        return null;
    }
    
    public function getAttachments() {
		$retArray = array();
		$fields = $this->atom->xquery('./snx:field[@type="file"]');
		for($i = 0; $i < $fields->length; $i++) {
			$enclosure = $this->atom->xquery("./default:link", $fields->item($i));
			if($enclosure->length <= 0) {
				continue;
			}
			$enclosure = $enclosure->item(0);
			$file = array();
			$file['id'] = $fields->item($i)->attributes->getNamedItem('fid')->textContent;
			$file['title'] = $fields->item($i)->attributes->getNamedItem('name')->textContent;
			$file['href'] = $enclosure->attributes->getNamedItem('href')->textContent;
			$file['length'] = $enclosure->attributes->getNamedItem('length')->textContent;
			$file['size'] = $enclosure->attributes->getNamedItem('size')->textContent;
			
			$pos = strripos($file['href'], "/");
			//$file["fileName"] = substr($file['href'], $pos + 1, strlen($file['href']) - ($pos + 1));
			//$file["fileName"] = urldecode($file["fileName"]);
			
			$pos = strripos($file['href'], ".");
			$file["fileExtension"] = substr($file['href'], $pos + 1, strlen($file['href']) - ($pos + 1));
			
			$retArray[] = $file;
		}
		return $retArray;
	}


}
