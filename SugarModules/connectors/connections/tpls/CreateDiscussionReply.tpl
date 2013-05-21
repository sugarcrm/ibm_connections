<div id='replyDiscussion'>
	<form action="index.php" method="POST" name="ReplyDiscussionForm" id="ReplyDiscussionForm">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_parent_id' value='{$ibm_parent_id}' />
	<input type='hidden' name='reply_to' value='{$reply_to}' />
	<input type='hidden' name='ibm_discussion_id' value='{$ibm_discussion_id}' />

 
        <div>
            <div>{$language.LBL_TITLE}:</div><div><input type='text' size='40' id='reply_title' name='reply_title' /></div>
        </div>
        <div>
            <div><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_CONTENT}:</div><div><textarea id='reply_content' name='reply_content' ></textarea></div>
        </div>
        <div>
            <div>&nbsp;</div>
        </div>
        
        <div>
            <div>
            <button class="ibm_button" onclick="if(replyDiscussionValidation()) {ldelim} document.getElementById('ReplyDiscussionForm').method.value='replyDiscussion'; return replyDiscussion(); {rdelim} "> {$language.LBL_SAVE}</button> 
            <button class="ibm_button" onclick="closeCreationWindow(); return false;" >{$language.LBL_CANCEL}</button></div>
        </div>
    </form>
</div>
