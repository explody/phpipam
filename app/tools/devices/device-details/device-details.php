<?php

/**
 * Script to display devices.
 */

// verify that user is logged in


// check
is_numeric($_GET['id']) ?: $Result->show('danger', _('Invalid ID: '.$_GET['id']), true);

// fetch device
$device = (array) $Tools->fetch_object('devices', 'id', $_GET['id']);

// get custom fields
$cfs = $Tools->fetch_custom_fields('devices');

// title
echo '<h4>'._('Device details').'</h4>';
echo '<hr>';

// print link to manage
echo "<div class='btn-group'>";
echo "<a class='btn btn-sm btn-default' href='".create_link('tools', 'devices')."' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-angle-left'></i> "._('All devices').'</a>';
echo '</div>';

// print
if ($_GET['id'] != 0 && sizeof($device) > 0) {
    echo "<table class='table'>";
    echo '<tr>';
    echo "<td style='vertical-align:top !important;'>";

    // set type
    $device_type = $Tools->fetch_object('deviceTypes', 'id', $device['type']);

    // device
    echo "<table class='ipaddress_subnet table-condensed table-auto'>";

    echo '<tr>';
    echo '	<th>'._('Name').'</a></th>';
    echo "	<td>$device[hostname]</td>";
    echo '</tr>';
    echo '<tr>';
    echo '	<th>'._('IP address').'</th>';
    echo "	<td>$device[ip_addr]</td>";
    echo '</tr>';
    echo '<tr>';
    echo '	<th>'._('Description').'</th>';
    echo "	<td>$device[description]</td>";
    echo '</tr>';
    echo '<tr>';
    echo '	<th>'._('Type').'</th>';
    echo "	<td>$device_type->name</td>";
    echo '</tr>';

    if ($User->settings->enableLocations == '1') {
        ?>
    	<tr>
    		<th><?php echo _('Location'); ?></th>
    		<td>
    		<?php

            // Only show nameservers if defined for subnet
            if (!empty($device['location']) && $device['location'] != 0) {
                // fetch recursive nameserver details
                $location2 = $Tools->fetch_object('locations', 'id', $device['location']);
                if ($location2 !== false) {
                    echo "<a href='".create_link('tools', 'locations', $device['location'])."'>$location2->name</a>";
                }
            } else {
                echo "<span class='text-muted'>/</span>";
            } ?>
    		</td>
    	</tr>
        <?php 
    }

    echo '<tr>';
    echo "	<td colspan='2'><hr></td>";
    echo '</tr>';

    echo '<tr>';
    echo '	<th>'._('Sections').':</th>';
    echo '	<td>';
    if (strlen($device['hostname']) > 0) {
        $section_ids = explode(';', $device['sections']);
        foreach ($section_ids as $k => $id) {
            $section = $Sections->fetch_section(null, $id);
            $section_print[$k] = '&middot; '.$section->name;
            $section_print[$k] .= strlen($section->description) > 0 ? " <span class='text-muted'>($section->description)</span>" : '';
        }
        echo implode('<br>', $section_print);
    }
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo "	<td colspan='2'><hr></td>";
    echo '</tr>';

    echo '<tr>';
    echo ' <th>'._('Subnets').'</th>';
    echo " <td><span class='badge badge1 badge5'>$cnt_subnets "._('Subnets').'</span></td>';
    echo '</tr>';
    echo '<tr>';
    echo ' <th>'._('Addresses').'</th>';
    echo " <td><span class='badge badge1 badge5'>$cnt_addresses "._('Addresses').'</span></td>';
    echo '</tr>';
    echo '<tr>';
    echo ' <th>'._('NAT').'</th>';
    echo " <td><span class='badge badge1 badge5'>$cnt_nat "._('NAT').'</span></td>';
    echo '</tr>';

    echo '<tr>';
    echo "	<td colspan='2'><hr></td>";
    echo '</tr>';

    if (sizeof($cfs) > 0) {
        foreach ($cfs as $cf) {

                // fix for boolean
                if ($field->type == 'boolean') {
                    if ($device[$cf->name] == '0') {
                        $device[$cf->name] = 'false';
                    } elseif ($device[$cf->name] == '1') {
                        $device[$cf->name] = 'true';
                    } else {
                        $device[$cf->name] = '';
                    }
                }

                // create links
                $device[$cf->name] = $Result->create_links($device[$cf->name]);

            echo '<tr>';
            echo '<th>' . ($cf->display_name ? $cf->display_name : $cf->name) . '</th>';
            echo '<td>'.$device[$cf->name].'</d>';
            echo '</tr>';
        }

        echo '<tr>';
        echo "	<td colspan='2'><hr></td>";
        echo '</tr>';
    }

    echo '<tr>';
    echo '	<td></td>';

    if ($User->is_admin(false)) {
        echo "	<td class='actions'>";
        echo "	<div class='btn-group'>";
        echo "		<button class='btn btn-xs btn-default editSwitch' data-action='edit'   data-switchid='".$device['id']."'><i class='fa fa-gray fa-pencil'></i></button>";
        echo "		<button class='btn btn-xs btn-default editSwitch' data-action='delete' data-switchid='".$device['id']."'><i class='fa fa-gray fa-times'></i></button>";
        echo '	</div>';
        echo ' </td>';
    } else {
        echo "	<td class='small actions'>";
        echo "	<div class='btn-group'>";
        echo "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-pencil'></i></button>";
        echo "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-times'></i></button>";
        echo '	</div>';
        echo ' </td>';
    }
    echo '</tr>';

    echo '</table>';
    echo '</td>';

    // format device
    if (empty($device['hostname'])) {
        $device['hostname'] = _('Device not specified');
    } else {
        if (empty($device['hostname'])) {
            $device['hostname'] = 'Unspecified';
        }
    }

    // rack
    if ($User->settings->enableRACK == '1') {
        echo '<td>';

        // validate rack
        $rack = $Tools->fetch_object('racks', 'id', $device['rack']);
        if ($rack !== false) {
            echo " <td style='width:200px; vertical-align:top !important;'>";
            // title
            echo "     <img src='".$Tools->create_rack_link($device['rack'], $device['id'])."' class='pull-right' style='width:200px;'>";
            echo ' </td>';
        }

        echo '</td>';
    }

    echo '</tr>';

    echo '</table>';
} else {
    $Result->show('danger', _('Invalid ID'), false);
}

?>