<?php
/*
 * VLAN Import
 ************************************************/

$Tools->csrf_validate($csrf, $Result);

# load data from uploaded file
include FUNCTIONS . '/ajax/import-load-data.php';
# check data and mark the entries to import/update
include FUNCTIONS . '/ajax/import-vlan-check.php';

?>

<!-- header -->
<div class="pHeader"><?php print _("VLAN import"); ?></div>

<!-- content -->
<div class="pContent">

<?php

$msg = "";
$rows = "";

# import VLANs
foreach ($data as &$cdata) {
	if (($cdata['action'] == "add") || ($cdata['action'] == "edit")) {
		# set update array
		$values = array("vlanId"=>$cdata['vlanId'],
						"number"=>$cdata['number'],
						"name"=>$cdata['name'],
						"description"=>$cdata['description'],
						"domainId"=>$cdata['domainId']
						);
		# add custom fields
		if(sizeof($custom_fields) > 0) {
			foreach($custom_fields as $cf) {
				if(isset($cdata[$cf->name])) { $values[$cf->name] = $cdata[$cf->name]; }
			}
		}

		# update
		$cdata['result'] = $Admin->object_modify("vlans", $cdata['action'], "vlanId", $values);

		if ($cdata['result']) {
			$trc = $colors[$cdata['action']];
			$msg = "VLAN ".$cdata['action']." successful.";
		} else {
			$trc = "danger";
			$msg = "VLAN ".$cdata['action']." failed.";
		}
		$rows.="<tr class='".$trc."'><td><i class='fa ".$icons[$cdata['action']]."' rel='tooltip' data-placement='bottom' title='"._($msg)."'></i></td>
			<td>".$cdata['name']."</td>
			<td>".$cdata['number']."</td>
			<td>".$cdata['description']."</td>
			<td>".$cdata['domain']."</td>
			".$cfieldtds."
			<td>"._($msg)."</td></tr>";
	}
}

print "<table class='table table-condensed table-hover' id='resultstable'><tbody>";
print "<tr class='active'>".$hrow."<th>Result</th></tr>";
print $rows;
print "</tbody></table><br>";
?>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-sm btn-default hidePopups"><?php print _('Close'); ?></button>
</div>
