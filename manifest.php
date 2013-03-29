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
      0 => '6\.4\.*',
      1 => '6\.5\.*',
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
  'published_date' => '2012-03-27 20:00:00',
  'type' => 'module',
  'version' => '2.0.beta6',
  'remove_tables' => 'prompt',
);


$installdefs = array (
  'id' => 'IBM',
  'beans' => 
  array (
    0 => 
    array (
      'module' => 'ibm_connections',
      'class' => 'ibm_connections',
      'path' => 'modules/ibm_connections/ibm_connections.php',
      'tab' => false,
    ),
  ),
  'layoutdefs' => 
  array (
  ),
  'relationships' => 
  array (
  ),
  'image_dir' => '<basepath>/icons',
  'copy' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/modules/ibm_connections',
      'to' => 'modules/ibm_connections',
    ),
    1 => 
    array (
      'from' => '<basepath>/SugarModules/custom',
      'to' => 'custom',
    ),
   /* 1 => 
    array (
      'from' => '<basepath>/SugarModules/custom/include/externalAPI/Connections/ConnectionsXML.php',
      'to' => 'custom/include/externalAPI/Connections/ConnectionsXML.php',
    ),
    2 => 
    array (
      'from' => '<basepath>/SugarModules/custom/include/externalAPI/Connections/ExtAPIConnections.php',
      'to' => 'custom/include/externalAPI/Connections/ExtAPIConnections.php',
    ),
    3 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tabs.meta.php',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tabs.meta.php',
    ),
    4 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/Connections.js',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/Connections.js',
    ),
    5 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php',
    ),
    6 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsLogicHook.php',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsLogicHook.php',
    ),
    7 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php',
    ),
    8 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/connections.php',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/connections.php',
    ),
    9 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_collapsed.png',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_collapsed.png',
    ),
    10 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_expanded.png',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_expanded.png',
    ),
    11 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/language/en_us.lang.php',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/language/en_us.lang.php',
    ),
    //12 => 
    //array (
    //  'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/mapping.php',
    //  'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/mapping.php',
    //),
    13 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Connections.tpl',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Connections.tpl',
    ),
    14 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ConnectionsError.tpl',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ConnectionsError.tpl',
    ),
    15 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateCommunity.tpl',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateCommunity.tpl',
    ),
    16 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateFile.tpl',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/CreateFile.tpl',
    ),
    17 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.tpl',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.tpl',
    ),
    18 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.css',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.css',
    ),
    19 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Search.tpl',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Search.tpl',
    ),
    20 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/controller.php',
      'to' => 'custom/modules/Connectors/controller.php',
    ),
    21 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/metadata/connectors.php',
      'to' => 'custom/modules/Connectors/metadata/connectors.php',
    ),
    22 => 
    array (
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/metadata/display_config.php',
      'to' => 'custom/modules/Connectors/metadata/display_config.php',
    ),
    */
  ),
  'language' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
      'to_module' => 'application',
      'language' => 'en_us',
    ),
  ),
);
