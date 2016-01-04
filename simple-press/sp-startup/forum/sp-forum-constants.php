<?php
/*
Simple:Press
DESC:
$LastChangedDate: 2014-06-21 20:33:29 -0700 (Sat, 21 Jun 2014) $
$Rev: 11585 $
*/

# ==========================================================================================
#
# 	FORUM PAGE
#	This file loads for forum page loads only
#
# ==========================================================================================

global $spStatus;

$redirect = (isset($_SERVER['REDIRECT_URL'])) ? $_SERVER['REDIRECT_URL'] : '';

if (!defined('SPMEMBERLIST')) define('SPMEMBERLIST', sp_url('members'));

# hack to get around wp_list_pages() bug
if ($spStatus == 'ok') {
	# go for whole row so it gets cached.
	$t = spdb_table(SFWPPOSTS, 'ID='.sp_get_option('sfpage'), 'row');
	if (!defined('SFPAGETITLE')) define('SFPAGETITLE', $t->post_title);
}

do_action('sph_forum_constants');

?>