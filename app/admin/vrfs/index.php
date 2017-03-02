<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/




# fetch all vrfs
$all_vrfs = $Admin->fetch_all_objects("vrf", "name");

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('vrf');

?>

<h4><?php print _('Manage VRF'); ?></h4>
<hr><br>

<div class="btn-group">
    <button class='btn btn-sm btn-default vrfManagement' data-action='add' data-vrfid='' style='margin-bottom:10px;'><i class='fa fa-plus'></i> <?php print _('Add VRF'); ?></button>
    <?php
    // snmp
    if($User->is_admin()===true && $User->settings->enableSNMP==1) { ?>
	<button class="btn btn-sm btn-default" id="snmp-vrf" data-action="add"><i class="fa fa-cogs"></i> <?php print _('Scan for VRFs'); ?></button>
	<?php } ?>

</div>

<!-- vrfs -->
<?php

# first check if they exist!
if($all_vrfs===false) { $Result->show("info", _("No VRFs configured")."!", false);}
else {
	print '<table id="vrfManagement" class="table sorted table-striped table-top table-hover">'. "\n";

	# headers
	print "<thead>";
	print '<tr>'. "\n";
	print '	<th>'._('Name').'</th>'. "\n";
	print '	<th>'._('RD').'</th>'. "\n";
	print '	<th>'._('Sections').'</th>'. "\n";
	print '	<th>'._('Description').'</th>'. "\n";

	foreach($cfs as $cf) {
		if($cf->visible) {
			print "<th class='customField hidden-xs hidden-sm'>$cf->name</th>";
		}
	}

	print '	<th></th>'. "\n";
	print '</tr>'. "\n";
	print "</thead>";

    print "<tbody>";
	# loop
	foreach ($all_vrfs as $vrf) {
		//cast
		$vrf = (array) $vrf;

    	// format sections
    	if(strlen($vrf['sections'])==0) {
    		$sections = "All sections";
    	}
    	else {
    		//explode
    		unset($sec);
    		$sections_tmp = explode(";", $vrf['sections']);
    		foreach($sections_tmp as $t) {
    			//fetch section
    			$tmp_section = $Sections->fetch_section(null, $t);
    			$sec[] = " &middot; ".$tmp_section->name;
    		}
    		//implode
    		$sections = implode("<br>", $sec);
    	}

		//print details
		print '<tr class="text-top">'. "\n";
		print '	<td class="name">'. $vrf['name'] .'</td>'. "\n";
		print '	<td class="rd">'. $vrf['rd'] .'</td>'. "\n";
		print "	<td><span class='text-muted'>$sections</span></td>";
		print '	<td class="description">'. $vrf['description'] .'</td>'. "\n";

		// custom fields

		foreach($cfs as $cf) {
			if($cf->visible) {

				print "<td class='customField hidden-xs hidden-sm'>";

				// create links
				$vrf[$cf->name] = $Result->create_links ($vrf[$cf->name], $cf->type);

				//booleans
				if($field['type']=="boolean")	{
					if($vrf[$cf->name] == "0")		{ print _("No"); }
					elseif($vrf[$cf->name] == "1")	{ print _("Yes"); }
				}
				//text
				elseif($field['type']=="text") {
					if(strlen($vrf[$cf->name])>0)	{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $vrf[$cf->name])."'>"; }
					else											{ print ""; }
				}
				else {
					print $vrf[$cf->name];

				}
				print "</td>";
			}
		}


		print "	<td class='actions'>";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-xs btn-default vrfManagement' data-action='edit'   data-vrfid='$vrf[vrfId]'><i class='fa fa-pencil'></i></button>";
		print "		<button class='btn btn-xs btn-default vrfManagement' data-action='delete' data-vrfid='$vrf[vrfId]'><i class='fa fa-times'></i></button>";
		print "	</div>";
		print "	</td>";
		print '</tr>'. "\n";
	}
	print "</tbody>";
	print '</table>'. "\n";
}
?>

<!-- edit result holder -->
<div class="vrfManagementEdit"></div>