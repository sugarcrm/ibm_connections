<?php
if(!defined('sugarEntry'))define('sugarEntry', true);


function pre_uninstall() {
	$mapping = array('beans' => array());	
	require_once 'include/connectors/sources/SourceFactory.php';
	$source = SourceFactory::getSource('ext_eapm_connections');
	$source->saveMappingHook($mapping);
}

?>
