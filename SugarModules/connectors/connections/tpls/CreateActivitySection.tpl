<div id='createActivitySection'>
	<form action="index.php" method="POST" name="CreateActivitySection" id="CreateActivitySection">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_parent_id' value='{$ibm_parent_id}' />

    <table cellpadding='0' cellspacing='0' border='0' width='100%'>
       
        <tr>
            <td><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_NAME}:</td><td><input type='text' size='40' id='name' name='name' /></td>
        </tr>
        
        <tr>
            <td colspan='2'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='2'>
            	<button class="ibm_button" onclick="if(activitySectionValidation()) {ldelim} document.getElementById('CreateActivitySection').method.value='saveActivitySection'; return createActivitySection(); {rdelim}" >
            		{$language.LBL_SAVE}       </button> 
            	<button class="ibm_button" onclick="closeCreationWindow(); return false;" >{$language.LBL_CANCEL}</button></td>
        </tr>
    </table>
    </form>
</div>
