<div class='ibm-quick-post'>
{$language.LBL_POST_ON} &nbsp;
<select onchange='selectQuickCreateElement(this.options[this.selectedIndex].value);' name='ibm_asset' id='ibm_asset'>
	<option value='update'>{$language.LBL_UPDATE}</option>
	<option value='activity'>{$language.LBL_ACTIVITY}</option>
	<option value='question'>{$language.LBL_QUESTION}</option>
	<option value='file'>{$language.LBL_FILE}</option>
	<option value='bookmark'>{$language.LBL_BOOKMARK}</option>
</select>
<input type="text" size="20" maxlength="255" name="ibm_name" id="ibm_name" value="" onkeypress="quickCreateIBM(event);"/>
<form action="index.php" name="QuickpostFile" id="QuickpostFile">

<input type="file" id="file_el" name="file_el" style="display:none;" onchange="document.getElementById('ibm_name').value = this.value.replace(/^.*[\\\/]/, ''); document.getElementById('ibm_name').focus();" />
</form>
</div>
