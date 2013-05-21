<div id='createDiscussion'>
	<form action="index.php" method="POST" name="CreateDiscussionForm" id="CreateDiscussionForm">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />

 
        <div>
            <div><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_NAME}:</div><div><input type='text' size='40' id='discussion_name' name='discussion_name' /></div>
        </div>
         <div>
            <div>Mark as question:</div><div><input type='checkbox' id='discussion_is_question' name='discussion_is_question' /></div>
        </div>
       
        <div style="display:none;">
            <div>{$language.LBL_TAGS}:</div><div><input type='text' size='40' name='discussion_tags' /></div>
        </div>
        <div>
            <div>{$language.LBL_CONTENT}:</div><div><textarea id='discussion_content_form' name='discussion_content_form' ></textarea></div>
        </div>
        <div>
            <div>&nbsp;</div>
        </div>
        
        <div>
            <div><button class="ibm_button" onclick="if(discussionValidation()) {ldelim} document.getElementById('CreateDiscussionForm').method.value='saveDiscussion';return createDiscussion(); {rdelim}" >{$language.LBL_SAVE} </button>
            <button class="ibm_button" onclick="closeCreationWindow();return false;" >{$language.LBL_CANCEL}</button></div>
        </div>
    </form>
</div>
