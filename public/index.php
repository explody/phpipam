<?php
ob_start();

/*
|--------------------------------------------------------------------------
| Include the paths config
|--------------------------------------------------------------------------
|
| If you need to move files and directories of the app around, this can be 
| done in paths.php.  But, this file must always be able to find paths.php
| so if you reorganize the structure, update the next line.
|
*/

define('AJAX', false);

require_once dirname(__FILE__) . "/../paths.php";

/* config */
try {
    $c = require(CONFIG);
} catch (Exception $e) {
    header("Location: /broken/");
}

/* site functions */
require FUNCTIONS . '/functions.php';

/* composer */
require_once VENDOR . '/autoload.php';

// Pick some variables out of the request data,
// for use in included files without needing to 
// repeat this frequently in said files
if(array_key_exists('action', $_REQUEST)) {
    $action = $_REQUEST['action'];
}

# set default page
if(!isset($_GET['page'])) { 
    $_GET['page'] = "dashboard"; 
}

# if not install fetch settings etc
if($_GET['page'] != "setup") {

    try {
        # database object
    	$Database 	= new Database_PDO;
        $Database->connect();
    } catch (Exception $e) {
        header("Location: /broken/");
    }
    
    // If setup is not complete, send to setup but let errors through
    if ($Database->setup_required() && $_GET['page'] != "error") {
        header("Location: " . BASE . "setup/");
    }

	$Result		= new Result;
	$User		= new User ($Database);
	$Sections	= new Sections ($Database);
	$Subnets	= new Subnets ($Database);
	$Tools      = new Tools ($Database);
	$Addresses	= new Addresses ($Database);
    $Log 		= new Logging ($Database);
	$Components = new Components ($Tools);

    # reset url for base
    $url = $User->createURL ();
    
    $_GET     = $Tools->strip_input_tags($_GET);
    $_POST    = $Tools->strip_input_tags($_POST);
    $_REQUEST = $Tools->strip_input_tags($_REQUEST);
    
    // csrf instance available to all includes
    $csrf = init_csrf();
}



