<?php
/*
 * insert new hosts to database
 *******************************/

# check for number of input values
$max = ini_get("max_input_vars");
if(sizeof($_POST)>=ini_get("max_input_vars")) 							{ $Result->show("danger", _("Number of discovered hosts exceed maximum possible defined by php.ini - set to ")." $max <hr>"._("Please adjust your php.ini settings for value `max_input_vars`"), true); }

// fetch custom fields and check for required
$cfs = $Tools->fetch_custom_fields ('vrf');

$required_fields = [];
foreach ($cfs as $cf) {
    if ($cf->required) {
        $required_fields[] = $cf;
    }
}

# ok, lets get results form post array!
foreach($_POST as $key=>$line) {
	// IP address
	if(substr($key, 0,2)=="rd") 			    { $res[substr($key, 2)]['rd']  	        = $line; }
	// mac
	elseif(substr($key, 0,4)=="name") 		    { $res[substr($key, 4)]['name']  	    = $line; }
	// description
	elseif(substr($key, 0,11)=="description") 	{ $res[substr($key, 11)]['description'] = $line; }
	// custom fields
	elseif (isset($required_fields)) {
    	foreach ($required_fields as $rf) {
        	if((strpos($key, $rf->name)) !== false) {
                                                { $res[substr($key, strlen($rf->name))][$rf->name] = $line; }
        	}
    	}
	}
}

# insert entries
if(sizeof($res)>0) {
	$errors = 0;
	foreach($res as $r) {
		# set insert values
		$values = array("rd"=>$r['rd'],
						"name"=>$r['name'],
						"description"=>$r['description']
						);
        # custom fields
		if (isset($required_fields)) {
			foreach ($required_fields as $k=>$f) {
				$values[$f['name']] = $r[$f['name']];
			}
		}
        # insert vrfs
        if(!$Admin->object_modify("vrf", "add", "vrfId", $values))	{ $Result->show("danger", _("Failed to import entry")." ".$r['number']." ".$r['name'], false); $errors++; }
	}

	# success if no errors
	if($errors==0) {  $Result->show("success", _("Scan results added to database")."!", true); }
}
# error
else { $Result->show("danger", _("No entries available"), true); }
?>
