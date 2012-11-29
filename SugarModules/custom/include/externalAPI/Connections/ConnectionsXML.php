<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bkilgore
 * Date: 3/10/12
 * Time: 10:03 AM
 * To change this template use File | Settings | File Templates.
 */

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
