<div id='createCommunity'>
	<form action="index.php" method="POST" name="CreateCommunity" id="CreateCommunity">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='saveCommunity' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_id' value='{$community_id}' />

	<table width='100%'>
        <tr>
            <td width='25%'><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_COMMUNITY_NAME}:</td>
            <td width='70%'><input type='text' size='40' name='community_name' value='{$name}'/></td>
            <td width='5%'>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_ACCESS}:</td>
            <td>
            	<select name="access" onchange = 'changeAccessDesc(this.options[this.selectedIndex].value);' >
            		<option value="public" {if $type == "public"}selected{/if}>{$language.LBL_COMMUNITY_ACCESS_PUBLIC}</option>
            		<option value="publicInviteOnly"{if $type == "publicInviteOnly"}selected{/if}>{$language.LBL_COMMUNITY_ACCESS_MODERATED}</option>
            		<option value="private" {if $type == "private"}selected{/if}>{$language.LBL_COMMUNITY_ACCESS_RESTRICTED}</option>
            	</select>
            	<span id='access_desription'>{$type_description}</span>
            </td>
            <td><img src='custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/icons/help.PNG' title='{$language.LBL_HELP_ACCESS}'></img></td>
        </tr>
        
        <tr>
            <td>{$language.LBL_ADD}:</td>
            <td>
		        	 <!-- <div class='scroll'>
		        {foreach from=$members key=connectionsId item=userName}
		        <input type='checkbox' name='members[]' value='{$connectionsId}' /> {$userName}<br/>
		        {/foreach}
		        </div>-->
		       <select name="role" id="ibm_member_role">
            		<option value="member" selected>{$language.LBL_MEMBER_ROLE_MEMBER}</option>
            		<option value="owner">{$language.LBL_MEMBER_ROLE_OWNER}</option>
            	</select>
            	&nbsp;
            	<input type='text' size='28' name='member_name' id='member_name'  onfocus = "addMemberAutocomplete();"'/>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td>
		        <div id='member_list_div'>
						<select name="owner_list[]" id="owner_list" multiple style="display:none;">
							{section name=members start=0 loop=$community_members step=1}
								{if $community_members[members].member_role == 'owner' }<option value="{$community_members[members].member_id}" selected></option>{/if}
							 {/section}
						</select>
						<select name="member_list[]" id="member_list" multiple style="display:none;">
							{section name=members start=0 loop=$community_members step=1}
								{if $community_members[members].member_role == 'member' }<option value="{$community_members[members].member_id}" selected></option>{/if}
							 {/section}
						</select>
						
						{section name=members start=0 loop=$community_members step=1}
							<span style=" border: 1px solid #999999;background-color: #fafafa;padding: 1px 0 1px 5px;display:inline;" id="{$community_members[members].member_id}_{$community_members[members].member_role}"> 
								<span  style="border-right-style: 1px solid #999999;" onclick="removeMemberFromList('{$community_members[members].member_id}', '{$community_members[members].member_role}')";> X </span> 
								{$community_members[members].member_name} ({$community_members[members].member_role})
							</span>&nbsp;
						{/section}
						
					</div>
            	&nbsp;
            </td>
            <td>&nbsp;</td>
        </tr>
        
        <tr>
            <td>{$language.LBL_TAGS}:</td>
            <td>
		        <input type='text' size='40' name='community_tags' value='{$tags}' />
            </td>
            <td> <img src='custom/modules/Connectors/connectors/sources/ext/eapm/connections/images/icons/help.PNG' title='{$language.LBL_HELP_TAGS}'></img></td>
        </tr>

 		<tr>
            <td>{$language.LBL_DESCRIPTION}:</td>
            <td>
		        <textarea rows='10' cols='40' name='description'>{$description}</textarea>
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
            <td colspan='3'><button class="ibm_button" onclick="if(communityValidation()) {ldelim} return saveNewCommunity(); {rdelim}" >{$language.LBL_SAVE}</button>
            <button class="ibm_button" onclick="closeCreationWindow();closeCommunityPanel();" >{$language.LBL_CANCEL}</button></td>
        </tr>
    </table>
    </form>
</div>

