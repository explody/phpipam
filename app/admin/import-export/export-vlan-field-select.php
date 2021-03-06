<?php

/**
 *	VLAN export
 */

# fetch all l2 domains
$vlan_domains = $Admin->fetch_all_objects("vlanDomains", "id");

# prepare HTML variables
$custom_fields_names = "";
$custom_fields_boxes = "";

foreach($Tools->fetch_custom_fields('vlans') as $cf) {
	$custom_fields_names.= "	<th>$cf->name</th>";
	$custom_fields_boxes.= "	<td><input type='checkbox' name='$cf->name' checked> </td>";
}

?>

<!-- header -->
<div class="pHeader"><?php print _("Select VLAN fields to export"); ?></div>

<!-- content -->
<div class="pContent" style="overflow:auto;">

<form id="selectExportTargets" method="post">
<input type="hidden" name="<?php print $csrf->getFormIndex(); ?>" />
<input type="hidden" name="<?php print $csrf->getFormToken(); ?>" />

<?php
print "	<table class='table table-striped table-condensed'>";
print "	<tr>";
print "	<th>"._('Name')."</th>";
print "	<th>"._('Number')."</th>";
print "	<th>"._('Domain')."</th>";
print "	<th>"._('Description')."</th>";
print $custom_fields_names;
print "	</tr>";

print "	<tr>";
print "	<td><input type='checkbox' name='name' checked title='"._('Mandatory')."'></td>";
print "	<td><input type='checkbox' name='number' checked> </td>";
print "	<td><input type='checkbox' name='domain' checked> </td>";
print "	<td><input type='checkbox' name='description' checked> </td>";
print $custom_fields_boxes;
print "	</tr>";
print '</table>';

if(sizeof($vlan_domains) > 0) {
	print '<h4>L2 Domains</h4>';
	print "	<table class='table table-striped table-condensed'>";
	print "	<tr>";
    print "	<th>"._('Name')."</th>";
    print "	<th>"._('Description')."</th>";
    print "	<th>"._('Export')."</th>";
    print "	</tr>\n";

	foreach($vlan_domains as $domain) {
		$domain = (array) $domain;

		print "	<tr>";
		print "	<td>".$domain['name']."</th>";
		print "	<td>".$domain['description']."</th>";
		print "	<td><input type='checkbox' name='exportDomain__".str_replace(" ", "_",$domain['name'])."' checked> </td>";
		print "	</tr>\n";
	}
}

print '</table>';
print '<div class="checkbox"><label><input type="checkbox" name="exportVLANDomains" checked>'._("Include the L2 domains in a separate sheet.").'</label></div>';

?>

</form>

</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-success" id="dataExportSubmit" data-form="selectExportTargets" data-type="vlan" data-action="export" data-csrf="<?php print htmlspecialchars(json_encode($csrf->getTokenArray('/ajx/admin/import-export/export-vlan'))); ?>"><i class="fa fa-upload"></i> <?php print _('Export'); ?></button>
	</div>
</div>
