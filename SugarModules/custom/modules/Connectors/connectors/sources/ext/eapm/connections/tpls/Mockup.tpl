{*
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
 * by SugarCRM are Copyright (C) 2004-2013 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

*}

<table cellpadding='0' cellspacing='0' width='100%' border='0' class='edit view'>
	<tr><td>
		<div class='connections-header'>
		  <nav>
		    <ul id='ibm_community_navigation'>{$ibm_header}</ul>
		  </nav>
			<div id='ibm_tabs'>
			<div class="ibm-header-tabs">
				<div class='before-nav'>
					<input type="button" value="{$language.LBL_OVERVIEW_TAB}" class='ibm_tabs' onclick='loadTabData("Overview", 1, "");' id = 'ibm_Overview_tab' />
					<input type="button" value="{$language.LBL_UPDATES_TAB}" class='ibm_tabs' onclick='loadTabData("Updates", 1, "");'  id = 'ibm_Updates_tab' />
					<input type="button" value="{$language.LBL_ACTIVITIES_TAB}" class='ibm_tabs' onclick='loadTabData("Activities", 1,"");'  id = 'ibm_Activities_tab' />
					<input type="button" value="{$language.LBL_FILES_TAB}" class='ibm_tabs' onclick='loadTabData("Files", 1, "");'  id = 'ibm_Files_tab' />
					<input type="button" value="{$language.LBL_DISCUSSIONS_TAB}" class='ibm_tabs' onclick='loadTabData("Discussions", 1, "");'  id = 'ibm_Discussions_tab' />
				</div>
				
					<nav class='ibm-actions-nav nav-issets'>	
						<ul >
							<li class='more-isset'>	
								<ul>
									<li><input type="button" value="{$language.LBL_BOOKMARKS_TAB}" class='ibm_tabs' onclick='loadTabData("Bookmarks", 1, "");'  id = 'ibm_Bookmarks_tab' /></li>
									<li><input type="button" value="{$language.LBL_BLOG_TAB}" class='ibm_tabs' onclick='loadTabData("Blog", 1, "");'  id = 'ibm_Blog_tab' /></li>
									<li><input type="button" value="{$language.LBL_WIKI_TAB}" class='ibm_tabs' onclick='loadTabData("Wiki", 1, "");'  id = 'ibm_Wiki_tab'/></li>
								</ul>
							</li>
						</ul>
					</nav>
					<br style="clear: both;">

				</div>
			</div>
			<div id='ibm_search'>{include file="custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/SearchInTab.tpl"}</div>
		</div>
		</td>
	</tr>
<tr><td><div width='100%' heigth='250px;' id='ibm_connection_body'></div></td></tr>
<tr class='ibm-connection-footer'><td><div  id='quick_post_div' width='100%'><div style='float:right;'>{include file="$quickPostTpl"}</div></div></td></tr>
</table>
