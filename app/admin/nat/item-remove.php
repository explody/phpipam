<?php

/**
 *	remove item from nat
 ************************************************/

# validate csrf cookie
$Tools->csrf_validate($csrf, $Result);

# get NAT object
$nat = $Admin->fetch_object ("nat", "id", $_POST['id']);
$nat!==false ? : $Result->show("danger", _("Invalid ID"), true, true);

# disable edit on delete
$readonly = $_POST['action']=="delete" ? "readonly" : "";
$link = $readonly ? false : true;
?>


<!-- header -->
<div class="pHeader"><?php print _('Remove NAT item'); ?></div>

<!-- content -->
<div class="pContent">
    <?php
    # remove item from nat
    $s = json_decode($nat->src, true);
    $d = json_decode($nat->dst, true);

    if(is_array($s[$_POST['type']]))
    $s[$_POST['type']] = array_diff($s[$_POST['type']], array($_POST['item_id']));
    if(is_array($d[$_POST['type']]))
    $d[$_POST['type']] = array_diff($d[$_POST['type']], array($_POST['item_id']));

    # save back and update
    $src_new = json_encode(array_filter($s));
    $dst_new = json_encode(array_filter($d));

    if($Admin->object_modify ("nat", "edit", "id", array("id"=>$_POST['id'], "src"=>$src_new, "dst"=>$dst_new))!==false) {
        $Result->show("success", "Object removed", false);
    }
    ?>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Close'); ?></button>
	</div>
</div>
