<?php

use Phinx\Db\Adapter\MysqlAdapter;

require_once(FUNCTIONS . '/classes/Migration/class.TransientAdapter.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableTable.php');

/**
 * Edit custom IP field
 ************************/

# validate csrf cookie
$User->csrf_validate("custom_field", $_POST['csrf_cookie'], $Result);

$action = $_POST['action'];
$table  = $_POST['table'];

list($add, $edit, $delete) = array_fill(0, 3, false);

if (in_array($action, ['edit','delete'])) {

    if(!is_numeric($_POST['id'])) {
        $Result->show("danger", _("Invalid field ID"), true);
    }

    $cfield = $Tools->fetch_object('customFields', 'id', $_POST['id']);

    if ($action == "delete") {
        $delete = true;
    }

} else {
    $cfield = (object) [];
}

/* checks */
if (in_array($action, ['edit','add'])) {

    $action === 'edit' ? $edit = true : $add = true;
    $newname = false;
    
    if ($add) {
        $cfield->name = trim($_POST['name']);
    } else {
        if (trim($_POST['name']) != $cfield->name) {
            $newname = trim($_POST['name']);
        }
    }
    // Only certain types require limit to be set
    if ((!isset($_POST['limit']) || empty($_POST['limit'])) &&
          in_array($_POST['type'], ['string',
                                    'text',
                                    'integer',
                                    'float',
                                    'boolean',
                                    'char'] )) {
        $errors[] = _('Invalid length/limit');
    } else {
        $cfield->limit = $_POST['limit'];
    }

    if (!is_numeric($_POST['null']) || $_POST['null'] > 1) {
        $errors[] = _('Invalid "NULL" value');
    }

    if (!is_numeric($_POST['required']) || $_POST['required'] > 1) {
        $errors[] = _('Invalid "required" value');
    } else {
        $cfield->required = $_POST['required'];
    }

    if ($add) {

        if (empty($_POST['table'])) {
            $Result->show("danger", _("Error! No table specified"), true);
        } else {
            $cfield->table = $_POST['table'];
        }

        if (!is_numeric($_POST['order'])) {
            $errors[] = _('Invalid order value');
        } else {
            $cfield->order = $_POST['order'];
        }

    }

    // Error check changes to 'null'
    if ($edit) {

        // If a column that allows NULL is about to switch to a column that does not allow NULL,
        // there must not currently be any NULL values in that column.
        if ($cfield->null == 1 && $_POST['null'] == 0) {
            $nullcount = $Database->numObjectsConditional($cfield->table, $cfield->name . ' is NULL');

            if ($nullcount > 0) {
                $errors[] = _("Field is being changed from NULL to NOT NULL, but there are NULL " .
                              "values in the table. You must fill them or set NULL: yes.");
            }
        }

    }

    $cfield->null = $_POST['null'];

    $params = [];
    foreach ($_POST['params'] as $param => $pval) {
        $pval ? $params[$param] = $pval : null;
    }

    // remove spaces
    $cfield->display_name = trim($_POST['display_name']);
    $cfield->description  = trim($_POST['description']);
    $cfield->default      = trim($_POST['default']);

    $cfield->type = $_POST['type'];

    // Check or enforce some values
    if (empty($cfield->display_name)) {
        $cfield->display_name = $cfield->name;
    }

    if ($cfield->type === 'boolean') {
        $cfield->limit = 1;
        if ($cfield->default != '1' && $cfield->default != '0') {
            $errors[] = _('Default value for boolean fields may only be 1 (yes) or 0 (no)');
        }
    } elseif ($cfield->type === 'enum' || $cfield->type === 'set') {
        if(empty($params['values'])) {
            $errors[] = _('A list of values is required for enum and set fields');
        } else {
            $values = explode(",", $params['values']);
            $values = array_map('trim', $values);
            $params['values'] = $values;

            if(!empty($cfield->default) && !in_array($cfield->default, $params['values'])) {
                $errors[] = _("Default value for and enum/set must be one of their available values.");
            }

        }
    } elseif ($cfield->type === 'float') {
        $scale = explode(',', $cfield->limit);
        if (sizeof($scale) > 1) {

            $params['scale'] = $scale[1];
            $params['precision'] = $scale[0];
            if ($scale[1] > $scale[0]) {
                $errors[] = _('For float(M,D), M must be larger than D (check the length)');
            }
        }
    }

    // if not set or enum, strip out 'values'. this allows for changing types.
    if ($cfield->type != 'enum' && $cfield->type != 'set') {
        unset($params['values']);
    }

    # length > 2 and < 25
    if ((strlen($cfield->name) < 3) || (strlen($cfield->name) > 24)) {
        $errors[] = _('Name must be between 3 and 24 characters');
    }

    # must not start with number
    if (is_numeric(substr($cfield->name, 0, 1))) {
        $errors[] = _('Name must not start with number');
    }

    # only alphanumeric and _ are allowed
    if (!preg_match('/^[a-zA-Z0-9\_]+$/i', $cfield->name)) {
        $errors[] = _('Only alphanumeric and underscore characters are allowed in the name');
    }

}

/* die if errors otherwise execute */
if (sizeof($errors) != 0) {
    print '<div class="alert alert alert-danger">'._('Please correct the following errors').':'. "\n";
    print '<ul>'. "\n";
    foreach ($errors as $error) {
        print '<li style="text-align:left">'. $error .'</li>'. "\n";
    }
    print '</ul>'. "\n";
    print '</div>'. "\n";
} else {

    // We're storing the variable column params as JSON, so convert params before
    // saving the custom field.
    $cfield->params = json_encode($params);

    // Now, we populate the params array for addColumn
    $params['null']  = $cfield->null;
    $params['limit'] = $cfield->limit;
    ($cfield->default == '0' || !empty($cfield->default)) ? $params['default'] = $cfield->default : null;

    /*
     * Harness the power of phinx
     */
    // $c (config) comes from ajax.php
    $a = new Ipam\Migration\TransientAdapter(['name' => $c->db->name,
                                              'connection' => $Database->get_connection()]);
    $rt = new Ipam\Migration\RepeatableTable($cfield->table, [], $a);

    try {
        // If the column is being renamed, do this first before other updates
        if ($edit && $newname) {
            $rt->renameColumn($cfield->name, $newname)->update();
            $cfield->name = $newname;
        }

        // add/edit RepeatableTable::addColumn will add if absent and update if the column exists
        ($add || $edit) ? $rt->addColumn($cfield->name, $cfield->type, $params)->update() : null;

        // delete
        $delete ? $rt->removeColumn($cfield->name)->update() : null;

    } catch (Exception $e) {
        $Result->show("danger", _("Failed to modify custom field: " . $e), true);
    }

    if ($add) {

        $Database->insertObject('customFields', $cfield) ||
                $Result->show("danger", _("Failed to add custom field"), true);

    } else if ($edit) {

        $Database->updateObject('customFields', $cfield) ||
                $Result->show("danger", _("Failed to save custom field"), true);

    } else if ($delete) {

        $Database->deleteObject('customFields', $cfield->id);

    }

    $Result->show("success", _("Field ${action} succeeded"), true);
}

?>
