<?php
/**
 *	firewall zone ajax.php
 *	deliver content for ajax requests
 **************************************/

$Zones 	  = new FirewallZones($Database);

# generate a dropdown list for all subnets within a section
if ($_POST['operation'] == 'fetchSectionSubnets') {
	if($_POST['sectionId']) {
		if(preg_match('/^[0-9]+$/i',$_POST['sectionId'])) {
			$sectionId = $_POST['sectionId'];
			print $Subnets->print_mastersubnet_dropdown_menu($sectionId);
		} else {
			$Result->show('danger', _('Invalid ID.'), true);
		}
	}
}

# deliver zone details
if ($_POST['operation'] == 'deliverZoneDetail') {
	if ($_POST['zoneId']) {
		if(preg_match('/^[0-9]+$/i',$_POST['zoneId'])) {
			# return the zone details
			$Zones->get_zone_detail($_POST['zoneId']);

		} else {
			$Result->show('danger', _('Invalid zone ID.'), true);
		}
	}
}

# deliver networkinformations about a specific zone
if ($_POST['netZoneId']) {
	if(preg_match('/^[0-9]+$/i',$_POST['netZoneId'])) {
		# return the zone details
		$Zones->get_zone_network($_POST['netZoneId']);
	} else {
		$Result->show('danger', _('Invalid netZone ID.'), true);
	}
}

# deliver networkinformations about a specific zone
if ($_POST['noZone'] == 1) {
	if($_POST['masterSubnetId']) {
		$_POST['network'][] = $_POST['masterSubnetId'];
	}
	if ($_POST['network']) {
		$rowspan = count($_POST['network']);
		$i = 1;
		print '<table class="table table-noborder table-condensed" style="padding-bottom:20px;">';
		foreach ($_POST['network'] as $key => $network) {
			$network = $Subnets->fetch_subnet(null,$network);
			print '<tr>';
			if ($i === 1) {
				print '<td rowspan="'.$rowspan.'" style="width:150px;">Network</td>';
			}
			print '<td>';
			print '<span alt="'._('Delete Network').'" title="'._('Delete Network').'" class="deleteTempNetwork" style="color:red;margin-bottom:10px;margin-top: 10px;margin-right:15px;" data-action="delete" data-subnetArrayKey="'.$key.'"><i class="fa fa-close"></i></span>';
			if ($network->isFolder == 1) {
				print 'Folder: '.$network->description.'</td>';
			} else {
				# display network information with or without description
				if ($network->description) 	{	print $Subnets->transform_to_dotted($network->subnet).'/'.$network->mask.' ('.$network->description.')</td>';	}
				else 						{	print $Subnets->transform_to_dotted($network->subnet).'/'.$network->mask.'</td>';	}
			}
			print '<input type="hidden" name="network['.$key.']" value="'.$network->id.'">';
			print '</tr>';
			$i++;
		}
		print '</table>';
	}
}


# generate a new firewall address object on request
if ($_POST['operation'] == 'autogen') {
	if ($_POST['action'] == 'net') {
		if (preg_match('/^[0-9]+$/i',$_POST['subnetId'])){
			$Zones->update_address_objects($_POST['subnetId']);
		}
	} elseif ($_POST['action'] == 'adr') {
		if (preg_match('/^[0-9]+$/i',$_POST['subnetId']) && preg_match('/^[0-9a-zA-Z-.]+$/i',$_POST['dnsName']) && preg_match('/^[0-9]+$/i',$_POST['IPId'])) {
			$Zones->update_address_object($_POST['subnetId'],$_POST['IPId'],$_POST['dnsName']);
		}
	} elseif ($_POST['action'] == 'subnet') {
		if (preg_match('/^[0-9]+$/i',$_POST['subnetId'])) {
			$Zones->generate_subnet_object ($_POST['subnetId']);
		}
	}
}

# check if there is any mapping for a specific zone, if not, display inputs
if ($_POST['operation'] == 'checkMapping') {

	if (!$Zones->check_zone_mapping($_POST['zoneId']) && $_POST['zoneId'] != 0) {
		# fetch all firewall zones
		$firewallZones = $Zones->get_zones();

		# fetch settings
		$firewallZoneSettings = json_decode($User->settings->firewallZoneSettings,true);

		# fetch all devices
		$devices = $Tools->fetch_multiple_objects ("devices", "type", $firewallZoneSettings['deviceType']);

		?>
		<table class="table table-noborder table-condensed">
			<tr>
				<td colspan="2">
					<?php print _('In order to map this network to a zone without an existing device mapping you have to specify the following values.'); ?>
				</td>
			</tr>
			<tr>
				<!-- zone indicator -->
				<td>
					<?php print _('Firewall to map'); ?>
				</td>
				<td>
					<select name="deviceId" id="fw-device-select" class="select2" <?php print $readonly; ?>>
					<option value="0"><?php print _('Select firewall'); ?></option>
					<?php
                    // DRY - repeat in mapping-edit.php
					if(!empty($devices)) {
                        $Components->render_options($devices, 
                              'id', 
                              ['name','description'], 
                               array(
                                   'sort' => true,
                                   'group' => true,
                                   'groupby' => 'sections',
                                   'resolveGroupKey' => 'name',
                                   'extFields' => Devices::$extRefs,
                                   'selected' => array('id' => $mapping->deviceId),
                               )
                           );
					}
					?>
					</select>
                    <?php
                    Components::render_select2_js('#fw-device-select',
                                                  ['templateResult' => '$(this).s2boldDescTwoLine']);
                    ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php print _('Interface'); ?>
				</td>
				<td>
					<input type="text" class="form-control input-sm" name="interface" placeholder="<?php print _('Firewall interface'); ?>" value="<?php print $mapping->interface; ?>" <?php print $readonly; ?>>
				</td>
			</tr>
			<tr>
				<!-- description -->
				<td>
					<?php print _('Zone alias'); ?>
				</td>
				<td>
					<input type="text" class="form-control input-sm" name="alias" placeholder="<?php print _('Local zone alias'); ?>" value="<?php print $mapping->alias; ?>" <?php print $readonly; ?>>
				</td>
			</tr>
		</table>
		<?php
	} elseif ($_POST['zoneId'] != 0) {
		# return the zone details
		$Zones->get_zone_detail($_POST['zoneId']);
	}
}


?>
