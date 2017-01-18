<?php

/**
 * print subnet masks popup
 */

?>

<!-- header -->
<div class="pHeader"><?php print _('Subnet masks'); ?></div>

<!-- content -->
<div class="pContent">
	<?php
	// set popup
	$popup = true;
	// table
	include('print-table.php');
	?>
</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default <?php print @$_POST['closeClass']; ?>"><?php print _('Close'); ?></button>
	</div>
</div>

