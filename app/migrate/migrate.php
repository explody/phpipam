<?php 

// $Tools needs to be set in anything including this code.

$migrate = false;
$cmv = "0";

if ($Database->tableExists('migrations')) {
    
    $cmv = $Database->currentMigrationVersion();
    if ($cmv === false) {
        $migrate = true;
    } else {
        if ($cmv < $c->migration_version) {
            $migrate = true;
        } else {
            // current migration version _could_ be ahead of the detected latest migraion here.
            // I'm going to assume that if it is, someone is modifying migration files on purpose
            // so we won't try to migrate
            
            // Version is ok, send user back to the base
            header("Location: ".create_link('dashboard'));
        }
    }
    
} else {
    if ($User->settings->version < LAST_POSSIBLE) {
        $title 	  = "Database migration check";
        $content  = "<div class='alert alert-danger'>Your phpIPAM version is too old to be migrated. Please upgrade to phpipam version ".LAST_POSSIBLE." first.</div>";
    } else {
        $migrate = true;
    }
}

if ($migrate) {
    $title	  = "<h4>phpipam database migration required</h4>";
    $title   .= "<hr><div class='text-muted' style='font-size:13px;padding-top:5px;'>Database needs to be migrated to version <strong>v" . $c->migration_version . "</strong>. Your database is currently at version <strong>v".$cmv."</strong>!</div>";

    // automatic
    $content .= "<h5 style='padding-top:10px;'>Automatic database migration</h5><hr>";
    $content .= "<div style='padding:0;'>";
    $content .= "<div class='alert alert-warning' style='margin-bottom:5px;'><strong>Warning!</strong> Back up database first before attempting to migrate it! You have been warned.</div>";
    
    $content .= "<div class='text-left' style='padding-left: 10px;'><input type='button' class='migrate btn btn-sm btn-default btn-success' style='margin-top:10px;' version='".$c->migration_version."' value='Migrate database to the latest version'></div>";
    $content .= "<div id='migrationResult'></div>";
    $content .= "</div>";

    // manual
    $content .= "<h5 style='padding-top:10px;'>Manual migration instructions</h5><hr>";
    $content .= "<pre>"; 
    $content .= "cd " . IPAM_ROOT . "\n";
    $content .= "vendor/bin/phinx migrate -c config/phinx.php -e " . $c->environment . " \n";
    $content .= "</pre>";
    $content .= "</div>";
    $content .= "</div>";
} else {
    # migration not needed
    
    // Only redirect if the 'setup_completed' setting exists and is true. Otherwise we may be in the middle of setup.
    if (property_exists($Tools->settings, 'setup_completed') && $Tools->settings->setup_completed) {
        header("Location: ".create_link('dashboard'));
    } else {
        header("Location: ".create_link('setup'));
    }
    
}

?>