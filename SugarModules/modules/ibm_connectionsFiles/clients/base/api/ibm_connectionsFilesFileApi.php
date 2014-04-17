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

require_once 'modules/ibm_connectionsFiles/ibm_connectionsFiles.php';
require_once 'clients/base/api/FileApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsFilesFileApi extends FileApi
{

    public function registerApiRest()
    {
        return array(
            'getFileContents' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsFiles', '?', 'file'),
                'pathVars' => array('', 'record'),
                'method' => 'getFile',
                'rawReply' => true,
                'allowDownloadCookie' => true,
                'shortHelp' => 'Gets the contents of a single file from IBM Connections',
                'longHelp' => '',
            ),
        );
    }

    /**
     * Gets a single file for rendering
     *
     * @param ServiceBase $api The service base
     * @param array $args Arguments array built by the service base
     * @return string
     * @throws SugarApiExceptionMissingParameter|SugarApiExceptionNotFound
     */
    public function getFile($api, $args)
    {
        // Get the field
        if (empty($args['record'])) {
            // @TODO Localize this exception message
            throw new SugarApiExceptionMissingParameter('Record id is missing');
        }

        $id = $args['record'];
        $bean = new ibm_connectionsFiles();
        $bean->retrieve($id);

        $filename = $bean->documentFilename;

        require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
        $connectionsApi = new ExtAPIConnections();
        $eapmBean = EAPM::getLoginInfo('Connections');
        if (!empty($eapmBean)) {
            $connectionsApi->loadEAPM($eapmBean);
        }

        $fileContent = $connectionsApi->downloadFile($id);
        $filesize = strlen($fileContent);

        header("Pragma: public");
        header("Cache-Control: maxage=1, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
        header("Content-Length: " . strlen($fileContent));
        header("X-Content-Type-Options: nosniff");
        header("Expires: 0");
        set_time_limit(0);
        //ob_clean();
        ob_start();
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
        echo $fileContent;
        @ob_end_flush();
        exit();
    }


}
