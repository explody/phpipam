<?php

/**
 *	subnet-to-zone.php
 *	add subnet (from detail view) to existing firewall zone
 *************************************************************/

$Zones    = new FirewallZones($Database);

# validate $_POST['operation'] values
if ($_POST['operation'] != 'subnet2zone') 				{ $Result->show("danger", _("Invalid operation. Do not manipulate the POST values!"), true); }

# validate $_POST['subnetId'] values
if (!preg_match('/^[0-9]+$/i', $_POST['subnetId'])) 	{ $Result->show("danger", _("Invalid subnet ID. Do not manipulate the POST values!"), true); }

$firewallZones = $Zones->get_zones();

# no zones
if($firewallZones===false)                              { $Result->show("danger", _("No zones available"), true, true); }
?>

<!-- header  -->
<div class="pHeader"><?php print _('Add this subnet to a firewall zone'); ?></div>
<!-- content -->
<div class="pContent">
<!-- form -->
<form id="subnet-to-zone-edit">
    
<?php $csrf->insertToken('/ajx/admin/firewall-zones/subnet-to-zone-save'); ?>

<input type="hidden" name="subnetId" value="<?php print $_POST['subnetId']; ?>">
<!-- table -->
<table class="table table-noborder table-condensed">
	<!-- zone -->
	<tr>
		<td style="width:150px;">
			<?php print _('Zone name'); ?>
		</td>
		<td>
			<select name="zoneId" class="form-control input-sm input-w-auto input-max-200 checkMapping">
			<option value="0"><?php print _('Select a Zone'); ?></option>
			<?php
				foreach ($firewallZones as $firewallZone) {
					print '<option value="'.$firewallZone->id.'">'.$firewallZone->zone.' '.(($firewallZone->description) ? ' ('.$firewallZone->description.')' : '' ).'</option>';
				}
			?>
			</select>
		</td>
	</tr>
</table>
<div class="mappingAdd"></div>
</div>
</form>
</div>
<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success" id="subnet-to-zone-submit" data-action="edit"><i class="fa fa-plus"></i> <?php print _('Add this subnet to a firewall zone'); ?></button>
	</div>
	<!-- result -->
	<div class="subnet-to-zone-result"></div>
</div>
