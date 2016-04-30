<?php

/**
 * Settings for external HTTP auth
 *****************/

# verify that user is logged in
$User->check_user_session();

$placeholders = array(
    'username_variable' => 'PHP_AUTH_USER',
    'email_variable'    => 'REMOTE_USER_EMAIL',
    'role_variable'     => 'REMOTE_USER_ROLE'
);

# ID must be numeric */
if($_POST['action'] != "add") {
	if(!is_numeric($_POST['id']))	{ $Result->show("danger", _("Invalid ID"), true, true); }

	# feth method settings
	$method_settings = $Admin->fetch_object ("usersAuthMethod", "id", $_POST['id']);
	$method_settings->params = json_decode($method_settings->params);
}
else {
	$method_settings = new StdClass ();
	# set default values
    @$method_settings->params->enable_provisioning = 0;
    $method_settings->params->enable_update = 0;
    $method_settings->params->username_variable = 'PHP_AUTH_USER';
    $method_settings->params->email_variable = 'REMOTE_USER_EMAIL';
    $method_settings->params->role_variable = 'REMOTE_USER_ROLE';
}

# set delete flag
$delete = $_POST['action']=="delete" ? "disabled" : "";
?>

<!-- header -->
<div class="pHeader"><?php print _('HTTP settings'); ?></div>

<!-- content -->
<div class="pContent">

	<form id="editAuthMethod" name="editAuthMethod">
	<table class="editAuthMethod table table-noborder table-condensed">

	<!-- description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" name="description" class="form-control input-sm" value="<?php print @$method_settings->description; ?>" <?php print $delete; ?>>
		</td>
		<td class="info2">
			<?php print _('Set name for authentication method'); ?>
		</td>
	</tr>

	<tr>
		<td colspan="3"><hr></td>
	</tr>

	<!-- Enable provisioning -->
	<tr>
		<td style="width:130px;"><?php print _('Enable Provisioning'); ?></td>
		<td style="width:250px;">
			<input type="checkbox" name="enable_provisioning" class="form-control input-sm" value="1" <?php print $method_settings->params->enable_provisioning ? 'checked' : ''; ?><?php print $delete; ?>>
			<input type="hidden" name="type" value="HTTP">
			<input type="hidden" name="id" value="<?php print @$method_settings->id; ?>">
			<input type="hidden" name="action" value="<?php print @$_POST['action']; ?>">
			<input type="hidden" name="csrf_cookie" value="<?php print $csrf; ?>">
		</td>
		<td class="info2"><?php print _('If provisioning is enabled, users will be auto-created based on HTTP auth headers.'); ?>
		</td>
	</tr>

	<!-- Environment mappings -->
    <!-- Username -->
    <tr>
        <td><?php print _('Username Variable (required)'); ?></td>
        <td>
            <input type="text" name="username_variable" class="form-control input-sm" value="<?php print @$method_settings->params->username_variable; ?>" placeholder="PHP_AUTH_USER" <?php print $delete; ?> >
        </td>
        <td class="username_variable info2">
            <?php print _('Server variable that contains the username.'); ?>
        </td>
    </tr>
    
    <!-- Email -->
	<tr>
		<td><?php print _('Email Variable (required)'); ?></td>
		<td>
			<input type="text" name="email_variable" class="form-control input-sm" value="<?php print @$method_settings->params->email_variable; ?>" placeholder="REMOTE_USER_EMAIL" <?php print $delete; ?>>
		</td>
		<td class="email_variable info2">
			<?php print _('Server variable that contains the user email address.'); ?>
		</td>
	</tr>

	<!-- Role -->
	<tr>
		<td><?php print _('Role Variable (optional)'); ?></td>
		<td>
			<input type="text" name="role_variable" class="form-control input-sm" value="<?php print @$method_settings->params->role_variable; ?>" placeholder="REMOTE_USER_ROLE" <?php print $delete; ?>>
		</td>
		<td class="role_variable info2">
			<?php print _('Optional server variable that contains the phpipam role. Default role: "User"' ); ?>
		</td>
	</tr>

	</table>
	</form>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editAuthMethodSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<?php
	if($_POST['action']=="delete") {
		# check for mathing users
		$users = $Admin->fetch_multiple_objects ("users", "authMethod", @$method_settings->id);
		if($users!==false) {
			$Result->show("warning", sizeof($users)._(" users have this method for logging in. They will be reset to local auth!"), false);
		}
	}
	?>

	<!-- Result -->
	<div class="editAuthMethodResult"></div>
</div>
