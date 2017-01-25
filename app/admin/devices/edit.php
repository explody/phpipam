<?php

/**
 *	Edit device details
 ************************/

# create csrf token
$csrf = $User->csrf_create('device');

# fetch custom fields
$custom = $Tools->fetch_custom_fields('devices');

if(!in_array($_POST['action'], ['add','edit','delete'])) { 
    $Result->show("danger", _("Invalid action"), true, true); 
}

# ID must be numeric
if($_POST['action'] != "add" && !is_numeric($_POST['switchId'])) { 
    $Result->show("danger", _("Invalid ID"), true, true);
}

# fetch device details
if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
	$device = $Admin->fetch_object("devices", "id", $_POST['switchId']);
	// false
	if ($device===false) {
        $Result->show("danger", _("Invalid ID"), true, true);
    }
} else {
    $device = new stdClass(); // empty object for adds
}

$action = $_POST['action'];

# set readonly flag
$readonly = $action == "delete" ? "readonly" : "";

# all locations
if($User->settings->enableLocations == "1")   {
    $locations = $Tools->fetch_all_objects ("locations", "name");
}

// set show for rack
if (is_null($device->rack)) {
    $display='display:none';
} else {
    $display='';
}

?>
<!-- select2 -->
<script async type="text/javascript" src="<?php print MEDIA; ?>/js/select2.js"></script>

<!-- common jquery plugins -->
<script async type="text/javascript" src="<?php print MEDIA; ?>/js/common.plugins.js"></script>

