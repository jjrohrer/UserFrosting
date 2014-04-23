<?php

require_once(realPath(dirName(__FILE__) . "/../includes/_EtFramework.inc.php"));
require_once("models/config.php");

//UserFrosting_pageIs();
print "<br>".__FILE__.__LINE__." UserFrosting_pageIs => ".UserFrosting_pageIs();

print "<br>".__FILE__.__LINE__."<br><pre>";

print_r( getPageFiles());
exit;
