<?php
/*
 * L2 Domain Import
 ************************************************/
 
 $User->csrf_cookie ("validate", "import-l2dom", $_POST['csrf_cookie']) === false ? $Result->show("danger", _("Invalid CSRF cookie"), true) : "";

# load data from uploaded file
include FUNCTIONS . '/ajax/import-load-data.php';
# check data and mark the entries to import/update
include FUNCTIONS . '/ajax/import-l2dom-check.php';

?>

<!-- header -->
<div class="pHeader"><?php print _("L2 Domain import"); ?></div>

<!-- content -->
<div class="pContent">

<?php

$msg = "";
$rows = "";

# import L2 Domains
foreach ($data as &$cdata) {
	if (($cdata['action'] == "add") || ($cdata['action'] == "edit")) {
		# set update array
		$values = array("id"=>$cdata['id'],
						"name"=>$cdata['name'],
						"description"=>$cdata['description']
						);

		# update
		$cdata['result'] = $Admin->object_modify("vlanDomains", $cdata['action'], "id", $values);

		if ($cdata['result']) {
			$trc = $colors[$cdata['action']];
			$msg = "L2 Domain ".$cdata['action']." successful.";
		} else {
			$trc = "danger";
			$msg = "L2 Domain ".$cdata['action']." failed.";
		}
		$rows.="<tr class='".$trc."'><td><i class='fa ".$icons[$cdata['action']]."' rel='tooltip' data-placement='bottom' title='"._($msg)."'></i></td>
			<td>".$cdata['name']."</td>
			<td>".$cdata['description']."</td>
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
