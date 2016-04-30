<?php
# verify php build
include 'functions/checks/check_php_build.php';        # check for support for PHP modules and database connection

$http_auth = $User->fetch_object('usersAuthMethod', 'type', 'HTTP');
$http_auth_settings = json_decode($http_auth->params);

if (array_key_exists('username_variable', $http_auth_settings) && !empty($http_auth_settings['username_variable'])) 
{
    $user_variable = $http_auth_settings['username_variable'];
} else {
    $user_variable = 'PHP_AUTH_USER';
}

// http auth
if (!empty($_SERVER[$user_variable])) {
    
    $username = $_SERVER[$user_variable];
    
    try { 
        $user = $this->Database->findObject("users", "username", $username); 
    }
    catch (Exception $e) { 
        $this->Result->show("danger", _("Error: ").$e->getMessage(), true);
    }
    
    // If the username does not exist, provisioning is enabled and the email variable exists,
    // try to autoprovision the user
    if ((!$user && $http_auth_settings['enable_provisioning']) && array_key_exists('email_variable', $http_auth_settings)) 
    {
            $email = $_SERVER[$http_auth_settings['email_variable']];
            $role = array_key_exists($_SERVER, $http_auth_settings['role_variable']) ? 
                    $_SERVER[$http_auth_settings['role_variable']] :
                    'User';
                    
            $userdata = array(
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'authMethod' => 'HTTP'
            )
            $Admin = new Admin ($Database);
            
            // This next line creates the user
            if (!$Admin->object_modify('users', 'add', 'id', $userdata)) 
            { 
                $Result->show("danger",  _("User autoprovision failed").'!', true); 
            }
    } 
    
    // Now we know a user either exists or was just created. Proceed with the auth flow.
    
    // try to authenticate
    $User->authenticate($_SERVER[$user_variable], '');
    // Redirect user where he came from, if unknown go to dashboard.
    if (isset($_COOKIE['phpipamredirect'])) {
        header('Location: '.$_COOKIE['phpipamredirect']);
    } else {
        header('Location: '.create_link('dashboard'));
    }
    exit();
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php echo $url.BASE; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">

	<meta name="Description" content="">
	<meta name="title" content="<?php echo $User->settings->siteTitle; ?>">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=9" >

	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">

	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">

	<!-- title -->
	<title><?php echo $User->settings->siteTitle; ?></title>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/1.2/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/1.2/bootstrap/bootstrap-custom.css">
	<link rel="stylesheet" type="text/css" href="css/1.2/font-awesome/font-awesome.min.css">
	<link rel="shortcut icon" href="css/1.2/images/favicon.png">

	<!-- js -->
	<script type="text/javascript" src="js/1.2/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="js/1.2/login.js"></script>
	<script type="text/javascript" src="js/1.2/bootstrap.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
	     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
	});
	</script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="js/1.2/dieIE.js"></script>
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
	<a href="<?php echo create_link(null); ?>" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- Popups -->
<div id="popupOverlay"></div>
<div id="popup" class="popup popup_w400"></div>
<div id="popup" class="popup popup_w500"></div>
<div id="popup" class="popup popup_w700"></div>

<!-- loader -->
<div class="loading"><?php echo _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row header-install" id="header">
	<div class="col-xs-12">
		<div class="hero-unit" style="padding:20px;margin-bottom:10px;">
			<a href="<?php echo create_link(null); ?>"><?php echo $User->settings->siteTitle.' | '._('login');?></a>
		</div>
	</div>
</div>

<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">

	<?php
    # set default language
    if (isset($User->settings->defaultLang) && !is_null($User->settings->defaultLang)) {
        # get language
        $lang = $User->get_default_lang();

        putenv('LC_ALL='.$lang->l_code);
        setlocale(LC_ALL, $lang->l_code);                    // set language
        bindtextdomain('phpipam', './functions/locale');    // Specify location of translation tables
        textdomain('phpipam');                                // Choose domain
    }
    ?>

	<?php
    # include proper subpage
    if ($_GET['page'] == 'login') {
        include_once 'login_form.php';
    } elseif ($_GET['page'] == 'request_ip') {
        include_once 'request_ip_form.php';
    } else {
        $_GET['subnetId'] = '404';
        echo "<div id='error'>";
        include_once 'app/error.php';
        echo '</div>';
    }
    
    ?>

</div>
</div>

<!-- Base for IE -->
<div class="iebase hidden"><?php echo BASE; ?></div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- weather prettyLinks are user, for JS! -->
<div id="prettyLinks" style="display:none"><?php echo $User->settings->prettyLinks; ?></div>

<!-- Page footer -->
<div class="footer"><?php include 'app/footer.php'; ?></div>

<!-- end body -->
</body>
</html>
