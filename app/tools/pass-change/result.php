<?php

# checks
if(strlen($_POST['ipampassword1'])<8)					{ $Result->show("danger", _("Invalid password"), true); }
if($_POST['ipampassword1']!=$_POST['ipampassword2'])	{ $Result->show("danger", _("Passwords do not match"), true); }

# update pass
$User->update_user_pass($_POST['ipampassword1']);
?>
