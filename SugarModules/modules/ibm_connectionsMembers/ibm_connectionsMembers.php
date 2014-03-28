<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

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




class ibm_connectionsMembers extends SugarBean
{

    public $module_dir = "ibm_connectionsMembers";
    public $object_name = "ibm_connectionsMembers";


    function fetchFromQuery(SugarQuery $query, array $fields = array(), array $options = array())
    {
        $beans = array();
        $beans[0] = new ibm_connectionsMembers();
        $beans[0]->name = 'Alexander Razumenko';
        $beans[0]->id = '0bb7a4c0-4523-1033-8f73-df1a4e1805dc';
        $beans[0]->role = 'member';
        $beans[0]->picture = 'http://fox.local/img/1.png';
        $beans[0]->url = 'http://fox.local/img/1.png';

        $beans[1] = new ibm_connectionsMembers();
        $beans[1]->name = 'Andrii Fedyk';
        $beans[1]->id = '79ae9cc0-4520-1033-8f60-df1a4e1805dc';
        $beans[1]->role = 'owner';
        $beans[1]->picture = 'http://fox.local/img/2.png';
        $beans[1]->url = 'http://fox.local/img/2.png';

        return $beans;
    }

} 
