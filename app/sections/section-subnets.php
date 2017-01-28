<?php

/* prints all subnets in section */

# user must be authenticated
$User->check_user_session ();

# must be numeric
if(!is_numeric($_GET['section']))	{ $Result->show("danger", _("Invalid ID"), true); }

# set custom fields
$cfs = $Tools->fetch_custom_fields ("subnets");

# set colspan
$colspan = 8 + sizeof($csf);
if($User->settings->enableVRF == 1) { $colspan++; }

# title
print "<h4>"._('Available subnets')."</h4>";

# check permission
$permission = $Sections->check_permission ($User->user, $_GET['section']);


# permitted
if($permission != 0) {

	# print  table structure
	print "<table id='manageSubnets' class='table sorted table-striped table-condensed table-top'>";

		# set colcount
		if($User->settings->enableVRF == 1)		{ $colCount = 10; }
		else									{ $colCount = 9; }

		# get Available subnets in section - already provided in subnets_menu.php
		//$section_subnets = $Subnets->fetch_section_subnets($_GET['section']);

		# remove custom fields if all empty! */
        $idx = 0;
		foreach($cfs as $cf) {
			$sizeMyFields[$cf->name] = 0;				// default value
			# check against each IP address
			foreach($section_subnets as $subn) {
				if(strlen($subn->{$cf->name}) > 0) {
					$sizeMyFields[$cf->name]++;		// +1
				}
			}
			# unset if value == 0
			if($sizeMyFields[$cf->name] == 0) {
				unset($cfs[$idx]);
			}
			else {
				$colCount++;								// colspan
			}
            $idx++;
		}

		# collapsed div with details
		print "<thead>";
		# headers
		print "<tr>";
		print "	<th>"._('Subnet')."</th>";
		print "	<th>"._('Description')."</th>";
		print "	<th>"._('VLAN')."</th>";
		if($User->settings->enableVRF == 1) {
		print "	<th class='hidden-xs hidden-sm'>"._('VRF')."</th>";
		}
		print "	<th>"._('Master Subnet')."</th>";
		print "	<th>"._('Device')."</th>";
		if($User->settings->enableIPrequests == 1) {
			print "	<th class='hidden-xs hidden-sm'>"._('Requests')."</th>";
		}
 
        foreach($cfs as $cf) {
            if($cf->visible) {
                print " <th class='hidden-xs hidden-sm'>$field[name]</th>";
            }
        }
 
		print "	<th class='actions' style='width:140px;white-space:nowrap;'></th>";
		print "</tr>";
		print "</thead>";

        # body
        print "<tbody>";

		# add new link
		if ($permission>1) {
		print "<tr>";
		print "	<td colspan='$colCount'>";
		print "		<button class='btn btn-sm btn-default editSubnet' data-action='add' data-sectionid='$section[id]' data-subnetId='' rel='tooltip' data-placement='right' title='"._('Add new subnet to section')." $section[name]'><i class='fa fa-plus'></i> "._('Add subnet')."</button>";
		print "	</td>";
		print "	</tr>";
		}

		# no subnets
		if(sizeof($section_subnets) == 0) {
			print "<tr><td colspan='$colCount'><div class='alert alert-info'>"._('Section has no subnets')."!</div></td></tr>";

			# check Available subnets for subsection
			$subsections = $Sections->fetch_subsections($_GET['section']);
		}
		else {
			// print subnets
			if($Subnets->print_subnets_tools($User->user, $section_subnets, $cfs, true, $section['showSupernetOnly'])===false) {
				print "<tr>";
				print "	<td colspan='$colspan'><div class='alert alert-info'>"._('No subnets available')."</div></td>";
				print "</tr>";
				// hide left menu
				print "<script type='text/javascript'>";
				print "$(document).ready(function() { $('td#subnetsLeft').hide(); })";
				print "</script>";
			}
			else {
				// filtered
				if($section['showSupernetOnly']==1) {
				print "<tr>";
				print "	<td colspan='$colspan'><div class='alert alert-info'><i class='fa fa-info'></i> "._('Only master subnets are shown')."</div></td>";
				print "</tr>";
				}
			}
		}

		# subsection subnets
		if(sizeof($subsections)>0) {

			# subnets
			foreach($subsections as $ss) {
				# case
				$ss = (array) $ss;
				$slavesubnets = $Subnets->fetch_section_subnets($ss['id']);

				if(sizeof($slavesubnets)>0) {
					# headers
					print "<tr>";
					print "	<th colspan='$colspan'>"._('Available subnets in subsection')." $ss[name]:</th>";
					print "</tr>";

					// print subnets
					if($Subnets->print_subnets_tools($User->user, $slavesubnets, $cfs, true, $section['showSupernetOnly'])===false) {
						print "<tr>";
						print "	<td colspan='$colspan'><div class='alert alert-info'>"._('No subnets available')."</div></td>";
						print "</tr>";

						// hide left menu
						print "<script type='text/javascript'>";
						print "$(document).ready(function() { $('td#subnetsLeft').hide(); })";
						print "</script>";
					}
				}
				else {
					print "<tr>";
					print "	<th colspan='$colspan'>"._('Available subnets in subsection')." $ss[name]:</th>";
					print "</tr>";

					print "<tr>";
					print "	<td colspan='$colspan'><div class='alert alert-info'>"._('Section has no subnets')."!</div></td>";
					print "</tr>";
				}
			}
		}

		print "</tbody>";
		$m++;

	# end master table
	print "</table>";
}
else {
	print "<div class='alert alert-danger'>"._("You do not have permission to access this network")."!</div>";
}
?>
