<div id='createActivity'>
	<form action="index.php" method="POST" name="CreateActivityForm" id="CreateActivityForm">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />

	<div>
		<div><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_NAME}:	</div>
		<div><input type='text' size='40' id='activity_name' name='activity_name' /></div>
    </div>
    <div>
        <div>{$language.LBL_TAGS}:</div>
        <div><input type='text' size='40' name='activity_tags' /></div>
    </div>
    <div>
        <div>{$language.LBL_ACTIVITY_GOAL}:</div>
        <div><input type='text' size='40' id='activity_goal' name='activity_goal' /></div>
    </div>
    <div>
        <div>{$language.LBL_DUE_DATE}:</div>
        <div>
        <input onblur="parseDate(this, '{$cal_dateformat}');" class="text" name="due_date" size='12' maxlength='10' id='due_date'>
<!--not_in_theme!--><img src="{$jscalendarImage}" id="due_date_trigger" align="absmiddle" >

        </div>
    </div>
    &nbsp;
    <div>
        <div>
        	<button class="ibm_button" onclick="if(activityValidation()) {ldelim} document.getElementById('CreateActivityForm').method.value='saveActivity'; return createActivity(); {rdelim}">{$language.LBL_SAVE}</button> 
        	<button class="ibm_button" onclick="closeCreationWindow(); return false;" />{$language.LBL_CANCEL}</button>
        </div>
    </div>
    </form>
</div>


