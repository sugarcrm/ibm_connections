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
require_once 'modules/ibm_connectionsFiles/clients/base/api/ibm_connectionsFilesFilterApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsMembersFilterApi extends ibm_connectionsFilesFilterApi
{
    /**
     * @var ConnectionsHelper
     */
    private $helper;

    const MAX_PAGE_SIZE = 1500;

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
        $this->helper = new ConnectionsHelper();
        $this->checkLogin($this->helper);
        
        $bean = BeanFactory::newBean('ibm_connectionsMembers');

        if (empty($args['fields'])){
            $args['fields'] = implode(',', array_keys($bean->field_defs));
        }
        $options = $this->parseArguments($api, $args, $bean);
        $page = $this->pageNum($options['offset'], $options['limit']);

        $filter = $this->reformatFilter($args['filter']);

        if(empty($filter['community_id'])){
            throw new SugarApiException("ERROR_NO_COMMUNITY", null, null, 404);
        }

        $entries = $this->helper->getCommunityMemberArray(
            $filter['community_id'],
            $filter['name']['$starts'],
            $page,
            $options['limit']
        );


        if ( array_intersect(array('total_todos', 'completed_todos', 'completion'), $options['select'])){
            $entries['entries'] = $this->buildMembersTodosMap($filter['community_id'], $entries['entries']);
        }

        return $this->formatResult($api, $args, $entries, new ibm_connectionsMembers());

        return $data;
    }

    /**
     * @param $communityId
     * @param $entries array list members
     * @return array list members
     */
    protected function buildMembersTodosMap($communityId, $entries)
    {
        foreach ($entries as $k => $entry) {
            $todos = $this->helper->getCommunityTodos($communityId, $entry['id']);
            $completedTodos = 0;
            $totalTodos = count($todos);
            foreach($todos as $todo){
                if ($todo['completed']){
                    $completedTodos++;
                }
            }
            $entries[$k]['total_todos'] = $totalTodos;
            $entries[$k]['completed_todos'] = $completedTodos;
            $entries[$k]['completion'] = ($totalTodos > 0) ? round($completedTodos / $totalTodos * 100) : 0;
        }

        return $entries;
    }

} 
