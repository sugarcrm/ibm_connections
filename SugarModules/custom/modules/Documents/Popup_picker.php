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

global $theme;
//include tree view classes.
require_once('include/ytree/Tree.php');
require_once('include/ytree/Node.php');

require_once('custom/modules/Documents/TreeData.php');

class Popup_Picker
{
	
	
	/*
	 * 
	 */
	function Popup_Picker()
	{
		;
	}
	
	/**
	 *
	 */
	function process_page()
	{
		global $theme;
		global $mod_strings;
		global $app_strings;
		global $currentModule;
		global $sugar_version, $sugar_config;
		global $app_list_strings;
        global $sugar_config;
        
        //initalize template
        $form = new XTemplate('custom/modules/Documents/Popup_picker.html');
        $form->assign('MOD', $mod_strings);
        $form->assign('APP', $app_strings);
        $form->assign('THEME', $theme);
        $form->assign('MODULE_NAME', $currentModule);
        
        //tree header.
        $doctree=new Tree('ibmdoctree');
        $doctree->set_param('module','Documents');
        $nodes = get_main_folders();
        foreach ($nodes as $node) {
            $doctree->add_node($node);       
        }
        $form->assign("TREEHEADER",$doctree->generate_header());
        $form->assign("TREEINSTANCE",$doctree->generate_nodes_array());
        
        $site_data = "<script> var site_url= {\"site_url\":\"".getJavascriptSiteURL()."\"};</script>\n";
        $form->assign("SITEURL",$site_data);
       
        $lang_js = '<script type="text/javascript" src="' . $GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $GLOBALS['current_language'] . '.js?s=' . $GLOBALS['js_version_key'] . '&c=' . $GLOBALS['sugar_config']['js_custom_version'] . '&j=' . $GLOBALS['sugar_config']['js_lang_version'] . '"></script>';
        $form->assign("LANG_JS",$lang_js);
        $form->parse('main.SearchHeader.TreeView');
        $treehtml = $form->text('main.SearchHeader.TreeView');
        $form->reset('main.SearchHeader.TreeView');        
        //end tree  
        
        ////////////////////////process full search form and list view.//////////////////////////////
		$output_html = '';
		$button  = "<form action='index.php' method='post' name='form' id='form'>\n";
		$button .= "<input type='submit' name='button' class='button' onclick=\"window.close();\" title='"
			.$app_strings['LBL_CANCEL_BUTTON_TITLE']."' accesskey='"
			.$app_strings['LBL_CANCEL_BUTTON_KEY']."' value='  "
			.$app_strings['LBL_CANCEL_BUTTON_LABEL']."  ' />\n";
		$button .= "</form>\n";

      	$output_html .= insert_popup_header($theme);
		$output_html .= get_form_header('', '', false);
		$form->parse('main.SearchHeader');
		$output_html .= $form->text('main.SearchHeader');
		
		// Reset the sections that are already in the page so that they do not print again later.
		$form->reset('main.SearchHeader');

        //add tree view to output_html.
        $output_html .= $treehtml;
		$output_html .= insert_popup_footer();
		return $output_html;
	}
} // end of class Popup_Picker
?>
