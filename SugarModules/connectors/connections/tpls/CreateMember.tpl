<div id='AddMember'>
	<form action="index.php" method="POST" name="AddMemberForm" id="AddMemberForm">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_id' value='{$community_id}' />

	<table width='100%'>
        <tr>
            <td>{$language.LBL_ADD}:</td>
            <td>
		        	 <!-- <div class='scroll'>
		        {foreach from=$members key=connectionsId item=userName}
		        <input type='checkbox' name='members[]' value='{$connectionsId}' /> {$userName}<br/>
		        {/foreach}
		        </div>-->
		       <select name="role" id="mt_member_role">
            		<option value="member" selected>{$language.LBL_MEMBER_ROLE_MEMBER}</option>
            		<option value="owner">{$language.LBL_MEMBER_ROLE_OWNER}</option>
            	</select>
            	&nbsp;
            	<input type='text' size='28' name='mt_member_name' id='mt_member_name'  onfocus = "addMemberMembersTabAutocomplete();" />
            	<input type='hidden' name='mt_member_id' id='mt_member_id' />
            	
            </td>
            <td>&nbsp;</td>
        </tr>
        
        
       <tr>
            <td colspan='3'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3'><button class="ibm_button" onclick="return addMember();" >{$language.LBL_ADD}</button>
            <button class="ibm_button" onclick="closeCreationWindow();return false;" >{$language.LBL_CANCEL}</button></td>
        </tr>
    </table>
    </form>
</div>

