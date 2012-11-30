<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
 
$connectors = array (
  'ext_rest_linkedin' => 
  array (
    'id' => 'ext_rest_linkedin',
    'name' => 'LinkedIn&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/rest/linkedin',
    'eapm' => false,
    'modules' => 
    array (
      0 => 'Accounts',
      1 => 'Contacts',
      2 => 'Leads',
      3 => 'Prospects',
    ),
  ),
  'ext_soap_hoovers' => 
  array (
    'id' => 'ext_soap_hoovers',
    'name' => 'Hoovers&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/soap/hoovers',
    'eapm' => false,
    'modules' => 
    array (
      0 => 'Leads',
      1 => 'Accounts',
      2 => 'Contacts',
    ),
  ),
  'ext_rest_zoominfocompany' => 
  array (
    'id' => 'ext_rest_zoominfocompany',
    'name' => 'Zoominfo&#169; - Company',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/rest/zoominfocompany',
    'eapm' => false,
    'modules' => 
    array (
      0 => 'Leads',
      1 => 'Accounts',
      2 => 'Contacts',
    ),
  ),
  'ext_rest_zoominfoperson' => 
  array (
    'id' => 'ext_rest_zoominfoperson',
    'name' => 'Zoominfo&#169; - Person',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/rest/zoominfoperson',
    'eapm' => false,
    'modules' => 
    array (
      0 => 'Leads',
      1 => 'Accounts',
      2 => 'Contacts',
    ),
  ),
  'ext_rest_twitter' => 
  array (
    'id' => 'ext_rest_twitter',
    'name' => 'Twitter&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/rest/twitter',
    'eapm' => 
    array (
      'enabled' => true,
    ),
    'modules' => 
    array (
      0 => 'Accounts',
      1 => 'Contacts',
      2 => 'Leads',
      3 => 'Prospects',
    ),
  ),
  'ext_eapm_facebook' => 
  array (
    'id' => 'ext_eapm_facebook',
    'name' => 'Facebook&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/eapm/facebook',
    'eapm' => 
    array (
      'enabled' => true,
      'only' => true,
    ),
    'modules' => 
    array (
    ),
  ),
  'ext_eapm_google' => 
  array (
    'id' => 'ext_eapm_google',
    'name' => 'Google&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/eapm/google',
    'eapm' => 
    array (
      'enabled' => true,
      'only' => true,
    ),
    'modules' => 
    array (
    ),
  ),
  'ext_eapm_gotomeeting' => 
  array (
    'id' => 'ext_eapm_gotomeeting',
    'name' => 'GoToMeeting&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/eapm/gotomeeting',
    'eapm' => 
    array (
      'enabled' => true,
      'only' => true,
    ),
    'modules' => 
    array (
    ),
  ),
  'ext_eapm_lotuslive' => 
  array (
    'id' => 'ext_eapm_lotuslive',
    'name' => 'IBM LotusLive&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/eapm/lotuslive',
    'eapm' => 
    array (
      'enabled' => true,
      'only' => true,
    ),
    'modules' => 
    array (
    ),
  ),
  'ext_eapm_webex' => 
  array (
    'id' => 'ext_eapm_webex',
    'name' => 'WebEx&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/eapm/webex',
    'eapm' => 
    array (
      'enabled' => true,
      'only' => true,
    ),
    'modules' => 
    array (
    ),
  ),
  'ext_rest_insideview' => 
  array (
    'id' => 'ext_rest_insideview',
    'name' => 'InsideView&#169;',
    'enabled' => true,
    'directory' => 'modules/Connectors/connectors/sources/ext/rest/insideview',
    'eapm' => false,
    'modules' => 
    array (
    ),
  ),
  'ext_eapm_connections' => 
  array (
    'id' => 'ext_eapm_connections',
    'name' => 'IBM Connections&#169;',
    'enabled' => true,
    'directory' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections',
    'eapm' => 
    array (
      'enabled' => true,
      'only' => false,
    ),
    'modules' => 
    array (
    ),
  ),
);