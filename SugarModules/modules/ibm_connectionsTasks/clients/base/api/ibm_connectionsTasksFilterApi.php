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

require_once 'modules/ibm_connectionsTasks/ibm_connectionsTasks.php';
require_once 'clients/base/api/FilterApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsTasksFilterApi extends FilterApi
{

    private $communityId;

    public function registerApiRest()
    {
        return array(
            'filterList' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsTasks', 'filter'), //, 'filter'
                'pathVars' => array(''),
                'method' => 'filterList',
                'jsonParams' => array('filter'),
                'shortHelp' => 'Filter records from a single module',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastsFilter.html',
            )
        );
    }

    public function filterList(ServiceBase $api, array $args)
    {
        $this->communityId = $args['filter'][0]['community_id'];
        $helper = new ConnectionsHelper();

        $returnData = $helper->getActivitiesList($this->communityId);
        //$returnData = $helper->getActivity('f27a4ecc-e981-436e-be75-7d85f96fb4e6');

        $beans = array();
        foreach ($returnData as $item) {
            $beans[] = $this->buildBean($item);
        }

        $data = array(
            'next_offset' => -1,
            'records' => $this->formatBeans($api, $args, $beans)
        );

        return $data;
    }

    private function buildBean($data)
    {
        $data['name'] = $data['title'];
        $data['community_id'] = $this->communityId;
        $data['contributor_id'] = $data['contributor']['id'];
        $data['contributor_name'] = $data['contributor']['name'];
        $data['contributor_status'] = $data['contributor']['status'];
        $data['contributor_email'] = $data['contributor']['email'];
        $data['url'] = $data['webEditUrl'];

        $bean = new ibm_connectionsTasks();
        $bean->fromArray($data);
        return $bean;
    }



} 
