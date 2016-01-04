<?php
/*
Plugin Name: Simple:Press
Version: 5.5.8
Plugin URI: http://simple-press.com
Description: Fully featured but simple page-based forum
Author: Andy Staines & Steve Klasen
Author URI: http://simple-press.com
WordPress Versions: 4.2.2 and above
For full acknowledgements click on the copyright/version strip at the bottom of forum pages
$LastChangedDate: 2015-06-12 12:32:49 -0700 (Fri, 12 Jun 2015) $
$Rev: 13001 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ==============================================================================================
# Copyright 2006/2014  Andy Staines & Steve Klasen
# Please read the 'License' supplied with this plugin
# (goto /admin/help/documentation)
# and abide by it's few simple requests.
# ==============================================================================================

# version and system control constants
define('SPPLUGNAME',	'Simple:Press');
define('SPVERSION',		'5.5.8');
define('SPBUILD', 		 13000);
define('SPRELEASE', 	'Development');

define('SFPLUGHOME', 	'<a class="spLink" href="http://simple-press.com" target="_blank">Simple:Press</a>');
define('SFHOMESITE', 	'http://simple-press.com');

# Define startup constants

# IMPORTANT - SFHOMEURL is always slashed! check user_trailingslashit()) if using standalone (ie no args)
# IMPORTANT - This is NOT the same as what wp refers to as home url. This is actually URL to the WP files. Changing to be consistent ripples through everything.
$home = trailingslashit(site_url());
define('SFHOMEURL', $home);

# IMPORTANT - SFSITEURL is always slashed! check user_trailingslashit()) if using standalone (ie no args)
# IMPORTANT - This is NOT the same as what wp refers to as site url. This is actually to the site home URL. Changing to be consistent ripples through everything.
$site = trailingslashit(home_url());
define('SFSITEURL', $site);

define('SF_PLUGIN_DIR',	WP_PLUGIN_DIR.'/'.basename(dirname(__file__)));
define('SF_PLUGIN_URL', plugins_url().'/'.basename(dirname(__file__)));
define('SPBOOT', 		dirname(__file__).'/sp-startup/');
define('SPAPI', 		dirname(__file__).'/sp-api/');
define('SPINSTALLPATH',	'simple-press/sp-startup/sp-load-install.php');

# Define startup global variables
global $spAllOptions, $spPaths, $spIsAdmin, $spIsForumAdmin, $spStatus, $spAPage, $spIsForum, $spBootCache,
	   $spContentLoaded, $spMobile, $spDevice, $spImages, $wpdb;
$spAllOptions       = array();
$spPaths            = array();
$spIsAdmin          = false;
$spIsForumAdmin     = false;
$spStatus           = '';
$spAPage            = '';
$spIsForum          = false;
$spBootCache        = array();
$spContentLoaded    = false;
$spMobile		    = 0;
$spDevice		    = 'desktop';
$spImages			= array();

# Initialise the cache array
$spBootCache['globals'] 	= false;
$spBootCache['ranks'] 		= false;
$spBootCache['site_auths']	= false;

# if this is a network upgrade, make sure we switch to the site being updated
# this is so the constants are defined for right blog
if (isset($_GET['sfnetworkid'])) switch_to_blog(esc_sql($_GET['sfnetworkid']));

# Set the table prefix thst can be overridden in wp-config.sys
if (!defined('SF_PREFIX')) define('SF_PREFIX', $wpdb->prefix);

# Include minimum globally required startup files
include_once SPBOOT.'sp-load-core.php';
include_once SPBOOT.'sp-load-ahah.php';

# Load up admin boot files if an admin session
if ($spIsAdmin == true) include_once SPBOOT.'sp-load-core-admin.php';
if ($spIsForumAdmin == true) include_once SPBOOT.'sp-load-admin.php';

# Load up site boot files if a site session
if ($spIsAdmin == false) include_once SPBOOT.'sp-load-site.php';

# Finally wait to find out if this is a forum page being requested
if ($spIsAdmin == false) add_action('wp', 'sp_is_forum_page');

# Load up forum page code if a forum page request
do_action('sph_control_startup');

function sp_is_forum_page() {
	global $spIsForum, $wp_query;
	if ((is_page()) && ($wp_query->post->ID == sp_get_option('sfpage'))) {
		$spIsForum = true;
		sp_load_current_user();
		include_once SPBOOT.'sp-load-forum.php';
	}
}

?>