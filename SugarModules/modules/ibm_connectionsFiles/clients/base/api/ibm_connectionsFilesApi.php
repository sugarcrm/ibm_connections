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

require_once 'clients/base/api/ModuleApi.php';

class ibm_connectionsFilesApi extends ModuleApi 
{
    public function registerApiRest() {
        return array(
            'create' => array(
                'reqType' => 'POST',
                'path' => array('ibm_connectionsFiles'),
//                'pathVars' => array('module'),
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a new record of the specified type',
                'longHelp' => 'include/api/help/module_post_help.html',
            ),
        );
    }

    /**
     * @param $api
     * @param $args
     * @return array
     */
    public function createRecord($api, $args){
        
        
        $fileField = 'filename';
        $prefix = empty($args['prefix']) ? '' : $args['prefix'];
        $filesIndex = $prefix . $fileField;


        if (!empty($_FILES[$filesIndex]) && empty($_FILES[$filesIndex . '_file'])) {
            $_FILES[$filesIndex . '_file'] = $_FILES[$filesIndex];
            unset($_FILES[$filesIndex]);
        }        
            
        
        
        $api->action = 'save';
//        $this->requireArgs($args,array('module'));

        $bean = BeanFactory::newBean('ibm_connectionsFiles');

        $def = $bean->field_defs[$fileField];
        require_once 'include/SugarFields/SugarFieldHandler.php';
        $sfh = new SugarFieldHandler();
        $sf = $sfh->getSugarField($def['type']);


        $sf->save($bean, $args, $fileField, $def, $prefix);
        
        

        $id = $this->updateBean($bean, $api, $args);

        $args['record'] = $id;
        $args['module'] = $bean->module_name;

        return $this->getLoadedAndFormattedBean($api, $args, $bean);
    }
} 
