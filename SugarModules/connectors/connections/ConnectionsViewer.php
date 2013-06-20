<?php

class ConnectionsViewer 
{
	
	public $language;
	public $img_url;
	public $connection_url;
	public $table_start = "<table cellspacing='0' cellpadding='0' border='0' width='100%' class='list'>";
	public $table_end = '</table>';
	public $scrollable_div_start = "<div class='div-modal'>";
	public $scrollable_div_end = "</div>";
			
	
	protected $type_image = array(
						'web' => " axs css htc htm html java js jsp php sct stm wml xml xsl ",   
						'pdf' => " ai eps ich ich6 iwp pdf ps ", 
						'txt' => " 323 asc bas c etx h jw log mcw msg ow pfs qa rtf sow text tsv tw txt uls vw3 ws xy ",
						'img' => " jpg jpeg png bmp cgm cmx cod drw dxf eshr gif hgs ico ief img jfif pbm pcx pgl pgm pnm pntg ppm psd psp6 ras rgb sdw svg svgz tga wbmp wpg xbm xpm xwd ",
						'doc' => " doc docm docx dot dotm dotx en4 enw leg lwp lwp7 manu msw mwp mwp2 mwpf odc odg odt ort otg oth odm ott pages pcl rtx std stw sxd sxq sxw w6 w97 wm wp5 wp6 wpf ",
						'pres' => " flw key mass odp ope otc otp pot potm potx pp2 pp97 ppam pps ppsm ppsx ppt pptm pptx prz shw3 sti sxi ",
						'spread' => " 123 12m acs csv dbs dez ens fcd fcs mwkd mwks odf ods oss otf ots qad smd sms stc sxc sxm wg2 wk4 wk6 xla xlam xlc xlm xls xlsb xlsm xlsx xlt xltm xltx xlw ",
						'archive' => " cab dmg gtar gz jar rar sit sqx tar taz tgz z zip ",
						'sound' => " aac aif aifc aiff au kar m3u m4a mid midi mp3 mpga ra ram rm rmi rpm snd wav wma ");
	
	
		
	
	public function navWrapStart()
	{
		return "<div id='ibm_navigator'>
					<nav class='ibm-actions-nav'>
						<ul>
							<li>
								<a href='#' onclick='return false;'>
									<div class='ibm-action_icon'><img src='{$this->img_url}/new/ico_actions.png'></div>
										<div class='ibm-action-label'>{$this->language['LBL_ACTIONS']}</div></a><ul>";
	}
	
	public function navWrapEnd()
	{
		 return "</ul></li></ul></nav></div>";
	}
	
	public function wiki($arr)
	{
		return "<tr>
					<td class ='elements' id='wiki_{$arr['id']}' onclick=\"return onloadWikiContent('{$arr['id']}','{$arr['content']}');\">
						<span><b>{$arr['title']}</b></span><br/>
					</td>
				</tr>";
	}
	

	
	public function getBusinesscard($user_array)
	{
		return '<span class="vcard">
  				<a class="fn url" target="_blank" href="'.$this->connection_url.'/profiles/html/profileView.do?userid='.$user_array['id'].'">' . $user_array['name'] . '</a>
  				<span class="x-lconn-userid" style="display: none;">' . $user_array['id'] . '</span>
			</span>
			';

	}
	public function update($arr )
	{
		return "<div class='{$arr['type']}'>
					<span >
						<img src='{$this->connection_url}/profiles/photo.do?userid={$arr['contributor']['id']}' width='32px' height='32px'>
					</span>
					<span>
						<span><b>{$arr['content']}</b></span><br/>
						{$arr['updated']}
					</span>
				</div>";
	}
	
	public function bookmark($arr)
	{
		return "<td style='padding-left: 15px;width:33%'>
					<span>
						<a href='{$arr['link']}' target='_blank'><b>{$arr['title']}</b></a>
					</span><br/>
					{$this->getBusinesscard($arr['contributor'])} | {$this->language['LBL_LAST_UPDATED']} {$arr['updated']}
				</td>";
				
	}
	
