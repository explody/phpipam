<?php

/**
 *	Print all available locations
 ************************************************/

# get Location object
if($_POST['action']!="add") {
	$location = $Admin->fetch_object ("locations", "id", $_POST['id']);
	$location!==false ? : $Result->show("danger", _("Invalid ID"), true, true);
}

# disable edit on delete
$readonly = $_POST['action']=="delete" ? "readonly" : "";
$link = $readonly ? false : true;

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('locations');

?>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('Location'); ?></div>

<!-- content -->
<div class="pContent">

	<form id="editLocation">
	<table id="editLocation" class="table table-noborder table-condensed">

	<tbody>
    	<!-- name -->
    	<tr>
        	<th><?php print _('Name'); ?></th>
        	<td>
            	<input type="text" class="form-control input-sm" name="name" value="<?php print $location->name; ?>" placeholder='<?php print _('Name'); ?>' <?php print $readonly; ?>>
            	<?php $csrf->insertToken('/ajx/admin/locations/edit-result'); ?>
            	<input type="hidden" name="id" value="<?php print $location->id; ?>">
            	<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
        	</td>
        	<td>
            	<span class="text-muted"><?php print _("Set Location name"); ?></span>
        	</td>
        </tr>

        <!-- description -->
    	<tr>
        	<th><?php print _('Description'); ?></th>
        	<td colspan="2">
            	<textarea class="form-control input-sm" name="description" placeholder='<?php print _('Description'); ?>' <?php print $readonly; ?>><?php print $location->description; ?></textarea>
        	</td>
        </tr>

    	<!-- Address -->
    	<tr>
        	<td colspan="3"><hr></td>
    	</tr>
    	<tr>
        	<th><?php print _('Address'); ?></th>
        	<td>
            	<input type="text" class="form-control input-sm" name="address" value="<?php print $location->address; ?>" placeholder='<?php print _('Address'); ?>' <?php print $readonly; ?>>
            	<?php print _('or'); ?>
        	</td>
        	<td>
            	<span class="text-muted"><?php print _("Location address"); ?></span>
        	</td>
        </tr>

    	<tr>
        	<th><?php print _('Latitude'); ?></th>
        	<td>
            	<input type="text" class="form-control input-sm" name="lat" value="<?php print $location->lat; ?>" placeholder='<?php print _('Latitude'); ?>' <?php print $readonly; ?>>
        	</td>
        	<td>
            	<span class="text-muted"><?php print _("latitude"); ?></span>
        	</td>
        </tr>

    	<tr>
        	<th><?php print _('Longitude'); ?></th>
        	<td>
            	<input type="text" class="form-control input-sm" name="long" value="<?php print $location->long; ?>" placeholder='<?php print _('Longitude'); ?>' <?php print $readonly; ?>>
        	</td>
        	<td>
            	<span class="text-muted"><?php print _("Longitude"); ?></span>
        	</td>
        </tr>

    	<!-- Custom -->
    	<?php
    	if(sizeof($cfs) > 0) {

    		print '<tr>';
    		print '	<td colspan="2"><hr></td>';
    		print '</tr>';
            
            $index = false;

    		# all my fields
    		foreach($cfs as $cf) {
        		// create input > result is array (required, input(html), index)
        		$custom_input = $Components->render_custom_field_input ($cf, $location, $_POST['action'], $index);
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


	</tbody>

	</table>
	</form>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopupsReload"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editLocationSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	<!-- result -->
	<div class="editLocationResult"></div>
</div>
