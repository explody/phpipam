<?php
# print all domains or show records
if (@$_GET['sPage']=="page")		{ include(dirname(__FILE__) . "/domains-print.php"); }
elseif (@$_GET['sPage']=="search")	{ include(dirname(__FILE__) . "/domains-print.php"); }
elseif (isset($_GET['sPage']))		{ include(dirname(__FILE__) . "/domain-records.php"); }
else								{ include(dirname(__FILE__) . "/domains-print.php"); }
?>