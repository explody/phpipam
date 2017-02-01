<?php

/**
 * Script to edit / add / delete users
 *************************************************/




// switch user
if(@$_GET['subnetId']=="switch"){
	$_SESSION['realipamusername'] = $_SESSION['ipamusername'];
	$_SESSION['ipamusername'] = $_GET['sPage'];
	print '<script>window.location.href = "'.create_link(null).'";</script>';
}

# print all or specific user?
if(isset($_GET['subnetId']))	{ include(dirname(__FILE__) . "/print-user.php"); }
else							{ include(dirname(__FILE__) . "/print-all.php"); }
?>