	public function activityReply($arr)
	{
		return "<tr>
					<td>
						{$this->language['LBL_COMMENT_BY']} {$this->getBusinesscard($arr['contributor'])} | {$arr['updated']} {$arr['content']}
					</td>
				</tr>";
				
	}
	public function discussion($arr)
	{
		if (!empty( $tags ) ) $tags = "{$this->language['LBL_TAGS']}: " . $arr['tags'];
		return 
			 "<tr >
				<td class ='elements' id='discussion_".$arr['id']."' onclick=\"return onloadDiscusionReplies('{$arr['id']}');\">
					{$arr['hiddenView']}
					<div class='item-img'>
							<img src='{$this->img_url}/icons/{$arr['topicType']}.PNG' /> 
					</div>
					<div class='item-label'>
						<!-- <a href='#' onclick=\"return viewDiscussion('{$arr['id']}');\"> -->
							<b>{$arr['title']}</b>
						<!-- </a> -->
					{$tags}<br/>
					{$this->language['LBL_LAST_POST_BY']} {$this->getBusinesscard($arr['contributor'])} {$arr['updated']} | 
					<span id='reply_count_{$arr['id']}'>{$arr['repliesCount']}</span> {$this->language['LBL_REPLIES']} 
					</div>
				</td>
			</tr>";
			
	}
	
	
	public function community($arr) 
	{
				$members = ($arr['member_count'] > 1) ? $arr['member_count']." ". $this->language['LBL_PEOPLE'] : "1 ". $this->language['LBL_PERSON'];
				$tag_str = "";
				if (count($arr['tags']) > 0) $tag_str = "<br/>{$this->language['LBL_TAGS']}: " . implode(', ', $arr['tags']);
				
				return "<tr>
							<td>
									<input type='radio' name='community' value='{$arr['title']}' onchange=\"document.getElementById('community_id_selection').value='{$arr['id']}';\" {$arr['checked']}>
										<img src='{$arr['logo']}' style='width:32px;height:32px;'/> 
										<a href='{$arr['link']}' target='_blank'><b>{$arr['title']}</b></a>
										<img src='{$this->img_url}/icons/{$arr['type']}.PNG' />
									</input><br/>
									{$members} | {$this->language['LBL_UPDATED_BY']} ".$this->getBusinesscard($arr['author'])." {$arr['updated']}
									". $tag_str."
								<br/>
							</td>
						</tr>";
	}
	
	
	
	
	
	public function discussionReply($arr)
	{
		$attachments = '';
		$attach_arr = $arr['attachments'];
		for($i = 0; $i < count($attach_arr); $i++){
			$attachments .= "<br>"."<a href='{$attach_arr[$i]['href']}'>{$attach_arr[$i]['title']}</a>";
			
		}
		return "<li level='{$arr['deep']}'>
				<div class='discussionComment' style='margin-left:". 15 * $arr['deep'] ."px;display:block;'>
					<div class='discussion-comments-item'>
						<div style='width:35px;'>
							<img src='{$this->connection_url}/profiles/photo.do?userid={$arr['author']['id']}' width='32px' height='32px'>
						</div>
						<div style='padding-left: 5px;width:20%;'>
							{$this->getBusinesscard($arr['author'])}<br>{$arr['updated']}
						</div>
						<div style='padding-left: 10px;'>
							<b>{$arr['title']}</b><br/>
							{$arr['content']}
							{$attachments}
							<br>
							<a href='#' onclick=\"createIBMElement('DiscussionReply','&ibm_parent_id={$arr['id']}&reply_to=reply&ibm_discussion_id={$arr['forum_id']}');return false;\">{$this->language['LBL_REPLY']}</a>
						</div>
					</div>
				</div>
				</li>
				";
	}
	
