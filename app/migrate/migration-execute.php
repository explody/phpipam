<?php

/* ---------- Upgrade database ---------- */

$Install 	= new Install ($Database);

# admin user is required
if ($User->settings->setup_completed) { 
    $User->is_admin(true);
}

$migrate = $Install->migrate_database();

if($migrate) {
	
	# migrate settings
	$User->migrate_domain_settings ();
    $User->migrate_ldap_settings ();
    
    if (!property_exists($User->settings, 'setup_completed') || !$User->settings->setup_completed) {
        $return_to = "setup";
    } else {
        $return_to = "dashboard";
    }    
    
    # print success
	$Result->show("success", _("Database migrated successfully! <br /> <pre>$migrate</pre> <a class='btn btn-sm btn-default' href='".create_link($return_to)."'>Continue to " . ucfirst($return_to) . "</a>"), false);
    
}
else {
	# print failure
	$Result->show("danger", _("Failed to migrate database! <a class='btn btn-sm btn-default' href='".create_link('administration', "verify-database")."'>Go to administration and fix</a>"), false);
}
?>
