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
 
class ConnectionsLogicHook {
	
	public function publishToActivityStream(SugarBean $bean) {
	    require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsActivityStreamEntry.php');
		$entry = new ConnectionsActivityStreamEntry($bean);
		$entry->enqueue();				
	}

    protected function handleFieldMap(SugarBean $bean, $mapping) {
        $outArray = array();
        foreach ( $mapping as $dest => $src ) {
            // Initialize it to an empty string, so it is always set
            $outArray[$dest] = '';

            if ( is_array($src) ) {
                // The source can be any one of a number of fields
                // we must go deeper.
                foreach ( $src as $src2 ) {
                    if ( isset($bean->$src2) ) {
                        $outArray[$dest] = $bean->$src2;
                        break;
                    }
                }
            } else {
                if ( isset($bean->$src) ) {
                    $outArray[$dest] = $bean->$src;
                }
            }
        }

        $outStr = '';
        foreach ( $outArray as $k => $v ) {
            $outStr .= $k.'='.rawurlencode(html_entity_decode($v,ENT_QUOTES)).'&';
        }
        
        $outStr = rtrim($outStr,'&');
        
        return $outStr;
    }

    public function showFrame($event, $args) {
	    require_once('include/connectors/utils/ConnectorUtils.php');
	    require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php');
	    $eapm = ConnectionsHelper::getEAPM();
	    $connector = ConnectorUtils::getConnector('ext_eapm_connections');
	   // var_dump($connector);
		//if (!is_object($eapm) || !is_array($connector)) return;
	    if ( $GLOBALS['app']->controller->action == 'DetailView' && $connector['enabled'] && is_object($eapm) && $eapm->eapmBean->validated) {
		    $tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Connections.tpl';
	    }
	    else {
		    return;
	    }

        $smarty = new Sugar_Smarty();

        $connector_language = ConnectorUtils::getConnectorStrings('ext_eapm_connections');
	    $smarty->assign('language', $connector_language);
	    $smarty->assign('json_language', json_encode($connector_language));
        $smarty->assign('connections_url', $eapm->url);

        $smarty->assign('logo',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/insideview.png'));

        $smarty->assign('close',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/close.png'));
        $smarty->assign('logo_expanded',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_expanded.png'));
        $smarty->assign('logo_collapsed',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_collapsed.png'));
	    $smarty->assign('connections_js',getJSPath('custom/modules/Connectors/connectors/soureces/ext/eapm/connections/Connections.js'));
		$smarty->assign('parent_type', $_REQUEST['module']);
	    $smarty->assign('parent_id', $_REQUEST['record']);

	    require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/tabs.meta.php');
	    $tabs_meta_json = json_encode($tabs_meta);
	    $smarty->assign('tabs_meta', $tabs_meta_json);

        echo $smarty->fetch($tplName);
    }
}
