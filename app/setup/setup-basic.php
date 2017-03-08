<?php 

$admin = $Tools->fetch_object ("users","username","Admin");
$admin_exists = false;

// If admin has any password other than NULL, then the password has been set before and we're going to bail out
if(!is_null($admin->password)) {
    $admin_exists = true;
}

?>

<div class="widget-dash col-xs-12 col-md-8 col-md-offset-2">
<div class="inner setup" style="min-height:auto;">
	<h4>Basic Setup</h4>

	<div class="hContent">

		<div class="text-muted" style="margin:10px;">
		All settings can be changed from the admin area after setup.
		</div>
		<hr>
		<form name="setup-basic" id="setup-basic" class="form-inline" method="post">

        <?php $csrf->insertToken('/ajx/setup/setup-basic-result'); ?>

		<div class="row">
            
            <?php
            if ($admin_exists) {
            	$Result->show("warning", _("The admin password is already set."), false);
            }
            ?>
			<!-- MySQL install username -->
			<div class="col-xs-12 col-md-4"><strong>Admin password</strong></div>
			<div class="col-xs-12 col-md-8">
				<input type="password" style="width:100%;" name="password1" class="form-control" autofocus="autofocus" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" <?php $admin_exists ? print "disabled" : null; ?>>
			</div>

			<!-- MySQL install password -->
			<div class="col-xs-12 col-md-4"></div>
			<div class="col-xs-12 col-md-8">
				<input type="password" style="width:100%;" name="password2" class="form-control" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" <?php $admin_exists ? print "disabled" : null; ?>>
				<div class="text-muted">Set password for Admin user</div>
			</div>
			<hr>

			<div class="col-xs-12">
			<hr>
			</div>

			<!-- Site title -->
			<div class="col-xs-12 col-md-4"><strong>Site title</strong></div>
			<div class="col-xs-12 col-md-8">
				<input type="text" style="width:100%;" name="siteTitle" class="form-control" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="<?php print empty($Tools->settings->siteTitle) ? 'phpipam' : $Tools->settings->siteTitle;  ?>" <?php $admin_exists ? print "disabled" : null; ?>>
				<div class="text-muted"></div>
			</div>

			<!-- Site URL -->
			<div class="col-xs-12 col-md-4"><strong>Site URL</strong></div>
			<div class="col-xs-12 col-md-8">
				<input type="text" style="width:100%;" name="siteURL" class="form-control" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="<?php print empty($Tools->settings->siteURL) ? $_SERVER['SCRIPT_URI'] : $Tools->settings->siteURL;  ?>" <?php $admin_exists ? print "disabled" : null; ?>>
				<div class="text-muted"></div>
			</div>

			<!-- submit -->
			<div class="col-xs-12 text-right" style="margin-top:10px;">
				<hr>
				<div class="btn-block">
					<!-- Back -->
					<a class="btn btn-sm btn-default" href="<?php print create_link("install","install_automatic",null,null,null,true); ?>" >&lt; Back</a>
					<input type="submit" class="btn btn-sm btn-info" version="0" value="Save settings" <?php $admin_exists ? print "disabled" : null; ?>>
                    <?php 
                    if ($admin_exists) {
                        print "<input type='hidden' id='redirect' name='redirect' value='1' />";
                        print "<input type='submit' class='btn btn-sm btn-default' href='' value='Continue to login &gt;'>\n";
                    }
                    ?>
				</div>
			</div>
			<div class="clearfix"></div>

			<!-- result -->
			<div class="setup-basic-result" style="margin-top:15px;">
			</div>

		</div>
		</form>

	</div>
</div>
</div>
