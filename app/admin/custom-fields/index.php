<?php

/**
 * Script tomanage custom IP fields
 ****************************************/

# verify that user is logged in
$User->check_user_session();


/* fetch all custom fields */
$custom_tables = array( "ipaddresses"=>"IP address",
                        "subnets"=>"subnet",
                        "vlans"=>"VLAN",
                        "vrf"=>"VRF",
                        "users"=>"User",
                        "devices"=>"Device",
                        "racks"=>"Rack",
                        "locations"=>"Locations",
                        "pstnPrefixes"=>"PSTN Prefixes",
                        "pstnNumbers"=>"PSTN Numbers"
                        );

# create array
foreach ($custom_tables as $k=>$f) {
    $custom_fields_numeric[$k]        = $Tools->fetch_custom_fields($k);
    $custom_fields[$k]                = $Tools::index_array($custom_fields_numeric[$k], 'name');
    $custom_fields[$k]['title']       = "Custom $f fields";
    $custom_fields[$k]['tooltip']     = "Add new custom $f field";
}
?>

<h4><?php print _('Custom fields'); ?></h4>
<hr>

<div class="alert alert-info alert-absolute"><?php print _('You can add additional custom fields to IP addresses and subnets (like CustomerId, location, ...)'); ?>.</div>
<hr style="margin-top:50px;clear:both;">


<table class="customIP table table-striped table-auto table-top" style="min-width:400px;">

<tr>
    <td></td>
    <td><?php print _('Name'); ?></td>
    <td><?php print _('Display Name'); ?></td>
    <td><?php print _('Description'); ?></td>
    <td><?php print _('Field type'); ?></td>
    <td><?php print _('Length'); ?></td>
    <td><?php print _('Required'); ?></td>
    <td><?php print _('Null allowed?'); ?></td>
    <td><?php print _('Default value'); ?></td>
    <td><?php print _('Visible'); ?></td>
    <td></td>
</tr>

<?php

# printout each
foreach ($custom_fields as $table=>$cf) {

    # save vars and unset
    $title   = $cf['title'];
    $tooltip = $cf['tooltip'];

    unset($cf['title']);
    unset($cf['tooltip']);

    print "<tbody id='custom-$table'>";

    //title
    print "    <tr>";
    print "    <th colspan='11'>";
    print "        <h5>"._($title)."</h5>";
    print "    </th>";
    print "    </tr>";

    //empty
    if (sizeof($cf) == 0) {
        print "    <tr>";
        print "    <td colspan='11'>";
        print "        <div class='alert alert-info alert-nomargin'>"._('No custom fields created yet')."</div>";
        print "    </td>";
        print "    </tr>";
    }
    //content
    else {
        $size = sizeof($cf);        //we must remove title
        $m=0;

        foreach ($cf as $f) {
            print "<tr class='$class'>";

            # ordering
            if ((($m+1) != $size)) {
                print "<td style='width:10px;'><button class='btn btn-xs btn-default down' data-direction='down' data-table='$table' rel='tooltip' title='Move down' data-fieldname='".$custom_fields_numeric[$table][$m]->id."' data-nextfieldname='".$custom_fields_numeric[$table][$m+1]->id."'><i class='fa fa-chevron-down'></i></button></td>";
            } else {
                print "<td style='width:10px;'></td>";
            }

            print "<td class='name'>$f->name</td>";

            # display name
            print "<td>$f->display_name</td>";

            # description
            print "<td>$f->description</td>";

            # type
            print "<td>$f->type</td>";

            # limit
            print "<td>$f->limit</td>";

            # Required
            print "<td>" . ($f->required ? _('Yes') : _('No')) . "</td>";

            # Null allowed?
            print "<td>" . ($f->null ? _('Yes') : _('No')) . "</td>";

            # Default value?
            print "<td>" . ($f->default ? $f->default : '-') . "</td>";

            # visible
            if ($f->visible) {
                print "<td><span class='text-danger'>" . _('Yes') . "</span></td>";
            } else {
                print "<td><span class='text-success'>" . _('No') . "</span></td>";
            }

            #actions
            print "<td class='actions'>";
            print "    <div class='btn-group'>";
            print "        <button class='btn btn-xs btn-default edit-cf' data-action='edit'   data-order='$f->order' data-id='$f->id' data-table=''><i class='fa fa-pencil'></i></button>";
            print "        <button class='btn btn-xs btn-default edit-cf' data-action='delete' data-order='$f->order' data-id='$f->id' data-table=''><i class='fa fa-times'></i></button>";
            print "    </div>";

            # warning for older versions
            if ((is_numeric(substr($f->name, 0, 1))) || (!preg_match('!^[\w_ ]*$!', $f->name))) {
                print '<span class="alert alert-warning"><strong>Warning</strong>: '._('Invalid field name').'!</span>';
            }

            print "</td>";
            print "</tr>";

            $m++;
        }
    }

    //add
    print "<tr>";
    print "<td colspan='11' style='padding-right:0px;'>";
    print "    <button class='btn btn-xs btn-default pull-right edit-cf' data-action='add' data-id='' data-table='$table' rel='tooltip' data-placement='right' title='"._($tooltip)."'><i class='fa fa-plus'></i></button>";
    print "</td>";
    print "</tr>";

    //filter
    print "<tr>";
    print "<td colspan='11' style='padding-right:0px;'>";
    print "    <button class='btn btn-xs btn-info pull-right edit-custom-filter' data-table='$table' rel='tooltip' data-placement='right' title='"._("Set which field to display in table")."'><i class='fa fa-filter'></i> Filter</button>";
    print "</td>";
    print "</tr>";

    //result
    print "<tr>";
    print "    <td colspan='11' class='result'>";
    print "        <div class='$table-order-result'></div>";
    print "</td>";
    print "</tr>";


    print "</tbody>";
}

?>
