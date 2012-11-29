<?php
if(!defined('sugarEntry'))define('sugarEntry', true);

require_once('modules/Administration/QuickRepairAndRebuild.php');

function post_install() {
   $randc = new RepairAndClear();
   $randc->repairAndClearAll(array('clearAll'),array(translate('LBL_ALL_MODULES')), false, false);
}

?>
