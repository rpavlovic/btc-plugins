<?php
/*
Simple:Press
Admin Options Members Form
$LastChangedDate: 2015-01-08 08:45:24 -0800 (Thu, 08 Jan 2015) $
$Rev: 12326 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_options_members_form() {
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	spjAjaxForm('sfmembersform', 'sfreloadms');
    });
</script>
<?php
	global $wp_roles;

	$sfoptions = spa_get_members_data();

    $ahahURL = SFHOMEURL.'index.php?sp_ahah=options-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=members';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfmembersform" name="sfmembers">
	<?php echo sp_create_nonce('forum-adminform_members'); ?>
<?php
	spa_paint_options_init();

    #== MEMBERS Tab ============================================================

	spa_paint_open_tab(spa_text('Options').' - '.spa_text('Member Settings'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Member Profiles'), true, 'member-profiles');
				spa_paint_checkbox(spa_text('Disallow members not logged in to post as guests'), 'sfcheckformember', $sfoptions['sfcheckformember']);
				spa_paint_checkbox(spa_text('Allow members to hide their online status'), 'sfhidestatus', $sfoptions['sfhidestatus']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Member Name Linking'), true, 'member-name-linking');
				$values = array(spa_text('Nothing'), spa_text("Member's profile"), spa_text("Member's website"));
				spa_paint_radiogroup(spa_text("Link a member's name when displayed to"), 'namelink', $values, $sfoptions['namelink'], false, true);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Guest Settings'), true, 'guest-settings');
				spa_paint_checkbox(spa_text('Require guests to enter email address'), 'reqemail', $sfoptions['reqemail']);
				spa_paint_checkbox(spa_text('Store guest information in a cookie for subsequent visits'), 'storecookie', $sfoptions['storecookie']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Inactive Members Account Auto Removal'), true, 'user-removal');
				echo '<div class="sfoptionerror">';
				spa_etext('Remember - users are members of your WordPress site NOT members of Simple:Press. WordPress performs the actual user deletion which will include any components (like blog posts for example) that the user may have contributed. Use with care!');
				echo '</div>';
				spa_paint_checkbox(spa_text('Enable auto removal of member accounts'), 'sfuserremove', $sfoptions['sfuserremove']);
				spa_paint_checkbox(spa_text('Remove inactive members (if auto removal enabled)'), 'sfuserinactive', $sfoptions['sfuserinactive']);
				spa_paint_checkbox(spa_text('Remove members who have not posted  (if auto removal enabled)'), 'sfusernoposts', $sfoptions['sfusernoposts']);
				spa_paint_input(spa_text('Number of days back to remove inactive members and/or members with no forum posts (if auto removal enabled)'), 'sfuserperiod', $sfoptions['sfuserperiod']);
				if ($sfoptions['sched']) {
					$msg = spa_text('Users auto removal cron job is scheduled to run daily');
					echo '<tr><td class="message" colspan="2" style="line-height:2em;">&nbsp;<u>'.$msg.'</u></td></tr>';
				}
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Post Counts on Deletion'), true, 'delete-count');
				spa_paint_checkbox(spa_text('Adjust users post count when post deleted'), 'post_count_delete', $sfoptions['post_count_delete']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		do_action('sph_options_members_left_panel');

		spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Blacklists'), true, 'member-blacklists');
    			$submessage = spa_text('Enter a comma separated list of account names to disallow when a user registers');
				spa_paint_wide_textarea(spa_text('Blocked account names'), 'account-name', $sfoptions['account-name'], $submessage);
    			$submessage = spa_text('Enter a comma separated list of display names to disallow for users');
				spa_paint_wide_textarea(spa_text('Blocked display names'), 'display-name', $sfoptions['display-name'], $submessage);
    			$submessage = spa_text('Enter a comma separated list of guest names to disallow when a guest posts');
				spa_paint_wide_textarea(spa_text('Blocked guest posting names'), 'guest-name', $sfoptions['guest-name'], $submessage);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		do_action('sph_options_members_right_panel');

		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php spa_etext('Update Members Options'); ?>" />
	</div>
<?php
	spa_paint_close_tab();
?>
	</form>
<?php
}

function spa_create_usergroup_select($sfdefgroup) {
    $out = '';

    $ugid = spdb_table(SFUSERGROUPS, "usergroup_id=$sfdefgroup", 'usergroup_id');
	if (empty($ugid)) $out.= '<option selected="selected" value="-1">INVALID</option>';

	$usergroups = spa_get_usergroups_all();
	$default='';
	foreach ($usergroups as $usergroup) {
		if ($usergroup->usergroup_id == $sfdefgroup) {
			$default = 'selected="selected" ';
		} else {
			$default = null;
		}
		$out.= '<option '.$default.'value="'.$usergroup->usergroup_id.'">'.sp_filter_title_display($usergroup->usergroup_name).'</option>';
		$default = '';
	}
	return $out;
}
?>