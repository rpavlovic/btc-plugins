<?php
/*
Simple:Press
Admin Forums Delete Group Form
$LastChangedDate: 2014-09-15 19:29:25 -0700 (Mon, 15 Sep 2014) $
$Rev: 11975 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# function to display the delete group form.  It is hidden until the delete group link is clicked
function spa_forums_delete_group_form($group_id) {
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	jQuery('#grouprow-<?php echo $group_id; ?>').addClass('inForm');
    	spjAjaxForm('sfgroupdel<?php echo $group_id; ?>', 'sfreloadfb');
    });
</script>
<?php
	$group = spdb_table(SFGROUPS, "group_id=$group_id", 'row');

	spa_paint_options_init();

    $ahahURL = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=deletegroup';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfgroupdel<?php echo $group->group_id; ?>" name="sfgroupdel<?php echo $group->group_id; ?>">
<?php
		echo sp_create_nonce('forum-adminform_groupdelete');
		spa_paint_open_tab(spa_text('Forums').' - '.spa_text('Manage Groups and Forums'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(spa_text('Delete Group'), 'true', 'delete-forum-group');
?>
					<input type="hidden" name="group_id" value="<?php echo $group->group_id; ?>" />
					<input type="hidden" name="cgroup_seq" value="<?php echo $group->group_seq; ?>" />
<?php
					echo '<p>';
					spa_etext('Warning! You are about to delete a group');
					echo '</p>';
					echo '<p>';
					spa_etext('This will remove ALL forums, topics and posts contained in this group');
					echo '</p>';
					echo '<p>';
					echo sprintf(spa_text('Please note that this action %s can NOT be reversed %s'), '<strong>', '</strong>');
					echo '</p>';
					echo '<p>';
					spa_etext('Click on the delete group button below to proceed');
					echo '</p>';

				spa_paint_close_fieldset();
			spa_paint_close_panel();
			do_action('sph_forums_delete_group_panel');
		spa_paint_close_container();
?>
		<div class="sfform-submit-bar">
    		<input type="submit" class="button-primary" id="groupdel<?php echo $group->group_id; ?>" name="groupdel<?php echo $group->group_id; ?>" value="<?php spa_etext('Delete Group'); ?>" />
    		<input type="button" class="button-primary" onclick="javascript:jQuery('#group-<?php echo $group->group_id; ?>').html('');jQuery('#grouprow-<?php echo $group_id; ?>').removeClass('inForm');" id="sfgroupdel<?php echo $group->group_id; ?>" name="groupdelcancel<?php echo $group->group_id; ?>" value="<?php spa_etext('Cancel'); ?>" />
		</div>
	<?php spa_paint_close_tab(); ?>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}
?>