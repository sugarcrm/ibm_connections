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
class ibm_connectionsCommunity extends SugarBean
{

    public $module_dir = "ibm_connectionsCommunity";
    public $object_name = "ibm_connectionsCommunity";


    function fetchFromQuery(SugarQuery $query, array $fields = array(), array $options = array())
    {
        $beans = array();
        $beans[0] = new ibm_connectionsCommunity();
        $beans[0]->name = 'ft2 Account';
        $beans[0]->id = '39f2ab0a-9370-4269-bc5a-86301a8221ce';

        $beans[1] = new ibm_connectionsCommunity();
        $beans[1]->name = 'ft1 Community';
        $beans[1]->id = '8d07e618-75c6-45d0-acab-c9b21fa9356e';
        
        return $beans;
    }
    
    
} 
