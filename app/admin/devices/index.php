<?php

/**
 * Script to print devices
 ***************************/

# verify that user is logged in
$User->check_user_session();

# fetch custom fields
$custom = $Tools->fetch_custom_fields('devices');

$default_search_fields = ['hostname','ip_addr','description','version'];
$table_name = 'devices';
$search_term = false;
/** 
* Most of this from the API's Devices class
* TODO: violates DRY 
* TODO: make sure the search term is escaped/sanitized
**/
$l = array_key_exists('table-page-size', $_COOKIE) ? $_COOKIE['table-page-size'] : 50;
$p  = 1;  # page number

if (isset($_GET['l'])) {
    if (is_numeric($_GET['l']) && $_GET['l'] > 0) {
        $l = $_GET['l'];    
    }
}

if (isset($_GET['p'])) {
    if (is_numeric($_GET['p']) && $_GET['p'] > 0) {
        $p = $_GET['p'];    
    } 
}

$offset = ($p > 1 ? $p * $l : 0);

if (array_key_exists('search', $_GET)) {
    
    $search_term = $_GET['search'];
    
    $base_query = "SELECT * from " . $table_name . " where ";
    
    # Search all custom fields
    $custom_fields = array_keys($custom);
    
    # Merge default fields with custom fields
    $search_fields = array_merge($custom_fields, $default_search_fields);
    
    list($extended_query, $query_params) = $Database->constructSearch($search_fields,$search_term);
    
    # Put together with the base query
    $search_query = $base_query . $extended_query;

    # Search query
    $devices = $Database->getObjectsQuery($search_query, $query_params, "hostname", true, $l, $offset);
    $all_dev_count = $Database->numObjectsConditional($table_name, $extended_query, $query_params);
    
} else {
    
    # fetch Devices
    $devices = $Admin->fetch_objects("devices", "hostname", true, $l, $offset);
    $all_dev_count = $Database->numObjects('devices');
}

$dev_count = count($devices);

# fetch all Device types and reindex
$device_types = $Admin->fetch_all_objects("deviceTypes", "tid");
if ($device_types !== false) {
	foreach ($device_types as $dt) {
		$device_types_i[$dt->tid] = $dt;
	}
}

if ($dev_count > 0) {

    # get hidden fields
    $hidden_custom_fields = json_decode($User->settings->hiddenCustomFields, true);
    $hidden_custom_fields = is_array(@$hidden_custom_fields['devices']) ? $hidden_custom_fields['devices'] : array();

    # rack object
    $Racks      = new phpipam_rack ($Database);
}



?>

<h4><?php print _('Device management'); ?></h4>
<hr>
<div class="list_header">
    <div class="btn-group">
    	<button class='btn btn-sm btn-default editSwitch' data-action='add'   data-switchid='' style='margin-bottom:10px;'><i class='fa fa-plus'></i> <?php print _('Add device'); ?></button>
    	<a href="<?php print create_link("administration", "device-types"); ?>" class="btn btn-sm btn-default"><i class="fa fa-tablet"></i> <?php print _('Manage device types'); ?></a>
    </div>

    <div id="list_search">
        <form id="list_search" method="POST">
            <input type="text" class="form-control input-sm" id="list_search_term" name="search" placeholder="Search string" value="<?php print "$search_term"; ?>" size="40" />
            <input type="hidden" id="list_target" name="list_target" value="admin_devices" />
        </form>
        <span class="input-group-btn">
            <button class="btn btn-default btn-sm" id="listSearchSubmit" type="button">Search Devices</button>
        </span>
    </div>
    
</div>

<div class="dataTables_wrapper no-footer">
    
    <div id="list_count">
        <label>Show <select name="table_page_size" id="table_page_size">
            <?php 
            foreach(array(10,20,50,100,500,1000) as $cnt) {
            print "<option value=\"$cnt\" " . (($cnt == $l) ? 'selected' : '') . ">$cnt</option>\n";
            }
            print "<option value=\"$all_dev_count\"" . (($all_dev_count == $l) ? 'selected' : '') . ">All</option>";
            ?>
        </select> devices</label>
        (<?php print $all_dev_count;?> devices found)
    </div>
    
    <div class="dataTables_paginate paging_simple_numbers" id="switchManagement_paginate">

        <a class="paginate_button previous <?php print (($p > 1) ? '' : 'disabled' ); ?>" aria-controls="switchManagement" data-dt-idx="0" tabindex="0" id="switchManagement_previous">Previous</a>
        <span>
            <?php 
            
            $pages = floor($all_dev_count / $l);
            $pnlink = '<a class="paginate_button %s" '.
                      'href="/administration/devices/' . ($search_term ? 'search/' : null) .
                      $l . '/%%d/' . ($search_term ? $search_term : null) . 
                      '" aria-controls="switchManagement" data-dt-idx="%%d" tabindex="0">%%d</a>'."\n";
            
            print PaginationLinks::create($p,$pages,1,
                sprintf($pnlink, null),
                sprintf($pnlink, 'current')
             );

            ?>
        </span>
        <a class="paginate_button next <?php print (($p >= $pages) ? 'disabled' : '' ); ?>" aria-controls="switchManagement" data-dt-idx="6" tabindex="0" id="switchManagement_next">Next</a>
    </div>
</div>

<script language="JavaScript">
$(document).ready(function() {
    $('#switchManagement').dataTable( {
        "paging": false,
        "searching": false,
        "scrollX": true,
        "scrollCollapse": true,
        "language": {
            "info": "Showing page <?php print "$p"; ?>  of <?php print ($pages > 0 ? $pages : "1"); ?>"
        },
    } );
} );
</script>

