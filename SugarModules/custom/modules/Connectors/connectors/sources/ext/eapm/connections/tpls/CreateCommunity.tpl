<div id='createCommunity'>
	<form action="index.php" method="POST" name="CreateCommunity" id="CreateCommunity">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='saveNewCommunity' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />

    <table cellpadding='0' cellspacing='0' border='0' width='30%' class='edit view'>
        <tr>
            <th colspan='2'>{$language.LBL_CREATE_NEW_COMMUNITY}</th>
        </tr>
        <tr>
            <td>{$language.LBL_COMMUNITY_NAME}:</td><td><input type='text' size='35' name='community_name' /></td>
        </tr>
        <tr>
            <td>{$language.LBL_TAGS}:</td><td><input type='text' size='35' name='community_tags' /></td>
        </tr>
        <tr>
            <td>{$language.LBL_IS_PUBLIC}:</td><td><input type='checkbox' name='public_community' /></td>
        </tr>
        <tr>
            <td>{$language.LBL_MEMBERS}:</td>
            <td>
            <div class='scroll'>
            {foreach from=$members key=connectionsId item=userName}
            <input type='checkbox' name='members[]' value='{$connectionsId}' /> {$userName}<br/>
            {/foreach}
            </div>
            </td>
        </tr>
        <tr>
            <td>{$language.LBL_DESCRIPTION}:</td><td><textarea rows='10' cols='40' name='description'></textarea></td>
        </tr>
        <tr>
            <td colspan='2'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='2'><input type='button' value='Save' onclick='saveNewCommunity();' /> <input type='button' value='Cancel' onclick="closeTab('CreateCommunity');" /></td>
        </tr>
    </table>
    </form>
</div>