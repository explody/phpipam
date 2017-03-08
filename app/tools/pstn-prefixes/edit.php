<?php

/**
 *    Print all available locations
 ************************************************/

# check permissions
if($Tools->check_prefix_permission ($User->user) < 3)   { $Result->show("danger", _('You do not have permission to manage PSTN prefixes'), true, true); }

# get Location object
if($_POST['action']!="add") {
    $prefix = $Admin->fetch_object ("pstnPrefixes", "id", $_POST['id']);
    $prefix!==false ? : $Result->show("danger", _("Invalid ID"), true, true);

    $master_prefix = $Admin->fetch_object ("pstnPrefixes", "id", $prefix->master);
}
else {
    # init object
    $prefix = new StdClass ();
    $prefix->master = 0;

    $master_prefix = new StdClass ();
    $master_prefix->name = root;
    $master_prefix->prefix = "/";

    # if id is set we are adding slave prefix
    if (isset($_POST['id'])) {
        if($_POST['id']!=0) {
            $master_prefix = $Admin->fetch_object ("pstnPrefixes", "id", $_POST['id']);
            $master_prefix!==false ? : $Result->show("danger", _("Invalid master ID"), true, true);

            $prefix->master = $master_prefix->id;
            $prefix->prefix = $master_prefix->prefix;
            $prefix->start  = $master_prefix->start;
            $prefix->deviceId = $master_prefix->deviceId;
        }
    }
}

# disable edit on delete
$readonly = $_POST['action']=="delete" ? "readonly" : "";
$link = $readonly ? false : true;

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('pstnPrefixes');

?>

<!-- select2 -->
<script type="text/javascript" src="<?php print MEDIA; ?>/js/select2.js"></script>

<!-- common jquery plugins -->
<script type="text/javascript" src="<?php print MEDIA; ?>/js/common.plugins.js"></script>

<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('PSTN prefix'); ?></div>

<!-- content -->
<div class="pContent">

    <form id="editPSTN">
    <?php $csrf->insertToken('/ajx/tools/pstn-prefixes/edit-result'); ?>
    
    <table id="editPSTN" class="table table-noborder table-condensed">

    <tbody>

        <!-- Master prefix -->
        <?php if($prefix->master!=0) { ?>
        <tr>
            <th><?php print _('Master prefix'); ?></th>
            <th colspan="2">
                <?php print $master_prefix->name. " (".$master_prefix->prefix.")"; ?>
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2">
                <?php print _("Range").": ".$master_prefix->start. " - ".$master_prefix->stop; ?>
            </th>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <?php } ?>

        <!-- name -->
        <tr>
            <th><?php print _('Name'); ?></th>
            <td>
                <input type="text" class="form-control input-sm" name="name" value="<?php print $prefix->name; ?>" placeholder='<?php print _('Name'); ?>' <?php print $readonly; ?>>
                <input type="hidden" name="id" value="<?php print $prefix->id; ?>">
                <input type="hidden" name="master" value="<?php print $prefix->master; ?>">
                <input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
            </td>
            <td>
                <span class="text-muted"><?php print _("Set Prefix name"); ?></span>
            </td>
        </tr>

        <!-- Prefix -->
        <tr>
            <th><?php print _('Prefix'); ?></th>
            <td>
                <input type="text" class="form-control input-sm" name="prefix" value="<?php print $prefix->prefix; ?>" placeholder='<?php print _('Prefix'); ?>' <?php print $readonly; ?>>
            </td>
            <td>
                <span class="text-muted"><?php print _("Prefix"); ?></span>
            </td>
        </tr>

        <!-- Start -->
        <tr>
            <th><?php print _('Start'); ?></th>
            <td>
                <input type="text" class="form-control input-sm" name="start" style="width:70px;" value="<?php print $prefix->start; ?>" placeholder='<?php print _('Start'); ?>' <?php print $readonly; ?>>
            </td>
            <td>
                <span class="text-muted"><?php print _("Set start number"); ?></span>
            </td>
        </tr>

        <!-- Stop -->
        <tr>
            <th><?php print _('Stop'); ?></th>
            <td>
                <input type="text" class="form-control input-sm" name="stop" style="width:70px;"  value="<?php print $prefix->stop; ?>" placeholder='<?php print _('Stop'); ?>' <?php print $readonly; ?>>
            </td>
            <td>
                <span class="text-muted"><?php print _("Set stop number"); ?></span>
            </td>
        </tr>


        <tr>
            <td colspan="3"><hr></td>
        </tr>

        <!-- Master prefix -->
<!--
        <tr>
            <th><?php print _('Master prefix'); ?></th>
            <td>
                <?php $Tools->print_masterprefix_dropdown_menu ($prefix->master); ?>
            </td>
            <td>
                <span class='text-muted'><?php print _('Enter master prefix if you want to nest it under existing subnet, or select root to create root prefix'); ?></span>
            </td>
        </tr>
-->


        <!-- Device -->
        <tr>
            <th><?php print _('Device'); ?></th>
            <td id="deviceDropdown">
                <select name="deviceId" id="pstn-device-select" class="select2">
                    <option value="0"><?php print _('None'); ?></option>
                    <?php
                    // TODO: better performance for very large <select> lists 
                    // fetch all devices
                    $devices = $Admin->fetch_all_objects("devices");
                    // loop
                    if ($devices!==false) {
                        $Components->render_options($devices, 
                              'id', 
                              'hostname', 
                               array(
                                   'group' => $User->settings->devicegrouping,
                                   'groupby' => $User->settings->devicegroupfield,
                                   'resolveGroupKey' => true,
                                   'gsort' => true,
                                   'extFields' => Devices::$extRefs,
                                   'selected' => array('id' => $prefix->deviceId),
                               )
                           );
                    }
                    ?>
                </select>
                <?php
                Components::render_select2_js('#pstn-device-select',
                                              ['templateResult' => '$(this).s2oneLine']);
                ?>
            </td>
            <td class="info2"><?php print _('Select device where prefix is located'); ?></td>
        </tr>

        <!-- description -->
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <th><?php print _('Description'); ?></th>
            <td colspan="2">
                <textarea class="form-control input-sm" name="description" placeholder='<?php print $prefix->description; ?>' <?php print $readonly; ?>><?php print $prefix->description; ?></textarea>
            </td>
        </tr>


        <!-- Custom -->
        <?php
        // TODO: DRY
        if(sizeof($cfs) > 0) {

            print '<tr>';
            print '    <td colspan="2"><hr></td>';
            print '</tr>';

            # count datepickers
            $index = false;

            # all my fields
            foreach($cfs as $cf) {
                // create input > result is array (required, input(html), index)
                $custom_input = $Components->render_custom_field_input ($cf, $prefix, $_POST['action'], $index);
                // add datepicker index
                $index = $custom_input['index'];
                // print
                print "<tr>";
                print "    <td>".ucwords($cf->name)." ".$custom_input['required']."</td>";
                print "    <td>".$custom_input['field']."</td>";
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
        <button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
        <button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editPSTNSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
    </div>
    <!-- result -->
    <div class="editPSTNResult"></div>
</div>
