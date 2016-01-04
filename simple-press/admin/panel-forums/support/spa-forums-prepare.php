<?php
/*
Simple:Press
Admin Forums Data Prep Support Functions
$LastChangedDate: 2014-09-07 10:26:01 -0700 (Sun, 07 Sep 2014) $
$Rev: 11945 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_paint_custom_icons() {
	global $spPaths;

	$out = '';

	# Open custom icons folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$spPaths['custom-icons'].'/';
	$dlist = @opendir($path);
	if (!$dlist) {
		echo '<table><tr><td class="sflabel"><strong>'.spa_text('The custom icons folder does not exist').'</strong></td></tr></table>';
		return;
	}

	# start the table display
	$out.= '<table class="wp-list-table widefat"><tr>';
	$out.= '<th style="width:30%;text-align:center">'.spa_text('Icon').'</th>';
	$out.= '<th style="width:50%;text-align:center">'.spa_text('Filename').'</th>';
	$out.= '<th style="text-align:center">'.spa_text('Remove').'</th>';
	$out.= '</tr>';

    $out.= '<tr><td colspan="3">';
    $out.= '<div id="sf-custom-icons">';
	while (false !== ($file = readdir($dlist))) {
		if ($file != '.' && $file != '..') {
			$found = false;
		    $out.= '<table style="width:100%">';
			$out.= '<tr>';
			$out.= '<td style="text-align:center;width:30%" class="spWFBorder"><img class="sfcustomicon " src="'.esc_url(SFCUSTOMURL.'/'.$file).'" alt="" /></td>';
			$out.= '<td style="text-align:center;width:50%"  class="spWFBorder sflabel">';
			$out.= $file;
			$out.= '</td>';
			$out.= '<td style="text-align:center"  class="spWFBorder">';
			$site = esc_url(SFHOMEURL.'index.php?sp_ahah=forums&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;action=delicon&amp;file=$file");
			$out.= '<img src="'.SFCOMMONIMAGES.'delete.png" title="'.spa_text('Delete custom icon').'" alt="" onclick="spjDelRowReload(\''.$site.'\', \'sfreloadci\');" />';
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