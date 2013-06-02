<?php
die("Disabled");
if(!defined('sugarEntry'))define('sugarEntry', true);

require_once('include/entryPoint.php');

class OpportunityUpdater {

  public $json;
	public $user;
	public $opportunity;
	public $error;

	// Setup the variables
	public function __construct() {
		global $current_user;
		if ($this->validRequest()) {
			$this->loadUser();
			$this->updateOpportunity();
		} 
	}
	
	public function toJSON() {
	  if ($this->opportunity) {
	    header('Cache-Control: no-cache, must-revalidate');
  		header('Content-type: application/json');
  		echo json_encode(array("opportunity" => $this->OpportunityToJSON($this->opportunity)));
  		return true;
	  }
	  echo json_encode($this->error);
	  return false;
	}
	
	protected function validRequest() {
	  try {
	    $this->json = json_decode(sugar_file_get_contents('php://input'));
	    return true;
	  } catch (Exception $e) {
	    $this->error = array("Error" => $e);
	  }
    return false;
	}

	protected function loadUser() {
		global $current_user;
		require_once('data/BeanFactory.php');
		$current_user = new User();
		$current_user->retrieve($this->json->owner);
		$this->user   = $current_user;
	}

	protected function updateOpportunity() {
		$this->opportunity = BeanFactory::getBean('Opportunities', $this->json->opportunity->id);
		if ( $this->opportunity === FALSE ) {
        $this->error = 'Opportunity Not Found: ' . $this->json->opportunity->id;
        return false;
    }
    $this->opportunity->sales_stage = $this->json->opportunity->sales_stage;
    $this->opportunity->save();
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
			"description" => $opportunity->description,
			"next_step"	=> $opportunity->next_step,
			"owner"     => array(
			  "id"    => $this->user->id, 
			  "email" => $this->user->emailAddress->getPrimaryAddress($this->user),
			  "name"  => $this->user->name,
			  "url"   => $this->beanToUrl($this->user),
			),
			"account" 	=> array(
				"id"	=> $account->id,
				"url"	=> $this->beanToUrl($account),
				"name"	=> $account->name,
			),
		);
	}
}

$updater = new OpportunityUpdater();
$updater->toJSON();

?>
