<?php
/*
Simple:Press Admin
Ahah call for permalink update/integration
$LastChangedDate: 2014-06-20 20:47:00 -0700 (Fri, 20 Jun 2014) $
$Rev: 11582 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ahah_support();

# ----------------------------------
# Check Whether User Can Manage Integration
if (!sp_current_user_can('SPF Manage Integration')) {
	spa_etext('Access denied - you do not have permission');
	die();
}

if (isset($_GET['item'])) {
	$item = $_GET['item'];
	if ($item == 'upperm') spa_update_permalink_tool();
}

function spa_update_permalink_tool() {
	echo '<strong>&nbsp;'.sp_update_permalink(true).'</strong>';
?>
	<script type="text/javascript">window.location= "<?php echo SFADMININTEGRATION; ?>";</script>
<?php
	die();
}

die();
?>