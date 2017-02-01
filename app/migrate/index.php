<?php

# make migration and php build checks
include(FUNCTIONS . '/checks/check_php_build.php');		# check for support for PHP modules and database connection



?>

<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print $url.BASE; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">

	<meta name="Description" content="">
	<meta name="title" content="<?php print $User->settings->siteTitle; ?> :: migration">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=9" >

	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">

	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">

	<!-- title -->
	<title><?php print $User->settings->siteTitle; ?> :: migration</title>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/font-awesome.css" />
	<link rel="shortcut icon" href="<?php print MEDIA; ?>/images/favicon.png">

	<!-- js -->
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/login.js"></script>
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
	<a href="<?php print create_link(null); ?>" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- Popups -->
<div id="popupOverlay"></div>
<div id="popup" class="popup popup_w400"></div>
<div id="popup" class="popup popup_w500"></div>
<div id="popup" class="popup popup_w700"></div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row header-install" id="header">
	<div class="col-xs-12">
		<div class="hero-unit" style="padding:20px;margin-bottom:10px;">
			<a href="<?php print create_link(null); ?>"><?php print $User->settings->siteTitle;?></a>
            <p class="muted"><?php print _("Upgrade"); ?></p>
		</div>
	</div>
</div>

<!-- content -->
<div class="content_overlay">
<div class="container" id="dashboard">


<?php

/**
 * Check if database needs migration to newer version
 ****************************************************/


/**
 * checks
 *
 *	$User->settings->version //installed version (from database)
 *	VERSION 			 	 //file version
 *	LAST_POSSIBLE		 	 //last possible for migration
 */

# admins that are authenticated
if($User->is_admin(false)) {

    include(dirname(__FILE__) . "/migrate.php");
    
} else {
	$title 	  = 'phpipam database migration required';
	$content  = '<div class="alert alert-warning">Database needs migration. Please contact site administrator (<a href="mailto:'.$User->settings->siteAdminMail.'">'.$User->settings->siteAdminName.'</a>)!</div>';
}

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

</div>
</div>

<!-- Base for IE -->
<div class="iebase hidden"><?php print BASE; ?></div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- weather prettyLinks are user, for JS! -->
<div id="prettyLinks" style="display:none"><?php print $User->settings->prettyLinks; ?></div>

<!-- Page footer -->
<div class="footer"><?php include(APP . '/footer.php'); ?></div>

<!-- export div -->
<div class="exportDIV"></div>

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>