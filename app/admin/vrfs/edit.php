<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/

# make sue user can edit
if ($User->is_admin(false)==false && $User->user->editVlan!="Yes") {
    $Result->show("danger", _("Not allowed to change VRFs"), true, true);
}

# get VRF
if($_POST['action']!="add") {
	$vrf = $Admin->fetch_object ("vrf", "vrfId", $_POST['vrfId']);
	$vrf!==false ? : $Result->show("danger", _("Invalid ID"), true, true);
	$vrf = (array) $vrf;
}

# disable edit on delete
$readonly = $_POST['action']=="delete" ? "readonly" : "";

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('vrf');
?>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('VRF'); ?></div>

<!-- content -->
<div class="pContent">

	<form id="vrfManagementEdit">
    <?php $csrf->insertToken('/ajx/admin/vrfs/edit-result'); ?>
    
	<table id="vrfManagementEdit2" class="table table-noborder table-condensed">

	<!-- name  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" class="name form-control input-sm" name="name" placeholder="<?php print _('VRF name'); ?>" value="<?php print @$vrf['name']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>
	<!-- RD -->
	<tr>
		<td><?php print _('RD'); ?></td>
		<td>
			<input type="text" class="rd form-control input-sm" name="rd" placeholder="<?php print _('Route distinguisher'); ?>" value="<?php print @$vrf['rd']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>
	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) { print '<input type="hidden" name="vrfId" value="'. $_POST['vrfId'] .'">'. "\n";}
			?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
			<input type="text" class="description form-control input-sm" name="description" placeholder="<?php print _('Description'); ?>" value="<?php print @$vrf['description']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
    
	<!-- sections -->
	<tr>
		<td style="vertical-align: top !important"><?php print _('Sections'); ?>:</td>
		<td>
            <select multiple="multiple" id="sections" name="sections[]">
		<?php
        # TODO: DRY, see admin/devices/edit.php
		# select sections
		$sections = $Sections->fetch_all_sections('name');
		
        # reformat domains sections to array
		$vrf_sections = explode(";", @$vrf['sections']);
		$vrf_sections = is_array($vrf_sections) ? $vrf_sections : array();
        $selectedSections = array();
		// loop
        if ($sections !== false) {
			foreach($sections as $section) {
                if(in_array($section->id, $vrf_sections)) { 
                    $selectedSections[] = $section->id;
                } 
                print '<option value="' . $section->id . '">' . $section->name . '</option>';
			}
		}
		?>
            </select>
            <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.multi-select.js"></script>
            <script type="text/javascript">
               $('#sections').multiSelect({});
               $('#sections').multiSelect(
                   'select', <?php echo "['" . implode("','", $selectedSections) . "']"; ?>
               );
            </script>
		</td>
	</tr>

	<!-- Custom -->
	<?php
	if(sizeof($cfs) > 0) {

		print '<tr>';
		print '	<td colspan="2"><hr></td>';
		print '</tr>';

		# count datepickers
		$index = false;

		# all my fields
		foreach($cfs as $cf) {
    		// create input > result is array (required, input(html), timepicker_index)
    		$custom_input = $Components->render_custom_field_input($cf, $vrf, $_POST['action'], $index);
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
	if($_POST['action'] == "delete")	{ $Result->show("warning", "<strong>"._('Warning').":</strong> "._("removing VRF will also remove VRF reference from belonging subnets!"), false);}
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editVRF"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	<!-- result -->
	<div class="vrfManagementEditResult"></div>
</div>
