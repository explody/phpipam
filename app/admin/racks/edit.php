<?php

/**
 *	Edit rack details
 ************************/

$Racks      = new phpipam_rack ($Database);

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('racks');

# ID must be numeric
if($_POST['action']!="add" && !is_numeric($_POST['rackid']))		{ $Result->show("danger", _("Invalid ID"), true, true); }

# fetch device details
if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
	$rack = $Admin->fetch_object("racks", "id", $_POST['rackid']);
}
else {
    $rack = new StdClass ();
    $rack->size = 42;
}

# all locations
if($User->settings->enableLocations=="1")
$locations = $Tools->fetch_all_objects ("locations", "name");

# set readonly flag
$readonly = $_POST['action']=="delete" ? "readonly" : "";
?>

<!-- select2 -->
<script type="text/javascript" src="<?php print MEDIA; ?>/js/select2.js"></script>

<!-- common jquery plugins -->
<script type="text/javascript" src="<?php print MEDIA; ?>/js/common.plugins.js"></script>

<script type="text/javascript">
$(document).ready(function(){
     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
});
</script>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('rack'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="rackManagementEdit">
    <?php $csrf->insertToken('/ajx/admin/racks/edit-result'); ?>
    
	<table class="table table-noborder table-condensed">

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" name="name" class="form-control input-sm" placeholder="<?php print _('Name'); ?>" value="<?php if(isset($rack->name)) print $rack->name; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Type -->
	<tr>
		<td><?php print _('Size'); ?></td>
		<td>
			<select name="size" class="form-control input-sm input-w-auto">
			<?php
			foreach($Racks->rack_sizes as $s) {
				if($rack->size == $s)	{ print "<option value='$s' selected='selected'>$s U</option>"; }
				else					{ print "<option value='$s' >$s U</option>"; }
			}
			?>
			</select>
		</td>
	</tr>

	<!-- Location -->
	<?php if($User->settings->enableLocations=="1") { ?>
	<tr>
		<td><?php print _('Location'); ?></td>
		<td>
			<select name="location" id="r-location-select" class="select2">
    			<option value="0"><?php print _("None"); ?></option>
    			<?php
                if($locations!==false) {
                    $Components->render_options($locations, 
                          'id', 
                          ['name','description'], 
                           array(
                               'sort' => true,
                               'group' => false,
                               'selected' => array('id' => $rack->location),
                           )
                       );
    			}
    			?>
			</select>
            <?php
            Components::render_select2_js('#r-location-select',
                                          ['templateResult' => '$(this).s2boldDescTwoLine']);
            ?>
		</td>
	</tr>
	<?php } ?>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<textarea name="description" class="form-control input-sm" placeholder="<?php print _('Description'); ?>" <?php print $readonly; ?>><?php if(isset($rack->description)) print $rack->description; ?></textarea>
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
				print '<input type="hidden" name="rackid" value="'. $_POST['rackid'] .'">'. "\n";
			} ?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
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
    		$custom_input = $Components->render_custom_field_input ($cf, $rack, $_POST['action'], $index);
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
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editRacksubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- result -->
	<div class="rackManagementEditResult"></div>
</div>
