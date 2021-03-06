<?php

/*
 * Discover newsubnetshosts with snmp
 *******************************/

# validate csrf cookie
$User->csrf_validate("scan_all", $_POST['csrf_cookie'], $Result);

# section
$section_search = false;
foreach ($_POST as $k=>$p) {
    if (strpos($k, "sectionId")!==false) {
        $section = $Sections->fetch_section("id", $p);
        if ($section===false)                                           { $Result->show("danger", _("Invalid section Id"), true, false, false, true); }
    }
}

# scan disabled
if ($User->settings->enableSNMP!="1")                           { $Result->show("danger", _("SNMP module disbled"), true); }

# check section permissions
if($Sections->check_permission ($User->user, $_POST['sectionId']) != 3) { $Result->show("danger", _('You do not have permissions to add new subnet in this section')."!", true); }

# loop
foreach ($_POST as $k=>$p) {
    # explode
    $k = explode("-", $k);
    # numeric
    if (is_numeric($k[1])) {
        // output array
        $subnets_all[$k[1]][$k[0]]=$p;
    }
}

# sort by mask size
function cmp_subnets($a, $b) {
    if ($a['subnet_dec'] == $b['subnet_dec']) { return 0; }
    return ($a['subnet_dec'] < $b['subnet_dec']) ? -1 : 1;
}
usort($subnets_all, "cmp_subnets");


# recompute parents
foreach ($subnets_all as $k=>$s) {
    foreach ($subnets_all as $sb) {
        if ($sb['subnet_dec']!==$s['subnet_dec'] && $sb['mask']!==$s['mask']) {
            if ($Subnets->is_subnet_inside_subnet ($s['subnet'], $sb['subnet'])) {
                $subnets_all[$k]['master'] = $sb['subnet'];
            }
        }
    }
}

# import each
if (isset($subnets_all)) {
    foreach ($subnets_all as $s) {
        # set new POST
        $_POST = $s;
        # create csrf token
        # FIXME: what is this, fix it
        $_POST['csrf_cookie'] = $User->csrf_create('subnet');
        # permissions
        $subnet['permissions'] = $section->permissions;
        # check for master
        if (isset($s['master'])) {
            // find id
            $master = $Subnets->find_subnet ($s['sectionId'], $s['master']);
            if ($master!==false) {
                $_POST['masterSubnetId'] = $master->id;
            }
        }
        # include edit script
        include (dirname(__FILE__)."/../../admin/subnets/edit-result.php");
    }
}
else { $Result->show("danger", "No subnets selected", true); }

?>

