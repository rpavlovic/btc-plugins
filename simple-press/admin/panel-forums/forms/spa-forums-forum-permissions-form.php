<?php
/*
Simple:Press
Admin Forums Forum Permission Form
$LastChangedDate: 2014-09-15 19:29:25 -0700 (Mon, 15 Sep 2014) $
$Rev: 11975 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# function to display the current forum permission set.  It is hidden until the permission set link is clicked.
# additional forms to add, edit or delete these permission set are further hidden belwo the permission set information
function spa_forums_view_forums_permission_form($forum_id)
{
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	jQuery('#forumrow-<?php echo $forum_id; ?>').addClass('inForm');
    });
</script>
<?php
	$forum = spdb_table(SFFORUMS, "forum_id=$forum_id", 'row');

	spa_paint_options_init();
	spa_paint_open_tab(spa_text('Forums').' - '.spa_text('Manage Groups and Forums'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('View Forum Permissions'), false);
				$perms = sp_get_forum_permissions($forum->forum_id);
				if ($perms) {
?>
					<table class="sfmaintable" style="padding:5px;border-spacing:3px;border-collapse:separate;">
						<tr>
							<td style="text-align:center" colspan="3"><strong><?php echo spa_text('Current permission set for forum').' '.sp_filter_title_display($forum->forum_name); ?></strong></td>
						</tr>
<?php
					foreach ($perms as $perm) {
						$usergroup = spa_get_usergroups_row($perm->usergroup_id);
						$role = spa_get_role_row($perm->permission_role);
?>
						<tr>
							<td class="sflabel"><?php echo sp_filter_title_display($usergroup->usergroup_name); ?> => <?php echo sp_filter_title_display($role->role_name); ?></td>
							<td style="text-align:center">
<?php
                                $base = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah');
								$target = "curperm-$perm->permission_id";
								$image = SFADMINIMAGES;
?>
								<input type="button" class="spButton-tall" value="<?php echo sp_splice(spa_text('Edit Permission Set'), 0); ?>" onclick="spjLoadForm('editperm', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $perm->permission_id; ?>');" />
								<input type="button" class="spButton-tall" value="<?php echo sp_splice(spa_text('Delete Permission Set'), 0); ?>" onclick="spjLoadForm('delperm', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $perm->permission_id; ?>');" />
							</td>
			   			</tr>
						<tr class="sfinline-form"> <!-- This row will hold hidden forms for the current forum permission set -->
						  	<td colspan="3">
								<div id="curperm-<?php echo $perm->permission_id; ?>">
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<table class="sfmaintable" style="padding:5px;border-spacing:3px;border-collapse:separate;">
						<tr>
							<td>
								<?php spa_etext('No permission sets for any usergroup'); ?>
							</td>
						</tr>
				<?php } ?>
			   			<tr>
			   				<td colspan="3" style="text-align:center">
<?php
                                $base = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah');
								$target = "newperm-$forum->forum_id";
								$image = SFADMINIMAGES;
?>
								<input type="button" class="spButton-tall" value="<?php echo sp_splice(spa_text('Add Permission'), 0); ?>" onclick="spjLoadForm('addperm', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $forum->forum_id; ?>', 'sfopen');" />
			   				</td>
						</tr>
						<tr class="sfinline-form"> <!-- This row will hold ahah forms for adding a new forum permission set -->
						  	<td colspan="3">
								<div id="newperm-<?php echo $forum->forum_id; ?>">
								</div>
							</td>
						</tr>
					</table>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<form>
		<div class="sfform-submit-bar">
            <input type="button" class="button-primary" onclick="javascript:jQuery('#forum-<?php echo $forum->forum_id; ?>').html('');jQuery('#forumrow-<?php echo $forum_id; ?>').removeClass('inForm');" id="sfgroupdel<?php echo $forum->forum_id; ?>" name="forumcancel<?php echo $forum->forum_id; ?>" value="<?php spa_etext('Cancel'); ?>" />
		</div>
	</form>
	<?php spa_paint_close_tab(); ?>
	<div class="sfform-panel-spacer"></div>
<?php
}
?>