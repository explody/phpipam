<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/

# make sue user can edit
if ($User->is_admin(false)==false && $User->user->editVlan!="Yes") {
    $Result->show("danger", _("Not allowed to change VLANs"), true, true);
}

# fetch vlan details
$vlan = $Admin->fetch_object ("vlans", "vlanId", @$_POST['vlanId']);
$vlan = $vlan!==false ? (array) $vlan : array();
# fetch custom fields
$cfs = $Tools->fetch_custom_fields('vlans');

# set readonly flag
$readonly = $_POST['action']=="delete" ? "readonly" : "";

# set form name!
if(isset($_POST['fromSubnet'])) { $formId = "vlanManagementEditFromSubnet"; }
else 							{ $formId = "vlanManagementEdit"; }

# domain
if(!isset($_POST['domain'])) 	{ $_POST['domain']=1; }

# fetch l2 domain
if($_POST['action']=="add") {
	# all
	if (@$_POST['domain']=="all") {
		$vlan_domains = $Admin->fetch_all_objects("vlanDomains");
	} else {
		$vlan_domain = $Admin->fetch_object("vlanDomains", "id", $_POST['domain']);
	}
} else {
		$vlan_domain = $Admin->fetch_object("vlanDomains", "id", $vlan['domainId']);
}
if($vlan_domain===false)			{ $Result->show("danger", _("Invalid ID"), true, true); }
?>

<script type="text/javascript">
$(document).ready(function(){
     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
});
</script>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('VLAN'); ?></div>

<!-- content -->
<div class="pContent">
	<form id="<?php print $formId; ?>">
    <?php $csrf->insertToken('/ajx/admin/vlans/edit-result'); ?>
    
	<table id="vlanManagementEdit2" class="table table-noborder table-condensed">
	<!-- domain -->
	<tr>
		<td><?php print _('l2 domain'); ?></td>
		<th>
		<?php
		# not all
		if (@$_POST['domain']!="all") {
			print $vlan_domain->name." (".$vlan_domain->description.")";
		} else {
			print "<select name='domainId' class='form-control input-sm'>";
			foreach ($vlan_domains as $d) {
				print "<option value='$d->id'>$d->name</option>";
			}
			print "</select>";
		}
		?>
		</th>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<!-- number -->
	<tr>
		<td><?php print _('Number'); ?></td>
		<td>
			<input type="text" class="number form-control input-sm" name="number" placeholder="<?php print _('VLAN number'); ?>" value="<?php print @$vlan['number']; ?><?php print @$_POST['vlanNum']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" class="name form-control input-sm" name="name" placeholder="<?php print _('VLAN name'); ?>" value="<?php print @$vlan['name']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" class="description form-control input-sm" name="description" placeholder="<?php print _('Description'); ?>" value="<?php print @$vlan['description']; ?>" <?php print $readonly; ?>>
			<input type="hidden" name="vlanId" value="<?php print @$_POST['vlanId']; ?>">
			<?php if(@$_POST['domain']!=="all") { ?>
			<input type="hidden" name="domainId" value="<?php print $vlan_domain->id; ?>">
			<?php } ?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>

	<?php if($_POST['action']=="add" || $_POST['action']=="edit") { ?>
    <!-- require unique -->
    <tr>
	    <td colspan="2"><hr></td>
    </tr>
    <tr>
    	<td><?php print _('Unique VLAN'); ?></td>
    	<td>
	    	<input type="checkbox" name="unique" value="on">
	    	<span class="text-muted"><?php print _('Require unique vlan accross domains'); ?></span>
	    </td>
    </tr>
	<?php } ?>

	<!-- Custom -->
	<?php
	if(sizeof($cfs) > 0) {

		print '<tr>';
		print '	<td colspan="2"><hr></td>';
		print '</tr>';


		# custom field indexes
		$index = false;

		# all my fields
		foreach($cfs as $cf) {
    		// create input > result is array (required, input(html), timepicker_index)
    		$custom_input = $Components->render_custom_field_input($cf, $vlan, $_POST['action'], $index);
    		// add datepicker index
    		$index = $custom_input['index'];
            // print
			print "<tr>";
			print "	<td>".ucwords($cf->name)." ".$custom_input['required']."</td>";
			print "	<td>".$custom_input['field']."</td>";
			print "</tr>";
		}
	}
	?>

	</table>
	</form>

	<?php
	//print delete warning
	if($_POST['action'] == "delete")	{ $Result->show("warning", _('Warning').':</strong> '._('removing VLAN will also remove VLAN reference from belonging subnets')."!", false);  }
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default <?php if(isset($_POST['fromSubnet'])) { print "hidePopup2"; } else { print "hidePopups"; } ?>"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?> vlanManagementEditFromSubnetButton" id="editVLANsubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- result -->
	<div class="<?php print $formId; ?>Result"></div>
</div>
