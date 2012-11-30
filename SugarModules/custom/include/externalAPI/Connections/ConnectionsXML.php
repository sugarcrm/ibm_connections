<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

$create_community_xml=<<<CCX
<?xml version="1.0" encoding="UTF-8"?>
<entry
 xmlns="http://www.w3.org/2005/Atom"
 xmlns:app="http://www.w3.org/2007/app"
 xmlns:snx="http://www.ibm.com/xmlns/prod/sn">
<id>ignored</id>
<title type="text">ignored</title>
<category term="community" scheme="http://www.ibm.com/xmlns/prod/sn/type"></category>
<summary type="text">ignored</summary>
<content type="html"></content>
</entry>
CCX;

$upload_document_xml=<<<UDX
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
<entry>
<category term="community" label="community" scheme="tag:ibm.com,2006:td/type"></category>
</entry>
</feed>
UDX;

$community_member_xml=<<<UDX
<?xml version="1.0" encoding="UTF-8"?>
<entry
 xmlns="http://www.w3.org/2005/Atom"
 xmlns:app="http://www.w3.org/2007/app"
 xmlns:snx="http://www.ibm.com/xmlns/prod/sn">
 <contributor></contributor>
</entry>
UDX;