	public function discussionTitle($arr)
	{
		
		return "<div id='discussionTitle_{$arr['id']}' class='discussionTitle' style='margin-left:". 15 * $arr['deep'] ."px;display:none;'>
					<div class='discussion-comments-item'>
						<div style='width:35px;'>
							<img src='{$this->connection_url}/profiles/photo.do?userid={$arr['author']['id']}' width='32px' height='32px'>
						</div>
						<div style='padding-left: 5px;width:20%;'>
							{$this->getBusinesscard($arr['author'])}<br>{$arr['published']}
						</div>
						<div style='padding-left: 10px;'>
							<b>{$arr['title']}</b><br/>
							{$arr['content']}
						</div>
					</div>
				</div>
				";
	}
	
	
	
	public function activityToDo($arr)
	{
		$class = ($arr['completed']) ? "class=\"completed_todo\"" : "class=\"uncompleted_todo\"";
		$action = " onclick='markCompleted(\"{$arr['id']}\",\"{$arr['parent_id']}\");'";
		$type_content = "<div id='todo_{$arr['id']}' {$class} {$action} > </div>";
	
		$due_text = "";
		if (!empty($arr['dueDate'])) $due_text = " | due ". $arr['dueDate'];
		$att_icon = "";
		$comment_node_icon = "";
		$todo_node_icon = "";
		if ($arr['hasAttachments']) $att_icon = "<img src='{$this->img_url}/new/ico_attachmentBlue.png'>";
		if ($arr['hasTodo']) $todo_node_icon = "&nbsp;<img src='{$this->img_url}/new/ico_checkedBlue.png'/>";
		if ($arr['hasComment']) $comment_node_icon = " &nbsp;<img src='{$this->img_url}/new/ico_conversationBlue.png'/> ";
		
		$assignedText = "";
		if (!empty($arr['assignedTo']))
		{
			$assignedText = " &nbsp;{$this->language['LBL_ASSIGNED_TO']} ". $this->getBusinesscard($arr['assignedTo']) . " ";
		}
		
		return "<tr>
					<td>
						<div width='100%'>
							<span style='float:left;'>
								<div class='item-img'>
								{$type_content}
								</div>
								<div class='item-label'>
								<b>{$arr['title']}</b>
								</div> 
								{$assignedText} 
								{$due_text}
							</span> 
							<span style='float:right;'>
								{$todo_node_icon}
								{$att_icon} 
								{$comment_node_icon}
								<a href='#' onclick='modalActivityNode(\"{$arr['parent_id']}\",\"{$arr['id']}\");return false'>{$this->language['LBL_VIEW']}</a>
							</span><br/>
							
					</td>
				</tr>";
	}
	
	public function activityToDoModal($arr)
	{
		$icon = ($arr['completed']) ? 'ico_checkedBlueBig.png' :'ico_uncheckBlueBig.png';
		$type_content = "<img src='{$this->img_url}/new/{$icon}' id='todo_{$arr['id']}' />";
					
		$due_text = "";
		if (!empty($arr['dueDate'])) $due_text = " | due ". $arr['dueDate'];
		$att_icon = "";
		$comment_node_icon = "";
		$todo_node_icon = "";
		if ($arr['hasAttachments']) $att_icon = "<img src='{$this->img_url}/new/ico_attachmentBlue.png'>";
		if ($arr['hasTodo']) $todo_node_icon = "&nbsp;<img src='{$this->img_url}/new/ico_checkedBlue.png'/>";
		if ($arr['hasComment']) $comment_node_icon = " &nbsp;<img src='{$this->img_url}/new/ico_conversationBlue.png'/> ";
		
		$assignedText = "";
		if (!empty($arr['assignedTo']))
		{
			$assignedText = " {$this->language['LBL_ASSIGNED_TO']} ". $this->getBusinesscard($arr['assignedTo']) . " ";
		}
		
		return "<tr>
					<td >
						<div width='100%'>
							<span style='float:left;'>
								<div class='item-img'>
								{$type_content}
								</div>
								&nbsp;
								<div class='item-label'>
								<b>{$arr['title']}</b> 
								</div>
								{$assignedText} 
								{$due_text}
							</span> 
							<span style='float:right;'>
								{$todo_node_icon}
								{$att_icon} 
								{$comment_node_icon}
							</span><br/>
					</td>
				</tr>";
	}
	public function button($label, $click, $icon = '' )
	{
		$img = '';
		switch ($icon)
		{
			case 'add' : $img = '<div class="button-img"><img src="'.$this->img_url.'/new/ico_add.png" /></div>'; break;
			case 'newActivity' : $img = '<div class="button-img"><img src="'.$this->img_url.'/new/ico_newActivity.png" /></div>'; break;
		}
		return "<button class='ibm_button' onclick='{$click}'> {$img}&nbsp;<div class='button-label'>{$this->language[$label]}</div> </button>&nbsp;";
	}
	
