<div id='createActivityToDo'>
	<form action="index.php" method="POST" name="CreateActivityToDo" id="CreateActivityToDo">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='saveActivityToDo' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_parent_id' value='{$ibm_parent_id}' />
	<input type='hidden' name='ibm_section_id' value='{$ibm_section_id}' />
	
	<input type='hidden' id='assigned_id' name='assigned_id' value='' />
	<input type='hidden' id='assigned_name'name='assigned_name' value='' />

    <table cellpadding='0' cellspacing='0' border='0' width='100%'>
        
        <tr>
            <td><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_NAME}:</td><td><input type='text' size='40' id='title' name='title' /></td>
        </tr>
         <tr>
            <td>{$language.LBL_DUE_DATE}:</td><td> <input onblur="parseDate(this, '{$cal_dateformat}');" class="text" name="due_date" size='12' maxlength='10' id='due_date'>
<!--not_in_theme!--><img src="{$jscalendarImage}" id="due_date_trigger" align="absmiddle" >
</td>
        </tr>
       
        <tr>
            <td>{$language.LBL_TAGS}:</td><td><input type='text' size='40' name='tags' /></td>
        </tr>
         <tr>
            <td>{$language.LBL_DESCRIPTION}:</td><td> <textarea name='description' cols="45"></textarea></td>
        </tr>
        <tr>
            <td>{$language.LBL_ASSIGN}</td><td> <input id='need_assigne' type='checkbox' onchange='todoAssign();'></input></td>
        </tr>
        <tr style="display:none;" id='assign_member'>
            <td>
            {section name=members start=0 loop=$community_members step=1}
				<input type="radio" name="assignee" value="{$community_members[members].member_id}" onclick="selectAssign('{$community_members[members].member_id}', '{$community_members[members].member_name}');">{$community_members[members].member_name}</input><br>
			{/section}
            </td>
        </tr>
        <tr>
            <td colspan='2'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='2'><button class="ibm_button" onclick='if(activityToDoValidation()) {ldelim} return createActivityToDo(); {rdelim} '>{$language.LBL_SAVE} </button>
            <button class="ibm_button" onclick="closeCreationWindow();" >{$language.LBL_CANCEL}</button></td>
        </tr>
    </table>
    </form>
</div>

