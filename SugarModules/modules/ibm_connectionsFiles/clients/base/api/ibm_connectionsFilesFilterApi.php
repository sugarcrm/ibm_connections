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
require_once 'clients/base/api/FilterApi.php';
require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsFilesFilterApi extends FilterApi
{

    const LIST_MAX_ENTRIES_PER_PAGE = 5;

    public function registerApiRest()
    {
        return array(
            'filterList' => array(
                'reqType' => 'GET',
                'path' => array('ibm_connectionsFiles', 'filter'), //, 'filter'
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
        $filter = $this->reformatFilter($args['filter']);
        $options = $this->parseArguments($api, $args);
        $page = $this->pageNum($options['offset'], $options['limit']);
        $entries = $helper->getFilesList($filter['community_id'], '', $page, $options['limit']);

        return $this->formatResult($api, $args, $entries, 'ibm_connectionsFiles');
    }

    protected function formatResult($api, $args, $entries, $recordClass)
    {
        $options = $this->parseArguments($api, $args);

        $data = array(
            'next_offset' => -1,
            'records' => array()
        );

        if ($entries['total'] > $options['offset'] + $options['limit']) {
            $data['next_offset'] = $options['offset'] + $options['limit'];
        }

        if (!empty($entries['entries'])) {
            foreach ($entries['entries'] as $entry) {
                $bean = new $recordClass();
                $bean->fromArray($entry);
                $data['records'][] = $bean;
            }
        }

        $data['records'] = $this->formatBeans($api, $args, $data['records']);

        return $data;
    }

    protected function pageNum($offset, $limit)
    {
        $limit = empty($limit) ? self::LIST_MAX_ENTRIES_PER_PAGE : $limit;
        return floor($offset / $limit) + 1;
    }

    protected function reformatFilter($filter)
    {
        $out = array();
        foreach ($filter AS $condition) {
            $keys = array_keys($condition);
            $vals = array_values($condition);
            $out[$keys[0]] = $vals[0];
        }
        return $out;
    }

} 
