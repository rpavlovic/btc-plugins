<?php
/*
Simple:Press
Admin Components General Support Functions
$LastChangedDate: 2014-09-11 19:44:06 -0700 (Thu, 11 Sep 2014) $
$Rev: 11957 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_get_login_data() {
	$sfcomps = array();

	$sflogin = array();
	$sflogin = sp_get_option('sflogin');
	$sfcomps['sfregmath'] = $sflogin['sfregmath'];
	$sfcomps['sfloginurl'] = sp_filter_url_display($sflogin['sfloginurl']);
	$sfcomps['sfloginemailurl'] = sp_filter_url_display($sflogin['sfloginemailurl']);
	$sfcomps['sflogouturl'] = sp_filter_url_display($sflogin['sflogouturl']);
	$sfcomps['sfregisterurl'] = sp_filter_url_display($sflogin['sfregisterurl']);
	$sfcomps['sptimeout'] = sp_esc_int($sflogin['sptimeout']);

	$sfrpx = sp_get_option('sfrpx');
	$sfcomps['sfrpxenable'] = $sfrpx['sfrpxenable'];
	$sfcomps['sfrpxkey'] = $sfrpx['sfrpxkey'];
	$sfcomps['sfrpxredirect'] = sp_filter_url_display($sfrpx['sfrpxredirect']);

	return $sfcomps;
}

function spa_get_seo_data() {
	$sfcomps = array();

	# browser title
	$sfseo = sp_get_option('sfseo');
	$sfcomps['sfseo_overwrite'] = $sfseo['sfseo_overwrite'];
	$sfcomps['sfseo_blogname'] = $sfseo['sfseo_blogname'];
	$sfcomps['sfseo_pagename'] = $sfseo['sfseo_pagename'];
	$sfcomps['sfseo_homepage'] = $sfseo['sfseo_homepage'];
	$sfcomps['sfseo_topic'] = $sfseo['sfseo_topic'];
	$sfcomps['sfseo_forum'] = $sfseo['sfseo_forum'];
	$sfcomps['sfseo_noforum'] = $sfseo['sfseo_noforum'];
	$sfcomps['sfseo_page'] = $sfseo['sfseo_page'];
	$sfcomps['sfseo_sep'] = $sfseo['sfseo_sep'];

	# meta tags
	$sfmetatags = array();
	$sfmetatags = sp_get_option('sfmetatags');
	$sfcomps['sfdescr'] = sp_filter_title_display($sfmetatags['sfdescr']);
	$sfcomps['sfdescruse'] = $sfmetatags['sfdescruse'];
	$sfcomps['sfusekeywords'] = sp_filter_title_display($sfmetatags['sfusekeywords']);
	$sfcomps['sfkeywords'] = (isset($sfmetatags['sfkeywords'])) ? $sfmetatags['sfkeywords'] : 0;

	return $sfcomps;
}

function spa_get_forumranks_data() {
	$rankings = sp_get_sfmeta('forum_rank');

	return $rankings;
}

function spa_get_specialranks_data() {
	$special_rankings = sp_get_sfmeta('special_rank');

	return $special_rankings;
}

function spa_get_messages_data() {
	$sfcomps = array();

	# custom message for posts
	$sfpostmsg = array();
	$sfpostmsg = sp_get_option('sfpostmsg');
	$sflogin = array();
	$sflogin = sp_get_option('sflogin');

	$sfcomps['sfpostmsgtext'] = sp_filter_text_edit($sfpostmsg['sfpostmsgtext']);
	$sfcomps['sfpostmsgtopic'] = $sfpostmsg['sfpostmsgtopic'];
	$sfcomps['sfpostmsgpost'] = $sfpostmsg['sfpostmsgpost'];

	# custom editor message
	$sfcomps['sfeditormsg'] = sp_filter_text_edit(sp_get_option('sfeditormsg'));

	$sneakpeek = sp_get_sfmeta('sneakpeek', 'message');
	$adminview = sp_get_sfmeta('adminview', 'message');
	$userview = sp_get_sfmeta('userview', 'message');

	$sfcomps['sfsneakpeek'] = '';
	$sfcomps['sfadminview'] = '';
	$sfcomps['sfuserview'] = '';
	if (!empty($sneakpeek[0])) $sfcomps['sfsneakpeek'] = sp_filter_text_edit($sneakpeek[0]['meta_value']);
	if (!empty($adminview[0])) $sfcomps['sfadminview'] = sp_filter_text_edit($adminview[0]['meta_value']);
	if (!empty($userview[0])) $sfcomps['sfuserview'] = sp_filter_text_edit($userview[0]['meta_value']);
	$sfcomps['sfsneakredirect'] = sp_filter_url_display($sflogin['sfsneakredirect']);

	return $sfcomps;
}

function spa_paint_custom_smileys() {
	global $spPaths, $tab;

	$scount = -1;

	# load smiles from sfmeta
	$filelist = array();

	$meta = sp_get_sfmeta('smileys', 'smileys');
	$smeta = $meta[0]['meta_value'];

	# Open forum-smileys folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$spPaths['smileys'].'/';
	$dlist = @opendir($path);
	if (!$dlist) {
	   echo '<table><tr><td class="sflabel"><strong>'.spa_text('The forum-smileys folder does not exist').'</strong></td></tr></table>';
       return;
    }

	# start the table display
	echo '<table id="sfsmileytable" class="wp-list-table widefat">';
	$row = 0;

	# gather the file data
	while (false !== ($file = readdir($dlist))) {
		$path_info = pathinfo($path.$file);
		$ext = strtolower($path_info['extension']);
		if (($file != '.' && $file != '..') && ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'bmp')) {
			$filelist[] = $file;
		}
	}

	# now to sort them if required
	$newfiles = (count($filelist) + 1);
	$sortlist = array();

	if ($filelist) {
		foreach ($filelist as $file) {
			$found = false;
			if ($meta[0]['meta_value']) {
				foreach ($meta[0]['meta_value'] as $name => $info) {
					if ($info[0] == $file) {
						$found = true;
						break;
					}
				}
			}
			if ($found) {
				if (isset($info[3])) {
					$sortlist[$info[3]] = $file;
				} else {
					$sortlist[] = $file;
				}
			} else {
				$sortlist[$newfiles] = $file;
				$newfiles++;
			}
		}
		ksort($sortlist);
	}

	if ($sortlist) {
		foreach ($sortlist as $file) {
			$found = false;
			echo '<tr id="'.$row.'">';
			$row++;

			if ($meta[0]['meta_value']) {
				foreach ($meta[0]['meta_value'] as $name => $info) {
					if ($info[0] == $file) {
						$found = true;
						break;
					}
				}
			}
			if (!$found) {
				$sname = str_replace('.', '_', $file);
				$code = str_replace('.', '_', $file);
				$in_use = false;
				$break = false;
			} else {
				$code = stripslashes($info[1]);
				$sname = $name;
				$in_use = $info[2];
				$break = (isset($info[4])) ? $info[4] : false;
			}
			$scount++;

			# drag handle cell
			echo '<td class="dragHandle" style="text-align:center;width:2%">';
			echo '<img class="spSmiley" src="'.SFSMILEYS.$file.'" alt="" />';
			echo '</td>';

			# image and file name and inout fields
			echo '<td class="wp-core-ui">';
			spa_paint_open_fieldset($file, false);
				echo '<input type="hidden" name="smfile[]" value="'.$file.'" />';
				spa_paint_input(spa_text('Name'), 'smname[]', $sname, false, true);
				spa_paint_input(spa_text('Code'), 'smcode[]', $code, false, true);
				spa_paint_checkbox(spa_text('Break Smileys Row in Editor Display'), "smbreak-$sname", $break);
				spa_paint_checkbox(spa_text('Allow Use of this Smiley'), "sminuse-$sname", $in_use);
			spa_paint_close_fieldset();
			echo '</td>';

			echo '<td style="text-align: left;vertical-align: middle;margin:0;padding:0 4px 0 0;width:3%">';
			$site = esc_url(SFHOMEURL.'index.php?sp_ahah=components&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;action=delsmiley&amp;file=$file");
			echo '<img src="'.SFCOMMONIMAGES.'delete.png" title="'.spa_text('Delete Smiley').'" alt="" onclick="spjDelRowReload(\''.$site.'\', \'sfreloadsm\');" />';
			echo '</td>';
			echo '</tr>';
		}
	}

	echo '</table>';
	echo '<input type="hidden" id="smiley-count" name="smiley-count" value="'.$scount.'" />';
	closedir($dlist);
}

function spa_paint_rank_images() {
	global $tab, $spPaths;

	# Open badges folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$spPaths['ranks'].'/';
	$dlist = @opendir($path);
	if (!$dlist) {
		echo '<table><tr><td class="sflabel"><strong>'.spa_text('The rank badges folder does not exist').'</strong></td></tr></table>';
		return;
	}

	# start the table display
	$class = 'class ="spMobileTableData"';
?>
	<table class="widefat fixed spMobileTable800">
		<thead>
			<tr>
				<th style='text-align:center'><?php spa_etext('Badge'); ?></th>
				<th style='text-align:center'><?php spa_etext('Filename'); ?></th>
				<th style='text-align:center'><?php spa_etext('Remove'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	while (false !== ($file = readdir($dlist))) {
		$path_info = pathinfo($path.$file);
		$ext = strtolower($path_info['extension']);
		if (($file != '.' && $file != '..') && ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'bmp')) {
			$found = false;
?>
			<tr <?php echo $class; ?>>
				<td data-label='<?php spa_etext('Badge'); ?>'>
					<img class="sfrankbadge" src="<?php echo(esc_url(SFRANKS.'/'.$file)); ?>" alt="" />
				</td>
				<td data-label='<?php spa_etext('Filename'); ?>'>
					<?php echo($file); ?>
				</td>
				<td data-label='<?php spa_etext('Remove'); ?>'>
<?php
					$site = esc_url(SFHOMEURL.'index.php?sp_ahah=components&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;action=delbadge&amp;file=$file");
					echo '<img src="'.SFCOMMONIMAGES.'delete.png" title="'.spa_text('Delete Rank Badge').'" alt="" onclick="spjDelRowReload(\''.$site.'\', \'sfreloadfr\');" />';
?>
				</td>
			</tr>
<?php
			$class = (strpos($class, 'alternate') === false) ? 'class="spMobileTableData alternate"' : 'class="spMobileTableData"';
		}
	}
	echo '</table>';
	closedir($dlist);
}

?>