<?php

/**
 *	phpipam setup page!
 */
# check if php is built properly
include(FUNCTIONS . '/checks/check_php_build.php');		# check for support for PHP modules and database connection

try {
    # database object
    $Database 	= new Database_PDO;
    $Database->connect();
} catch (Exception $e) {
    header("Location: /broken/");
}

// If setup is marked complete, immediately redirect elsewhere
if (!$Database->setup_required()) {
    header("Location: ".create_link('dashboard'));
} 

$Result		= new Result;
$Tools	    = new Tools ($Database);
$Install 	= new Install ($Database);

$_GET     = $Tools->strip_input_tags($_GET);
$_POST    = $Tools->strip_input_tags($_POST);

if (!$Database->bootstrap_required()) {
    $Tools->get_settings();
    $url  = $Result->createURL();
    $User = new User ($Database);
}

# If User is not available create fake user object for create_link!
if(!is_object(@$User)) {
	$User = new StdClass ();
	@$User->settings->prettyLinks = "Yes";
}

?>
    
<html lang="en">

<head>
	<base href="<?php print $url.BASE; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">

	<meta name="Description" content="">
	<meta name="title" content="phpipam setup">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=9" >

	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">

	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">

	<!-- title -->
	<title>phpipam setup</title>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.custom.css">
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/font-awesome.css">
	<link rel="shortcut icon" href="<?php print MEDIA; ?>/images/favicon.png">

	<!-- js -->
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/setup.js"></script>
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
	<a href="<?php print create_link(null,null,null,null,null,true); ?>" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row header-setup" id="header">
	<div class="col-xs-12">
		<div class="hero-unit" style="padding:20px;margin-bottom:10px;">
			phpipam setup
		</div>
	</div>
</div>


<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
<div class='container' id='dashboard'>

<?php

$setup = true;

# select install type
if(!isset($_GET['section']) || $_GET['section'] == 'migrate') { 
    
    if ($Database->migration_required()) {
        
        include(APP . '/migrate/migrate.php');
?>

    <div class="widget-dash col-xs-12 col-md-8 col-md-offset-2">
    <div class="inner install" style="min-height:auto;">
        <div id="migration-title">
            <?php print $title; ?>
        </div>
        <div class="hContent">
        <div style="padding:10px;">
            <?php print $content; ?>
        </div>
        </div>
    </div>
    </div>
    
<?php
    } else {
        
        include(APP . '/setup/setup-basic.php');
    }
    
}

?>

<!-- Base for IE -->
<div class="iebase hidden"><?php print BASE; ?></div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- Page footer -->
<div class="footer"><?php include(APP . '/footer.php'); ?></div>

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>
