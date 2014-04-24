<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2014 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once 'include/download_file.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsFiles extends SugarBean
{
    public $module_dir = "ibm_connectionsFiles";
    public $object_name = "ibm_connectionsFiles";

    function fetchFromQuery(SugarQuery $query, array $fields = array(), array $options = array())
    {
        $beans = array();
        $beans[0] = new ibm_connectionsFiles();
        $beans[0]->id = '48de826e-2d01-491e-9c8f-140520aca72b';
        $beans[0]->name = '1.png';
        $beans[0]->url = 'https://greenhouse.lotus.com/communities/service/html/communityview?communityUuid=39f2ab0a-9370-4269-bc5a-86301a8221ce#fullpageWidgetId=W27eb67932aff_4f39_b053_4f9d5598c0d6&file=48de826e-2d01-491e-9c8f-140520aca72b';
        $beans[0]->picture = 'http://ult.sc.loc/custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/file_types/img.PNG';

        $beans[1] = new ibm_connectionsFiles();
        $beans[1]->id = '947e6dcd-787a-45dd-8092-c8138baaeec4';
        $beans[1]->name = '2.png ';
        $beans[1]->url = 'https://greenhouse.lotus.com/communities/service/html/communityview?communityUuid=39f2ab0a-9370-4269-bc5a-86301a8221ce#fullpageWidgetId=W27eb67932aff_4f39_b053_4f9d5598c0d6&file=947e6dcd-787a-45dd-8092-c8138baaeec4';
        $beans[1]->picture = 'http://ult.sc.loc/custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/file_types/img.PNG';

        return $beans;
    }

    public function retrieve($id)
    {
        $this->id = $id;
        require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
        $connectionsApi = new ExtAPIConnections();
        $eapmBean = EAPM::getLoginInfo('Connections');
        if (!empty($eapmBean)) {
            $connectionsApi->loadEAPM($eapmBean);
        }

        $fileData = $connectionsApi->getFileEntry($id);

        if (!is_null($fileData)) {
            $this->name = $fileData->getTitle();
            $this->community_id = $fileData->getCommunityId();
            $this->content_type = $fileData->getMimeType();
        }
        return $this;
    }

    public function save()
    {
        $helper = new ConnectionsHelper();

        $fileInfo = array(
            'path' => dirname(__FILE__).'/1.txt',
            'content-type' => 'application/octet-stream',
        );

        if (!empty($this->filename)) {
            $df = new DownloadFile();
            $fileInfo = $df->getFileInfo($this, 'filename');
            if (empty($this->community_id) && isset($_POST['community_id'])){
                $this->community_id = $_POST['community_id'];
            }
            if (empty($this->name)){
                $this->name = $fileInfo['name'];
            }
            $helper->uploadNewFile($this->community_id, $this->name, $fileInfo, 'private');
        }else{
            $this->id = $helper->uploadNewFile($this->community_id, $this->name, $fileInfo, 'private');
        }
    }

    public function mark_deleted($id)
    {
        require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
        $connectionsApi = new ExtAPIConnections();
        $eapmBean = EAPM::getLoginInfo('Connections');
        if (!empty($eapmBean)) {
            $connectionsApi->loadEAPM($eapmBean);
        }

        $connectionsApi->deleteFile($id);

    }

} 
