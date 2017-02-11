<?php

/**
 * Print all vlans
 */




# fetch l2 domain
$vlan_domain = new StdClass();

# get all VLANs and subnet descriptions
$vlans = $Tools->fetch_all_domains_and_vlans ();

# get custom VLAN fields
$cfs = $Tools->fetch_custom_fields('vlans');

# size of custom fields
$csize = Tools::count_where($cfs, 'required', 1);

# set disabled for non-admins
$disabled = $User->is_admin(false)==true ? "" : "hidden";

# title
print "<h4>"._('VLANs in all domains')."</h4>";
print "<hr>";
print "<div class='text-muted' style='padding-left:10px;'>"._('List of VLANS in all domains')."</div><hr>";

print "<div class='btn-group' style='margin-bottom:10px;'>";
print "<a class='btn btn-sm btn-default' href='".create_link($_GET['page'], $_GET['section'])."'><i class='fa fa-angle-left'></i> "._('L2 Domains')."</a>";
print "</div>";


# no VLANS?
if($vlans===false) {
	print "<hr>";
	$Result->show("info", _("No VLANS configured"), false);
}
else {
	# table
	print "<table class='table sorted vlans table-condensed table-top'>";

	# headers
	print "<thead>";
	print '<tr">' . "\n";
	print ' <th data-field="number" data-sortable="true">'._('Number').'</th>' . "\n";
	print ' <th data-field="name" data-sortable="true">'._('Name').'</th>' . "\n";
	print ' <th data-field="name" data-sortable="true">'._('L2domain').'</th>' . "\n";

	foreach($cfs as $cf) {
		if($cf->visible) {
			print "	<th class='hidden-xs hidden-sm hidden-md'>$cf->name</th>";
		}
	}

    print "<th></th>";
	print "</tr>";
	print "</thead>";

	print "<tbody>";
	$m = 0;
	foreach ($vlans as $vlan) {

		// show free vlans - start
		if($User->user->hideFreeRange!=1 && !isset($_GET['sPage'])) {
			if($m==0 && $vlan->number!=1)	{
				print "<tr class='success'>";
				print "<td></td>";
				print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN $disabled' data-action='add' data-domain='all' data-number='1'><i class='fa fa-plus'></i></btn> "._('VLAN')." 1 - ".($vlan->number -1)." (".($vlan->number -1)." "._('free').")</td>";
				print "</tr>";
			}
			# show free vlans - before vlan
			if($m>0)	{
				if( (($vlan->number)-($vlans[$m-1]->number)-1) > 0 ) {
				print "<tr class='success'>";
				print "<td></td>";
				# only 1?
				if( (($vlan->number)-($vlans[$m-1]->number)-1) ==1 ) {
				print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN $disabled' data-action='add' data-domain='all' data-number='".($vlan->number -1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlan->number -1)." (".(($vlan->number)-($vlans[$m-1]->number)-1)." "._('free').")</td>";
				} else {
				print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN $disabled' data-action='add' data-domain='all' data-number='".($vlans[$m-1]->number+1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlans[$m-1]->number+1)." - ".($vlan->number -1)." (".(($vlan->number)-($vlans[$m-1]->number)-1)." "._('free').")</td>";
				}
				print "</tr>";
				}
			}
		}

		// fixes
		$vlan->description = strlen($vlan->description)>0 ? " <span class='text-muted'>( ".$vlan->description." )</span>" : "";
		$vlan->domainDescription = strlen($vlan->domainDescription)>0 ? " <span class='text-muted'>( ".$vlan->domainDescription." )</span>" : "";

		//set odd / even
		$n = @$n==1 ? 0 : 1;
		$class = $n==0 ? "odd" : "even";
		//start - VLAN details
		print "<tr class='$class change'>";
		print "	<td><a href='".create_link($_GET['page'], $_GET['section'], $vlan->domainId, $vlan->id)."'>".$vlan->number."</td>";
		print "	<td>".$vlan->name.$vlan->description."</td>";
		print "	<td>".$vlan->domainName.$vlan->domainDescription."</td>";
        //custom fields - no subnets

   		foreach($cfs as $cf) {
	   		# hidden
	   		if($cf->visible) {

				// create links
				$vlan->{$cf->name} = $Result->create_links ($vlan->{$cf->name}, $cf->type);

				print "<td class='hidden-xs hidden-sm hidden-md'>";
				//booleans
				if($cf->type == "boolean")	{
                    print Components::boolean_display_value($vlan->{$cf->name});
				}
				//text
				elseif($cf->type == "text") {
					if(strlen($vlan->{$cf->name})>0)		{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $vlan->{$cf->name})."'>"; }
					else									{ print ""; }
				}
				else {
					print $vlan->{$cf->name};

				}
				print "</td>";
			}
    	}


        // actions
		print "	<td class='actions'>";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-xs btn-default editVLAN' data-action='edit'   data-vlanid='$vlan->id'><i class='fa fa-pencil'></i></button>";
		print "		<button class='btn btn-xs btn-default moveVLAN' 					 data-vlanid='$vlan->id'><i class='fa fa-external-link'></i></button>";
		print "		<button class='btn btn-xs btn-default editVLAN' data-action='delete' data-vlanid='$vlan->id'><i class='fa fa-times'></i></button>";
		print "	</div>";
		print "	</td>";

        print "</tr>";

		# show free vlans - last
		if($User->user->hideFreeRange!=1 && !isset($_GET['sPage'])) {
			if($m==(sizeof($vlans)-1)) {
				if($User->settings->vlanMax > $vlans[0]->number) {
					print "<tr class='success'>";
					print "<td></td>";
					print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN $disabled' data-action='add' data-domain='all' data-number='".($vlan->number+1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlan->number+1)." - ".$User->settings->vlanMax." (".(($User->settings->vlanMax)-($vlan->number))." "._('free').")</td>";
					print "</tr>";
				}
			}
		}
		# next index
		$m++;
	}
	print "</tbody>";

	print '</table>';
}
?>
