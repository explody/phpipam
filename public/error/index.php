<?php
/**
 *	Display an HTTP error as a standalone page
 */

 if (file_exists(dirname(__FILE__) . "/../../paths.php")) {
     require_once dirname(__FILE__) . "/../../paths.php";
 } else {
     broken("Application Error", "Cannot find paths.php.");
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
	<title>Oh noes!</title>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/bootstrap.custom.css">
	<link rel="stylesheet" type="text/css" href="<?php print MEDIA; ?>/css/font-awesome.css">
	<link rel="shortcut icon" href="<?php print MEDIA; ?>/images/favicon.png">

	<!-- js -->
	<script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.js"></script>
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
			<a href="/">doh.</a>
		</div>
	</div>
</div>

<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
<?php

/**
 *	Display the error
 */
 include(APP . '/error.php');
 
?>
</div>
</body>
</html>

?>


