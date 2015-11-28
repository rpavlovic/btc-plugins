<?php
/*
Simple:Press
Admin Profiles Support Functions
$LastChangedDate: 2014-09-07 10:26:01 -0700 (Sun, 07 Sep 2014) $
$Rev: 11945 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_get_options_data() {
	$sfprofile = sp_get_option('sfprofile');
	$sfsigimagesize = sp_get_option('sfsigimagesize');
	$sfprofile['sfsigwidth'] = $sfsigimagesize['sfsigwidth'];
	$sfprofile['sfsigheight'] = $sfsigimagesize['sfsigheight'];
	return $sfprofile;
}

function spa_get_tabsmenus_data() {
	$tabs = sp_profile_get_tabs();
	return $tabs;
}

function spa_get_avatars_data() {
	$sfavatars = sp_get_option('sfavatars');
	return $sfavatars;
}

function spa_paint_avatar_pool() {
	global $tab, $spPaths;

	$out = '';

	# Open avatar pool folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$spPaths['avatar-pool'].'/';
	$dlist = @opendir($path);
	if (!$dlist) {
		echo '<table><tr><td class="sflabel"><strong>'.spa_text('The avatar pool folder does not exist').'</strong></td></tr></table>';
		return;
	}

	# start the table display
	$out.= '<table class="wp-list-table widefat"><tr>';
	$out.= '<th style="width:30%;text-align:center">'.spa_text('Avatar').'</th>';
	$out.= '<th style="width:50%;text-align:center">'.spa_text('Filename').'</th>';
	$out.= '<th style="text-align:center">'.spa_text('Remove').'</th>';
	$out.= '</tr>';

    $out.= '<tr><td colspan="3">';
    $out.= '<div id="sf-avatar-pool">';
	while (false !== ($file = readdir($dlist))) {
		if ($file != "." && $file != "..") {
			$found = false;
		    $out.= '<table style="width:100%">';
			$out.= '<tr>';
			$out.= '<td style="text-align:center;width:30%" class="spWFBorder"><img class="sfavatarpool" src="'.esc_url(SFAVATARPOOLURL.'/'.$file).'" alt="" /></td>';
			$out.= '<td style="text-align:center;width:50%" class="spWFBorder sflabel">';
			$out.= $file;
			$out.= '</td>';
			$out.= '<td style="text-align:center" class="spWFBorder">';
            $site = esc_url(SFHOMEURL.'index.php?sp_ahah=profiles&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;action=delavatar&amp;file=$file");
			$out.= '<img src="'.SFCOMMONIMAGES.'delete.png" title="'.spa_text('Delete Avatar').'" alt="" onclick="spjDelRowReload(\''.$site.'\', \'sfreloadav\');" />';
			$out.= '</td>';
			$out.= '</tr>';
			$out.= '</table>';
		}
	}
	$out.= '</div>';
	$out.= '</td></tr></table>';
	closedir($dlist);

	echo $out;
}
?>