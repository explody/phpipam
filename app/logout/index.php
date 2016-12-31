<?php 

// Something is wrong with the $User object. the authmethodid always comes across as 1
// while in the 'user' attribute, the 'authMethod' is correct. So, go straight
// to the DB with the accurate ID here.
try { 
    $auth_method = $Database->getObject('usersAuthMethod', $User->user->authMethod); 
}
catch (Exception $e) {
    $Result->show('danger', _("Error: ").$e->getMessage(), true);
}
if ($auth_method->type == 'HTTP') {
    
    $http_auth_settings = json_decode($auth_method->params);
    
    if (!empty($http_auth_settings->logout_redirect_url)) {
        $logout_url = $http_auth_settings->logout_redirect_url;
    } else {
        $logout_url = create_link('login');
    }
    
} else {
    $logout_url = create_link('login');
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print $url.BASE; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
    
    <meta http-equiv="refresh" content="2;URL=<?php print $logout_url;?>">
    
	<meta name="Description" content="">
	<meta name="title" content="<?php print $User->settings->siteTitle; ?>">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=9" >

	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">

	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">

	<!-- title -->
	<title><?php print $User->settings->siteTitle; ?></title>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/font-awesome.css" />
	<link rel="shortcut icon" href="<?php print MEDIA; ?>/images/favicon.png">

	<!-- js -->
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/login.js"></script>
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
			<a href="<?php print create_link(null); ?>"><?php print $User->settings->siteTitle." | "._('logout');?></a>
		</div>
	</div>
</div>

<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">

	<?php
	# set default language
	if(isset($User->settings->defaultLang) && !is_null($User->settings->defaultLang) ) {
		# get language
		$lang = $User->get_default_lang();

		putenv("LC_ALL=".$lang->l_code);
		setlocale(LC_ALL, $lang->l_code);					// set language
		bindtextdomain("phpipam", "./functions/locale");	// Specify location of translation tables
		textdomain("phpipam");								// Choose domain
	}
	?>
    
    <!-- logout -->
    <div id="loginCheck">
        <?php
        # deauthenticate user
        if ( $User->check_user_session(false) ) {
            # print result
            if($_GET['section']=="timeout")		{ $Result->show("success", _('You session has timed out')); }
            else								{ $Result->show("success", _('You have logged out')); }

            # write log
            $Log->write( "User logged out", "User $User->username has logged out", 0, $User->username );

            # destroy session
            $User->destroy_session();
        }
        ?>
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
<div class="footer"><?php include('app/footer.php'); ?></div>

<!-- end body -->
</body>
</html>
