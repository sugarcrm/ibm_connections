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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


$manifest = array (
  0 => 
  array (
    'acceptable_sugar_versions' => 
    array (
      0 => '7.0.0',
    ),
  ),
  1 => 
  array (
    'acceptable_sugar_flavors' => 
    array (
      0 => 'PRO',
      1 => 'CORP',
      2 => 'ENT',
      3 => 'ULT',
    ),
  ),
  'readme' => '',
  'key' => 'ibm',
  'description' => '',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'IBM Connections',
  'published_date' => '2012-07-02 20:00:00',
  'type' => 'module',
  'version' => '2.2.0-OpenSocial',
  'remove_tables' => 'prompt',
);


$installdefs = array (
  'id' => 'IBM',
  'beans' => 
  array (
    array (
      'module' => 'ibm_connections',
      'class' => 'ibm_connections',
      'path' => 'modules/ibm_connections/ibm_connections.php',
      'tab' => false,
    ),
      array (
      'module' => 'ibm_connectionsCommunity',
      'class' => 'ibm_connectionsCommunity',
      'path' => 'modules/ibm_connectionsCommunity/ibm_connectionsCommunity.php',
      'tab' => false,
    ),
  ),
  'layoutdefs' => 
  array (
  	array (
      'from' => '<basepath>/SugarModules/layoutdefs/Accounts/_overrideAccountDocuments_Connections.php',
      'to_module' => 'Accounts',
    ),
   array (
      'from' => '<basepath>/SugarModules/layoutdefs/Opportunities/_overrideOpportunityDocuments_Conn.php',
      'to_module' => 'Opportunities',
    ),
    array (
      'from' => '<basepath>/SugarModules/layoutdefs/Contacts/_overrideContactDocuments_Connections.php',
      'to_module' => 'Contacts',
    ),
    array (
      'from' => '<basepath>/SugarModules/layoutdefs/Cases/_overrideCaseDocuments_Connections.php',
      'to_module' => 'Cases',
    ),
    array (
      'from' => '<basepath>/SugarModules/layoutdefs/Bugs/_overrideBugDocuments_Connections.php',
      'to_module' => 'Bugs',
    ),
    array (
      'from' => '<basepath>/SugarModules/layoutdefs/Quotes/_overrideQuoteDocuments_Connections.php',
      'to_module' => 'Quotes',
    ),
    array (
      'from' => '<basepath>/SugarModules/layoutdefs/Products/_overrideProductDocuments_Connections.php',
      'to_module' => 'Products',
    ),
    
  ),
  'relationships' => 
  array (
  ),
  'image_dir' => '<basepath>/icons',
  'copy' => 
  array (
    array (
      'from' => '<basepath>/SugarModules/modules',
      'to' => 'modules',
    ),
    array (
      'from' => '<basepath>/SugarModules/custom',
      'to' => 'custom',
    ),
  ),
  'language' => 
  array (
    array (
      'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
      'to_module' => 'application',
      'language' => 'en_us',
    ),
    array (
      'from' => '<basepath>/SugarModules/language/modules/Documents/en_us.ConnectionsFolders.php',
      'to_module' => 'Documents',
      'language' => 'en_us',
    ),
  ),
  'connectors' => array (
    array (
      'connector' => '<basepath>/SugarModules/connectors/connections',
      'name' => 'ext_eapm_connections',
    ),
  ),
);
