<div id='createBookmark'>
	<form action="index.php" method="POST" name="CreateBookmarkForm" id="CreateBookmarkForm">
	<input type='hidden' name='module' value='Connectors' />
	<input type='hidden' name='action' value='Connections' />
	<input type='hidden' name='method' value='saveBookmark' />
	<input type='hidden' name='to_pdf' value='1' />
	<input type='hidden' name='parent_type' value='{$parent_type}' />
	<input type='hidden' name='parent_id' value='{$parent_id}' />

		<div>
            <div><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_URL}:</div><div><input type='text' size='40' id='bookmark_path' name='bookmark_path' /></div>
        </div>
        <div>
            <div><span class="required">{$language.LBL_REQUIRED_SYMBOL}</span>{$language.LBL_NAME}:</div><div><input type='text' size='40' id='bookmark_name' name='bookmark_name' /></div>
        </div>
         
        <div>
            <div>{$language.LBL_DESCRIPTION}:</div><div><input type='text' size='40' id='bookmark_description' name='bookmark_description' /></div>
        </div>
       
        <div>
            <div>{$language.LBL_TAGS}:</div><div><input type='text' size='40' name='bookmark_tags' /></div>
        </div>
         <div>
            <div><input type='checkbox' name='bookmark_important' /></div><div>{$language.LBL_IS_IMPORTANT}</div>
        </div>
        <div>
            <div>&nbsp;</div>
        </div>
        <div>
            <div><button class="ibm_button" onclick='if(bookmarkValidation()) {ldelim} return createBookmark(); {rdelim} ' >{$language.LBL_SAVE}</button>
             <button class="ibm_button" onclick="closeCreationWindow();" >{$language.LBL_CANCEL}</button></div>
        </div>
    </form>
</div>
