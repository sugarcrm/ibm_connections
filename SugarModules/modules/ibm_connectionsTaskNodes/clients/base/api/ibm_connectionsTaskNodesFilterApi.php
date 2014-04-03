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

require_once 'modules/ibm_connectionsTaskNodes/ibm_connectionsTaskNodes.php';
require_once 'clients/base/api/FilterApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsTaskNodesFilterApi extends FilterApi
{
    const NAME_SEPARATOR = ' :: ';

    public function registerApiRest()
    {
        return array(
            'filterList' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsTaskNodes', 'filter'), //, 'filter'
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
        //$beans = $this->buildBeansList('3e9fa159-5752-4a95-a9ba-bf34065b9f70'); //root
        //$beans = $this->buildBeansList('206fadff-9778-4dba-b589-a6f90d340f6c'); //section
        $beans = $this->buildBeansList($args['filter'][0]['activity_id']);

        $data = array(
            'next_offset' => -1,
            'records' => $this->formatBeans($api, $args, $beans)
        );
        return $data;
    }

    private function buildBeansList($id)
    {
        $helper = new ConnectionsHelper();
        $returnData = $helper->getActivity($id);

        $beans = array();
        foreach ($returnData as $item) {
            $beans[] = $this->buildBean($item);
            if (is_array($item['content']) && sizeof($item['content']) > 0) {
                foreach ($item['content'] as $subItem) {
                    $subItem['title'] = $item['title'] . self::NAME_SEPARATOR . $subItem['title'];
                    $beans[] = $this->buildBean($subItem);
                }
            }
        }
        return $beans;
    }

    private function buildBean($data)
    {
        $data['name'] = $data['title'];
        $data['contributor_id'] = $data['contributor']['id'];
        $data['contributor_name'] = $data['contributor']['name'];
        $data['contributor_status'] = $data['contributor']['status'];
        $data['contributor_email'] = $data['contributor']['email'];
        $data['url'] = $data['webEditUrl'];

        $bean = new ibm_connectionsTaskNodes();
        $bean->fromArray($data);
        return $bean;
    }

}
