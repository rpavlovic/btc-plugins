<?php
/*
Simple:Press
Admin Components Special Rank Add Member Form
$LastChangedDate: 2014-06-20 20:47:00 -0700 (Fri, 20 Jun 2014) $
$Rev: 11582 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_components_sr_add_members_form($rank_id) {
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	spjAjaxForm('sfmembernew<?php echo $rank_id; ?>', 'sfreloadfr');
    });
</script>
<?php
	spa_paint_options_init();

    $ahahURL = SFHOMEURL.'index.php?sp_ahah=components-loader&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;saveform=specialranks&amp;action=addmember&amp;id=$rank_id";
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfmembernew<?php echo $rank_id; ?>" name="sfmembernew<?php echo $rank_id ?>">
<?php
		echo sp_create_nonce('special-rank-add');
?>
					<p><?php spa_etext('Select members to add (use CONTROL for multiple members)') ?></p>
<?php
                	$from = esc_js(spa_text('Eligible members'));
                	$to = esc_js(spa_text('Selected members'));
                    $action = 'addru';
                	include_once SF_PLUGIN_DIR.'/admin/library/ahah/spa-ahah-multiselect.php';
?>
					<div class="clearboth"></div>
<?php
        $loc = 'sfrankshow-'.$rank_id;
?>
		<input type="submit" class="button-primary" id="sfnewmember<?php echo $rank_id; ?>" name="sfnewmember<?php echo $rank_id; ?>" onclick="javascript:jQuery('#amember_id<?php echo $rank_id; ?> option').each(function(i) {jQuery(this).attr('selected', 'selected');});" value="<?php spa_etext('Add Members'); ?>" />
		<input type="button" class="button-primary" onclick="spjToggleLayer('<?php echo $loc; ?>');javascript:jQuery('#members-<?php echo $rank_id; ?>').html('');" id="addmemberscancel<?php echo $rank_id; ?>" name="addmemberscancel<?php echo $rank_id; ?>" value="<?php spa_etext('Cancel'); ?>" />
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
}
?>