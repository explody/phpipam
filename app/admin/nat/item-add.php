<?php

/**
 *	remove item from nat
 ************************************************/

# validate id
if(!is_numeric($_POST['id']))                           { $Result->show("danger", _("Invalid ID"), true, true); }
# validate type
if(!in_array($_POST['type'], array("src", "dst")))      { $Result->show("danger", _("Invalid NAT direction"), true, true); }

# get NAT object
$nat = $Admin->fetch_object ("nat", "id", $_POST['id']);
$nat!==false ? : $Result->show("danger", _("Invalid ID"), true, true);

?>

<!-- header -->
<div class="pHeader"><?php print _('Add NAT item'); ?></div>

<!-- content -->
<div class="pContent">

    <h4><?php print _("Search objects"); ?></h4>
    <hr>

    <form id="search_nats" style="margin-bottom: 10px;" class="form-inline">
        <?php $csrf->insertToken('/ajx/admin/nat/item-add-search'); ?>
        <input type="hidden" name="id" value="<?php print $nat->id; ?>">
        <input type="hidden" name="type" value="<?php print $_POST['type']; ?>">
        <input type="text" class='form-control input-sm' name="ip" placeholder="<?php print _('Enter subnet/IP'); ?>" style='width:60%;margin:0px;'>
        <input type="submit" class="form-control input-sm" value="Search" style="width:20%">
    </form>

    <div id="nat_search_results" style="max-height: 300px;overflow-y: scroll;"></div>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Cancel'); ?></button>
	</div>
    <div id="nat_search_results_commit"></div>
</div>


<script type="text/javascript">
$(document).ready(function() {
    $('form#search_nats').submit(function() {
        $.post("ajx/admin/nat/item-add-search", $(this).serialize(), function(data) {
            $('#nat_search_results').html(data);
        });
    });
    return false;
})
</script>
