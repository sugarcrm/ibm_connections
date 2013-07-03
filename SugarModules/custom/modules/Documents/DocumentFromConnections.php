<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('custom/modules/Documents/TreeData.php');

function createDocument() {
	$retId = '';
	$associated_row_data = array();
	 if (isset($_REQUEST['fileId']) && !empty($_REQUEST['fileId'])) {
	 	$fileId = $_REQUEST['fileId'];
	 	require_once('modules/Documents/Document.php');
	 	$doc = new Document();
	 	$query = "SELECT id FROM documents WHERE doc_id='{$fileId}' and deleted=0";
	 	$id = $doc->db->getOne($query);
	 	if (!empty($id)) {
	 		$doc->retrieve($id);
	 	}
	 	if (empty($doc->id)) {
	 		$api = getAPI();
	 		$fileInfo = $api->getFileDetails($fileId, $_REQUEST['library']);
	 		$doc->doc_type = 'Connections';
	 		
	 		$doc->doc_url = $fileInfo->getFileLink();
	 		$doc->doc_id = $fileId;
	 		$doc->filename = $fileInfo->getTitle();
	 		$doc->document_name = $fileInfo->getTitle();
	 		$doc->file_mime_type = $fileInfo->getMimeType();
	 		if (strrpos($fileInfo->getTitle(), '.' ) !== false) {
	 			$doc->file_ext = substr($fileInfo->getTitle(), strrpos($fileInfo->getTitle(), '.') + 1);
	 		}
	 		$doc->status_id = 'Active';
	 		$doc->save();
	 	}
	 	$retId = $doc->id;
	 	$list_fields = $doc->list_fields;
	 	$fields=array();
	 	foreach($list_fields as $field)
	 	{
	 		$fields[strtoupper($field)] = $doc->$field;
	 	}
	 	
	 	if(isset($fields['ID'])) {
                $associated_row_data[$fields['ID']] = $fields;
                // Bug 38908: cleanup data for JS to avoid having &nbsp; shuffled around
                foreach($fields as $key => $value) {
                    if($value == '&nbsp;') {
                        $associated_row_data[$fields['ID']][$key] = '';
                    }
                }
            }
	 }
	 $is_show_fullname = showFullName() ? 1 : 0;
	 echo json_encode(array('docId'=> $retId, 'ajd'=>$associated_row_data, 'show_full_name' =>$is_show_fullname));
}

function getDocumentList($page){
	$api = getAPI();
	$files = array();
	if (isset($_REQUEST['folderId']) && !empty($_REQUEST['folderId'])  )
	{
		$res = $api->getFilesFromFolder($_REQUEST['folderId'], $page);
		$files = $res['files'];
		$data = $res['metadata'];
	}
	echo json_encode(array('files' =>  buildDocumentList($files, $data)));
} 

function getFolders($folderType){
	$str = get_subfolders($folderType);
	if (!empty($str)) {
		echo $str;
	}
}