/** include proper subpage **/
if($_GET['page']=='setup')	    { require(APP . '/setup/index.php'); }
elseif($_GET['page']=='migrate')	{ require(APP . '/migrate/index.php'); }
elseif($_GET['page']=='login')		{ 
    if ($_GET['section'] == 'captcha') {
        require(APP . '/login/captcha/captchashow.php'); 
    } else {
        require(APP . '/login/index.php'); 
    }
}
elseif($_GET['page']=='logout')		{ require(APP . '/logout/index.php'); }
elseif($_GET['page']=='temp_share')	{ require(APP . '/temp_share/index.php'); }
elseif($_GET['page']=='request_ip')	{ require(APP . '/login/index.php'); }
elseif($_GET['page']=='opensearch')	{ require(APP . '/tools/search/opensearch.php'); }
else {
    
	# ensure that user is logged in
	$User->check_user_session();

    if($_GET['page'] != "migrate" ) {
        if ($Database->migration_required()) {
            header("Location: /migrate/");
        }
    }

	# make upgrade and php build checks
	include(FUNCTIONS . '/checks/check_php_build.php');	# check for support for PHP modules and database connection
	if($_GET['device'] && $_SESSION['realipamusername'] && $_GET['device'] == "back"){
		$_SESSION['ipamusername'] = $_SESSION['realipamusername'];
		unset($_SESSION['realipamusername']);
		print	'<script>window.location.href = "'.create_link(null).'";</script>';
	}

	# set default pagesize
	if(!isset($_COOKIE['table-page-size'])) {
        setcookie("table-page-size", 50, time()+2592000, "/", false, false, false);
	}
    
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print $url.BASE; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="Public">

	<meta name="Description" content="">
	<meta name="title" content="<?php print $title = $User->get_site_title ($_GET); ?>">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=9" >

	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=yes">

	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">
    
    <!-- app info -->
    <meta name="application-name" content="phpipam" data-base="<?php print BASE; ?>" data-page="<?php print $_GET['page']; ?>" data-section="<?php print $_GET['section']; ?>" data-prettylinks="<?php print $User->settings->prettyLinks; ?>" />
    
	<!-- title -->
	<title><?php print $title; ?></title>

	<!-- OpenSearch -->
	<link rel="search" type="application/opensearchdescription+xml" href="/?page=opensearch" title="Search <?php print $User->settings->siteTitle; ?>">

    <!-- css -->
    <link rel="shortcut icon" type="image/png" href="<?php print MEDIA; ?>/images/favicon.png">

    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.switch.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/font-awesome.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/jquery.datatables.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/multi-select.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/select2.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/select2.bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.custom.css" />


    <!-- js -->
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.js"></script>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.ui.js"></script>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.datatables.js"></script>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.stickytableheaders.js"></script>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/bootstrap.switch.js"></script>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/magic.js"></script>
    <script type="text/javascript">
    $.ajaxSetup({
      cache: false
    });
    </script>
<?php
    if($_GET['page']=="login" || $_GET['page']=="request_ip") {
	       print '<script type="text/javascript" src="' . MEDIA . '/js/login.js"></script>';
	} 
?>

	<script type="text/javascript">
	$(document).ready(function(){
	     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
	});
	</script>
	<?php 
    if ($User->settings->enableThreshold=="1") {
        print '<link rel="stylesheet" type="text/css" href="' . MEDIA . '/css/bootstrap.slider.css" />';
        print '<script type="text/javascript" src="' . MEDIA . '/js/bootstrap.slider.js"></script>';
    }	
    ?>
    
	<!--[if lt IE 9]>
    <script type="text/javascript" src="<?php print MEDIA; ?>/js/dieIE.js"></script>
	<![endif]-->
    
    <?php 
    if ($User->settings->enableLocations=="1") { 
        # API key check
        $key = strlen($gmaps_api_key)>0 ? "?key=".$gmaps_api_key : "";
        print '<script type="text/javascript" src="https://maps.google.com/maps/api/js' . $key . '"></script>';
        print '<script type="text/javascript" src="' . MEDIA . '/js/gmaps.js"></script>';
    }	
    ?>
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
<div id="popupOverlay" class="popupOverlay">
	<div id="popup400" class="popup popup_w400"></div>
	<div id="popup500" class="popup popup_w500"></div>
    <div id="popup600" class="popup popup_w600"></div>
	<div id="popup700" class="popup popup_w700"></div>
	<div id="popupMasks" class="popup popup_wmasks"></div>
	<div id="popupMax" class="popup popup_max"></div>
</div>
<div id="popupOverlay2">
	<div id="popup2-400" class="popup popup_w400"></div>
	<div id="popup2-500" class="popup popup_w500"></div>
    <div id="popup2-600" class="popup popup_w600"></div>
	<div id="popup2-700" class="popup popup_w700"></div>
	<div id="popup2Masks" class="popup popup_wmasks"></div>
	<div id="popup2Max" class="popup popup_max"></div>
</div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row" id="header">
    <!-- logo -->
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
    <?php
	if(file_exists( MEDIA . "/images/logo/logo.png")) {
    	print '<img style="width:220px;margin:10px;margin-top:20px;" alt="phpipam" src="' . MEDIA . '/images/logo/logo.png">';
	}
    ?>
	</div>
	<!-- title -->
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="hero-pusher hidden-xs hidden-sm"></div>
		<div class="hero-unit">
			<a href="<?php print create_link(null); ?>"><?php print $User->settings->siteTitle; ?></a>
			<p class="muted">
            <?php
            $title = str_replace(" / ", "<span class='divider'>/</span>", $title);
            $tmp = explode($User->settings->siteTitle, $title);
            unset($tmp[0]);
            print implode($User->settings->siteTitle, $tmp);
            ?>
            </p>
		</div>
	</div>
	<!-- usermenu -->
	<div class="col-lg-3 col-lg-offset-0 col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-6 col-xs-12 " id="user_menu">
		<?php include(APP . '/sections/user-menu.php'); ?>
	</div>
</div>


<!-- page sections / menu -->
<div class="content">
<div id="sections_overlay">
    <?php if($_GET['page']!="login" && $_GET['page']!="request_ip" && $_GET['page']!="migrate" && $_GET['page']!="install" && $User->user->passChange!="Yes")  include(APP . '/sections/index.php');?>
</div>
</div>


<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
        
		<?php
        $User->check_maintenance_mode();
        
		if($User->user->passChange=="Yes") {
			print "<div id='dashboard' class='container'>";
			include_once(APP . '/tools/pass-change/form.php');
			print "</div>";
		}
		/* dashboard */
		elseif(!isset($_GET['page']) || $_GET['page'] == "dashboard") {
			print "<div id='dashboard'>";
			include_once(APP . '/dashboard/index.php');
			print "</div>";
		}
		/* widgets */
		elseif($_GET['page']=="widgets") {
			print "<div id='dashboard' class='container'>";
			include_once(APP . '/dashboard/widgets/index.php');
			print "</div>";
		}
		/* all sections */
		elseif($_GET['page']=="subnets" && strlen($_GET['section'])==0) {
			print "<div id='dashboard' class='container'>";
			include_once(APP . '/sections/all-sections.php');
			print "</div>";
		}
		/* content */
		else {
			print "<table id='subnetsMenu'>";
			print "<tr>";

			# fix for empty section
			if( isset($_GET['section']) && (strlen(@$_GET['section']) == 0) )			{ unset($_GET['section']); }

			# hide left menu
			if( ($_GET['page']=="tools"||$_GET['page']=="administration") && !isset($_GET['section'])) {
				//we dont display left menu on empty tools and administration
			}
			else {
				# left menu
				print '<td id="subnetsLeft">';
				print '<div id="leftMenu" class="menu-' . $_GET['page'] . '">';
					if($_GET['page'] == 'subnets' || $_GET['page'] == 'vlan' ||
					   $_GET['page'] == 'vrf' 	  || $_GET['page'] == 'folder')			{ include(APP . '/subnets/subnets-menu.php'); }
					else if ($_GET['page'] == 'tools')									{ include(APP . '/tools/tools-menu.php'); }
					else if ($_GET['page'] == 'administration')							{ include(APP . '/admin/admin-menu.php'); }
				print '</div>';
				print '</td>';

			}
			# content
			print '<td id="subnetsContent">';
			print '<div class="row menu-' . $_GET['page'] . '" id="content">';
				# subnets
				if ($_GET['page']=='subnets') {
					if(@$_GET['sPage'] == 'address-details')							{ include(APP . '/subnets/addresses/address-details-index.php'); }
					elseif(!isset($_GET['subnetId']))									{ include(APP . '/sections/section-subnets.php'); }
					else																{ include(APP . '/subnets/index.php'); }
				}
				# vrf
				elseif ($_GET['page']=='vrf') 											{ include(APP . '/vrf/index.php'); }
				# vlan
				elseif ($_GET['page']=='vlan') 											{ include(APP . '/vlan/index.php'); }
				# folder
				elseif ($_GET['page']=='folder') 										{ include(APP . '/folder/index.php'); }
				# tools
				elseif ($_GET['page']=='tools') {
					if (!isset($_GET['section']))										{ include(APP . '/tools/index.php'); }
					else {
                        if (!in_array($_GET['section'], $tools_menu_items))             { header('Location: '.create_link('error','400')); die(); }
						elseif (!file_exists(APP . "/tools/$_GET[section]/index.php") && !file_exists(APP . "/tools/custom/$_GET[section]/index.php"))
						                                                                { header('Location: '.create_link('error','404')); die(); }
						else 															{
    						if(file_exists(APP . "/tools/$_GET[section]/index.php")) {
        						include(APP . "/tools/$_GET[section]/index.php");
    						}
    						else {
        					    include(APP . "/tools/custom/$_GET[section]/index.php");
    						}
                        }
					}
				}
				# admin
				elseif ($_GET['page']=='administration') {
					# Admin object
					$Admin = new Admin ($Database);

					if (!isset($_GET['section']))										{ include(APP . '/admin/index.php'); }
					elseif (@$_GET['subnetId']=='section-changelog')					{ include(APP . '/sections/section-changelog.php'); }
					else {
                        if (!in_array($_GET['section'], $admin_menu_items))             { header('Location: '.create_link('error','400')); die(); }
						elseif(!file_exists(APP . "/admin/$_GET[section]/index.php")) 		{ header('Location: '.create_link('error','404')); die(); }
						else 															{ include(APP . "/admin/$_GET[section]/index.php"); }
					}
				}
				# default - error
				else {
																						{ header('Location: '.create_link('error','400')); die(); }
				}
			print '</div>';
			print '</td>';

			print '</tr>';
			print '</table>';
    	}
    	?>

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

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>
<?php } ?>
