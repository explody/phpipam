<?php

/**
 *	IP Addresses import form + upload
 */

$tpl_field_names = "";
$tpl_field_types = "";

# predefine field list
$expfields = array ("section","ip_addr","dns_name","description","vrf","subnet","mac","owner","device","note","tag","gateway");
//$disfields = array ("Section","IP Address","Hostname","Description","VRF","Subnet","MAC","owner","device","note","TAG");
$mtable = "ipaddresses"; # main table where to check the fields

# extra fields
$extfields["section"]["table"] = "sections";
$extfields["section"]["field"] = "name";
$extfields["section"]["pname"] = "section";
$extfields["vrf"]["table"] = "vrf";
$extfields["vrf"]["field"] = "name";
$extfields["vrf"]["pname"] = "vrf";
$extfields["subnet"]["table"] = "subnets";
$extfields["subnet"]["field"] = "subnet";
$extfields["subnet"]["pname"] = "subnet";
//$extfields["mask"]["table"] = "subnets";
//$extfields["mask"]["field"] = "mask";
//$extfields["mask"]["pname"] = "subnet";
$extfields["device"]["table"] = "devices";
$extfields["device"]["field"] = "hostname";
$extfields["device"]["pname"] = "device";
$extfields["tag"]["table"] = "ipTags";
$extfields["tag"]["field"] = "type";
$extfields["tag"]["pname"] = "tag";

## using the extra fields as a trick to display some nicer names for these regular fields
$extfields["ip_addr"]["table"] = "ipaddresses";
$extfields["ip_addr"]["field"] = "ip_addr";
$extfields["ip_addr"]["pname"] = "IP address";
$extfields["dns_name"]["table"] = "ipaddresses";
$extfields["dns_name"]["field"] = "dns_name";
$extfields["dns_name"]["pname"] = "Hostname";
$extfields["gateway"]["table"] = "ipaddresses";
$extfields["gateway"]["field"] = "is_gateway";
$extfields["gateway"]["pname"] = "Gateway";

# required fields without which we will not continue
$reqfields = array("section","ip_addr","subnet");

# manually adjust the standard fields
foreach($expfields as $std_field) {
	# extra table and field
	if (isset($extfields[$std_field])) {
		$cfield = $extfields[$std_field]["field"];
		$ctable = $extfields[$std_field]["table"];
		$pname  = $extfields[$std_field]["pname"]." ";
	} else {
		# default table and field
		$cfield = $std_field;
		$ctable = $mtable;
		$pname = "";
	}

	# read field attributes
	$field = $Tools->fetch_full_field_definition($ctable,$cfield);
	$field = (array) $field;

	# mark required fields with *
	$msgr = in_array($std_field,$reqfields) ? "*" : "";

	#prebuild template table rows to avoid useless foreach loops
	$tpl_field_names.= "<th>".($pname ? $pname : $field['Field']).$msgr."</th>";
	$tpl_field_types.= "<td><small>". wordwrap($field['Type'],18,"<br>\n",true) ."</small></td>";
}

# append the custom fields, if any
foreach($Tools->fetch_custom_fields($mtable) as $cf) {
	# add field to required fields if needed
	if (!$cf->null) { $reqfields[] = $cf->name; }
	# mark required fields with *
	$msgr = in_array($cf->name,$reqfields) ? "*" : "";

	$tpl_field_names.= "<th>".$cf->name.$msgr."</th>";
	$tpl_field_types.= "<td><small>". wordwrap($cf->type,18,"<br>\n",true) ."</small></td>";
	$expfields[] = $cf->name;
}


?>

<!-- header -->
<div class="pHeader"><?php print _("Select IP Addresses file and fields to import"); ?></div>

<!-- content -->
<div class="pContent">

<?php

# print template form
print "<form id='selectImportFields'><div id='topmsg'>";
$csrf->insertToken('/ajx/admin/import-export/import-ipaddr-preview');
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
print "<div id='bottommsg'>"._("The fields marked with * are mandatory.")."
	<br>"._("Providing a subnet is optional, the system will add the IP to the longest match if no subnet is provided.")."
	<br>"._("The mask can be provided either as a separate field or with the subnet, sparated by \"/\"")."
	</div>";
print "<div class='checkbox'><label><input name='searchallvrfs' id='searchallvrfs' type='checkbox' unchecked>"._("Search for matching subnet in all VRFs (ignore provided VRF).")."</label></div>";
#TODO# add option to hide php fields
#print "<div class='checkbox'><label><input name='showspecific' id='showspecific' type='checkbox' unchecked>"._("Show PHPIPAM specific columns.")."</label></div>";
print "</form>";

$templatetype = 'ipaddr';
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
		<button class="btn btn-sm btn-default" id="dataImportPreview" data-type="ipaddr" disabled><i class="fa fa-eye"></i> <?php print _('Preview'); ?></button>
	</div>
</div>
