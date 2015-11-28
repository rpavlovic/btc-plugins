<?php
/*
Simple:Press
Installer/Upgrader
$LastChangedDate: 2015-06-12 12:31:03 -0700 (Fri, 12 Jun 2015) $
$Rev: 13000 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ==========================================================================================
#
#	INSTALL ROUTER
#	The SP Install Router. Handles all Installs and Updates of SP as needed.
#
# ==========================================================================================

?>
<style type="text/css">
.imessage, .zmessage, #debug {
	display: none;
	width: 820px;
	height: auto;
	color: #000000;
	font-weight: bold;
	font-size: 11px;
	font-family: Tahoma, Helvetica, Arial, Verdana;
	margin: 2px 10px;
	padding: 5px;
	border: 2px solid #555555;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}

.updated h3,
.error h3 {
	border: none;
	font-size: 20px;
	margin: 2px 0 10px;
	line-height: 1.2em;
    min-height: 50px;
}

.imessage {
	background-color: #FFF799;
}

.zmessage {
	background-color: #A7C1FF;
}

.pbar {
	margin: 2px 20px;
	width: 820px;
}

#zonecount {
	display:none;
}

.stayleft {
	float: left;
	padding-right: 15px;
}
</style>
<?php
global $spAllOptions, $spStatus;
$spAllOptions = array();
$spAllOptions = sp_load_alloptions();

# get current version  and build from database
$current_version = sp_get_option('sfversion');
$current_build = sp_get_option('sfbuild');

# check if we are coming back in with post values to install
if (isset($_POST['goinstall'])) {
	sp_go_install();
	return;
}

# check if we are coming back in with post values to upgrade
if (isset($_POST['goupgrade'])) {
	# run the upgrade
	sp_go_upgrade($current_version, $current_build);
	return;
}

# check if we are coming back in with post values to upgrade network
if (isset($_POST['gonetworkupgrade'])) {
	# run the upgrade
	sp_go_network_upgrade($current_version, $current_build);
	return;
}

# dont check for downgrade if is version 6.0 and build 12969 since we screwed up
# and released that version/build instead of version 5.5.7
# need to bypass the check so we can downgrade the release to 5.5.8 and current build
# safe since no database changes
if ($current_version == '6.0' && $current_build == 12969) {
    # do upgrade to correct version and build
?>
	<div class="wrap"><br />
		<div class="updated">
    		<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
    		<h3><?php echo sprintf(spa_text('Upgrade Simple:Press From Version %s to %s'), sp_get_option('sfversion'), SPVERSION); ?><br />
    		(<?php spa_etext('Build'); ?> <?php echo sp_get_option('sfbuild'); ?> <?php spa_etext('to'); ?> <?php spa_etext('Build'); ?> <?php echo SPBUILD; ?>)<br /><br />
            <?php spa_etext('Please note:  You are NOT downgrading.  This upgrade corrects the version/build that inadvertently showed up as part of the 5.5.7 release.'); ?></h3>
		</div>
        <hr />
		<form name="sfupgrade" method="post" action="<?php echo admin_url('admin.php?page='.SPINSTALLPATH); ?>"><br />
			<input type="submit" class="button-primary" id="sbutton" name="goupgrade" value="<?php spa_etext('Perform Upgrade'); ?>" />
			<?php if (is_multisite() && is_super_admin()) { ?>
				<input type="submit" class="button-primary" id="sbutton" name="gonetworkupgrade" value="<?php spa_etext('Perform Network Upgrade'); ?>" />
			<?php } ?>
		</form>
	</div>
<?php
   	return;
} else {
    # downgrading? not good
    if (SPBUILD < $current_build || SPVERSION < $current_version) {
    	sp_no_downgrade();
    	return;
    }
}

# Has the systen been installed?
if (version_compare($current_version, '1.0', '<')) {
	sp_install_required();
	return;
}

# Base already installed - check Version and Build Number
if (($current_build < SPBUILD) || ($current_version > SPVERSION)) {
	sp_upgrade_required();
	return;
}

# simple press files are younger than db version - warn you cannot downgrade like that
function sp_no_downgrade() {
?>
	<div class="wrap"><br />
<?php
		# Warn you can't downgrade
?>
		<div class="updated">
			<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<h3><?php spa_etext('Downgrade Warning'); ?></h3>
			<p><?php spa_etext('It appears you are attempting to downgrade your Simple:Press Version. The Build or Version number in the sp-control.php file is lower than the currently installed version in the database.'); ?></p>
			<p><?php spa_etext('You must restore your database to this earlier version before you can continue. You cannot simply downgrade Simple:Press files as the database has been upgraded beyond the version you are attempting to downgrade to and may cause irreparable damage to the database.'); ?></p>
			</div>
		</div>
<?php
}

# set up install
function sp_install_required() {
?>
	<div class="wrap"><br />
<?php
		# Check versions
		$bad = sp_version_checks();
		if ($bad != '') {
			echo $bad.'</div>';
			return;
		}
		# Check we can create a folder in wp-content
		$bad = sp_check_folder_creation();
		if ($bad != '') {
			echo $bad.'</div>';
			return;
		}
		# OK - we can contiunue to offer full install
?>
		<div class="updated">
    		<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
    		<h3><?php spa_etext('Install Simple:Press Version'); ?> <?php echo SPVERSION; ?> - <?php spa_etext('Build'); ?> <?php echo SPBUILD; ?></h3>
		</div>
        <br />
		<form name="sfinstall" method="post" action="<?php echo admin_url('admin.php?page='.SPINSTALLPATH); ?>"><br />
			<input type="submit" class="button-primary" id="sbutton" name="goinstall" value="<?php spa_etext('Perform Installation'); ?>" />
		</form>
	</div>
<?php
}

# set up upgrade
function sp_upgrade_required() {
?>
	<div class="wrap"><br />
<?php
        $bad = sp_version_checks();
		if ($bad != '') {
			echo $bad.'</div>';
			return;
		}
?>
		<div class="updated">
    		<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
    		<h3><?php echo sprintf(spa_text('Upgrade Simple:Press From Version %s to %s'), sp_get_option('sfversion'), SPVERSION); ?><br />
    		(<?php spa_etext('Build'); ?> <?php echo sp_get_option('sfbuild'); ?> <?php spa_etext('to'); ?> <?php spa_etext('Build'); ?> <?php echo SPBUILD; ?>)</h3>
		</div>
        <hr />
		<form name="sfupgrade" method="post" action="<?php echo admin_url('admin.php?page='.SPINSTALLPATH); ?>"><br />
			<?php if (SPVERSION == '5.0.0' && substr(sp_get_option('sfversion'), 0, 1) != '5') { ?>
			<p><b><input type="checkbox" name="dostorage" id="dostorage" />
			<label for="dostorage"><?php spa_etext('Check this box to have the upgrade attempt to convert current storage locations to V5 format (optional)'); ?></label>
			<br /><br /></b></p>
			<?php } ?>

			<input type="submit" class="button-primary" id="sbutton" name="goupgrade" value="<?php spa_etext('Perform Upgrade'); ?>" />
			<?php if (is_multisite() && is_super_admin()) { ?>
				<input type="submit" class="button-primary" id="sbutton" name="gonetworkupgrade" value="<?php spa_etext('Perform Network Upgrade'); ?>" />
			<?php } ?>
		</form>
	</div>
<?php
}

# perform install
function sp_go_install() {
	global $current_user;

	add_option('sfInstallID', $current_user->ID); # use wp option table

	$phpfile = SFHOMEURL.'index.php?sp_ahah=install&sfnonce='.wp_create_nonce('forum-ahah');
	$image = SFCOMMONIMAGES.'working.gif';

	# how many users passes at 200 a pop?
	$users = spdb_count(SFUSERS);

	$subphases = ceil($users / 200);
	$nextsubphase = 1;
?>
	<div class="wrap"><br />
		<div class="updated">
		<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
		<h3><?php spa_etext('Simple:Press is being installed'); ?></h3></div>
		<div style="clear: both"></div>
		<br />
		<div class="wrap sfatag">
			<div class="imessage" id="imagezone"></div><br />
			<div class="pbar" id="progressbar"></div><br />
		</div>
		<div style="clear: both"></div>
		<table id="SPLOADINSTALLtable" style="padding:2px;border-spacing:6px;border-collapse:separate;">
			<tr><td><div class="zmessage" id="zone0"><?php spa_text('Installing'); ?>...</div></td></tr>
			<tr><td><div class="zmessage" id="zone1"></div></td></tr>
			<tr><td><div class="zmessage" id="zone2"></div></td></tr>
			<tr><td><div class="zmessage" id="zone3"></div></td></tr>
			<tr><td><div class="zmessage" id="zone4"></div></td></tr>
			<tr><td><div class="zmessage" id="zone5"></div></td></tr>
			<tr><td><div class="zmessage" id="zone6"></div></td></tr>
			<tr><td><div class="zmessage" id="zone7"></div></td></tr>
			<tr><td><div class="zmessage" id="zone8"></div></td></tr>
			<tr><td><div class="zmessage" id="zone9"></div></td></tr>
			<tr><td><div class="zmessage" id="zone10"></div></td></tr>
			<tr><td><div class="zmessage" id="zone11"></div></td></tr>
		</table>
		<div class="zmessage" id="errorzone"></div>
		<div id="finishzone"></div>
<?php
		$pass = 11;
		$curr = 0;
		$messages = esc_js(spa_text('Go to Forum Admin')).'@'.esc_js(spa_text('Installation is in progress - please wait')).'@'.esc_js(spa_text('Installation Completed')).'@'.esc_js(spa_text('Installation has been Aborted'));
		$out = '<script type="text/javascript">'."\n";
		$out.= 'spjPerformInstall("'.$phpfile.'", "'.$pass.'", "'.$curr.'", "'.$subphases.'", "'.$nextsubphase.'", "'.$image.'", "'.$messages.'");'."\n";
		$out.= '</script>'."\n";
		echo $out;
?>
	</div>
<?php
}

# perform upgrade
function sp_go_upgrade($current_version, $current_build) {
	global $current_user;

	if (SPVERSION == '5.0.0') {
		$dostorage = false;
		if (isset($_POST['dostorage'])) $dostorage = true;
		sp_add_option('V5DoStorage', $dostorage);
	}

	update_option('sfInstallID', $current_user->ID); # use wp option table
	sp_update_option('sfStartUpgrade', $current_build);

	$phpfile = SFHOMEURL.'index.php?sp_ahah=upgrade&sfnonce='.wp_create_nonce('forum-ahah');
	$image = SFCOMMONIMAGES.'working.gif';

	$targetbuild = SPBUILD;
?>
	<div class="wrap"><br />
		<div class="updated">
		<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
		<h3><?php spa_etext('Simple:Press is being upgraded'); ?></h3>
		</div><br />
		<div class="wrap sfatag">
			<div class="imessage" id="imagezone"></div>
		</div><br />
		<div class="pbar" id="progressbar"></div><br />
		<div class="wrap sfatag">
			<div class="zmessage" id="errorzone"></div>
			<div id="finishzone"></div><br />
		</div><br />
		<div id="debug">
			</p><b>Please copy the details below and include them on any support forum question you may have:</b><br /><br /></p>
		</div>
<?php
		$messages = esc_js(spa_text('Go to Forum Admin')).'@'.esc_js(spa_text('Upgrade is in progress - please wait')).'@'.esc_js(spa_text('Upgrade Completed')).'@'.esc_js(spa_text('Upgrade Aborted')).'@'.esc_js(spa_text('Go to Forum'));
		$out = '<script type="text/javascript">'."\n";
		$out.= 'spjPerformUpgrade("'.$phpfile.'", "'.$current_build.'", "'.$targetbuild.'", "'.$current_build.'", "'.$image.'", "'.$messages.'", "'.sp_url().'");'."\n";
		$out.= '</script>'."\n";
		echo $out;
?>
	</div>
<?php
	# clear any combined css/js cached files
	sp_clear_combined_css('all');
	sp_clear_combined_css('mobile');
	sp_clear_combined_css('tablet');

	sp_clear_combined_scripts('desktop');
	sp_clear_combined_scripts('mobile');
	sp_clear_combined_scripts('tablet');
}

# perform network upgrade
function sp_go_network_upgrade($current_version, $current_build) {
	global $current_user;
?>
	<div class="wrap"><br />
		<div class="updated">
		<img class="stayleft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
		<h3><?php spa_etext('Simple:Press is upgrading the Network.'); ?></h3>
		</div><br />
		<div class="wrap sfatag">
			<div class="imessage" id="imagezone"></div>
		</div><br />
		<div class="pbar" id="progressbar"></div><br />
		<div class="wrap sfatag">
			<div class="zmessage" id="errorzone"></div>
			<div id="finishzone"></div><br />
		</div><br />
		<div id="debug">
			</p><b>Please copy the details below and include them on any support forum question you may have:</b><br /><br /></p>
		</div>
	</div>
<?php
	# get list of network sites
	$sites = wp_get_sites();

	# save current site to restore when finished
	$current_site = get_current_site();

	# loop through all blogs and upgrade ones with active simple:press
	foreach ($sites as $site) {
		# switch to network site and see if simple:press is active
		switch_to_blog($site['blog_id']);
		global $wpdb;
		$installed = spdb_select('set', 'SELECT option_id FROM '.$wpdb->prefix."sfoptions WHERE option_name='sfversion'");
		if ($installed) {
			$phpfile = SFHOMEURL.'index.php?sp_ahah=upgrade&sfnonce='.wp_create_nonce('forum-ahah').'&sfnetworkid='.$site['blog_id'];
			$image = SFCOMMONIMAGES.'working.gif';
			$targetbuild = SPBUILD;
			update_option('sfInstallID', $current_user->ID); # use wp option table

			# save the build info
			$out = spa_text('Upgrading Network Site ID').': '.$site['blog_id'].'<br />';
			sp_update_option('sfStartUpgrade', $current_build);

			# upgrade the network site
			$messages = esc_js(spa_text('Go to Forum Admin')).'@'.esc_js(spa_text('Upgrade is in progress - please wait')).'@'.esc_js(spa_text('Upgrade Completed')).'@'.esc_js(spa_text('Upgrade Aborted')).'@'.esc_js(spa_text('Go to Forum'));
			$out.= '<script type="text/javascript">'."\n";
			$out.= 'spjPerformUpgrade("'.$phpfile.'", "'.$current_build.'", "'.$targetbuild.'", "'.$current_build.'", "'.$image.'", "'.$messages.'", "'.sp_url().'");'."\n";
			$out.= '</script>'."\n";
			echo $out;

			# clear any combined css/js cached files
			sp_clear_combined_css('all');
			sp_clear_combined_css('mobile');
	        sp_clear_combined_css('tablet');

    		sp_clear_combined_scripts('desktop');
    		sp_clear_combined_scripts('mobile');
    		sp_clear_combined_scripts('tablet');
		}
	}

	#restore original network site
	switch_to_blog($current_site);
}

# Perform version checks prior to install
function sp_version_checks() {
	global $wp_version, $wpdb;

	$message = '';
	$testtable = true;

	$logo = '<div class="error"><img src="'.SFCOMMONIMAGES.'sp-small-logo.png" alt="" title="" /><br /><hr />';

	$xml = sp_load_version_xml();
	if ($xml) {
    	# WordPress version check
        $wp_required = (string) $xml->core->requires;
    	if (sp_version_compare($wp_required, $wp_version) == false) {
    		$message.= $logo;
    		$message.= '<h3>'.sprintf(spa_text('%s Version %s'), 'WordPress', $wp_version).'</h3>';
    		$message.= '<p>'.sprintf(spa_text('Your version of %s is not supported by %s %s'), 'WordPress', 'Simple:Press', SPVERSION).'<br />';
    		$message.= sprintf(spa_text('%s version %s or above is required'), 'WordPress', $wp_required).'</p><br />';
    		$logo = '<hr />';
    	}

    	# MySQL Check
        $mysql_required = (string) $xml->core->mysql_ver;
    	if (sp_version_compare($mysql_required, $wpdb->db_version()) == false) {
    		$message.= $logo;
    		$message.= '<h3>'.sprintf(spa_text('%s Version %s'), 'MySQL', $wpdb->db_version()).'</h3>';
    		$message.= '<p>'.sprintf(spa_text('Your version of %s is not supported by %s %s'), 'MySQL', 'Simple:Press', SPVERSION).'<br />';
    		$message.= sprintf(spa_text('%s version %s or above is required'), 'MySQL', $mysql_required).'</p><br />';
    		$logo = '<hr />';
    		$testtable = false;
    	}

    	# PHP Check
        $php_required = (string) $xml->core->php_ver;
    	if (sp_version_compare($php_required, phpversion()) == false) {
    		$message.= $logo;
    		$message.= '<h3>'.sprintf(spa_text('%s Version %s'), 'PHP', phpversion()).'</h3>';
    		$message.= '<p>'.sprintf(spa_text('Your version of %s is not supported by %s %s'), 'PHP', 'Simple:Press', SPVERSION).'<br />';
    		$message.= sprintf(spa_text('%s version %s or above is required'), 'PHP', $php_required).'</p><br />';
    		$logo = '<hr />';
    	}

    	# test we can create database tables
    	if ($testtable) {
    		if (sp_test_table_create() == false) {
    			$message.= $logo;
    			$message.= '<h3>'.spa_text('Database Problem').'</h3>';
    			$message.= '<p>'.sprintf(spa_text('%s can not Create Tables in your database'), 'Simple:Press').'</p><br />';
    		}
    	}
    } else {
	   $message.= '<h3>'.spa_text('Unable to communicate with SP servers for version requirements. Please try again in few minutes.').'</h3>';
    }

	if ($message) $message.= '</div>';
	return $message;
}

function sp_version_compare($need, $got) {
	$need= explode('.', $need);
	$got = explode('.', $got);

	if (isset($need[0]) && intval($need[0]) > intval($got[0])) return false;
	if (isset($need[0]) && intval($need[0]) < intval($got[0])) return true;

	if (isset($need[1]) && intval($need[1]) > intval($got[1])) return false;
	if (isset($need[1]) && intval($need[1]) < intval($got[1])) return true;

	if (isset($need[2]) && intval($need[2]) > intval($got[2])) return false;
	return true;
}

function sp_test_table_create() {
	# make sure we can create database tables
	$sql = '
		CREATE TABLE sfCheckCreate (
			id int(4) NOT NULL,
			item varchar(15) default NULL,
			PRIMARY KEY	 (id)
		) '.spdb_charset();
	spdb_query($sql);

	$success = spdb_select('var', 'SHOW TABLES LIKE "sfCheckCreate"');
	if ($success == false) {
		return false;
	} else {
		spdb_query('DROP TABLE sfCheckCreate');
		return true;
	}
}

function sp_check_folder_creation() {
	# Make sure we have write access to the wp-content folder
	$message = '';
	$logo = '<div class="error"><img src="'.SFCOMMONIMAGES.'sp-small-logo.png" alt="" title="" /><br /><hr />';

	if (!is_writable(WP_CONTENT_DIR)) {
		$message.= $logo;
		$message.= '<h3>'.spa_text('Permission Problem').'</h3>';
		$message.= '<p>'.sprintf(spa_text('%s can not create sub-folders under wp-content. Please assign correct permissions and re-run the install'), 'Simple:Press').'</p><br />';
	}
	return $message;
}

?>