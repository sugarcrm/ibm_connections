<?php
if(!defined('sugarEntry'))define('sugarEntry', true);

require_once('include/entryPoint.php');

class OpportunityLoader {

	public $user_id;
	public $user;
	public $opportunities;

	// Setup the variables
	public function __construct() {
		global $current_user;
		if ($this->validRequest()) {
			$this->loadUser();
			$this->loadOpportunities();
		}
	}
	
	public function toJSON() {
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode(array("opportunities" => $this->opportunities));
	}

	protected function validRequest() {
		if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
			$this->user_id = $_GET['user_id'];
			return true;
		}
		die("Please specify a user_id");
	}

	protected function loadUser() {
		require_once('data/BeanFactory.php');
		$current_user = new User();
		$current_user->retrieve($this->user_id);
		$this->user   = $current_user; 
	}

	protected function loadOpportunities() {
		$bean = BeanFactory::getBean('Opportunities');
		$opportunities = array();
		$opportunities = $bean->get_full_list("date_closed", "opportunities.assigned_user_id = '" . $this->user_id . "'");
		$this->opportunities = array();
		foreach ($opportunities as $opportunity) {
			$this->opportunities[] = $this->OpportunityToJSON($opportunity);
		}
	}
	
	private function siteUrl() {
		// Returns the site URL without whitespace, or a trailing /
		global $sugar_config; 
		return trim(rtrim($sugar_config['site_url'], '/')); 
	}
	
	private function beanToUrl(SugarBean $bean) {
		// Returns a URL Path to the Bean, so an Opportunity with an ID of 123456 would return:
		// http://localhost/sugarcrm/index.php?action=DetailView&module=Opportunities&record=123456
		return $this->siteUrl() . "/index.php?action=DetailView&module=" . $bean->module_dir . "&record=" . $bean->id;
	}
	
	private function OpportunityToJSON(Opportunity $opportunity) {
		$opportunity->load_relationship('accounts');
		$accounts = $opportunity->accounts->getBeans();
		$account  = array_shift($accounts);
		return array(
			"id" 		=> $opportunity->id,
			"url"		=> $this->beanToUrl($opportunity),
			"name"		=> $opportunity->name,
			"amount"	=> $opportunity->amount,
			"date_closed" => $opportunity->date_closed,
			"probability" => $opportunity->probability,
			"sales_stage" => $opportunity->sales_stage,
			"next_step"	=> $opportunity->next_step,
			"account" 	=> array(
				"id"	=> $account->id,
				"url"	=> $this->beanToUrl($account),
				"name"	=> $account->name,
			),
		);
	}
}

$loader = new OpportunityLoader();
$loader->toJSON();

?>