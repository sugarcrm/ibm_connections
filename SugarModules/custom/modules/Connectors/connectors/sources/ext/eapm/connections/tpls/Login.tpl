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
<script type="text/javascript" src="{$connections_js}"></script>
<form action="index.php" method="POST" name="loginConnections" id="loginConnections">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="actionsContainer">
		<tbody>
			<tr> 
				<td class="buttons">
					<input type="hidden" name="module" value="Connectors">
					<input type="hidden" name="action" value="Connections">
					<input type="hidden" name="method" value="saveEAPM">
					<input type="hidden" name="offset" value="1">
					<input type="hidden" name="to_pdf" value="true">
				</td>
				<td align="right">	</td>
			</tr>
	</tbody>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="yui3-skin-sam edit view dcQuickEdit" >
	<tbody>
		<tr>
			<td>&nbsp;</td>	
		</tr>
		<tr>
			<td colspan="5">{$language.LBL_LOGIN_LABEL}</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr style="">
			<td valign="top" width="20%" class="">
				<input type="text" name="name" id="connection_user_name" size="30" maxlength="255" value="{$language.LBL_USER_NAME}" title="" onfocus="this.value='';">
			</td>
			<td valign="top" width="20%" class="">
				<input type="password" id="connection_password" name="password" size="30" value="{$language.LBL_PASSWORD}" title="" tabindex="0" onfocus="this.value='';">
			</td>

			<td valign="top" width="10%" class="">
				<button class="ibm_button" onclick="if(check_form('loginConnections')) connectionsLogin();return false;" name="login_button" class="ibm_button">{$language.LBL_BUTTON_CONNECT_DESC}</button> 
			</td>
			<td valign="top" id="note_label" width="50%" class="">
				<div id="eapm_notice_div">{$language.LBL_STATUS}{$language.LBL_STATUS_NOT_CONNECTED}</div>
			</tr>
		<tr>
			<td>&nbsp;</td>	
		</tr>
	</tbody>
</table>



</form>
