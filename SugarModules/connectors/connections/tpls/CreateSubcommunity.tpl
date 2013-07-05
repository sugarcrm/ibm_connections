<div id='CreateSubcommunity'>
	<form action="index.php" method="POST" name="CreateSubcommunityForm" id="CreateSubcommunityForm">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_id' value='{$community_id}' />

	<table width='100%'>
        <tr>
            <td width='25%'><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_COMMUNITY_NAME}:</td>
            <td width='70%'><input type='text' size='40' name='community_name' value=''/></td>
            <td width='5%'>&nbsp;</td>
        </tr>
        <tr>
            <td style='vertical-align:top;'><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_ACCESS}:</td>
            <td>
            	<select name="access" onchange = 'changeAccessDesc(this.options[this.selectedIndex].value);'>
            		{if $type == "public"} <option value="public" selected>{$language.LBL_COMMUNITY_ACCESS_PUBLIC}</option>{/if}
            		{if $type == "public" || $type == "publicInviteOnly"}<option value="publicInviteOnly"{if $type == "publicInviteOnly"}selected {/if}>{$language.LBL_COMMUNITY_ACCESS_MODERATED}</option>{/if}
            		<option value="private" {if $type == "private"}selected {/if}>{$language.LBL_COMMUNITY_ACCESS_RESTRICTED}</option>
            	</select>
            	<b><span id='access_desription'> {$type_description}</span></b><br/>
            </td>
            <td><img src='custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/icons/help.PNG' title='{$language.LBL_HELP_ACCESS}'></img></td>
        </tr>
        
        <tr>
            <td style='vertical-align:top;'>{$language.LBL_ADD}:</td>
            <td>
		       {$language.LBL_PARENT_COMMUNITY_OWNERS}<br><br>
		       <input type="checkbox" name="add_all_parent_members" id="add_all_parent_members" checked onclick="subcommunityParentMembers(this);">{$language.LBL_ALL_PARENT_COMMUNITY_MEMBERS}</input>
		       <div id="member_autocomplete" style="display:none;">
				   <br>{$language.LBL_ADD_MEMBERS_FROM_PARENT_COMMUNITY}<br><br>
				   <select name="role" id="subcomm_member_role">
		        		<option value="member" selected>{$language.LBL_MEMBER_ROLE_MEMBER}</option>
		        		<option value="owner">{$language.LBL_MEMBER_ROLE_OWNER}</option>
		        	</select>
		        	&nbsp;
		        	
		        	<input type='text' size='28' name='member_name' id='subcomm_member_name'  onfocus = "addMemberInSubcommunityAutocomplete();"/>
            	</div>
            	
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td>
		        <div id='subcomm_member_list_div'>
						<select name="owner_list[]" id="subcomm_owner_list" multiple style="display:none;">
							{section name=members start=0 loop=$community_members step=1}
								{if $community_members[members].member_role == 'owner' }<option value="{$community_members[members].member_id}" selected></option>{/if}
							 {/section}
						</select>
						<select name="member_list[]" id="subcomm_member_list" multiple style="display:none;">
							{section name=members start=0 loop=$community_members step=1}
								{if $community_members[members].member_role == 'member' }<option value="{$community_members[members].member_id}" selected></option>{/if}
							 {/section}
						</select>
					</div>
            	&nbsp;
            </td>
            <td>&nbsp;</td>
        </tr>
        
        <tr>
            <td>{$language.LBL_TAGS}:</td>
            <td>
		        <input type='text' size='40' name='community_tags' value='' />
            </td>
            <td> <img src='custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/icons/help.PNG' title='{$language.LBL_HELP_TAGS}'></img></td>
        </tr>

 		<tr>
            <td style='vertical-align:top;'>{$language.LBL_DESCRIPTION}:</td>
            <td>
		        <textarea rows='10' cols='40' name='description'></textarea>
            </td>
            <td> &nbsp;</td>
        </tr>
        <tr style="display:none;">
            <td width='25%'>Logo:</td>
            <td width='70%'><input type='text' size='40' name='logo' value=''/></td>
            <td width='5%'>&nbsp;</td>
        </tr>
        
       <tr>
            <td colspan='3'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='3'><button class="ibm_button" onclick="if(subcommunityValidation()) {ldelim} document.getElementById('CreateSubcommunityForm').method.value='saveSubcommunity'; saveNewSubcommunity(); {rdelim} return false;" >{$language.LBL_SAVE}</button>
            <button class="ibm_button" onclick="closeCreationWindow();return false;" >{$language.LBL_CANCEL}</button></td>
        </tr>
    </table>
    </form>
</div>

