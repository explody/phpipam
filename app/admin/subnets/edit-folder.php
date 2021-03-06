<?php

/*
 * Print edit folder
 *********************/

# ID must be numeric
if($action != "add") {
	if(!is_numeric($_POST['subnetId'])) { 
        $Result->show("danger", _("Invalid ID"), true, true); 
    } else {
        $fid = $_POST['subnetId'];
    }
}

# verify that user has permissions to add subnet
if($action == "add") {
	if($Sections->check_permission ($User->user, $_POST['sectionId']) != 3) { 
        $Result->show("danger", _('You do not have permissions to add new subnet in this section')."!", true, true); 
    }
}
# otherwise check subnet permission
else {
	if($Subnets->check_permission ($User->user, $fid) != 3) { 
        $Result->show("danger", _('You do not have permissions to add edit/delete this subnet')."!", true, true);
    }
}


# we are editing or deleting existing subnet, get old details
if ($action != "add") {
    $folder_old_details = (array) $Subnets->fetch_subnet(null, $fid);
}
# we are adding new folder - get folder details
else {
	# for selecting master subnet if added from subnet details!
	if(strlen($fid) > 0) {
    	$subnet_old_temp = (array) $Subnets->fetch_subnet(null, $fid);
    	$subnet_old_details['masterSubnetId'] 	= @$subnet_old_temp['id'];			// same master subnet ID for nested
    	$subnet_old_details['vlanId'] 		 	= @$subnet_old_temp['vlanId'];		// same default vlan for nested
    	$subnet_old_details['vrfId'] 		 	= @$subnet_old_temp['vrfId'];		// same default vrf for nested
	}
}

# fetch custom fields
$cfs = $Tools->fetch_custom_fields('subnets');
# fetch all sections
$sections = $Sections->fetch_all_sections();


# set readonly flag
$readonly = $action=="edit" || $action=="delete" ? true : false;
?>

<!-- select2 -->
<script type="text/javascript" src="<?php print MEDIA; ?>/js/select2.js"></script>

<!-- common jquery plugins -->
<script type="text/javascript" src="<?php print MEDIA; ?>/js/common.plugins.js"></script>

<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('folder'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="editFolderDetails">
	<table class="editSubnetDetails table table-noborder table-condensed">

    <!-- name -->
    <tr>
        <td class="middle"><?php print _('Name'); ?></td>
        <td>
            <input type="text" class="form-control input-sm input-w-250" id="field-description" name="description" value="<?php print @$folder_old_details['description']; ?>">
        </td>
        <td class="info2"><?php print _('Enter folder name'); ?></td>
    </tr>

    <?php if($action != "add") { ?>
    <!-- section -->
    <tr>
        <td class="middle"><?php print _('Section'); ?></td>
        <td>
            <select name="sectionIdNew" id="sn-section-select" class="select2">
                <option value="0"><?php print _('None'); ?></option>
            	<?php
            	if($sections!==false) {

                    $Components->render_options($sections, 
                          'id', 
                          ['name','description'], 
                           array(
                               'sort' => true,
                               'group' => false,
                               'selected' => array('id' => $_POST['sectionId']),
                           )
                       );
            	}
            ?>
        	</select>
            <?php
            Components::render_select2_js('#sn-section-select',
                                          ['templateResult' => '$(this).s2boldDescTwoLine']);
            ?>
        </td>
        <td class="info2"><?php print _('Move to different section'); ?></td>
    </tr>
    <?php } ?>

    <!-- Master subnet -->
    <tr>
        <td><?php print _('Master Folder'); ?></td>
        <td>
        	<?php 
            $Subnets->print_mastersubnet_dropdown_menu($_POST['sectionId'], @$folder_old_details['masterSubnetId'], true); 
            Components::render_select2_js('#master-select',
                                          ['templateResult' => '$(this).s2oneLine']);
            ?>
        </td>
        <td class="info2"><?php print _('Enter master folder if you want to nest it under existing folder, or select root to create root folder'); ?>!</td>
    </tr>

    <!-- hidden values -->
    <input type="hidden" name="sectionId"       value="<?php print $_POST['sectionId'];    ?>">
    <input type="hidden" name="subnetId"        value="<?php print $fid;     ?>">
    <input type="hidden" name="action"    		value="<?php print $action; ?>">
	<input type="hidden" name="vlanId" 			value="0">
	<input type="hidden" name="vrfId" 			value="0">
    <?php
    $csrf->insertToken('/ajx/admin/subnets/edit-folder-result');
    
    if(sizeof($cfs) > 0) {

    	print "<tr>";
    	print "	<td colspan='3' class='hr'><hr></td>";
    	print "</tr>";
        $index = false;
	    foreach($cfs as $cf) {
            
            // create input > result is array (required, input(html), timepicker_index)
            $custom_input = $Components->render_custom_field_input($cf, $folder_old_details, $action, $index);
            $index = $custom_input['index'];
            
            // print
            print "<tr>";
            print "	<td>" . Components::custom_field_display_name($cf) . " ".$custom_input['required']."</td>";
            print "	<td>".$custom_input['field']."</td>";
            print " <td class=\"info2\">$cf->description</td>";
            print "</tr>";
            
	    }
    }


    # divider
    print "<tr>";
    print "	<td colspan='3' class='hr'><hr></td>";
    print "</tr>";
    ?>

    </table>
    </form>

    <?php
    # warning if delete
    if($action == "delete") {
	    print "<div class='alert alert-warning' style='margin-top:0px;'><strong>"._('Warning')."</strong><br>"._('Removing subnets will delete ALL underlaying subnets and belonging IP addresses')."!</div>";
    }
    ?>


</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<?php
		//if action == edit and location = IPaddresses print also delete form
		if(($action == "edit") && ($_POST['location'] == "IPaddresses") ) {
			print "<button class='btn btn-sm btn-default btn-danger editFolderSubmitDelete' data-action='delete' data-subnetId='$folder_old_details[id]'><i class='fa fa-trash-o'></i> "._('Delete folder')."</button>";
		}
		?>
		<button class="btn btn-sm btn-default editFolderSubmit <?php if($action=="delete") print "btn-danger"; else print "btn-success"; ?>"><i class="<?php if($action=="add") { print "fa fa-plus"; } else if ($action=="delete") { print "fa fa-trash-o"; } else { print "fa fa-check"; } ?>"></i> <?php print ucwords(_($action)); ?></button>
	</div>

	<div class="manageFolderEditResult"></div>
</div>
