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
        if(empty($this->id) || !empty($this->new_with_id)) {
            $this->uploadFile();
        }
    }

    protected function uploadFile()
    {
        $helper = new ConnectionsHelper();
        $df = new DownloadFile();
        $fileInfo = $df->getFileInfo($this, 'filename');
        if (empty($this->name)){
            $this->name = $fileInfo['name'];
        }
        $id = $helper->uploadNewFile($this->community_id, $this->name, $fileInfo, 'private');

        require_once 'include/upload_file.php';
        $upload = new UploadFile('filename');
        $upload->unlink_file($this->id);

        $this->id = $id;

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
