<div id='createFile'>
	<form action="index.php" method="POST" name="CreateFile" id="CreateFile" enctype="multipart/form-data">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='uploadNewFile' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />

    <table cellpadding='0' cellspacing='5' border='5' width='100%' class='edit view'>
        <tr>
            <th colspan='4'>{$language.LBL_SHARE_FILE_WITH_COMMUNITY}</th>
        </tr>
         <tr>
            <td rowspan='7'  width='5%'></td>
        </tr>
        <tr>
            <td rowspan='2' ><div class='ibm_drop_file' id="file_dropbox">{$language.LBL_DROP_FILE_HERE} </div></td><td>{$language.LBL_FILE_NAME}:</td><td><input type='text' size='40' id='file_name' name='file_name' /></td>
        </tr>
        <tr>
           <td>{$language.LBL_SHARE_WITH}:</td>
            <td>
            	<select name="visibility">
            		<option name="private" value="private" selected>{$language.LBL_PRIVATE}</option>
            		<option name="public" value="public">{$language.LBL_PUBLIC}</option>
            	</select>
            </td>
        </tr>
      
        <tr>
            <td colspan='3'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3'>
            	<input type="file" id="new_file" name="new_file" style='display:none'/>
            	<input type='button' class="ibm_button"onclick="document.getElementById('new_file').click();" value='{$language.LBL_SELECT_FILE}'></input>
            	<span id='file_path'></span>
            	</td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td colspan='2'><input type='button' class="ibm_button" value='{$language.LBL_SAVE}' onclick='saveNewFile(); return false;' /></td>
        </tr>
    </table>
    </form>
</div>

