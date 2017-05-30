<?php

/*
 * Print select vlan in subnets
 *******************************/

# fetch all permitted domains
$permitted_domains = $Sections->fetch_section_domains ($_POST['sectionId']);

?>

<select name="vlanId" id="ip-vlan-select" class="select2">
	<optgroup label="<?php print _('Select VLAN'); ?>:">
	<option value="0"><?php print _('No VLAN'); ?></option>
    </optgroup>
	<?php
	# print all available domains
	foreach($permitted_domains as $did=>$d) {
		//more than default
			print "<optgroup label='".$d->name." L2 domain'>";
			//add
			print "<option value='Add' data-domain='".$did."'>"._('+ Add new VLAN')."</option>";

            $vlans = $Tools->fetch_multiple_objects("vlans", "domainId", $did, "number");
            
			if($vlans) {
				foreach($vlans as $v) {
					// set print
					$printVLAN = $v->number;
					if(!empty($v->name)) {
						if(strlen($v->name)>25)	{
							$printVLAN .= " (".substr($v->name,0,25)."...)";
						}
						else {
							$printVLAN .= " ($v->name)";
						}
					}

					/* selected? */
					if(@$subnet_old_details['vlanId']==$v->vlanId) 	{ print '<option value="'. $v->vlanId .'" selected>'. $printVLAN .'</option>'. "\n"; }
					elseif(@$_POST['vlanId'] == $v->vlanId) 	{ print '<option value="'. $v->vlanId .'" selected>'. $printVLAN .'</option>'. "\n"; }
					else 										{ print '<option value="'. $v->vlanId .'">'. $printVLAN .'</option>'. "\n"; }
				}
			} else {
				print "<option value='0' disabled>"._('No VLANs')."</option>";
			}
			print "</optgroup>";
	}
	?>
</select>
<?php
Components::render_select2_js('#ip-vlan-select',
                              ['templateResult' => '$(this).s2boldDescOneLine']);
?>
