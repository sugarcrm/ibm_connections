<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class ConnectionsActivityStreamEntry {
	
	public 	  $actor;
	public 	  $title;
	public	  $object;
	public	  $target;
	public 	  $summary;
	public	  $gadget;
	public    $jsonEntry = array(); // Holds the actual JSON we are going to send over
	protected $bean;
	protected $beanName; // Holds the bean name, needed for formatting
	protected $newBean = false;  // Is this a new record or not?  Affects content and formatting
	
	// Setup the variables
	public function __construct(SugarBean $bean) {
		$this->bean = $bean;
		$this->resolveBeanState();
		$this->populateBeanName();
		$this->populateActor();
		$this->populateTitle();
		$this->populateSummary();
		$this->populateObject();
		$this->populateTarget();
		$this->populateGadget();
		$this->populateJsonEntry();
	}
	
	public function toJSON() {
		// JSON Helper
		return json_encode($this->jsonEntry);
	}
	
	public function __toString() {
		// JSON makes a nice string representation.
		return $this->toJSON();
	}
	
	public function enqueue() {
		// Add this entry to the job queue
		require_once('include/SugarQueue/SugarJobQueue.php');

        $job         = new SchedulersJob();
        $job->name   = "Activity Stream Entry";
        $job->data   = $this->toJSON();  
        $job->target = "class::ConnectionsActivityStreamPublisherJob";
		$job->assigned_user_id = $this->actor->id;

        $job_queue   = new SugarJobQueue();
        $job_id      = $job_queue->submitJob($job);
		return $job_id;
	}

	protected function resolveBeanState() {
		// Look at the bean id column to see if it has a value.  
		// If it does, this isn't a new record
		$this->newBean = empty($this->bean->fetched_row['id']); 
	}

	protected function populateBeanName() {
		// We need to know which kind of Bean we are working with.
		// This gives us the singular form.
		$this->beanName = BeanFactory::getBeanName($this->bean->module_dir); 
	}

	protected function populateActor() {
		// Actor is the person that triggered the event.
		// We need this information so we can lookup their 
		// credentials (via EAPM) and push the event to Connections.
		global $current_user;
		$this->actor = $current_user;
	}
 
	protected function populateTitle() {
		// This should give us a nice title like:
		// Max Jensen created an Opportunity: A Dozen Llamas (Acme Co.).
		$action 	  = $this->newBean ? "created" : "updated";
		$this->title  = join(' ', array('${Actor}', $action, "an", strtolower($this->beanName)));
		$this->title .= $this->titleDetail();
	}
	
	protected function titleDetail() {
		// This returns the suffix for the title, such as the opportunity name
		switch ($this->beanName) {
			case "Opportunity":
				return ' ${Object} for ${Target}.'  ;
				break;
			default:
				if ($this->beanHasFieldWithValue($bean, 'name')) {
					return ': ' . $this->bean->name;
				} 
				return "";
		}
		
	}
	
	protected function populateObject() {
		$this->object = array(
			"id" 		  => $this->bean->id,
			"displayName" => $this->bean->name,
			"url"		  => $this->beanToUrl($this->bean),
			"summary"	  => $this->summary,
		);
	}
	
	protected function populateTarget() {
		// The target in this case is an account.  It's generally
		// the parent of whatever thing is being manipulated.
		// i.e. John uploaded a photo to his photo album
		//  - John is the actor
		//  - photo is the object
		//  - photo album is the target
		$account = $this->accountForBean();
		$this->target = array(
			"id"			=> $account->id,
			"url"			=> $this->beanToUrl($account),
			"displayName"	=> $account->name,
		);
	}
	
	protected function populateSummary() {
		require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsUtils.php');
		$this->summary = "Forecasting " . $this->bean->probability . "% chance to close $" . number_format($this->bean->amount_usdollar, 2) . " in " . $this->timeInWords(time(), $this->bean->date_closed) . "."; 
	}
	
	protected function populateGadget() {
		$this->gadget = array(
			"embed"  => array(
				"gadget"  => $this->gadgetUrl(),
				"context" => $this->gadgetContext(),
			),
		);
	}
	
	private function timeInWords($from, $to) {
		return ConnectionsUtils::distance_of_time_in_words($from, strtotime($to));
	}
	
	private function accountForBean() {
		// Loads the account associated with the bean
		// Raises an exception if there's no account, or no relationship
		if (!$this->bean->load_relationship('accounts')) throw new Exception('Invalid link_field "accounts"');
		$accounts = $this->bean->accounts->getBeans();
		if (count($accounts) == 0) throw new Exception('No account for ' . $this->beanName . ": " . $this->bean->id);
		return array_shift($accounts);
	}
	
	private function beanToUrl(SugarBean $bean) {
		// Returns a URL Path to the Bean, so an Opportunity with an ID of 123456 would return:
		// http://localhost/sugarcrm/index.php?action=DetailView&module=Opportunities&record=123456
		return $this->siteUrl() . "/index.php?action=DetailView&module=" . $bean->module_dir . "&record=" . $bean->id;
	}
	
	private function beanHasFieldWithValue(SugarBean $bean, $field) {
		// Just tells us if the bean has a field, and if that field has a value
		return (property_exists($bean, $field) && isset($bean, $field));
	}
	
	private function siteUrl() {
		// Returns the site URL without whitespace, or a trailing /
		global $sugar_config; 
		return trim(rtrim($sugar_config['site_url'], '/')); 
	}
	
	private function timestamp() {
		// Gives us a timestamp in a format that Connections will use
		$time = time();
		return date("Y-m-d", $time) . 'T' . date("H:i:s", $time) .'.000Z';
	}
	
	private function gadgetUrl() {
		// Returns the path to the gadget
		return $this->siteUrl() . "/modules/OpenSocial/sugarcrm.xml";
	}
	
	private function gadgetContext() {
		return array( "opportunity" => array(
			"id" 		=> $this->bean->id,
			"url"		=> $this->object["url"],
			"name"		=> $this->bean->name,
			"amount"	=> $this->bean->amount,
			"date_closed" => $this->bean->date_closed,
			"probability" => $this->bean->probability,
			"sales_stage" => $this->bean->sales_stage,
			"next_step"	=> $this->bean->next_step,
			"account" 	=> array(
				"id"	=> $this->bean->account_id,
				"url"	=> $this->target["url"],
				"name"	=> $this->bean->account_name,
			),
		));
	}
	
	private function populateJsonEntry() {
		global $sugar_config; 
		$this->jsonEntry = array(
			"generator" => array(
				"image" => array("url" => 'https://www.sugarcrm.com/sites/default/files/favicon_0.ico'),
				"id"	=> "sugarcrm",
				"url"	=> $this->siteUrl(),
				"displayName" => "SugarCRM",
			),
			"actor"   => array("id" => "@me"),
			"verb"	  => "post",
			"title"	  => $this->title,
			"updated" => $this->timestamp(),
			"object"  => $this->object,
			"target"  => $this->target,
			"openSocial" => $this->gadget,
		);
	}

}

?>
