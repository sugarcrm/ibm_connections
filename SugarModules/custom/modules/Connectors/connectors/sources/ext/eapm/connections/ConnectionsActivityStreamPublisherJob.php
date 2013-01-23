<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class ConnectionsActivityStreamPublisherJob implements RunnableSchedulerJob {

	private $eapm;
	private $client;
	private $response;

	public function setJob(SchedulersJob $job) {
        $this->job = $job;
    }

    public function run($json) {
		$this->toggleStatus();
		return $this->publishActivity($json);
    }

	private function toggleStatus() {
		// Toggles between Queued and Done
		switch ($this->job->status) {
			case SchedulersJob::JOB_STATUS_QUEUED:
				$this->job->status = SchedulersJob::JOB_STATUS_DONE;
				break;
			case SchedulersJob::JOB_STATUS_DONE:
				$this->job->status = SchedulersJob::JOB_STATUS_QUEUED;
				break;
		}
	}
	
	private function publishActivity($json) {
		$this->sendRequest($json);
		return $this->handleResponse();
	}
	
	private function sendRequest($json) {
		require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php');
		require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/config.php');
		require_once('Zend/Http/client.php');
		$this->eapm = ConnectionsHelper::getEAPM();
		
		$this->client = new Zend_Http_Client();
		$this->client->setUri($config['properties']['company_url'] . "/connections/opensocial/basic/rest/activitystreams/@me/@all");
		$this->client->setAuth($this->eapm->account_name, $this->eapm->account_password);
		$this->client->setHeaders(array(
			'Content-Type' => 'application/json; charset=UTF-8',
			'Accept'	   => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		));
		$this->client->setRawData($json);
		$this->response = $this->client->request('POST');
	}
	
	private function handleResponse() {
		if ($this->response->isError()) {
			$this->toggleStatus();
			$this->job->resolution = SchedulersJob::JOB_FAILURE;
			$this->job->message = "Server reply was: " . $this->response->getStatus() .
		       " " . $this->response->getMessage() . "\n";
			return false;
		} 
		$this->toggleStatus();
		$this->job->resolution = SchedulersJob::JOB_SUCCESS;
		$this->job->message    = "Server reply was: " . $this->response->getStatus() .
	       " " . $this->response->getMessage() . "\n";
		return true;
	}
}

?>