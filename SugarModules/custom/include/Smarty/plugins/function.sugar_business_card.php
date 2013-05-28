<?php

function smarty_function_sugar_business_card($params, &$smarty)
{	
	if (!empty($params['user_id'])) {
		$eapmBean = new EAPM();
		$eapm = $eapmBean->retrieve_by_string_fields(array(
			'assigned_user_id' => $params['user_id'],
			'application' => "Connections",
			'deleted' => 0
		));
	}
	if (!empty($params['user_id']) && !empty($eapm->api_data)) {
	
		require "custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php";
		$connectionsUrl = $config['properties']['company_url'];
	
		$apiData = json_decode(base64_decode($eapm->api_data), true);
		if (isset($apiData['userId'])) {
			$ibmUserId = $apiData['userId'];
		}
		$userUrl =  $connectionsUrl . "/profiles/html/profileView.do?userid={$ibmUserId}";
		
		$containerId = $params['user_id'] . "_" . rand(1000, 9999);
		
		$html = "";
		if (empty($GLOBALS['ibmBusinessCardJsDisplayed'])) {			
			$html .= '
				<script src="http://download.dojotoolkit.org/dojo_0.3.1.js"></script>			
				<script type="text/javascript" src="'.$connectionsUrl.'/profiles/ibm_semanticTagServlet/javascript/semanticTagService.js?loadCssFiles=false&inclDojo=false"></script>
				<link rel="stylesheet" href="custom/modules/Connectors/connectors/sources/ext/eapm/connections/css/ProfileCard.css" type="text/css"/>
			';
			$GLOBALS['ibmBusinessCardJsDisplayed'] = true;
		}	
			
		$html .= "
			<script type=\"text/javascript\">				
				SUGAR.util.doWhen(function () {
					return typeof SemTagSvc != 'undefined';
				}, function () {
					setTimeout(function () {											
						SemTagSvc.parseDom(null, '{$containerId}');
					}, 500);
				});
			</script>
		";
		
		$html .= "
			<div id=\"{$containerId}\">
			<span class=\"vcard\">
				<a class=\"fn url\" target=\"_blank\" href=\"{$userUrl}\">{$params['user_name']}</a>
				<span class=\"x-lconn-userid\" style=\"display: none;\">{$ibmUserId}</span>
			</span>
			</div>
		";
		
		return $html;
	}
	
	return $params['user_name'];	
}
