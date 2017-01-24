<?php

use Phinx\Db\Adapter\MysqlAdapter;

require_once(FUNCTIONS . '/classes/Migration/class.TransientAdapter.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableTable.php');

/**
 * Script tomanage custom IP fields
 ****************************************/

# some verifications
if ((!is_numeric($_POST['current'])) || (!is_numeric($_POST['next']))) {
    $Result->show("danger", _('Invalid field ID')."!", true);
} else {
    $current = $_POST['current'];
    $next    = $_POST['next'];
}

if (empty($_POST['table'])) {
    $Result->show("danger", _("Error! Missing table"), true);
} else {
    if (!$Tools->table_exists($_POST['table'])) {
        $Result->show("danger", _("No such table"), true);
    } else {
        $table = $_POST['table'];
    }
}

/*
 * Harness the power of phinx 
 */
// $c (config) comes from ajax.php 
$a = new Ipam\Migration\TransientAdapter(['name' => $c->db->name, 
                                          'connection' => $Database->get_connection()]);
$rt = new Ipam\Migration\RepeatableTable($table, [], $a);

$cfs = $Tools::index_array($Tools->fetch_custom_fields($table), 'id');

$col = $rt->getColumnByName($cfs[$current]->name);

//print_r($col);
// swap order position for current and next
$cfs[$next]->order = $cfs[$current]->order;
$cfs[$current]->order = $cfs[$current]->order + 1;

// params are stored as JSON. decode and add 'after'
//$params = json_decode($cfs[$current]->params, true);
//$params['after'] = $cfs[$next]->name;
$col->setAfter($cfs[$next]->name);

print_r($params);
try {
    $rt->changeColumn($cfs[$current]->name, $col)->save();
    foreach([$cfs[$current], $cfs[$next]] as $ucf) {
        $Database->updateObject('customFields', $ucf) || 
                $Result->show("danger", _("Failed to save custom field"), true);        
    }
} catch (Exception $e) {
    $Result->show("danger", _('Reordering failed')."! " . $e, true);
}

$Result->show("success", _('Fields reordered successfully')."!");
