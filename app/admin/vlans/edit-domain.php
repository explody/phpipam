<?php

/**
 *	Edis vlan domains
 ************************************************/

# fetch vlan details
$l2_domain = $Admin->fetch_object ("vlanDomains", "id", @$_POST['id']);
$l2_domain = $l2_domain!==false ? (array) $l2_domain : array();

# set readonly flag
$readonly = $_POST['action']=="delete" ? "readonly" : "";

?>

<script type="text/javascript">
$(document).ready(function(){
     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
});
</script>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('l2 domain'); ?></div>

<!-- content -->
<div class="pContent">
	<form id="editVLANdomain">
    <?php $csrf->insertToken('/ajx/admin/vlans/edit-domain-result'); ?>
    
	<table class="table table-noborder table-condensed">
	<!-- number -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" class="number form-control input-sm" name="name" placeholder="<?php print _('domain name'); ?>" value="<?php print @$l2_domain['name']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>
	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" class="description form-control input-sm" name="description" placeholder="<?php print _('Description'); ?>" value="<?php print @$l2_domain['description']; ?>" <?php print $readonly; ?>>
			<input type="hidden" name="id" value="<?php print @$_POST['id']; ?>">
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<!-- sections -->
	<tr>
		<td style="vertical-align: top !important"><?php print _('Sections to display domain in'); ?>:</td>
		<td>
            <select multiple="multiple" id="sections" name="sections[]">
        <?php
        # TODO: DRY, see admin/devices/edit.php
        # select sections
        $sections = $Sections->fetch_all_sections('name');
        
        # reformat domains sections to array
        $domain_sections = explode(";", @$l2_domain['permissions']);
        $domain_sections = is_array($domain_sections) ? $domain_sections : array();
        $selectedSections = array();
        // loop
        if ($sections !== false) {
            foreach($sections as $section) {
                if(in_array($section->id, $domain_sections)) { 
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
	</tr>

	</table>
	</form>

	<?php
	//print delete warning
	if($_POST['action'] == "delete")	{ $Result->show("warning", _('Warning').':</strong> '._('removing vlan domain will move all belonging vlans to default domain')."!", false);  }
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?> editVLANdomainsubmit" id="editVLANdomainsubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- result -->
	<div class="domainEditResult"></div>
</div>
