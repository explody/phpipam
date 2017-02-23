<?php

/**
 * set which custom field to display
 ************************/

$User->csrf_validate("filter_fields", $_POST['csrf_cookie'], $Result);

// This is backwards compared to db values because 'on' means hidden
$visibility = [ 'on' => 0, 'off' => 1];

/* anything to write? */
if (sizeof($_POST['visible']) > 0) {
    foreach ($_POST['visible'] as $id=>$value) {
        
        if (!is_numeric($id)) {
            $Result->show("danger", _("Invalid filter ID"), true);
        }
        if (!array_key_exists($value, $visibility)) {
            $Result->show("danger", _("Invalid visibility value"), true);
        }
        
        $updated = $Database->updateField('customFields','visible', $id, $visibility[$value]);
        
        if (!$updated) {
            $Result->show("danger", _("Error saving filters"), true);
        }
    }
} 

$Result->show("success", _('Filter saved'));

?>
