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

<div id='createFile'>
	<form action="index.php" method="POST" name="CreateFile" id="CreateFile" enctype="multipart/form-data">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='uploadNewFile' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />

    <table cellpadding='0' cellspacing='0' border='0' width='35%' class='edit view'>
        <tr>
            <th colspan='2'>{$language.LBL_SHARE_FILE_WITH_COMMUNITY}</th>
        </tr>
        <tr>
            <td>&nbsp;</td><td><input type="file" id="new_file" name="new_file" onchange="document.getElementById('file_name').value = this.value.replace(/^.*[\\\/]/, '');"/</td>
        </tr>
        <tr>
            <td>{$language.LBL_FILE_NAME}:</td><td><input type='text' size='40' id='file_name' name='file_name' /></td>
        </tr>
        <tr>
            <td>{$language.LBL_CAN_USERS_SHARE}:</td><td><input type='checkbox' name='propogate' /></td>
        </tr>
        <tr>
            <td>{$language.LBL_TAGS}:</td><td><input type='text' size='40' name='file_tags' /></td>
        </tr>
        <tr>
            <td colspan='2'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='2'><input type='button' value='Save' onclick='saveNewFile();' /> <input type='button' value='Cancel' onclick="closeTab('CreateFile');" /></td>
        </tr>
    </table>
    </form>
</div>