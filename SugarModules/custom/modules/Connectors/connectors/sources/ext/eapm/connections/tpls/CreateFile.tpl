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