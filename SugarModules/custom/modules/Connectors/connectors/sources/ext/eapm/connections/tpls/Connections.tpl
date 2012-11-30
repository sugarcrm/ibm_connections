{*
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
*}


<script type="text/javascript">
SUGAR.util.doWhen("typeof(markSubPanelLoaded) != 'undefined' && document.getElementById('subpanel_connections')", function() {ldelim}
markSubPanelLoaded('connections');
var connectionsSubPanel = document.getElementById("subpanel_connections" );
connectionsSubPanel.cookie_name="connections_v";
if(div_cookies['connections_v']){ldelim}
if(div_cookies['connections_v'] == 'none')
{ldelim}
hideSubPanel('connections');
document.getElementById('hide_link_connections').style.display='none';
document.getElementById('show_link_connections').style.display='';
{rdelim}
{rdelim}
{rdelim});

</script>

<script type="text/javascript" src="{$connections_url}/profiles/ibm_semanticTagServlet/javascript/semanticTagService.js"></script>

<div id="communities_dialog" class="yui-pe-content">
<div class="hd">{$language.LBL_PLEASE_SELECT_COMMUNITY}</div>
<div class="bd">
    <div id='connections_communities' style='width:100%' class="yui-navset detailview_tabs yui-navset-top"></div>
</form>
</div>
</div>

<div id="profile_dialog"></div>

<div id='connectionsDiv' style='width:100%' class="doNotPrint">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="formHeader h3Row">
        <tbody>
        <tr>
<td nowrap="" >
                <h3>
                    IBM Connections
                </h3>
            </td>
                        <td>
                <img height="1" width="1" src="{sugar_getimagepath file='blank.gif'}" alt="">
            </td>

            <td  valign="middle" nowrap="" width="100%">
                <a name="connections"> </a>
                <span id="show_link_connections" style="display: none">
                    <a href="#" onclick="current_child_field = 'connections';showSubPanel('connections',null,null,'connections');document.getElementById('show_link_connections').style.display='none';document.getElementById('hide_link_connections').style.display='';return false;"><img src="{sugar_getimagepath file='advanced_search.gif'}" border="0"></a>
                </span>
                <span id="hide_link_connections" style="display: ">
                    <a href="#" onclick="hideSubPanel('connections');document.getElementById('hide_link_connections').style.display='none';document.getElementById('show_link_connections').style.display='';return false;"><img src="{sugar_getimagepath file='basic_search.gif'}" border="0"></a>
                </span>
            </td>
            
        </tr>
        </tbody>
    </table>
    <div id='subpanel_connections' style='width:100%' class="yui-navset detailview_tabs yui-navset-top"></div>
</div>

<div id="loading_modal"></div>

<script type="text/javascript" src="custom/modules/Connectors/connectors/sources/ext/eapm/connections/Connections.js"></script>

<script language="javascript">
    YAHOO.connections.data.parent_type = '{$parent_type}';
    YAHOO.connections.data.parent_id ='{$parent_id}';
    YAHOO.connections.tabs_meta = {$tabs_meta};
    YAHOO.connections.language = {$json_language};

    YAHOO.util.Event.onDOMReady(loadTabGroup);
</script>