<script type="text/javascript">
$(document).ready(function(){
     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
});
// form change
$('#rack-select').change(function() {
   //change id
   $('.showRackPopup').attr("data-rackid",$('#switchManagementEdit select[name=rack]').val());
   //toggle show
   if($('#switchManagementEdit select[name=rack]').val().length == 0) { 
       $('tbody#rack').hide(); 
   } else { 
       $('tbody#rack').show();
   }
});
</script>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('device'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="switchManagementEdit">
	<table class="table table-noborder table-condensed">

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" name="hostname" class="form-control input-sm" placeholder="<?php print _('Hostname'); ?>" value="<?php if(isset($device->hostname)) print $device->hostname; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- IP address -->
	<tr>
		<td><?php print _('IP address'); ?></td>
		<td>
			<input type="text" name="ip_addr" class="form-control input-sm" placeholder="<?php print _('IP address'); ?>" value="<?php if(isset($device->ip_addr)) print $device->ip_addr; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Type -->
	<tr>
		<td><?php print _('Device type'); ?></td>
		<td>
			<select name="type" id="dtype-select" class="select2">
			<?php
			$types = $Admin->fetch_all_objects("deviceTypes", "id");
            $Components->render_options($types, 
                  'id', 
                  ['name','description'], 
                   array(
                       'sort' => true,
                       'group' => false,
                       'selected' => array('id' => $device->type),
                   )
               );
			?>
			</select>
            <?php 
            Components::render_select2_js('#dtype-select',
                                          ['templateResult' => '$(this).s2boldDescOneLine']);
            ?>
		</td>
	</tr>

	<!-- Location -->
	<?php if($User->settings->enableLocations=="1") { ?>
	<tr>
		<td><?php print _('Location'); ?></td>
		<td>
			<select name="location_item" id="location-select" class="select2">
    			<option value="0"><?php print _("None"); ?></option>
                <?php
                if($locations!==false) {
                    $Components->render_options($locations, 
                          'id', 
                          ['name','description'], 
                           array(
                               'sort' => true,
                               'group' => false,
                               'selected' => array('id' => $device->location),
                           )
                       );
                }
                ?>
			</select>
            <?php 
            Components::render_select2_js('#location-select',
                                          ['templateResult' => '$(this).s2boldDescOneLine']);
            ?>
		</td>
	</tr>
	<?php } ?>

    <!-- Rack -->
    <?php if($User->settings->enableRACK == "1") { ?>
	<tr>
	   	<td colspan="2"><hr></td>
    </tr>
    <tr>
        <?php
        $Racks = new phpipam_rack ($Database);
        $Racks->fetch_all_racks();
        ?>
        <td><?php print _('Rack'); ?></td>
        <td>
            <select name="rack" id="rack-select" class="select2">
                <option value=""><?php print _("None"); ?></option>
                <?php
                $Components->render_options((array) $Racks->all_racks, 
                      'id', 
                      ['name','description'], 
                       array(
                           'sort' => true,
                           'group' => false,
                           'selected' => array('id' => $device->rack),
                       )
                   );
                ?>
            </select>
            <?php 
            Components::render_select2_js('#rack-select',
                                          ['templateResult' => '$(this).s2boldDescOneLine']);
            ?>
        </td>
    </tr>

    <tbody id="rack" style="<?php print $display; ?>">
    <tr>
        <td><?php print _('Start position'); ?></td>
        <td>
            <div class="input-group" style="width:100px;">
                <input type="text" name="rack_start" size="2" class="form-control input-w-auto input-sm" placeholder="1" value="<?php print @$device->rack_start; ?>">
                <a href="" class="input-group-addon showRackPopup" rel='tooltip' data-placement='right' data-rackid="<?php print @$device->rack; ?>" data-deviceid='<?php print @$device->id; ?>' title='<?php print _("Show rack"); ?>'><i class='fa fa-server'></i></a>
            </div>
        </td>
    </tr>
    <tr>
        <td><?php print _('Size'); ?> (U)</td>
        <td>
            <input type="text" name="rack_size" size="2" class="form-control input-w-auto input-sm" style="width:100px;" placeholder="1" value="<?php print @$device->rack_size; ?>">
        </td>
    </tr>
    </tbody>
	<tr>
	   	<td colspan="2"><hr></td>
    </tr>
    <?php } ?>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<textarea name="description" class="form-control input-sm" placeholder="<?php print _('Description'); ?>" <?php print $readonly; ?>><?php if(isset($device->description)) print $device->description; ?></textarea>
			<?php
			if( ($action == "edit") || ($action == "delete") ) {
				print '<input type="hidden" name="switchId" value="'. $_POST['switchId'] .'">'. "\n";
			} ?>
			<input type="hidden" name="action" value="<?php print $action; ?>">
			<input type="hidden" name="csrf_cookie" value="<?php print $csrf; ?>">
		</td>
	</tr>

	<!-- Custom -->
	<?php
	if(sizeof($custom) > 0) {

		print '<tr>';
		print '	<td colspan="2"><hr></td>';
		print '</tr>';

		# count datepickers
		$timepicker_index = 0;
        
		# all my fields
		foreach($custom as $field) {
    		// create input > result is array (required, input(html), timepicker_index)
    		$custom_input = $Components->render_custom_field_input($field, $device, $action, $timepicker_index);
    		// add datepicker index
    		$timepicker_index = $timepicker_index + $custom_input['timepicker_index'];
            // print
			print "<tr>";
			print "	<td>".ucwords((empty($field->display_name) ? $field->name : $field->display_name))." ".$custom_input['required']."</td>";
			print "	<td>".$custom_input['field']."</td>";
			print "</tr>";
		}
	}

	?>

	<!-- Sections -->
	<tr>
		<td colspan="2">
			<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2"><?php print _('Sections to display device in'); ?>:</td>
	</tr>
	<tr>
		<td></td>
		<td>
            <select multiple="multiple" id="sections" name="sections[]">
		<?php
		# select sections
		$Sections = new Sections ($Database);
		$sections = $Sections->fetch_all_sections('name');

		# reformat device sections to array
		$deviceSections = explode(";", $device->sections);
		$deviceSections = is_array($deviceSections) ? $deviceSections : array();
        $selectedSections = array();
		if ($sections!==false) {
			foreach($sections as $section) {
				if(in_array($section->id, $deviceSections)) { 
                    $selectedSections[] = $section->id;
                } 
                print '<option value="' . $section->id . '">' . $section->name . '</option>';
			}
		}
		?>
            </select>
            <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.multi-select.js"></script>
            <script type="text/javascript">
               $('#sections').multiSelect({});
               $('#sections').multiSelect(
                   'select', <?php echo "['" . implode("','", $selectedSections) . "']"; ?>
               );
            </script>
		</td>
	</tr>

	</table>
	</form>
</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($action=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editSwitchsubmit"><i class="fa <?php if($action=="add") { print "fa-plus"; } else if ($action=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($action)); ?></button>
	</div>

	<!-- result -->
	<div class="switchManagementEditResult"></div>
</div>
