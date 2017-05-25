<?php

# Check if the method is protected
if ($_POST['action'] == 'delete') {
    $auth_method = $Admin->fetch_object('usersAuthMethod', 'id', $_POST['id']);
    if ($auth_method->protected == 'Yes') {
        $Result->show('danger', _('Method cannot be deleted as it is protected'), true, true);
    }
}

# route to proper auth method editing
if (!file_exists(dirname(__FILE__)."/edit-$_POST[type].php")) {
    $Result->show('danger', _('Invalid method type'), true, true);
} else {
    include "edit-$_POST[type].php";
}
