<?php

/**
 * Script to print devices
 ***************************/

# verify that user is logged in
$User->check_user_session();

$default_search_fields = ['hostname','ip_addr','description','version'];
$table_name = 'devices';

$s = new PagedSearch($Database, $table_name, $default_search_fields, "hostname", true);

# fetch all Device types and reindex
$device_types = $Admin->fetch_all_objects("deviceTypes", "id");
if ($device_types !== false) {
	foreach ($device_types as $dt) {
		$device_types_i[$dt->id] = $dt;
	}
}

if ($s->count() > 0) {
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
            <input type="text" class="form-control input-sm" id="list_search_term" name="search" placeholder="Search string" value="<?php print "$s->search_term"; ?>" size="40" />
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
            print "<option value=\"$cnt\" " . (($cnt == $s->limit) ? 'selected' : '') . ">$cnt</option>\n";
            }
            print "<option value=\"$s->all_obj_count\"" . (($s->all_obj_count == $s->limit) ? 'selected' : '') . ">All</option>";
            ?>
        </select> devices</label>
        (<?php print $s->all_obj_count;?> devices found)
    </div>
        
    <div class="dataTables_paginate paging_simple_numbers" id="switchManagement_paginate">

        <?php
        
        $prev = $s->page - 1;
        $next = $s->page + 1;
        $srch = ($s->search_term ? $s->search_term : null);
        
        if ($s->count() > 1) { 
        ?>
        <a class="paginate_button previous <?php print (($s->page > 1) ? '' : 'disabled' ); ?>" aria-controls="switchManagement" data-dt-idx="0" tabindex="0" id="switchManagement_previous" href="/administration/devices/search/<?php print $prev . '/'; print $srch; ?>">Previous</a>
        <?php } ?>
        <span>
            <?php 
            
            $pnlink = '<a class="paginate_button %s" '.
                      'href="/administration/devices/search' .
                      '/%%d/' . ($s->search_term ? $s->search_term : null) . 
                      '" aria-controls="switchManagement" data-dt-idx="%%d" tabindex="0">%%d</a>'."\n";
            
            print PaginationLinks::create($s->page,$s->pages,1,
                sprintf($pnlink, null),
                sprintf($pnlink, 'current')
             );

            ?>
        </span>
        <?php 
        if ($s->count() > 1) { 
        ?>
        <a class="paginate_button next <?php print (($s->page >= $s->pages) ? 'disabled' : '' ); ?>" aria-controls="switchManagement" data-dt-idx="6" tabindex="0" id="switchManagement_next" href="/administration/devices/search/<?php print $next . '/'; print $srch; ?>">Next</a>
        <?php } ?>
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
            "info": "Showing page <?php print $s->page; ?>  of <?php print ($s->pages > 0 ? $s->pages : "1"); ?>"
        },
        "columnDefs": [
            { "orderable": false, "targets": 0 },
        ],
    } );
} );
</script>

<?php
/* first check if they exist! */
if($s->found_objects===false) {
	$Result->show("warning", _('No devices configured').'!', false);
} elseif ($s->count() == 0) {
    $Result->show("warning", _('No devices found'), false);
}
/* Print them out */
else {

	print '<table id="switchManagement" class="table table-striped sorted table-td-top">';

	# headers
	print "<thead>";
	print '<tr>';
    print ' <th class="actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>';
	print '	<th>'._('Name').'</th>';
	print '	<th>'._('IP address').'</th>';
	print '	<th>'._('Type').'</th>';
	print '	<th>'._('Version').'</th>';
	print '	<th>'._('Description').'</th>';
    if($User->settings->enableSNMP=="1")
	print '	<th>'._('SNMP').'</th>';
    if($User->settings->enableRACK=="1")
	print '	<th>'._('Rack').'</th>';
	print '	<th><i class="icon-gray icon-info-sign" rel="tooltip" title="'._('Shows in which sections device will be visible for selection').'"></i> '._('Sections').'</th>';

    if(sizeof($s->custom) > 0) {
    	foreach($s->custom as $field) {
    		if($field->visible) {
                $field_header = empty($field->display_name) ? $field->name : $field->display_name;
    			print "<th class='hidden-sm hidden-xs hidden-md'><span rel='tooltip' data-container='body' title='"._('Sort by')." $field_header'>".$field_header."</th>";
    			$colspanCustom++;
    		}
    	}
    }
	
	print '</tr>';
    print "</thead>";

    print "<tbody>";
	# loop through devices
	foreach ($s->found_objects as $device) {
		//cast
		$device = (array) $device;

		//print details
		print '<tr>'. "\n";
        
        print '	<td class="actions">'. "\n";
		print '	<div class="btn-group actions">';
		print "		<button class='btn btn-xs btn-default editSwitch' data-action='edit'   data-switchid='$device[id]'><i class='fa fa-pencil'></i></button>";
        print "		<button class='btn btn-xs btn-default editSwitch' data-action='delete' data-switchid='$device[id]'><i class='fa fa-times'></i></button>";
		if($User->settings->enableSNMP=="1") {
		          print "		<button class='btn btn-xs btn-default editSwitchSNMP' data-action='edit' data-switchid='$device[id]'><i class='fa fa-cogs'></i></button>";
        }
		print "	</div>";
		print '	</td>'. "\n";
        
		print '	<td><a href="'.create_link("tools","devices",$device['id']).'">'. $device['hostname'] .'</a></td>'. "\n";
		print '	<td>'. $device['ip_addr'] .'</td>'. "\n";
		print '	<td>'. @$device_types_i[$device['type']]->name .'</td>'. "\n";
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
		if(sizeof($s->custom) > 0) {
			foreach($s->custom as $field) {
				if($field->visible) {
					print "<td class='hidden-xs hidden-sm hidden-md'>";

					// create links
					$device[$field->name] = $Result->create_links ($device[$field->name], $field->type);

					//booleans
					if($field->type == "boolean") {
                        print $device[$field->name] ? _("Yes") : _("No");
					}
					//text
					elseif($field->type == "text") {
						if(strlen($device[$field->name])>0) {
                            print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $device[$field->name])."'>";
                        } else {
                            print "";
                        }
					}
					else {
						print $device[$field->name];
					}
					print "</td>";
				}
			}
		}


		print '</tr>'. "\n";

	}
	print "</tbody>";
	print '</table>';
}
?>

<div class="dataTables_wrapper no-footer">
    <div class="dataTables_paginate paging_simple_numbers" id="switchManagement_paginate">

        <?php 
        if ($s->count() > 1) { 
        ?>
        <a class="paginate_button previous <?php print (($s->page > 1) ? '' : 'disabled' ); ?>" aria-controls="switchManagement" data-dt-idx="0" tabindex="0" id="switchManagement_previous" href="/administration/devices/search/<?php print $prev . '/'; print $srch; ?>">Previous</a>
        <?php } ?>
        <span>
            <?php 
            
            # $s->pages and $pnlink are set up above where the top links are generated
            print PaginationLinks::create($s->page,$s->pages,1,
                 sprintf($pnlink, null),
                 sprintf($pnlink, 'current')
             );

            ?>
        </span>
        <?php 
        if ($s->count() > 1) { 
        ?>
        <a class="paginate_button next <?php print (($s->page >= $s->pages) ? 'disabled' : '' ); ?>" aria-controls="switchManagement" data-dt-idx="6" tabindex="0" id="switchManagement_next" href="/administration/devices/search/<?php print $next . '/'; print $srch; ?>">Next</a>
        <?php } ?>
    </div>
</div>
<!-- edit result holder -->
<div class="switchManagementEdit"></div>
