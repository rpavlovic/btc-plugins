<?php
/*
Simple:Press
Desc:
$LastChangedDate: 2014-06-21 20:33:29 -0700 (Sat, 21 Jun 2014) $
$Rev: 11585 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ==========================================================================================
#
# 	SITE
#	This file loads the asdditional core SP support needed by the site (front end) for all
#	page loads - not just for the forum. It also exposes base api files that may be needed by
#	plugins, template tags etc., and creates items needed by the header for non forum use.
#
# ==========================================================================================

# Include core api files

# Load blog script support
add_action('wp_enqueue_scripts', 'sp_load_blog_script');

# Load blog header support
add_action('wp_head', 'sp_load_blog_support');

do_action('sph_site_startup');

?>