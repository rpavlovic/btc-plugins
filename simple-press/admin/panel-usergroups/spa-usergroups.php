<?php
/*
Simple:Press
Admin User Groups
$LastChangedDate: 2014-06-20 20:47:00 -0700 (Fri, 20 Jun 2014) $
$Rev: 11582 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# Check Whether User Can Manage User Groups
if (!sp_current_user_can('SPF Manage User Groups')) {
	spa_etext('Access denied - you do not have permission');
	die();
}

global $spStatus;

include_once SF_PLUGIN_DIR.'/admin/panel-usergroups/spa-usergroups-display.php';
include_once SF_PLUGIN_DIR.'/admin/panel-usergroups/support/spa-usergroups-prepare.php';
include_once SF_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

if ($spStatus != 'ok') {
	include_once SPLOADINSTALL;
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-usergroups';
# --------------------------------------------------------------------

$tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'usergroups';
spa_panel_header();
spa_render_usergroups_panel($tab);
spa_panel_footer();

?>