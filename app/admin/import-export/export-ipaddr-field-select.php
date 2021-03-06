<?php

/**
 *	IP Addresses export
 */

# Won't check per subnet/section rights since this is an admin section, where the admin user has full access

# fetch all sections
$all_sections = $Sections->fetch_all_sections();

# Lets do some reordering to show slaves!
if($all_sections!==false) {
	foreach($all_sections as $s) {
		if($s->masterSection=="0") {
			# it is master
			$s->class = "master";
			$sectionssorted[] = $s;
			# check for slaves
			foreach($all_sections as $ss) {
				if($ss->masterSection==$s->id) {
					$ss->class = "slave";
					$sectionssorted[] = $ss;
				}
			}
		}
	}
	# set new array
	$sections_sorted = @$sectionssorted;
}

# prepare HTML variables
$custom_fields_names = "";
$custom_fields_boxes = "";

foreach($Tools->fetch_custom_fields('ipaddresses') as $cf) {
	$custom_fields_names .= "	<th>" . Components::custom_field_display_name($cf) . "</th>";
	$custom_fields_boxes .= "	<td><input type='checkbox' name='$cf->name' checked> </td>";
}

?>

<!-- header -->
<div class="pHeader"><?php print _("Select sections and IP address fields to export"); ?></div>

<!-- content -->
<div class="pContent">

<form id="selectExportTargets" method="post">
<input type="hidden" name="<?php print $csrf->getFormIndex(); ?>" />
<input type="hidden" name="<?php print $csrf->getFormToken(); ?>" />

<?php
print '<h4>Fields</h4>';
print "	<table class='table table-striped table-condensed'>";
print "	<tr>";
print "	<th>"._('Section')."</th>";
print "	<th>"._('IP Address')."</th>";
print "	<th>"._('Hostname')."</th>";
print "	<th>"._('Description')."</th>";
print "	<th>"._('VRF')."</th>";
print "	<th>"._('Subnet')."</th>";
print "	<th>"._('MAC')."</th>";
print "	<th>"._('Owner')."</th>";
print "	<th>"._('Device')."</th>";
print "	<th>"._('Note')."</th>";
print "	<th>"._('Tag')."</th>";
print "	<th>"._('Gateway')."</th>";
print $custom_fields_names;
print "	</tr>";

print "	<tr>";
print "	<td><input type='checkbox' name='section' checked></td>";
print "	<td><input type='checkbox' name='ip_addr' checked title='"._('Mandatory')."'> </td>";
print "	<td><input type='checkbox' name='dns_name' checked title='"._('Mandatory')."'></td>";
print "	<td><input type='checkbox' name='description' checked> </td>";
print "	<td><input type='checkbox' name='vrf' checked></td>";
print "	<td><input type='checkbox' name='subnet' checked></td>";
print "	<td><input type='checkbox' name='mac'> </td>";
print "	<td><input type='checkbox' name='owner'> </td>";
print "	<td><input type='checkbox' name='device'> </td>";
print "	<td><input type='checkbox' name='note'> </td>";
print "	<td><input type='checkbox' name='tag'> </td>";
print "	<td><input type='checkbox' name='gateway'> </td>";
print $custom_fields_boxes;
print "	</tr>";
print '</table>';

# show sections
if($all_sections!==false) {
	print '<h4>Sections</h4>';
	print "	<table class='table table-striped table-condensed'>";
	print "	<tr>";
    print '	<th><input type="checkbox" id="exportSelectAll" checked> '._('Name').'</th>';
    print "	<th>"._('Description')."</th>";
    print "	<th>"._('Parent')."</th>";
    print "	</tr>\n";

	# existing sections
	foreach ($sections_sorted as $section) {
		//cast
		$section = (array) $section;

		print '<tr>';
		print '	<td><div class="checkbox"><label><input type="checkbox" id="exportCheck" name="exportSection__'.str_replace(" ", "_", $section['name']).'" checked>'.str_replace("_", " ", $section['name']).'</label></div></td>';
		print '	<td>'. $section['description'] .'</td>'. "\n";
		//master Section
		if($section['masterSection']!=0) {
			# get section details
			$ssec = $Admin->fetch_object("sections", "id", $section['masterSection']);
			print "	<td>$ssec->name</td>";
		} else {
			print "	<td>/</td>";
		}
		print '</tr>'. "\n";
	}

	print '</table>';

	print '<div class="checkbox"><label><input type="checkbox" name="exportSections" checked>'._("Include the sections in a separate sheet.").'</label></div>';
//	print '<div class="checkbox"><label><input type="checkbox" name="separateSheets">'._("Export each section in a separate sheet.").'</label></div>';

}

?>

</form>

</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-success" id="dataExportSubmit" data-form="selectExportTargets" data-type="ipaddr" data-action="export" data-csrf="<?php print htmlspecialchars(json_encode($csrf->getTokenArray('/ajx/admin/import-export/export-ipaddr'))); ?>"><i class="fa fa-upload"></i> <?php print _('Export'); ?></button>
	</div>
</div>