	public function actionsEditCommunity($communityId)
	{
		$res = "<a href='#' onclick='if (document.getElementById(\"community_id_selection\") != null) document.getElementById(\"community_id_selection\").value=\"\";selectCommunity();return false;'>
										<div class='ibm_button ico_close'>
											
										</div>
											{$this->language['LBL_CLOSE']}
									</a>";
		if (!empty($communityId)) $res .= "
									<a href='#' onclick='editIBMElement(\"Community\",\"$communityId\");return false;'>
										<div class='ibm_button ico_edit'>
											
										</div>
											{$this->language['LBL_EDIT']}
									</a>
									<a href='#' onclick='deleteCommunity(\"$communityId\");return false;'>
										<div class='ibm_button ico_delete'>
											
										</div>
										{$this->language['LBL_DELETE']}
									</a>		";
		return $res;
	}
	

	public function activityEntry($arr)
	{
		$att_icon = "";
		$comment_node_icon = "";
		$todo_node_icon = "";
		if ($arr['hasAttachments']) $att_icon = "<img src='{$this->img_url}/new/ico_attachmentBlue.png'>";
		if ($arr['hasTodo']) $todo_node_icon = "&nbsp;<img src='{$this->img_url}/new/ico_checkedBlue.png'/>";
		if ($arr['hasComment']) $comment_node_icon = " &nbsp;<img src='{$this->img_url}/new/ico_conversationBlue.png'/> ";
		
		return "<tr>
					<td >
						<div width='100%'>
							<span style='float:left;'>
								<div class='item-img'><img src='{$this->img_url}/new/ico_entryBlue.png'></img></div>&nbsp;
								<div class='item-label'><b>{$arr['title']}</b></div> 
								{$this->language['LBL_BY']} {$this->getBusinesscard($arr['contributor'])} {$arr['updated']}
							</span> 
							<span style='float:right;'>
								{$todo_node_icon}
								{$att_icon} 
								{$comment_node_icon}
								<a href='#' onclick='modalActivityNode(\"{$arr['parent_id']}\",\"{$arr['id']}\");return false'>{$this->language['LBL_VIEW']}</a>
							</span><br/>
					</td>
				</tr>";
	}
	public function activityEntryModal($arr)
	{
		$att_icon = "";
		$comment_node_icon = "";
		$todo_node_icon = "";
		if ($arr['hasAttachments']) $att_icon = "<img src='{$this->img_url}/new/ico_attachmentBlue.png'>";
		if ($arr['hasTodo']) $todo_node_icon = "&nbsp;<img src='{$this->img_url}/new/ico_checkedBlue.png'/>";
		if ($arr['hasComment']) $comment_node_icon = " &nbsp;<img src='{$this->img_url}/new/ico_conversationBlue.png'/> ";
		
		return "<tr>
					<td >
						<div width='100%'>
							<span style='float:left;'>
								<div class='item-img'><img src='{$this->img_url}/new/ico_entryBlue.png'></img></div>
								<div class='item-label'><b>{$arr['title']}</b></div> 
								{$this->language['LBL_BY']} {$this->getBusinesscard($arr['contributor'])} {$arr['updated']}
							</span> 
							<span style='float:right;'>
								{$todo_node_icon}
								{$att_icon} 
								{$comment_node_icon}
							</span><br/>
					</td>
				</tr>";
	}
	public function selectCommunityMenu()
	{
		return "<li class='select-community'>
					<a href='#' onclick='showCommunities();return false;'>
						<div class='ibm_button'>

							{$this->language['LBL_SELECT_COMMUNITY_BUTTON']}
						</div>
					</a>
				</li>";
	}
	public function activitySection($arr)
	{
		return "
				<tr>
					<td>
						<div class='before-section'>
							<table class='activity-section' cellspacing='0' cellpadding='0'>
								<tr class='header-section'> 
									<td>
									<span  onclick=\"collapseSection('{$arr['id']}')\">
										<img src='{$this->img_url}/icons/expanded.PNG' id='expanded_icon_{$arr['id']}'>
										<img src='{$this->img_url}/icons/collapsed.PNG' id='collapsed_icon_{$arr['id']}' style='display:none;'>
									</span>
									&nbsp;<b>{$arr['title']}</b>
						
									&nbsp;<a href='#' onclick=\"createIBMElement('ActivityToDo','&ibm_parent_id={$arr['parent_id']}&ibm_section_id={$arr['id']}');return false;\">{$this->language['LBL_ADD_TODO']}</a>
									&nbsp;<a href='#' onclick=\"createIBMElement('ActivityEntry','&ibm_parent_id={$arr['parent_id']}&ibm_section_id={$arr['id']}');return false;\">{$this->language['LBL_ADD_ENTRY']}</a>	
									</td>	
								</tr>
								 <tr id='section_{$arr['id']}'>
									 <td>
										<table width='100%'> 
											{$arr['content']}
						
										</table>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>";
	}
	
	
	public function discussionModal($arr)
	{
		$attachments = '';
		$attach_arr = $arr['attachments'];
		for($i = 0; $i < count($attach_arr); $i++){
			$attachments .= "<br>"."<a href='{$attach_arr[$i]['href']}'>{$attach_arr[$i]['title']}</a>";
			
		}
		
		return "<tr>
					<td style='padding-left: ". 15 * $arr['deep'] ."px;border-bottom: solid 1px grey;width:32px;'>
						<img src='{$this->connection_url}/profiles/photo.do?userid={$arr['author']['id']}' width='32px' height='32px'>
					</td>
		 			<td style='padding-left: 5px;border-bottom: solid 1px grey;'>
						".$this->getBusinesscard($arr['author'])."<br>{$arr['updated']}
					</td>
					<td style='padding-left: 5px;border-bottom: solid 1px grey;'>
						<span> &nbsp;<b>{$arr['title']}</b></span><br/>
						{$arr['content']}{$attachments}
					</td>
				</tr>";
	}


