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

require_once 'modules/ibm_connectionsMembers/ibm_connectionsMembers.php';
require_once 'clients/base/api/FilterApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsMembersFilterApi extends FilterApi
{

    public function registerApiRest()
    {
        return array(
            'filterList' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsMembers', 'filter'), //, 'filter'
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

        $helper = new ConnectionsHelper();

        $returnData = $helper->getCommunityMemberArray($args['filter'][0]['community_id']);

        $beans = array();
        foreach ($returnData as $key => $item) {
            $beans[$key] = new ibm_connectionsMembers();
            $beans[$key]->id = $item['member_id'];
            $beans[$key]->community_id = $args['filter'][0]['community_id'];
            $beans[$key]->name = $item['member_name'];
            $beans[$key]->role = $item['member_role'];
            $beans[$key]->picture = 'https://greenhouse.lotus.com//profiles/photo.do?userid=' . $item['member_id'];
            $beans[$key]->url = 'https://greenhouse.lotus.com/profiles/html/profileView.do?userid=' . $item['member_id'];
        }

        $data = array(
            'next_offset' => -1,
            'records' => $this->formatBeans($api, $args, $beans)
        );

        return $data;
    }


} 
