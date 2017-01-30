<?php

/**
 *	The app is broken. Try things and report back how/why it's not working.
 *  This code it not especially groovy
 *  TOOD: improve this. check more thoroughly, handle more things.
 */
 
 function broken($errtype, $message) {
     print "<div class='alert alert-danger' style='margin:auto;margin-top:20px;width:700px;'><strong>$errtype:</strong><br><hr>";
     print $message;
     print "</div></div></body></html>";
     ob_end_flush();
     die();
 }
 
 if (file_exists(dirname(__FILE__) . "/../paths.php")) {
     require_once dirname(__FILE__) . "/../paths.php";
 } else {
     broken("Application Error", "Cannot find my paths.php. Can't proceed");
 }
 
 ?>

<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print BASE; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">

	<meta name="Description" content="">
	<meta name="title" content="phpipam installation">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=9" >

	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">

	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">

	<!-- title -->
	<title>Something is awry.</title>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.custom.css">
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/font-awesome.css">
	<link rel="shortcut icon" href="<?php print MEDIA; ?>/images/favicon.png">

	<!-- js -->
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/install.js"></script>
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/bootstrap.js"></script>
    
	<script type="text/javascript">
	$(document).ready(function(){
	     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
	});
	</script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/dieIE.js"></script>
	<![endif]-->
</head>


<!-- body -->
<body>

<!-- wrapper -->
<div class="wrapper">

<!-- jQuery error -->
<div class="jqueryError">
	<div class='alert alert-danger' style="width:400px;margin:auto">jQuery error!</div>
	<div class="jqueryErrorText"></div><br>
	<a href="/" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row header-install" id="header">
	<div class="col-xs-12">
		<div class="hero-unit" style="padding:20px;margin-bottom:10px;">
            <i class="fa fa-bomb"></i>
			<a href="/">I am sad</a>
		</div>
	</div>
</div>

<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
<div style='margin:auto;margin-top:20px;width:700px;'>
<?php

/**
 *	The app is broken (or at least we think it is). Try things and report back how/why it's not working.
 */
 
// Tests checking PHP build will die immediately if they fail as we can't continue with other tests
// if they don't work.
include(FUNCTIONS . '/checks/check_php_build.php');		# check for support for PHP modules and database connection

try {
    $c = require(CONFIG);
} catch (IpamConfigNotFound $e) {
    broken('Application Error', '<br />' .$e . '<br /> <strong>Make sure you have copied config.dist.yml to config.yml.</strong>' );
} catch (IpamEnvironmentNotFound $e) {
    broken('Application Error', '<br />' . $e . '<br /> <strong>No environment config found. Ensure your environment is specified correctly in '.
        'config.yml and that environment config exists </strong>' );
} catch (Exception $e) {
    broken('Application Error', '<br />Error loading config<br /> The config loader reports: ' . $e );
}

/* site functions */

// TODO: consider E_PARSE error handling, maybe a prepend script 
try {
    require FUNCTIONS . '/functions.php';
} catch (Exception $e) {
    broken("Application Error", $e->getMessage());
}

try {
    $Database 	= new Database_PDO;
    $Database->connect();
} catch (PDOException $e) { 
    broken ("Database Error", $e->getMessage());
}

// If we make it down here, the app does not appear to be broken 

header("Location: " . BASE);

?>


