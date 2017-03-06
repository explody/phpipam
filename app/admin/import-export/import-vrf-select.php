<?php

/**
 *	VRF import form + upload
 */

$tpl_field_names = "";
$tpl_field_types = "";

# predefine field list
$expfields = array ("name","rd","description");
# required fields without which we will not continue
$reqfields = array("rd","name");

# manually adjust the standard fields
foreach($expfields as $std_field) {
	if (in_array($std_field,$reqfields)) {
		$msgr = "*";
	} else {
		$msgr = "";
	}

	$field = $Tools->fetch_full_field_definition("vrf",$std_field);
	$field = (array) $field;

    # TODO: $res is not used anywhere
	$res[$field['Field']]['name'] 	 = $field['Field'];
	$res[$field['Field']]['type'] 	 = $field['Type'];
	$res[$field['Field']]['Comment'] = $field['Comment'];
	$res[$field['Field']]['Null'] 	 = $field['Null'];
	$res[$field['Field']]['Default'] = $field['Default'];

	#prebuild template table rows to avoid useless foreach loops
	$tpl_field_names.= "<th>".$field['Field'].$msgr."</th>";
	$tpl_field_types.= "<td><small>". wordwrap($field['Type'],18,"<br>\n",true) ."</small></td>";
}

# append the custom fields, if any
$cfs = $Tools->fetch_custom_fields("vrf");
$res[] = $cfs;
foreach($cfs as $cf) {
	$tpl_field_names.= "<th>". $cf->name ."</th>";
	$tpl_field_types.= "<td><small>". wordwrap($cf->type,18,"<br>\n",true) ."</small></td>";
	$expfields[] = $cf->name;
}


?>

<!-- header -->
<div class="pHeader"><?php print _("Select VRF file and fields to import"); ?></div>

<!-- content -->
<div class="pContent">

<?php

# print template form
print "<form id='selectImportFields'><div id='topmsg'>";
$csrf->insertToken('/ajx/admin/import-export/import-vrf-preview');
print '<h4>'._("Template").'</h4><hr>';
print _("The import XLS/CSV should have the following fields and a <b>header row</b> for a succesful import:");
print "</div>";
print "<input name='expfields' type='hidden' value='".implode('|',$expfields)."' style='display:none;'>";
print "<input name='reqfields' type='hidden' value='".implode('|',$reqfields)."' style='display:none;'>";
print "<input name='filetype' id='filetype' type='hidden' value='' style='display:none;'>";
print "<table class='table table-striped table-condensed' id='fieldstable'><tbody>";
print "<tr>" . $tpl_field_names . "</tr>";
print "<tr>" . $tpl_field_types . "</tr>";
print "</tbody></table>";
print "<div id='bottommsg'>"._("The fields marked with * are mandatory.")."</div>";
print "</form>";

$templatetype = 'vrf';
# print upload section
print "<div id='uplmsg'>";
print '<h4>'._("Upload file").'</h4><hr>';
include FUNCTIONS . '/ajax/import-button.php';
print "</div>";

?>

</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default" id="dataImportPreview" data-type="vrf" disabled><i class="fa fa-eye"></i> <?php print _('Preview'); ?></button>
	</div>
</div>
