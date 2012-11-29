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


// THIS CONTENT IS GENERATED BY MBPackage.php
$manifest = array (
  0 => 
  array (
    'acceptable_sugar_versions' => 
    array (
      0 => '6\.4\.*',
      1 => '6\.5\.*',
	  2 => '6\.6\.*'
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
  'author' => 'sugarcrm',
  'description' => '',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'IBM Connections',
  'published_date' => '2012-11-28 11:00:00',
  'type' => 'module',
  'version' => '1.1',
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
      'from' => '<basepath>/SugarModules/custom/modules/Connectors/connectors/sources/ext/eapm/connections/language',
      'to' => 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/language',
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
  ),
  'language' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
      'to_module' => 'application',
      'language' => 'en_us',
    ),
	1 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/bg_BG.lang.php',
	  'to_module' => 'application',
	  'language' => 'bg_BG',
	),
	2 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/ca_ES.lang.php',
	  'to_module' => 'application',
	  'language' => 'ca_ES',
	),
	3 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/cs_CZ.lang.php',
	  'to_module' => 'application',
	  'language' => 'cs_CZ',
	),
	4 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/da_DK.lang.php',
	  'to_module' => 'application',
	  'language' => 'da_DK',
	),
	5 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/de_DE.lang.php',
	  'to_module' => 'application',
	  'language' => 'de_DE',
	),
	6 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/en_UK.lang.php',
	  'to_module' => 'application',
	  'language' => 'en_UK',
	),
	7 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/es_ES.lang.php',
	  'to_module' => 'application',
	  'language' => 'es_ES',
	),
	8 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/et_EE.lang.php',
	  'to_module' => 'application',
	  'language' => 'et_EE',
	),
	9 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/fr_FRhe_IL.lang.php',
	  'to_module' => 'application',
	  'language' => 'fr_FRhe_IL',
	),
	10 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/hu_HU.lang.php',
	  'to_module' => 'application',
	  'language' => 'hu_HU',
	),
	11 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/it_it.lang.php',
	  'to_module' => 'application',
	  'language' => 'it_it',
	),
	12 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/ja_JP.lang.php',
	  'to_module' => 'application',
	  'language' => 'ja_JP',
	),
	13 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/lt_LT.lang.php',
	  'to_module' => 'application',
	  'language' => 'lt_LT',
	),
	14 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/lv_LV.lang.php',
	  'to_module' => 'application',
	  'language' => 'lv_LV',
	),
	15 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/nb_NO.lang.php',
	  'to_module' => 'application',
	  'language' => 'nb_NO',
	),
	16 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/nl_NL.lang.php',
	  'to_module' => 'application',
	  'language' => 'nl_NL',
	),
	17 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/pl_PL.lang.php',
	  'to_module' => 'application',
	  'language' => 'pl_PL',
	),
	18 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/pt_BR.lang.php',
	  'to_module' => 'application',
	  'language' => 'pt_BR',
	),
	19 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/pt_PT.lang.php',
	  'to_module' => 'application',
	  'language' => 'pt_PT',
	),
	20 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/ro_RO.lang.php',
	  'to_module' => 'application',
	  'language' => 'ro_RO',
	),
	21 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/ru_RU.lang.php',
	  'to_module' => 'application',
	  'language' => 'ru_RU',
	),
	22 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/sr_RS.lang.php',
	  'to_module' => 'application',
	  'language' => 'sr_RS',
	),
	23 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/sv_SE.lang.php',
	  'to_module' => 'application',
	  'language' => 'sv_SE',
	),
	24 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/tr_TR.lang.php',
	  'to_module' => 'application',
	  'language' => 'tr_TR',
	),
	25 =>
	array (
	  'from' => '<basepath>/SugarModules/language/application/zh_CN.lang.php',
	  'to_module' => 'application',
	  'language' => 'zh_CN',
	),
  ),
);
