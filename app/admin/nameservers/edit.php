<?php

/**
 *	Print all available nameserver sets and configurations
 ************************************************/

# get Nameserver sets
if($_POST['action']!="add") {
	$nameservers = $Admin->fetch_object ("nameservers", "id", $_POST['nameserverId']);
	$nameservers!==false ? : $Result->show("danger", _("Invalid ID"), true, true);
	$nameservers = (array) $nameservers;
}

# disable edit on delete
$readonly = $_POST['action']=="delete" ? "readonly" : "";

# set nameservers
$nameservers['namesrv1'] = !isset($nameservers) ? array(" ") : explode(";", $nameservers['namesrv1']);
?>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('Nameserver set'); ?></div>

<!-- content -->
<div class="pContent">

	<form id="nameserverManagementEdit">
    <?php $csrf->insertToken('/ajx/admin/nameservers/edit-result'); ?>
    
	<table id="nameserverManagementEdit2" class="table table-noborder table-condensed">

	<tbody>
	<!-- name  -->
	<tr>
		<td style="width: 200px;"><?php print _('Name'); ?></td>
		<td>
			<input type="text" class="name form-control input-sm" name="name" placeholder="<?php print _('Nameserver set'); ?>" value="<?php print @$nameservers['name']; ?>" <?php print $readonly; ?>>
		</td>
		<td></td>
	</tr>
	</tbody>

	<tbody id="nameservers">
	<!-- Nameservers -->
	<?php
	//loop
	$m=1;
	foreach ($nameservers['namesrv1'] as $ns) {
		print "<tr id='namesrv-$m'>";
		print "	<td>"._("Nameserver")." $m</td>";
		print "	<td>";
		print "	<input type='text' class='rd form-control input-sm' name='namesrv-$m' value='$ns' $readonly>";
		print "	</td>";
		print "	<td><button class='btn btn-sm btn-default' id='remove_nameserver' data-id='namesrv-".$m."'><i class='fa fa-trash-o'></i></button></td>";
		print "</tr>";
		//next
		$m++;
	}
	?>
	</tbody>

	<tbody>
	<!-- add new -->
	<tr>
		<td></td>
		<td>
			<button class="btn btn-sm btn-default" id="add_nameserver" data-id='<?php print $m; ?>' <?php print $readonly; ?>><i class="fa fa-plus"></i> <?php print _('Add nameserver'); ?></button>
		</td>
		<td></td>
	</tr>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) { print '<input type="hidden" name="nameserverId" value="'. $_POST['nameserverId'] .'">'. "\n";}
			?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
			<input type="text" class="description form-control input-sm" name="description" placeholder="<?php print _('Description'); ?>" value="<?php print @$nameservers['description']; ?>" <?php print $readonly; ?>>
		</td>
		<td></td>
	</tr>

	<!-- sections -->
	<tr>
		<td style="vertical-align: top !important"><?php print _('Sections to display nameserver set in'); ?>:</td>
		<td>
            <select multiple="multiple" id="sections" name="sections[]">
		<?php
        # TODO: DRY, see admin/devices/edit.php
		# select sections
		$sections = $Sections->fetch_all_sections('name');
        
		# reformat domains sections to array
		$nameservers_sections = explode(";", @$nameservers['permissions']);
		$nameservers_sections = is_array($nameservers_sections) ? $nameservers_sections : array();
        $selectedSections = array();
		// loop
		if ($sections !== false) {
			foreach($sections as $section) {
                if(in_array($section->id, $nameservers_sections)) { 
                    $selectedSections[] = $section->id;
                } 
                print '<option value="' . $section->id . '">' . $section->name . '</option>';
			}
		}
		?>
            </select>
            <script type="text/javascript" src="<?php print MEDIA; ?>/js/jquery.multi-select.js"></script>
            <script type="text/javascript">
               $('#sections').multiSelect({});
               $('#sections').multiSelect(
                   'select', <?php echo "['" . implode("','", $selectedSections) . "']"; ?>
               );
            </script>
		</td>
		<td></td>
	</tr>
	</tbody>

	</table>
	</form>

	<?php
	//print delete warning
	if($_POST['action'] == "delete")	{ $Result->show("warning", "<strong>"._('Warning').":</strong> "._("removing nameserver set will also remove all references from belonging subnets!"), false);}
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editNameservers"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	<!-- result -->
	<div class="nameserverManagementEditResult"></div>
</div>
