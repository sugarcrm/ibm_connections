<?php
if(!defined('sugarEntry'))define('sugarEntry', true);

require_once('modules/Administration/QuickRepairAndRebuild.php');

function post_install() {
   $randc = new RepairAndClear();
   $randc->repairAndClearAll(array('clearAll'),array(translate('LBL_ALL_MODULES')), false, false);
   
	$fileNameBase = 'modules/Connectors/metadata/display_config.php';
	$fileName = get_custom_file_if_exists($fileNameBase);
	include($fileName);
	$fileName = "custom/" . $fileNameBase;
	$moduleList = array(
		'Accounts',
		'Opportunities',
		'Cases',
	);
	
	$mapping = array(
		'beans' => array(),
	);
	
	foreach ($moduleList as $moduleName) {
		$modules_sources[$moduleName]['ext_eapm_connections'] = 'ext_eapm_connections';
		$mapping['beans'][$moduleName] = true;
	}
	write_array_to_file('modules_sources', $modules_sources, $fileName);
	
	require_once 'include/connectors/sources/SourceFactory.php';
	$source = SourceFactory::getSource('ext_eapm_connections');
	$source->saveMappingHook($mapping);
}

?>