	public function fileSection($arr)
	{
	
				$download_link = "index.php?module=Connectors&action=Connections&method=downloadFile&documentId={$arr['id']}&documentName=".htmlspecialchars($arr['title'], ENT_QUOTES)."&documentSize={$arr['fileSize']}";
				$title = $arr['title'];
				$pos = strrpos($title, "."); 
				if ($pos !== false) {
					$type = substr($title,$pos + 1);
				}
				$icon_name = "undefined";
				if (!empty($type)) {
					foreach ($this->type_image as $key => $val){
						if ( strpos($val,' '. $type .' ') !== false){
							$icon_name = $key;
							break;
						}
					}
				}
				$tags = '';
				if (strlen($arr['tags']) > 0 ) $tags = $this->language['LBL_TAGS'].': '.$arr['tags'];
				return "<tr class='elements'>
							<td>
									<div class='item-img'><a href=\"{$download_link}\"><img src='{$this->img_url}/file_types/{$icon_name}.PNG' /></a></div>
									<div class='item-label'>
									<a href='{$download_link}'><b>{$arr['title']}</b></a>
								&nbsp;<img src='{$this->img_url}/icons/{$arr['visibility']}.PNG' title='{$arr['visibility']}'/><br/>
								<span id='like_{$arr['id']}'>{$arr['recomendationsCount']} </span>
								<a href='#' onclick='likeFile(\"{$arr['author']['id']}\",\"{$arr['id']}\");return false;'>{$this->language['LBL_LIKE']}</a> 
								<span id='comment_{$arr['id']}'>{$arr['commentsCount']} </span>
								<a href='#' onclick='createIBMElement(\"CommentFile\", \"&ibm_user_id={$arr['author']['id']}&ibm_doc_id={$arr['id']}\"); return false;'>{$this->language['LBL_COMMENT']}</a> 
								{$this->getBusinesscard($arr['author'])}
								{$this->language['LBL_UPLOADED']} {$arr['uploaded']} | 
								version {$arr['version']}| {$tags}
								{$arr['downloadsCount']} <img src='{$this->img_url}/icons/downloads.PNG'></img>  | 
								<a href='{$arr['viewLink']}' target='_blank'>{$this->language['LBL_VIEW']}</a> | <a href='#' onclick='deleteFile(\"{$arr['id']}\");return false;'>{$this->language['LBL_DELETE']}</a>
								</div>
							</td>
						</tr>";

	}
	
