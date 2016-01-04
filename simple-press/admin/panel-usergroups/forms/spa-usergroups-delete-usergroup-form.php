<?php
/*
Simple:Press
Admin User Groups Delete User Group Form
$LastChangedDate: 2014-06-20 20:47:00 -0700 (Fri, 20 Jun 2014) $
$Rev: 11582 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# function to display the delete user group form.  It is hidden until the delete user group link is clicked
function spa_usergroups_delete_usergroup_form($usergroup_id) {
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	jQuery('#usergrouprow-<?php echo $usergroup_id; ?>').addClass('inForm');
    	spjAjaxForm('sfusergroupdel<?php echo $usergroup_id; ?>', 'sfreloadub');
    });
</script>
<?php
	$usergroup = spa_get_usergroups_row($usergroup_id);

	spa_paint_options_init();

    $ahahURL = SFHOMEURL.'index.php?sp_ahah=usergroups-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=delusergroup';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" name="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>">
<?php
		echo sp_create_nonce('forum-adminform_usergroupdelete');
		spa_paint_open_tab(spa_text('User Groups').' - '.spa_text('Manage User Groups'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(spa_text('Delete User Group'), 'true', 'delete-user-group');
?>
					<input type="hidden" name="usergroup_id" value="<?php echo $usergroup->usergroup_id; ?>" />
<?php
					echo '<p>';
					spa_etext("Warning! You are about to delete a User Group!");
					echo '</p>';
					echo '<p>';
					spa_etext("This will remove the usergroup and also remove user memberships contained in this usergroup.");
					echo '</p>';
					echo '<p>';
					echo sprintf(spa_text('Please note that this action %s can NOT be reversed %s'), '<strong>', '</strong>');
					echo '</p>';
					echo '<p>';
					spa_etext('Click on the Delete User Group button below to proceed');
					echo '</p>';
				spa_paint_close_fieldset();
			spa_paint_close_panel();
			do_action('sph_usergroup_delete_panel');
		spa_paint_close_container();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="button-primary" id="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" name="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" value="<?php spa_etext('Delete User Group'); ?>" />
		<input type="button" class="button-primary" onclick="javascript:jQuery('#usergroup-<?php echo $usergroup->usergroup_id; ?>').html('');jQuery('#usergrouprow-<?php echo $usergroup_id; ?>').removeClass('inForm');" id="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" name="delusergroupcancel<?php echo $usergroup->usergroup_id; ?>" value="<?php spa_etext('Cancel'); ?>" />
		</div>
		</form>
	<?php spa_paint_close_tab(); ?>

	<div class="sfform-panel-spacer"></div>
<?php
}
?>