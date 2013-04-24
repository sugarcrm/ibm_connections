<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


class ConnectionsLogicHook 
{
  
    public function publishToActivityStream(SugarBean $bean) {
        require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsActivityStreamEntry.php');
        $entry = new ConnectionsActivityStreamEntry($bean);
        $entry->enqueue();
    }

    protected function handleFieldMap($bean, $mapping) 
    {
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

    public function showFrame($event, $args) 
    {
		if ($GLOBALS['app']->controller->action != 'DetailView') return;
		global $app_list_strings, $beanList, $current_user, $db;
		require_once('include/connectors/utils/ConnectorUtils.php');
	    require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php');
	    $eapm = ConnectionsHelper::getEAPM();
	    $connector = ConnectorUtils::getConnector('ext_eapm_connections');
	    $bean = new $beanList[$_REQUEST['module']];
		$bean->retrieve($_REQUEST['record']);
		
		$header = '';
		$connector_language = ConnectorUtils::getConnectorStrings('ext_eapm_connections');
		$tplName = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/SubPanel.tpl';
        $smarty = new Sugar_Smarty();

        
	    $smarty->assign('language', $connector_language);
	    $smarty->assign('json_language', json_encode($connector_language));
		$smarty->assign('current_user_id', $current_user->id);
		
        $smarty->assign('logo',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/insideview.png'));
		
		
		
	     if ( $connector['enabled']) {
	     	if (empty($eapm->url)){
	     		
	     		$query = "SELECT ea.email_address FROM users
	     		INNER JOIN email_addr_bean_rel eabl ON eabl.bean_module='Users' AND eabl.bean_id = users.id
	     		INNER JOIN email_addresses ea ON ea.id=eabl.email_address_id
	     		WHERE users.deleted=0 AND users.status='active' AND users.is_admin=1 AND eabl.deleted=0 AND eabl.primary_address=1 AND users.id=1 LIMIT 1";
	     		
	     		$composeData['to_email_addrs'] = $db->getOne($query);
                
                
                require_once('modules/Emails/EmailUI.php');
                $eUi = new EmailUI();
                $j_quickComposeOptions = $eUi->generateComposePackageForQuickCreate($composeData, http_build_query($composeData), true);
	     		$link_quickComposeOptions = "<a href='javascript:void(0);' onclick='SUGAR.quickCompose.init($j_quickComposeOptions);'>" . $connector_language['LBL_EMAIL'] . "</a>";
	     		$documentation_link = "<a href='http://google.com' target='_blank'>" . $connector_language['LBL_CLICK'] . "</a>";
	     		$smarty->assign('documentation_link',$documentation_link);
	     		$smarty->assign('j_quickComposeOptions' ,$link_quickComposeOptions);
	     		
	     		$contentTpl = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/NotConfigured.tpl';
	     		
	     	}
	     	else{
		    	$contentTpl = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Mockup.tpl';
				$comm_id = "";
				
				if(isset($_REQUEST['record']) && !empty($_REQUEST['record'])) {
					$connections = new ibm_connections();
					$connections->retrieve_by_string_fields(array('parent_id' => $_REQUEST['record']));
					$comm_id = $connections->community_id;
					$recordOwner = ($bean->assigned_user_id != $current_user->id) ? false : true;
					$helper = new ConnectionsHelper();
					$comm_info = $helper->getCommunityNavigation($comm_id, $recordOwner);
					$header = $comm_info['head'];
					$allow = 'no';
					if ($recordOwner || $comm_info['visibility'] != 'private'){
						$allow = 'yes';
					}
					else {
						$my_id = $eapm->api_data['userId'];
						if (!empty($my_id))
						{
							$members = $helper->getCommunityMemberArray($comm_id);
							foreach($members as $member){
								if ($member['member_id'] == $my_id){
									$allow = 'yes';
									break;
								}
							}
						}
						else {
							$allow = 'no';
						}
					}
					if (empty($comm_id)) $allow = 'no';
					$smarty->assign('allow_mod', $allow);
					$smarty->assign('ibm_header', $header);
				}
				if (empty($comm_id) && !$recordOwner){
					$contentTpl = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/NoCommunityNotOwner.tpl';
				}
				//echo "<pre>";
				//print_r($eapm->api_data['userId']);
				if (empty($eapm->eapmBean)) {
					$contentTpl = 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/Login.tpl';
				}
				$smarty->assign('quickPostTpl', 'custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/QuickPost.tpl');
	    	}
	    }
	    else {
		    return;
	    }
		
		
		
		$smarty->assign('contentTpl', $contentTpl);
        $smarty->assign('close',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/close.png'));
        $smarty->assign('logo_expanded',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_expanded.png'));
        $smarty->assign('logo_collapsed',getWebPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/connections_collapsed.png'));
	    $smarty->assign('connections_js',getJSPath('custom/modules/Connectors/connectors/sources/ext/eapm/connections/Connections.js'));
		$smarty->assign('parent_type', $_REQUEST['module']);
	    $smarty->assign('parent_id', $_REQUEST['record']);
	    $smarty->assign('connections_url', $eapm->url);
	    $smarty->assign('assigned_user_id', $bean->assigned_user_id);
	    $smarty->assign('assigned_user_name', $bean->assigned_user_name);
	    
	    //levels :member
	    //$smarty->assign('access_level', );
	    //if (!$recordOwner || empty($comm_id))
    	
	    $smarty->assign('object_type', $app_list_strings['moduleListSingular'][$_REQUEST['module']]);
	    include('custom/modules/Connectors/connectors/sources/ext/eapm/connections/tabs.meta.php');
	    $tabs_meta_json = json_encode($tabs_meta);
	    $smarty->assign('tabs_meta', $tabs_meta_json);
       echo $smarty->fetch($tplName);

    }
    
    public function BusinesscardJS($event, $args)
    {
		if ($_REQUEST['ajax_load'] != 1) return;
		require_once('include/connectors/utils/ConnectorUtils.php');
	    require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php');
	    $eapm = ConnectionsHelper::getEAPM();
	    $connector = ConnectorUtils::getConnector('ext_eapm_connections');
	    //echo "<pre>";
	  //  print_r($_REQUEST);
	    //echo "</pre>";
	     if ( $connector['enabled']) {
	     	if (!empty($eapm->url)){
    
    			//echo "<script type='text/javascript' src='{$eapm->url}/profiles/ibm_semanticTagServlet/javascript/semanticTagService.js?loadCssFiles=true'></script>";
    		}
    	}
    }
}
