<?php
/*
Simple:Press
Admin User Groups Main Display
$LastChangedDate: 2014-11-27 08:43:55 -0800 (Thu, 27 Nov 2014) $
$Rev: 12111 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_usergroups_usergroup_main() {
	$usergroups = spa_get_usergroups_all(null);
	if ($usergroups) {
?>
			<table class="wp-list-table widefat">
				<tr>
					<th style="text-align:center;width:9%" scope="col"><?php spa_etext('ID'); ?></th>
					<th scope="col"><?php spa_etext('Name'); ?></th>
					<th style="text-align:center;width:8%" scope="col"><?php spa_etext('Moderator'); ?></th>
				</tr>
			</table>
<?php

		foreach ($usergroups as $usergroup) {
			# display the current usergroup information in table format
?>
			<table id="usergrouprow-<?php echo($usergroup->usergroup_id); ?>" class="wp-list-table widefat">
				<tr>
					<td style="width:9%;text-align:center;padding:10px 0;" class='row-title BGhighLight'><?php echo $usergroup->usergroup_id; ?></td>
					<td><span class='row-title'><strong><?php echo sp_filter_title_display($usergroup->usergroup_name); ?></strong></span><span><br /><?php echo sp_filter_title_display($usergroup->usergroup_desc); ?></span>
					<br />
<?php
					sp_display_item_stats(SFMEMBERSHIPS, 'usergroup_id', $usergroup->usergroup_id, spa_text('Members'));
?>
					</td>
					<td style="width:8%;text-align:center;padding:10px 0;" class='row-title'><?php if ($usergroup->usergroup_is_moderator == 1) echo spa_etext("Yes"); else echo spa_etext("No"); ?></td>
				</tr>

				<tr>
					<td class='smallLabel'><?php spa_etext('Manage Group'); ?></td>
					<td colspan="2" style="padding:0 0 0 3px;text-align:left;">
<?php
                        $base = SFHOMEURL.'index.php?sp_ahah=usergroups-loader&amp;sfnonce='.wp_create_nonce('forum-ahah');
						$target = "usergroup-$usergroup->usergroup_id";
						$image = SFADMINIMAGES;
?>
						<input type="button" class="button-secondary" value="<?php echo spa_text('Edit User Group'); ?>" onclick="spjLoadForm('editusergroup', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $usergroup->usergroup_id; ?>');" />
						<input type="button" class="button-secondary" value="<?php echo spa_text('Delete User Group'); ?>" onclick="spjLoadForm('delusergroup', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $usergroup->usergroup_id; ?>');" />

						<?php sp_paint_usergroup_tip($usergroup->usergroup_id, sp_filter_title_display($usergroup->usergroup_name)); ?>

					</td>
				</tr>

				<tr class="sfinline-form"> <!-- This row will hold ahah forms for the current user group -->
				  	<td colspan="3" style="padding:0 10px 0 0;">
						<div id="usergroup-<?php echo $usergroup->usergroup_id; ?>">
						</div>
					</td>
				</tr>
				<tr class="sfsubtable sfugrouptable">
					<td class='smallLabel'><?php spa_etext('Manage Users'); ?></td>
					<td colspan="2" style="padding:0 0 0 3px;text-align:left;">
<?php
                        $site = SFHOMEURL.'index.php?sp_ahah=usergroups&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;ug=$usergroup->usergroup_id";
						$gif= SFCOMMONIMAGES.'working.gif';
						$text = esc_js(spa_text('Show/Hide'));
?>
						<input type="button" id="show<?php echo $usergroup->usergroup_id; ?>" class="button-secondary" value="<?php echo $text; ?>" onclick="spjShowMemberList('<?php echo $site; ?>', '<?php echo $gif; ?>', '<?php echo $usergroup->usergroup_id; ?>');" />
<?php
                        $base = SFHOMEURL.'index.php?sp_ahah=usergroups-loader&amp;sfnonce='.wp_create_nonce('forum-ahah');
						$target = "members-$usergroup->usergroup_id";
						$image = SFADMINIMAGES;
?>
						<input type="button" id="add<?php echo $usergroup->usergroup_id; ?>" class="button-secondary" value="<?php spa_etext('Add New'); ?>" onclick="spjLoadForm('addmembers', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $usergroup->usergroup_id; ?>'); " />
						<input type="button" id="remove<?php echo $usergroup->usergroup_id; ?>" class="button-secondary" value="<?php spa_etext('Move/Delete'); ?>" onclick="spjLoadForm('delmembers', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '<?php echo $usergroup->usergroup_id; ?>'); " />
					</td>
				</tr>
				<tr class="sfinline-form"> <!-- This row will hold hidden forms for the current user group membership-->
				  	<td colspan="3" style="padding: 0 10px 0 0;">
                        <div id="members-<?php echo $usergroup->usergroup_id; ?>"></div>
					</td>
				</tr>
			</table>
<?php
     	}
	} else {
		echo '<div class="sfempty">&nbsp;&nbsp;&nbsp;&nbsp;'.spa_text('There are no User Groups defined').'</div>';
	}
?>
    <div class="sfform-panel-spacer"></div>
	<table class="sfmaintable" style="padding:0;border-spacing:0;border-collapse:separate;">
		<tr>
			<th style="text-align:left;padding:10px 20px;" scope="col"><?php spa_etext('Members Not Belonging To Any Usergroup') ?></th>
		</tr>
		<tr class="sfsubtable sfugrouptable">
			<td style="padding:10px 20px;">
<?php
                $site = SFHOMEURL.'index.php?sp_ahah=usergroups&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;ug=0';
				$gif= SFCOMMONIMAGES.'working.gif';
				$text = esc_js(spa_text('Show/Hide Members with No Memberships'));
				?>
				<input type="button" id="show-0" class="button-secondary" value="<?php echo $text; ?>" onclick="spjShowMemberList('<?php echo $site; ?>', '<?php echo $gif; ?>', '0');" />
			</td>
		</tr>
		<tr class="sfinline-form"> <!-- This row will hold hidden forms for the current user group membership-->
		  	<td>
                <div id="members-0"></div>
			</td>
		</tr>
	</table>
<?php
}

function sp_paint_usergroup_tip($ugid, $ugname) {
	$site = SFHOMEURL.'index.php?sp_ahah=usergroup-tip&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;group=$ugid";
	$title = esc_js($ugname);
	echo "<input type='button' class='button-secondary' value='".spa_text('User Group Usage')."' onclick='spjDialogAjax(this, \"$site\", \"$title\", 600, 0, \"center\");' />";
}

?>