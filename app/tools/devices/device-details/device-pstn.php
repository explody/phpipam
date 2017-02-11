<?php

/**
 * Script to display devices
 */




# fetch custom fields
$cfs = $Tools->fetch_custom_fields('pstnPrefixes');

# check
is_numeric($_GET['id']) ? : $Result->show("danger", _("Invalid ID"), true);

# title - subnets
print "<h4>"._("Belonging PSTN prefixes")."</h4><hr>";

//fetch
$subprefixes = $Tools->fetch_multiple_objects ("pstnPrefixes", "deviceId", $device->id, 'prefix', true );


# Hosts table
print "<table id='switchMainTable' class='devices table table-striped table-top table-condensed'>";

# headers
if ($User->settings->enablePSTN!="1") {
    $Result->show("danger", _("PSTN prefixes module disabled."), false);
}
else {
    // table
    print "<table id='manageSubnets' class='ipaddresses table sorted table-striped table-top table-td-top'>";
    // headers
    print "<thead>";
    print "<tr>";
    print " <th>"._('Prefix')."</th>";
    print " <th>"._('Name')."</th>";
    print " <th>"._('Range')."</th>";
    print " <th>"._('Start')."</th>";
    print " <th>"._('Stop')."</th>";
    print " <th>"._('Objects')."</th>";

	foreach($cfs as $cf) {
		if($cf->visible) {
			print "<th class='hidden-xs hidden-sm hidden-md'>$field[name]</th>";
			$colspan++;
		}
	}

    if($admin)
    print " <th style='width:80px'></th>";
    print "</tr>";
    print "</thead>";


    # if none than print
    if($subprefixes===false) {
        print "<tr>";
        print " <td colspan='$colspan'>".$Result->show("info","No PSTN prefixes attached to device", false, false, true)."</td>";
        print "</tr>";
    }
    else {

        # print
        foreach ($subprefixes as $k=>$sp) {

    		print "<tr>";
    		//prefix, name
    		print "	<td><a href='".create_link($_GET['page'],"pstn-prefixes",$sp->id)."'>  ".$sp->prefix."</a></td>";
    		print "	<td><strong>$sp->name</strong></td>";
    		// range
    		print " <td>".$sp->prefix.$sp->start."<br>".$sp->prefix.$sp->stop."</td>";
    		//start/stop
    		print "	<td>".$sp->start."</td>";
    		print "	<td>".$sp->stop."</td>";
    		//count slaves
    		$cnt_sl = $Tools->count_database_objects("pstnPrefixes", "master", $sp->id);
    		if($cnt_sl!=0) {
                $cnt = $cnt_sl." Prefixes";
    		}
    		else {
                $cnt = $Tools->count_database_objects("pstnNumbers", "prefix", $sp->id). " Addresses";
    		}
            print "	<td><span class='badge badge1 badge5'>".$cnt."</span></td>";


    		//custom
            // TODO: DRY ffs
	   		foreach($cfs as $cf) {
		   		# hidden?
		   		if($cf->visible) {

		   			$html[] =  "<td class='hidden-xs hidden-sm hidden-md'>";
		   			//booleans
					if($cf->type == "boolean")	{
                        $html[] = Components::boolean_display_value($sp->{$cf->name});
					}
					//text
					elseif($cf->type == "text") {
						if(strlen($sp->{$cf->name})>0)		{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $sp->{$cf->name})."'>"; }
						else										{ print ""; }
					}
					else {
						$html[] = $sp->{$cf->name};

					}
		   			$html[] =  "</td>";
	   			}
	    	}


    		# set permission
    		$permission = $Tools->check_prefix_permission ($User->user);

    		print "	<td class='actions' style='padding:0px;'>";
    		print "	<div class='btn-group'>";

    		if($permission>1) {
    			print "		<button class='btn btn-xs btn-default editPSTN' data-action='edit'   data-id='".$sp->id."'><i class='fa fa-gray fa-pencil'></i></button>";
    			print "		<button class='btn btn-xs btn-default editPSTN' data-action='delete' data-id='".$sp->id."'><i class='fa fa-gray fa-times'></i></button>";
    		}
    		else {
    			print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-pencil'></i></button>";
    			print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-times'></i></button>";
    		}
    		print "	</div>";
    		print "	</td>";

    		print "</tr>";
        }
    }
    print "</tbody>";
    print "</table>";

}
?>