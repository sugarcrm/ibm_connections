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

require_once 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php';

class ibm_connectionsCommunity extends SugarBean
{

    public $module_dir = "ibm_connectionsCommunity";
    public $object_name = "ibm_connectionsCommunity";

    public function retrieve($id)
    {
        $helper = new ConnectionsHelper();
        $community = $helper->getCommunity($id);

        $this->id = $id;
        $this->name = $community->getTitle();
        $this->tags = $community->getTags();
        $this->description = $community->getDescription();
        $this->url = $community->getWebUrl();
        $this->logo = $community->getLogoLink();
        $this->access = $community->getCommunityType();

        return $this;
    }

    public function save()
    {
        if ($this->new_with_id) {
            $name = $this->name;
            $descr = $this->description;
            $members = $this->members;
            $access = $this->access;

            $helper = new ConnectionsHelper();
            $communityId = $helper->createCommunity($name, $access, $descr, "");

            if (is_array($members) && sizeof($members) > 0) {
                foreach ($members as $member) {
                    if (isset($member['id'])) {
                        $helper->addMember($communityId, $member['id'], $member['role']);
                    }
                }
            }
            $this->id = $communityId;
            return $communityId;
        } else {
            return $this->id;
        }
    }

    public function mark_deleted($id)
    {
        $helper = new ConnectionsHelper();
        $helper->deleteCommunity($id);
    }

}
