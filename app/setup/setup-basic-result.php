<?php
// TODO: JS password requirements and comparison
/**
 *	Post-installation submit
 */
 
$Tools->csrf_validate("setup-basic", $_POST['csrf_cookie'], $Result);

$Install  = new Install($Database);

# This was checked before at the form but check again on submit
$admin = $Admin->fetch_object("users", "username", "Admin");

# If admin already has a password, ensure setup_completed is set and redirect to login
if (!empty($admin->password)) {
    $Install->mark_setup_completed();
    $Result->show("success", "Basic setup complete!", false);
} else {
    
    # check lengths
    if (strlen($_POST['password1'])<8) {
        $Result->show("danger", _("Password must be at least 8 characters long!"), true);
    }
    if (strlen($_POST['password2'])<8) {
        $Result->show("danger", _("Password must be at least 8 characters long!"), true);
    }

    # check password match
    if ($_POST['password1']!=$_POST['password2']) {
        $Result->show("danger", _("Passwords do not match"), true);
    }

    # Crypt password
    $_POST['password1'] = $User->crypt_user_pass($_POST['password1']);

    # all good, update password!
    $Install->setup_basic_save($_POST['password1'], $_POST['siteTitle'], $_POST['siteURL']);
    
    # Mark setup as complete 
    $Install->mark_setup_completed();
    
    # ok
    $Result->show("success", "Basic setup complete!", false);
    
}
