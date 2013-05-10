<div id='commentFile'>
	<form action="index.php" method="POST" name="CommentFileForm" id="CommentFileForm" enctype="multipart/form-data">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='commentFile' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />
	<input type='hidden' name='ibm_user_id' value='{$user_id}' />
	<input type='hidden' name='ibm_doc_id' value='{$document_id}' />

         <div>
            <div><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_COMMENT}:</div>
            <div><textarea id='comment_content' name='comment_content' ></textarea></div>
        </div>
        <div>
            <div>&nbsp;</div>
        </div>

        <div>
            <div><button class="ibm_button" onclick='if(commentFileValidation()) {ldelim} return commentFile(); {rdelim}  ' > {$language.LBL_SAVE}</button>
            <button class="ibm_button" onclick="closeCreationWindow();" > {$language.LBL_CANCEL}</button></div>
        </div>
    </form>
</div>
