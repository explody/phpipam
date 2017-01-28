<?php

/**
 * Function to get RIPe info for network
 ********************************************/

# try to fetch
$res = $Subnets->resolve_ripe_arin ($_POST['subnet']);
?>

<!-- header -->
<div class="pHeader"><?php print _(ucwords($res['result'])); ?></div>

<!-- content -->
<div class="pContent">
	<?php
	// error ?
	if ($res['result']=="error") {
		$Result->show("danger", _(ucwords($res['error'])), false);
	}
	// ok, print field matching
	else {
		// fetch all fields for subnets
		$standard_fields = array("description");
		$cfs = $Tools->fetch_custom_fields ("subnets");

		// leave only varchar and text
		foreach ($cfs as $idx=>$cf) {
			if ($cf->type != "string" || $cf->type != "text")) {
				unset($cfs[$k]);
			}
		}
        
		// FIXME: this is silly
        $desc_f = new stdClass();
        $desc_f->name = "description";
        array_push($cfs, $desc_f);

		print "<h4>"._("Please select fields to populate:")."</h4>";
		// form
		print "<form name='ripe-fields' id='ripe-fields'>";
		print "<table class='table'>";
		// loop
		if (isset($res['data'])) {
			foreach ($res['data'] as $k=>$d) {
				print "<tr>";
				print "<td>";
				print "	<span class='text-muted'>$k</span>:  $d";
				print "</td>";

				print "<td>";
				// add +
				print "<select name='$d' class='form-control input-sm'>";
				print "<option value='0'>None</option>";
				// print custom

				foreach ($cfs as $cf) {
					// replace descr with description
					if ($k == "descr") { 
                        $k = "description"; 
                    }

					if (strtolower($cf->name) == strtolower($k)) {
                        print "<option values='$cf->name' selected='selected'>$cf->display_name</option>";
                    } else {
                        print "<option values='$cf->name'>$cf->display_name</option>";
                    }
				}

				print "</select>";
				print "</td>";
				print "</tr>";
			}
		}
		else {
			$Result->show("info", _("No result"), false);
		}
		print "</table>";
		print "</form>";
	}
	?>
	</pre>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Cancel'); ?></button>
		<?php if($res['result']!="error") { ?>
		<button class="btn btn-sm btn-default btn-success" id="ripeMatchSubmit"><i class="fa fa-check"></i> <?php print _('fill'); ?></button>
		<?php } ?>
	</div>
</div>
