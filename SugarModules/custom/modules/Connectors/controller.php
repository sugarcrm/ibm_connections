<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bkilgore
 * Date: 3/7/12
 * Time: 3:14 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('custom/include/externalAPI/Connections/ExtAPIConnections.php');
require_once('modules/Connectors/controller.php');
require_once('custom/modules/Connectors/connectors/sources/ext/eapm/connections/ConnectionsHelper.php');

class CustomConnectorsController extends ConnectorsController {

	public function action_Connections() {
		if(isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {
			$method = $_REQUEST['method'];
			$ch = new ConnectionsHelper();
			$ch->$method();
		}
	}
}