function getPagination($data) 
	{
		if(!empty($data['totalResults']))
			$totalResults = $data['totalResults'];
		else $totalResults = 0;
		if(!empty($data['itemsPerPage']))
			$itemsPerPage = $data['itemsPerPage'];
		else $itemsPerPage = 10;
		if(!empty($data['startIndex']))
			$startIndex = $data['startIndex'];
		else $startIndex = 1;

		$totalPages = ceil($totalResults / $itemsPerPage);

		if($totalPages == 0)
			$current_page = 0;
		elseif($totalPages > 1)
			$current_page = ceil($startIndex / $itemsPerPage);
		else
			$current_page = 1;
		$endIndex = $startIndex + $itemsPerPage - 1;
		if ($endIndex > $totalResults) $endIndex = $totalResults;
		if ($endIndex < 0) $endIndex = 0;
		if ($totalResults == 0) $startIndex = $totalResults;
		
		$pageCount = "<span class='pageNumbers'>({$startIndex} - {$endIndex} of {$totalResults})</span>";
		if($current_page > 1) {
			$start_button_src = SugarThemeRegistry::current()->getImageURL('start.png');
			$start_page = 1;
			$start_button_onclick = "onclick=getDocumentList('{$start_page}')";
			$start_button_disabled = "";
		}
		else {
			$start_button_onclick = "";
			$start_button_src = SugarThemeRegistry::current()->getImageURL('start_off.png');
			$start_button_disabled = "disabled='disabled'";
		}

		if($current_page > 1) {
			$previous_button_src = SugarThemeRegistry::current()->getImageURL('previous.png');
			$previous_page = $current_page - 1;
			$prev_button_onclick = "onclick=getDocumentList('{$previous_page}');";
			$prev_button_disabled = "";
		}
		else {
			$prev_button_onclick = "";
			$previous_button_src = SugarThemeRegistry::current()->getImageURL('previous_off.png');
			$prev_button_disabled = "disabled='disabled'";
		}

		if($current_page < $totalPages) {
			$next_button_src = SugarThemeRegistry::current()->getImageURL('next.png');
			$next_page = $current_page + 1;
			$next_button_onclick = "onclick=getDocumentList('{$next_page}');";
			$next_button_disabled = "";
		}
		else {
			$next_button_onclick = "";
			$next_button_src = SugarThemeRegistry::current()->getImageURL('next_off.png');
			$next_button_disabled = "disabled='disabled'";
		}

		if($current_page < $totalPages) {
			$end_button_src = SugarThemeRegistry::current()->getImageURL('end.png');
			$end_page = $totalPages;
			$end_button_onclick = "onclick=getDocumentList('{$end_page}');";
			$end_button_disabled = "";
		}
		else {
			$end_button_onclick = "";
			$end_button_src = SugarThemeRegistry::current()->getImageURL('end_off.png');
			$end_button_disabled = "disabled='disabled'";
		}

		$start_button = "<button type='button' id='' name='listViewStartButton' title='Start' class='button' {$start_button_disabled} {$start_button_onclick}><img src='{$start_button_src}' align='absmiddle' /></button>";
		$previous_button = "<button type='button' name='listViewPrevButton' title='Previous' class='button' {$prev_button_disabled} {$prev_button_onclick}><img src='{$previous_button_src}' align='absmiddle' /></button>";
		$next_button = "<button type='button' name='listViewNextButton' title='Next' class='button' {$next_button_disabled} {$next_button_onclick}><img src='{$next_button_src}' align='absmiddle' /></button>";
		$end_button = "<button type='button' name='listViewEndButton' title='End' class='button' {$end_button_disabled} {$end_button_onclick}><img src='{$end_button_src}' align='absmiddle' /></button>";

		$pagination = "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td align='right'>{$start_button} {$previous_button} {$pageCount} {$next_button} {$end_button}</td></tr></tbody></table>";

		return $pagination;
	}

function buildDocumentList($files, $data){
	$html = '';
	$html .= '<table cellpadding="0" cellspacing="0" width="100%" border="0" class="list view">
				<tr class="pagination" role="presentation">
					<td colspan="20" align="right">';
	$html .= getPagination($data);
	$html .= "		</td>
				</tr>";
	$sign = 1;
	foreach($files as $file) {
		$class = ($sign > 0) ? 'oddListRowS1' : 'evenListRowS1';
		$sign *= -1;
		$html .= "<tr height='20' class='{$class}'>
    <td colspan='8'>
        <a href='#' onclick='select_document(\"". $file->getId()."\", \"". $file->getLibraryId() ."\");return false;'>" .$file->getTitle(). "</a>
    </td>
</tr>";
	}
	$html .= "</table>";
	return $html;
}

//////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST['mode']) && !empty($_REQUEST['mode'])) {
	if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
		$page = $_REQUEST['page'];
	}
	else {
		$page = 1;
	}
	switch ($_REQUEST['mode']) {
		case 'create': createDocument(); break;
		case 'getFiles': getDocumentList($page); break;
		case 'getFolders': getFolders($_REQUEST['folderType']); break;
	}
}
 
 
 ?>
