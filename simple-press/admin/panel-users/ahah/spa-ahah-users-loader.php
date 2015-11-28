<?php
/*
Simple:Press Users Admin
Ahah form loader - Users
$LastChangedDate: 2014-06-20 20:47:00 -0700 (Fri, 20 Jun 2014) $
$Rev: 11582 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ahah_support();

global $spStatus;
if ($spStatus != 'ok') {
	echo $spStatus;
	die();
}

include_once SF_PLUGIN_DIR.'/admin/panel-users/spa-users-display.php';
include_once SF_PLUGIN_DIR.'/admin/panel-users/support/spa-users-prepare.php';
include_once SF_PLUGIN_DIR.'/admin/panel-users/support/spa-users-save.php';
include_once SF_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

global $adminhelpfile;
$adminhelpfile = 'admin-users';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Users
if (!sp_current_user_can('SPF Manage Users')) {
	spa_etext('Access denied - you do not have permission');
	die();
}

if (isset($_GET['loadform'])) {
	spa_render_users_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform'])) {
    die();
}

die();

?>