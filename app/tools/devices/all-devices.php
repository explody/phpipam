<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>

<?php

/**
 * Script to display devices
 *
 */




$default_search_fields = ['hostname','ip_addr','description','version'];
$table_name = 'devices';

$s = new PagedSearch($Database, $table_name, $default_search_fields, "hostname", true);

# fetch all Device types and reindex
$device_types = $Tools->fetch_all_objects("deviceTypes", "id");
if ($device_types !== false) {
  foreach ($device_types as $dt) {
      $device_types_i[$dt->id] = $dt;
  }
}

if ($s->count() > 0) {
    # rack object
    $Racks      = new phpipam_rack ($Database);
}

# title
print "<h4>"._('List of network devices')."</h4>";
print "<hr>";
print "<div class=\"list_header\">";

# print link to manage
print "<div class='btn-group'>";

if(isset($_GET['sPage'])) {
    print "<a class='btn btn-sm btn-default' href='javascript:history.back()' style='margin-bottom:10px;'><i class='fa fa-chevron-left'></i> ". _('Back')."</a>";
} elseif($User->is_admin(false)) {
    print "<a class='btn btn-sm btn-default' href='".create_link("administration","devices")."' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-pencil'></i> ". _('Manage')."</a>";
}

print "</div>";

?>

    <div id="list_search">
        <form id="list_search" method="POST">
            <input type="text" class="form-control input-sm" id="list_search_term" name="search" placeholder="Search string" value="<?php print "$s->search_term"; ?>" size="40" />
            <input type="hidden" id="list_target" name="list_target" value="user_devices" />
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
        if ($s->count() > 1) {
        ?>
        <a class="paginate_button previous <?php print (($s->page > 1) ? '' : 'disabled' ); ?>" aria-controls="switchManagement" data-dt-idx="0" tabindex="0" id="switchManagement_previous">Previous</a>
        <?php } ?>
        <span>
            <?php

            $pnlink = '<a class="paginate_button %s" '.
                      'href="/tools/devices/search' .
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
        <a class="paginate_button next <?php print (($s->page >= $s->pages) ? 'disabled' : '' ); ?>" aria-controls="switchManagement" data-dt-idx="6" tabindex="0" id="switchManagement_next">Next</a>
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
# table
print '<table id="switchManagement" class="table sorted table-striped table-top">';

#headers
print "<thead>";
print '<tr>';
print '   <th>'._('Name').'</th>';
print '   <th>'._('IP address').'</th>';
print "   <th style='color:#428bca'>"._('Number of hosts').'</th>';
print '   <th>'._('Description').'</th>';
print '   <th>'._('Type').'</th>';
print '   <th>'._('Version').'</th>';


if($User->settings->enableSNMP=="1")
print '   <th>'._('SNMP').'</th>';
if($User->settings->enableRACK=="1")
print '   <th>'._('Rack').'</th>';


foreach($s->custom as $cf) {
  if($cf->visible) {
      $field_header = Components::custom_field_display_name($cf);
      print "<th class='hidden-sm hidden-xs hidden-md'><span rel='tooltip' data-container='body' title='"._('Sort by')." $field_header'>".$field_header."</th>";
      $colspanCustom++;
  }
}


print '	<th class="actions"></th>';
print '</tr>';
print "</thead>";

// no devices
if($s->found_objects===false) {
	$colspan = 8 + $colspanCustom;
	print "<tr>";
	print "	<td colspan='$colspan'>".$Result->show('info', _('No results')."!", false, false, true)."</td>";
	print "</tr>";
}
// result
else {
	foreach ($s->found_objects as $device) {
    	//cast
    	$device = (array) $device;

    	//count items
    	$cnt1 = $Tools->count_database_objects("ipaddresses", "switch", $device['id']);
    	$cnt2 = $Tools->count_database_objects("subnets", "device",  $device['id']);
    	$cnt = $cnt1 + $cnt2;

    	// reindex types
    	if (isset($device_types)) {
    		foreach($device_types as $dt) {
    			$device_types_indexed[$dt->id] = $dt;
    		}
    	}

    	//print details
    	print '<tr>'. "\n";

    	print "	<td><a href='".create_link("tools","devices",$device['id'])."'><i class='fa fa-desktop'></i> ". $device['hostname'] .'</a></td>'. "\n";
    	print "	<td>". $device['ip_addr'] .'</td>'. "\n";
    	print '	<td><span class="badge badge1 badge5">'. $cnt .'</span> '._('Objects').'</td>'. "\n";
        print '	<td class="description">'. $device['description'] .'</td>'. "\n";
    	print '	<td class="hidden-sm">'. $device_types_indexed[$device['type']]->name .'</td>'. "\n";
        print '	<td>'. $device['version'] .'</td>'. "\n";
        
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
        
        //custom
		foreach($s->custom as $cf) {
			if($cf->visible) {
				print "<td class='hidden-xs hidden-sm hidden-md'>";

				// create links
				$device[$cf->name] = $Result->create_links ($device[$cf->name], $cf->type);

				//booleans
				if($cf->type == "boolean")	{
                    print Components::boolean_display_value($device[$cf->name]);
				}
				//text
				elseif($cf->type == "text") {
					if(strlen($device[$cf->name])>0)	{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $device[$cf->name])."'>"; }
					else											{ print ""; }
				}
				else {
					print $device[$cf->name];

				}
				print "</td>";
			}
		}


    	print '	<td class="actions"><a href="'.create_link("tools","devices",$device['id']).'" class="btn btn-sm btn-default"><i class="fa fa-angle-right"></i> '._('Show details').'</a></td>';
    	print '</tr>'. "\n";

	}

}

print '</table>';

?>

<div class="dataTables_wrapper no-footer">
    <div class="dataTables_paginate paging_simple_numbers" id="switchManagement_paginate">

        <?php 
        if ($s->count() > 1) { 
        ?>
        <a class="paginate_button previous <?php print (($s->page > 1) ? '' : 'disabled' ); ?>" aria-controls="switchManagement" data-dt-idx="0" tabindex="0" id="switchManagement_previous">Previous</a>
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
        <a class="paginate_button next <?php print (($s->page >= $s->pages) ? 'disabled' : '' ); ?>" aria-controls="switchManagement" data-dt-idx="6" tabindex="0" id="switchManagement_next">Next</a>
        <?php } ?>
    </div>
</div>
