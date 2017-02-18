<?php

/*
 * Print select vlan in subnets
 *******************************/

# fetch all permitted domains
$permitted_nameservers = $Sections->fetch_section_nameserver_sets ($_POST['sectionId']);

# fetch all belonging nameserver set
$cnt = 0;
$nsout = [];

# Only parse nameserver if any exists
if($permitted_nameservers != false) {
	foreach($permitted_nameservers as $k=>$n) {
		// fetch nameserver sets and append
		$nameserver_set = $Tools->fetch_multiple_objects("nameservers", "id", $n, "name", "namesrv1");

		//save to array
		$nsout[$n] = $nameserver_set;

		//count add
		$cnt++;
	}
	//filter out empty
	$permitted_nameservers = array_filter($nsout);
}

?>

<select name="nameserverId" id="nameserver-select" class="select2">
	<optgroup label='<?php print _('Select nameserver set'); ?>'>

	<option value="0"><?php print _('No nameservers'); ?></option>
	<?php
	# print all available nameserver sets
	if ($permitted_nameservers!==false) {
		foreach($permitted_nameservers as $n) {

			if($n[0]!==null) {
				foreach($n as $ns) {
					// set print
					$printNS = "$ns->name";
					$printNS .= " (" . array_shift(explode(";",$ns->namesrv1)).",...)";

					/* selected? */
					if(@$subnet_old_details['nameserverId']==$ns->id) 	{ print '<option value="'. $ns->id .'" selected>'. $printNS .'</option>'. "\n"; }
					elseif(@$_POST['nameserverId'] == $ns->id) 			{ print '<option value="'. $ns->id .'" selected>'. $printNS .'</option>'. "\n"; }
					else 												{ print '<option value="'. $ns->id .'">'. $printNS .'</option>'. "\n"; }
				}
			}
		}
	}
	?>
	</optgroup>


</select>
<?php
Components::render_select2_js('#nameserver-select',
                              ['templateResult' => '$(this).s2oneLine']);
?>
