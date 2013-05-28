<?php

require_once('include/SugarFields/Fields/Relate/SugarFieldRelate.php');

class CustomSugarFieldRelate extends SugarFieldRelate
{

	function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
	{
	   	if ($vardef['module'] == "Users" || $vardef['module'] == "Employees") {
	   		$this->ss->assign('nolink', true);
			$this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
		    return $this->fetch('custom/include/SugarFields/Fields/Relate/BusinessCardDetailView.tpl');			
		} else {
			return parent::getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
		}
	}
	
	function getListViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
	{		
		if (!empty($vardef['module']) && ($vardef['module'] == "Users" || $vardef['module'] == "Employees")) {	
			$this->setup($parentFieldArray, $vardef, $displayParams, $tabindex, false);
		    $this->ss->left_delimiter = '{';
		    $this->ss->right_delimiter = '}';
		    $this->ss->assign('nameField', $vardef['name']);
		    $this->ss->assign('idField', strtoupper($vardef['id_name']));
		    return $this->fetch('custom/include/SugarFields/Fields/Relate/BusinessCardListView.tpl');			
		} else {
			return parent::getListViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
		}
	}

 
}