<?php
/* first check if they exist! */
if($devices===false) {
	$Result->show("warning", _('No devices configured').'!', false);
} elseif ($dev_count == 0) {
    $Result->show("warning", _('No devices found'), false);
}
/* Print them out */
else {

	print '<table id="switchManagement" class="table table-striped sorted table-td-top">';

	# headers
	print "<thead>";
	print '<tr>';
	print '	<th>'._('Name').'</th>';
	print '	<th>'._('IP address').'</th>';
	print '	<th>'._('Type').'</th>';
	print '	<th>'._('Vendor').'</th>';
	print '	<th>'._('Model').'</th>';
	print '	<th>'._('Version').'</th>';
	print '	<th>'._('Description').'</th>';
    if($User->settings->enableSNMP=="1")
	print '	<th>'._('SNMP').'</th>';
    if($User->settings->enableRACK=="1")
	print '	<th>'._('Rack').'</th>';
	print '	<th><i class="icon-gray icon-info-sign" rel="tooltip" title="'._('Shows in which sections device will be visible for selection').'"></i> '._('Sections').'</th>';
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			if(!in_array($field['name'], $hidden_custom_fields)) {
				print "<th class='hidden-xs hidden-sm hidden-md'>$field[name]</th>";
			}
		}
	}
	print '	<th class="actions"></th>';
	print '</tr>';
    print "</thead>";

    print "<tbody>";
	# loop through devices
	foreach ($devices as $device) {
		//cast
		$device = (array) $device;

		//print details
		print '<tr>'. "\n";

		print '	<td><a href="'.create_link("tools","devices",$device['id']).'">'. $device['hostname'] .'</a></td>'. "\n";
		print '	<td>'. $device['ip_addr'] .'</td>'. "\n";
		print '	<td>'. @$device_types_i[$device['type']]->tname .'</td>'. "\n";
		print '	<td>'. $device['vendor'] .'</td>'. "\n";
		print '	<td>'. $device['model'] .'</td>'. "\n";
		print '	<td>'. $device['version'] .'</td>'. "\n";
		print '	<td class="description">'. $device['description'] .'</td>'. "\n";

		// SNMP
		if($User->settings->enableSNMP=="1") {
    		print "<td>";
    		// not set
    		if ($device['snmp_version']==0 || strlen($device['snmp_version'])==0) {
        		print "<span class='text-muted'>"._("Disabled")."</span>";
    		}
    		else {
                print _("Version").": $device[snmp_version]<br>";
                print _("Community").": $device[snmp_community]<br>";
    		}
    		print "</td>";
		}

		// rack
        if($User->settings->enableRACK=="1") {
            print "<td>";
            # rack
            $rack = $Racks->fetch_rack_details ($device['rack']);
            if ($rack!==false) {
                print "<a href='".create_link("administration", "racks", $rack->id)."'>".$rack->name."</a><br>";
                print "<span class='badge badge1 badge5'>"._('Position').": $device[rack_start], "._("Size").": $device[rack_size] U</span>";
            }
            print "</td>";
        }

		//sections
		print '	<td class="sections">';
			$temp = explode(";",$device['sections']);
			if( (sizeof($temp) > 0) && (!empty($temp[0])) ) {
			foreach($temp as $line) {
				$section = $Sections->fetch_section(null, $line);
				if(!empty($section)) {
				print '<div class="switchSections text-muted">'. $section->name .'</div>'. "\n";
				}
			}
			}

		print '	</td>'. "\n";

		//custom
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				if(!in_array($field['name'], $hidden_custom_fields)) {
					print "<td class='hidden-xs hidden-sm hidden-md'>";

					// create links
					$device[$field['name']] = $Result->create_links ($device[$field['name']], $field['type']);

					//booleans
					if($field['type']=="tinyint(1)")	{
						if($device[$field['name']] == "0")		{ print _("No"); }
						elseif($device[$field['name']] == "1")	{ print _("Yes"); }
					}
					//text
					elseif($field['type']=="text") {
						if(strlen($device[$field['name']])>0)	{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $device[$field['name']])."'>"; }
						else											{ print ""; }
					}
					else {
						print $device[$field['name']];

					}
					print "</td>";
				}
			}
		}

		print '	<td class="actions">'. "\n";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-xs btn-default editSwitch' data-action='edit'   data-switchid='$device[id]'><i class='fa fa-pencil'></i></button>";
		if($User->settings->enableSNMP=="1")
		print "		<button class='btn btn-xs btn-default editSwitchSNMP' data-action='edit' data-switchid='$device[id]'><i class='fa fa-cogs'></i></button>";
		print "		<button class='btn btn-xs btn-default editSwitch' data-action='delete' data-switchid='$device[id]'><i class='fa fa-times'></i></button>";
		print "	</div>";
		print '	</td>'. "\n";

		print '</tr>'. "\n";

	}
	print "</tbody>";
	print '</table>';
}
?>

<div class="dataTables_wrapper no-footer">
    <div class="dataTables_paginate paging_simple_numbers" id="switchManagement_paginate">

        <a class="paginate_button previous <?php print (($p > 1) ? '' : 'disabled' ); ?>" aria-controls="switchManagement" data-dt-idx="0" tabindex="0" id="switchManagement_previous">Previous</a>
        <span>
            <?php 
            
            # $pages and $pnlink are set up above where the top links are generated
            print PaginationLinks::create($p,$pages,1,
                 sprintf($pnlink, null),
                 sprintf($pnlink, 'current')
             );

            ?>
        </span>
        <a class="paginate_button next <?php print (($p >= $pages) ? 'disabled' : '' ); ?>" aria-controls="switchManagement" data-dt-idx="6" tabindex="0" id="switchManagement_next">Next</a>
    </div>
</div>
<!-- edit result holder -->
<div class="switchManagementEdit"></div>
