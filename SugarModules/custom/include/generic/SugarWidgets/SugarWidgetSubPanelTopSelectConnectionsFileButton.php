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




require_once('include/generic/SugarWidgets/SugarWidgetSubPanelTopButton.php');

class SugarWidgetSubPanelTopSelectConnectionsFileButton extends SugarWidgetSubPanelTopButton
{
	//button_properties is a collection of properties associated with the widget_class definition. layoutmanager
	function SugarWidgetSubPanelTopSelectConnectionsFileButton($button_properties=array())
	{
		$this->button_properties=$button_properties;
	}

    public function getWidgetId()
    {
        return parent::getWidgetId(false) . 'select_connections_file_button';
    }

    public function getDisplayName()
    {
        return $GLOBALS['app_strings']['LBL_SELECT_CONNECTIONS_FILE_BUTTON_LABEL'];
    }
	//widget_data is the collection of attributes associated with the button in the layout_defs file.
	function display(&$widget_data)
	{
		global $app_strings;
		$initial_filter = '';

	    $this->title     = $this->getTitle();
        $this->accesskey = $this->getAccesskey();
        $this->value     = $this->getDisplayName();

		if (is_array($this->button_properties)) {
			if( isset($this->button_properties['title'])) {
				$this->title = $app_strings[$this->button_properties['title']];
			}
			if( isset($this->button_properties['accesskey'])) {
				$this->accesskey = $app_strings[$this->button_properties['accesskey']];
			}
			if( isset($this->button_properties['form_value'])) {
				$this->value = $app_strings[$this->button_properties['form_value']];
			}
			if( isset($this->button_properties['module'])) {
				$this->module_name = $this->button_properties['module'];
			}
		}


		$focus = $widget_data['focus'];
		if(ACLController::moduleSupportsACL($widget_data['module']) && !ACLController::checkAccess($widget_data['module'], 'list', true)){
			$button = ' <input type="button" name="' . $this->getWidgetId() . '" id="' . $this->getWidgetId() . '" class="button"' . "\n"
			. ' title="' . $this->title . '"'
			. ' value="' . $this->value . "\"\n"
			.' disabled />';
			return $button;
		}

		//refresh the whole page after end of action?
		$refresh_page = 0;
		if(!empty($widget_data['subpanel_definition']->_instance_properties['refresh_page'])){
			$refresh_page = 1;
		}
		$subpanel_definition = $widget_data['subpanel_definition'];
		$button_definition = $subpanel_definition->get_buttons();

        $subpanel_name = $subpanel_definition->get_name();
		if (empty($this->module_name)) {
			$this->module_name = $subpanel_definition->get_module_name();
		}
		$link_field_name = $subpanel_definition->get_data_source_name(true);
		$popup_mode='Single';
		if(isset($widget_data['mode'])){
			$popup_mode=$widget_data['mode'];
		}
		if(isset($widget_data['initial_filter_fields'])){
			if (is_array($widget_data['initial_filter_fields'])) {
				foreach ($widget_data['initial_filter_fields'] as $value=>$alias) {
					if (isset($focus->$value) and !empty($focus->$value)) {
						$initial_filter.="&".$alias . '='.urlencode($focus->$value);
					}
				}
			}
		}
		$create="true";
		if(isset($widget_data['create'])){
			$create=$widget_data['create'];
		}
		$return_module = $_REQUEST['module'];
		$return_action = 'SubPanelViewer';
		$return_id = $_REQUEST['record'];

		//field_to_name_array
		$fton_array= array('id' => 'subpanel_id');
		if(isset($widget_data['field_to_name_array']) && is_array($widget_data['field_to_name_array'])){
			$fton_array=array_merge($fton_array,$widget_data['field_to_name_array']);
		}

		$return_url = "index.php?module=$return_module&action=$return_action&subpanel=$subpanel_name&record=$return_id&sugar_body_only=1";

		$popup_request_data = array(
			'call_back_function' => 'set_return_and_save_background',
			'form_name' => 'DetailView',
			'field_to_name_array' => $fton_array,
			'passthru_data' => array(
				'child_field' => $subpanel_name,
				'return_url' => urlencode($return_url),
				'link_field_name' => $link_field_name,
				'module_name' => $subpanel_name,
				'refresh_page'=>$refresh_page,
			),
		);
		if (is_array($this->button_properties) && !empty($this->button_properties['add_to_passthru_data'])) {
			$popup_request_data['passthru_data']= array_merge($popup_request_data['passthru_data'],$this->button_properties['add_to_passthru_data']);
		}

		if (is_array($this->button_properties) && !empty($this->button_properties['add_to_passthru_data']['return_type'])) {

			if ($this->button_properties['add_to_passthru_data']['return_type']=='report') {
				$initial_filter = "&module_name=". urlencode($widget_data['module']);
			}
		}
		$initial_filter .= "&connections=true";
        $json_encoded_php_array = $this->_create_json_encoded_popup_request($popup_request_data);
		return ' <input type="button" name="' . $this->getWidgetId() . '" id="' . $this->getWidgetId() . '" class="button"' . "\n"
				. ' title="' . $this->title . '"'
			. ' value="' . $this->value . "\"\n"
			. " onclick='open_popup(\"$this->module_name\",600,400,\"$initial_filter\",true,true,$json_encoded_php_array,\"$popup_mode\",$create);' />\n";
	}

    /**
    * @return string
    */
    protected function getTitle()
    {
       return translate('LBL_SELECT_CONNECTIONS_FILE_BUTTON_TITLE');
    }

    /**
    * @return string
    */
    protected function getAccesskey()
    {
       return translate('LBL_SELECT_CONNECTIONS_FILE_BUTTON_KEY');
    }

}
?>
