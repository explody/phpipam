<?php

/**
 * Edit custom IP field
 ************************/


/*
    provided values are:
        table		= name of the table
        action		= action
        fieldName	= field name to edit
 */

# create csrf token
$csrf = $User->csrf_create('custom_field');

$action = $_POST['action'];

list($add, $edit, $delete) = array_fill(0, 3, false);

$field_types = [ 
            'Normal Text'=>'string', 
            'Large Text'=>'text',
            'Number'=>'integer',
            'Float'=>'float',
            'True/False'=>'boolean', 
            'Fixed Length Text'=>'char', 
            'Date'=>'date', 
            'Datetime'=>'datetime',
            'Time'=>'time', 
            'Timestamp'=>'timestamp',
            'Enum'=>'enum',
            'Set'=>'set' 
        ];

$date_must_allow_null = false;
$sm = $Database->sqlMode();
$modes = explode(",",$sm);

if (in_array('NO_ZERO_IN_DATE', $modes) || in_array('NO_ZERO_DATE', $modes)) {
    $date_must_allow_null = true;
}

if ($action == "add") {
    
    if ($Database->tableExists($_POST['table'])) {
        $table = $_POST['table'];
    } else {
        $Result->show("danger", _("Invalid table: ". $_POST['table']), true);
    }

    // This fetches an array of the 'order' values for the custom fields for current table
    $cf_table_order = $Database->findObjects('customFields', 'table', $table, 'order', false, false, false, ['order']);
    
    $order = sizeof($cf_table_order) > 0 ? $cf_table_order[0]->order+1 : 1;
    
    $add = true;
    
} elseif (in_array($action, ['edit','delete'])) {
    
    $action === 'edit' ? $edit = true : $delete = true;
    
    if(!is_numeric($_POST['id'])) {
        $Result->show("danger", _("Invalid field ID"), true);
    } else {
        $id = $_POST['id'];
        $table = $_POST['table'];
    }
    
    $cfield = $Tools->fetch_object('customFields', 'id', $id);
    
    if(!$cfield) {
        $Result->show("danger", _("Error fetching custom field data"), true);
    }
    $cfield->params = json_decode($cfield->params);

}

?>

<div class="pHeader"><?php print ucwords(_("$action")); ?> <?php print _('custom field'); ?></div>

<div class="pContent">

    <script type="text/javascript">
    $(document).ready(function() {
    /* bootstrap switch */
    var switch_options = {
    	onText: "Yes",
    	offText: "No",
        onColor: 'default',
        offColor: 'default',
        size: "mini",
        inverse: true
    };

    $(".input-switch").bootstrapSwitch(switch_options);
    });
    </script>
    
	<form id="editCustomFields">
	<table id="editCustomFields" class="table table-noborder table-condensed">

	<!-- Display Name -->
	<tr>
		<td><?php print _('Display Name'); ?></td>
		<td>
			<input type="text" name="display_name" id="cf-display-name" class="form-control input-sm" maxlength="48" value="<?php print $cfield->display_name; ?>" placeholder="<?php print _('Enter a user friendly name'); ?>" <?php $delete ? print 'readonly' : null; ?>>

			<input type="hidden" name="action" value="<?php print $action; ?>">
			<input type="hidden" name="table" value="<?php print $add ? $table : $cfield->table; ?>">
            <input type="hidden" name="id" value="<?php print $add ? null : $cfield->id; ?>">
			<input type="hidden" name="csrf_cookie" value="<?php print $csrf; ?>">
            <?php 
            print $add ? "<input type=\"hidden\" name=\"order\" value=\"$order\" />\n" : null;
            ?>
		</td>
	</tr>
    
    <!-- Name -->
    <tr>
        <td><?php print _('Name'); ?></td>
        <td>
            <input type="text" name="name" id="cf-name" class="form-control input-sm" maxlength="24" value="<?php print @$cfield->name; ?>" placeholder="<?php print _('Enter a database field name'); ?>" <?php $delete ? print 'readonly' : null; ?>>
        </td>
    </tr>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" name="description" class="form-control input-sm" maxlength="128" value="<?php print @$cfield->description; ?>" placeholder="<?php print _('Enter a description for users'); ?>" <?php $delete ? print 'readonly' : null; ?>>
		</td>
	</tr>

	<!-- type -->
	<tr>
		<td><?php print _('Type'); ?></td>
		<td>
			<select name="type" id="cf-type" class="input-sm input-w-auto form-control" <?php $delete ? print 'disabled' : null; ?>>
			<?php
            foreach ($field_types as $name=>$type) {
                if ($type == $cfield->type) {
                    print "<option value='$type' selected='selected'>$name</option>";
                } else {
                    print "<option value='$type'>$name</option>";
                }
            }
            ?>
			</select>
            <?php 
                if ($date_must_allow_null) {
                    print "<input type=\"hidden\" id=\"date-must-allow-null\" name=\"date-must-allow-null\" value=\"1\" />\n";
                }
            ?>
		</td>
	</tr>

    <!-- set/enum options -->
	<tr <?php print (in_array($cfield->type, ['set','enum']) ? null : 'style="display: none;"'); ?> id="set-values-row">
		<td><?php print _('Enum Values '); ?></td>
		<td>
			<input type="text" name="params[values]" id="cf-values" class="form-control input-sm" value="<?php print implode(',', (isset($cfield->params->values) ? $cfield->params->values : [] )); ?>" placeholder="<?php print _('Enter options for set/enum, separated with commas.'); ?>" <?php $delete ? print 'readonly' : null; ?>>
		</td>
	</tr>
    
	<!-- size -->
	<tr>
		<td><?php print _('Size / Length'); ?></td>
		<td>
			<input type="text" name="limit" id="cf-limit" class="form-control input-sm input-w-100" value="<?php $add ? print '64' : print @$cfield->limit; ?>" placeholder="<?php print _('Enter field length'); ?>" <?php $delete || $cfield->type == 'boolean' ? print 'readonly' : null; ?>>
		</td>
	</tr>

	<!-- Default -->
	<tr>
		<td><?php print _('Default value'); ?></td>
		<td>
			<input type="text" name="default" id="cf-default" class="form-control input-sm" value="<?php print @$cfield->default; ?>" placeholder="<?php print _('Enter default value'); ?>" <?php $delete ? print 'readonly' : null; ?>>
		</td>
	</tr>
    
    <!-- null -->
    <tr>
		<td><?php print _('Can be null?'); ?></td>
		<td>
            <input type="hidden" name="null" value="0" />
			<input name="null" id="cf-null" class='input-switch' type="checkbox" value="1" <?php $add || @$cfield->null ? print 'checked' : null; ?> <?php $delete ? print 'disabled' : null; ?>>
		</td>
	</tr>
    
	<!-- required -->
	<tr>
		<td><?php print _('Required field'); ?></td>
		<td>
            <input type="hidden" name="required" value="0" />
			<input name="required" id="cf-required" class='input-switch' type="checkbox" value="1" <?php @$cfield->required ? print 'checked' : null; ?> <?php $delete ? print 'disabled' : null; ?>>
		</td>
	</tr>

	</table>
	</form>

</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Close'); ?></button>
		<button class="btn btn-sm btn-default <?php if ($delete) {
                print "btn-danger";
            } else {
                print "btn-success";
            } ?>" id="editcustomSubmit"><i class="fa <?php if ($add) {
                print "fa-plus";
            } elseif ($delete) {
                print "fa-trash-o";
            } else {
                print "fa-check";
            } ?>"></i> <?php print ucwords(_($action)); ?></button>
	</div>
	<!-- result -->
	<div class="customEditResult"></div>
</div>
