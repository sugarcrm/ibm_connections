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

require_once 'modules/ibm_connectionsCommunity/ibm_connectionsCommunity.php';
require_once 'clients/base/api/FilterApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsCommunityFilterApi extends FilterApi
{

    public function registerApiRest()
    {
        return array(
            'communitiesList' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsCommunity'), //, 'filter'
                'pathVars' => array(''),
                'method' => 'communitiesList',
                'jsonParams' => array(),
                'shortHelp' => 'Filter records from a single module',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastsFilter.html',
            ),
            'filterList' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsCommunity', 'filter'), //, 'filter'
                'pathVars' => array(''),
                'method' => 'communitiesList',
                'jsonParams' => array('filter'),
                'shortHelp' => 'Filter records from a single module',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastsFilter.html',
            )
        );
    }

    public function communitiesList(ServiceBase $api, array $args)
    {

        $helper = new ConnectionsHelper();
        $returnData = $helper->getCommunityList('MyCommunities', 1, '');

        $beans = array();
        foreach ($returnData as $key => $item) {
            $beans[$key] = new ibm_connectionsCommunity();
            $beans[$key]->id = $item['id'];
            $beans[$key]->name = $item['name'];
            $beans[$key]->members = $item['members_list'];
            $beans[$key]->files = $item['files_list'];
            $beans[$key]->activities = $item['activities_list'];
        }

        $data = array(
            'next_offset' => -1,
            'records' => $this->formatBeans($api, $args, $beans)
        );

        return $data;
    }

    public function filterList(ServiceBase $api, array $args)
    {
        $helper = new ConnectionsHelper();
        $returnData = $helper->getCommunityList('MyCommunities', 1, '');

        //return $returnData;


        $beans = array();
        foreach ($returnData as $item) {
            $bean = new ibm_connectionsCommunity();
            $item['members'] = $item['members_list'];
            $item['files'] = $item['files_list'];
            $item['activities'] = $item['activities_list'];
            $bean->fromArray($item);

            $beans[] = $bean;
        }

        $data = array(
            'next_offset' => -1,
            'records' => $this->formatBeans($api, $args, $beans)
        );

        return $data;
    }


} 