	public function activity($arr)
	{
		$due_text = "";
		$comments_text = "";
		$completion_text = "";
		if ($arr['commentsCount']) {
			$comments_text = " " . $arr['commentsCount'] ."<img class='img-in-text' src='{$this->img_url}/new/ico_conversation.png' />";
		}
		if ($arr['completion'] !== null) {
			$completion_text = "&nbsp;<b><span id='activity_completion_{$arr['id']}'>" . $arr['completion'] ."</span>%</b>";
		}
		if (!empty($arr['dueDate'])) $due_text = " | {$this->language['LBL_DUE']} " . $arr['dueDate'];
		$content = (!empty($arr['content'])) ? " - " . $arr['content'] ."" : '';	
		$completed_img = ($arr['completed']) ? "ico_checked.png": "ico_uncheck.png" ; 
		
		return "<tr id='activity_{$arr['id']}' class='elements'>
		
					<td onclick='onloadActivityView(\"{$arr['id']}\")'>
						<div class='item-img'>
							<img src='{$this->img_url}/new/{$completed_img}'>
						</div>
						<div class='item-label'>
							<span>
								<a href='{$arr['webEditUrl']}' target='_blank'>{$arr['title']}</a> {$content}
							</span>  ". $completion_text . $comments_text."<br/>
							
						{$this->language['LBL_UPDATED_ON']} {$arr['last_updated']} {$this->language['LBL_BY']} ".$this->getBusinesscard($arr['contributor']).$due_text . "
						</div>
						<div class='active-arrow'>
						</div>
						
					</td>

				</tr>";
	}
	
	
	public function blog($arr)
	{
		return "<tr>
					<td class ='elements' id='blog_".$arr['id']."' onclick=\"return onloadBlogComments('{$arr['id']}');\">
						<b><span>{$arr['title']}</span></b>
						<span id='blog_content_{$arr['id']}'  style='display:none;'>{$arr['content']}</span><br/>
						{$this->language['LBL_UPDATED_ON']} {$arr['last_updated']} {$this->language['LBL_BY']} {$this->getBusinesscard($arr['contributor'])}
					 | " . $arr['commentsCount']. " ". $this->language['LBL_COMMENT'] . "<br>
					</td>
				</tr>";
	}
	
	public function member($arr, $isCommunityOwner = false)
	{
		$info = array('id' => $arr['member_id'] ,'name' => $arr['member_name']);
		$view = "<div id='ibm_member_{$arr['member_id']}' class='ibm-member'>
						<div>
						<img src='{$this->connection_url}/profiles/photo.do?userid={$arr['member_id']}' width='48px' height='48px' style='vertical-align:top;' >
						</div>
						<div>
						{$this->getBusinesscard($info)}
					 	<br /> " . $this->language['LBL_MEMBER_ROLE_'. strtoupper($arr['member_role'])];
		if ($isCommunityOwner) {
			$view .= "	<br /> <a href='#'onclick=\"return removeMember('{$arr['member_id']}');\">" . $this->language['LBL_REMOVE'] ."</a>";
		}
		$view .= "
					 	</div>
				</div>		";
		return $view;
		
	}
	
	
}
