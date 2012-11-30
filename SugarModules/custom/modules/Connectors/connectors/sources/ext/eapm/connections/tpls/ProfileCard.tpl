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

<link rel="stylesheet" type="text/css" href="custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.css" />

<div id="memberProfile">

    <table border="0" cellpadding="0" cellspacing="0" class="profileTabs">
        <tr>
            <td><a href="{$profile.member_blogs}" target="_blank">{$language.LBL_MEMBER_BLOGS}</a></td>
            <td><a href="{$profile.member_forums}" target="_blank">{$language.LBL_MEMBER_FORUMS}</a></td>
            <td><a href="{$profile.member_wikis}" target="_blank">{$language.LBL_MEMBER_WIKIS}</a></td>
            <td><a href="{$profile.member_files}" target="_blank">{$language.LBL_MEMBER_FILES}</a></td>
            <td><a href="{$profile.member_communities}" target="_blank">{$language.LBL_MEMBER_COMMUNITIES}</a></td>
            <td><a href="{$profile.member_bookmarks}" target="_blank">{$language.LBL_MEMBER_BOOKMARKS}</a></td>
            <td><a href="{$profile.member_profiles}" target="_blank">{$language.LBL_MEMBER_PROFILE}</a></td>
            <td><a href="{$profile.member_activities}" target="_blank">{$language.LBL_MEMBER_ACTIVITIES}</a></td>
        </tr>
    </table>
    <br/>
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="75"><img src="{$profile_image_url}" width="75" /></td>
            <td width="25">&nbsp;</td>
            <td width="300" valign="top">
                <span class="memberName">{$profile.member_name}</span><br/>
                <span>{$profile.member_title}</span><br/>
                <span>{$member_address}</span><br/>
            </td>
        </tr>
    </table>
    <pre>{$profile.rawResponse}</pre>

</div>
