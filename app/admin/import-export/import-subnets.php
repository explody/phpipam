<?php
/*
 * Subnets Import
 ************************************************/
 
 $Tools->csrf_validate($csrf, $Result);

# load data from uploaded file
include FUNCTIONS . '/ajax/import-load-data.php';
# check data and mark the entries to import/update
include FUNCTIONS . '/ajax/import-subnets-check.php';

?>

<!-- header -->
<div class="pHeader"><?php print _("Subnets import"); ?></div>

<!-- content -->
<div class="pContent">

<?php

$msg = "";
$rows = "";

# import Subnets
foreach ($data as &$cdata) {
	if (($cdata['action'] == "add") || ($cdata['action'] == "edit")) {
		// # set update array

		$values = array("id"=>$cdata['id'],
						"sectionId"=>$cdata['sectionId'],
						"subnet"=>$Subnets->transform_to_decimal($cdata['subnet']),
						"mask"=>$cdata['mask'],
						"description"=>@$cdata['description'],
						"vlanId"=>$cdata['vlanId'],
						"vrfId"=>$cdata['vrfId'],
						"masterSubnetId"=>$cdata['masterSubnetId'],
						"permissions"=>$cdata['permissions'],
						"isFolder"=>0
						);

		# add custom fields
		if(sizeof($custom_fields) > 0) {
			foreach($custom_fields as $cf) {
				if(isset($cdata[$cf->name])) { $values[$cf->name] = $cdata[$cf->name]; }
			}
		}

		# update
		$cdata['result'] = $Admin->object_modify("subnets", $cdata['action'], "id", $values);

		if ($cdata['result']) {
			$trc = $colors[$cdata['action']];
			$msg = "Subnets ".$cdata['action']." successful.";
		} else {
			$trc = "danger";
			$msg = "Subnets ".$cdata['action']." failed.";
		}

		$rows.="<tr class='".$trc."'><td><i class='fa ".$icons[$action]."' rel='tooltip' data-placement='bottom' title='"._($msg)."'></i></td>";
		foreach ($expfields as $cfield) { $rows.= "<td>".$cdata[$cfield]."</td>"; }
		$rows.= "<td>"._($msg)."</td></tr>";

	}
}
print _("After the import you should perform an automatic recomputation of the master/nested relations using the Recompute button after you close this window.");
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
