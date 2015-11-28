<?php
/*
Simple:Press
Admin Forums Ordering Form
$LastChangedDate: 2015-03-15 13:28:35 -0700 (Sun, 15 Mar 2015) $
$Rev: 12595 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_forums_ordering_form($groupId=0) {
	$where = '';
	if ($groupId) $where = "group_id=$groupId";
	$groups = spdb_table(SFGROUPS, $where, '', 'group_seq');
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	<?php if ($groupId != 0) { ?>
    	jQuery('#grouprow-<?php echo $groupId; ?>').addClass('inForm');
    	<?php } ?>
    	jQuery('#groupList').nestedSortable({
    		handle: 'div',
    		items: 'li',
    		tolerance: 'intersect',
    		listType: 'ul',
    		protectRoot: true,
    		placeholder: 'sortable-placeholder',
    		forcePlaceholderSize: true,
    		helper: 'clone',
    		tabSize: 30,
    		maxLevels: 10,
            scroll: true,
            scrollSensitivity: 1,
            scrollSpeed: 1
    	});

    	jQuery('#sfforumorder').ajaxForm({
    		target: '#sfmsgspot',
    		beforeSubmit: function() {
    			jQuery('#sfmsgspot').show();
    			jQuery('#sfmsgspot').html(pWait);
    		},
    		success: function() {
    			jQuery('#sfmsgspot').hide();
    			<?php if ($groupId == 0) { ?>
    			jQuery('#sfreloadfo').click();
    			<?php } else { ?>
    			jQuery('#sfreloadfb').click();
    			<?php } ?>
    			jQuery('#sfmsgspot').fadeIn();
    			jQuery('#sfmsgspot').fadeOut(6000);
    		},
    		beforeSerialize: function() {
    			jQuery("input#spForumsOrder").val(jQuery("#groupList").nestedSortable('serialize'));
    		}
    	});
    });
</script>
<?php
	spa_paint_options_init();

	$ahahURL = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=orderforum';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfforumorder" name="sfforumorder">
<?php
		echo sp_create_nonce('forum-adminform_forumorder');
		spa_paint_open_tab(spa_text('Forums').' - '.spa_text('Group and Forum Ordering'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(spa_text('Order Groups and Forums'), 'true', 'order-forums');
				?>
				<input type="hidden" id="cgroup" name="cgroup" value="<?php echo $groupId; ?>" />
				<?php
				echo '<p>'.spa_text('Here you can set the order of Groups, Forums and SubForums by dragging and dropping below. After ordering, push the save button.').'</p>';

				if (!empty($groups)) {
					echo '<ul id="groupList" class="groupList menu">';
					foreach ($groups as $group) {
						echo "<li id='group-G$group->group_id' class='menu-item-depth-0'>";
						echo "<div class='alt group-list menu-item'>";
						echo "<span class='item-name'>$group->group_name</span>";
						echo '</div>';

						# now output any forums in the group
						$allForums = spa_get_forums_in_group($group->group_id);
						$depth = 1;

						if (!empty($allForums)) {
							echo "<ul id='forumList-$group->group_id' class='forumList menu'>";
							foreach ($allForums as $thisForum) {
								if ($thisForum->parent == 0) {
									sp_paint_order_forum($thisForum, $allForums, $depth);
								}
							}
							echo '</ul>';
						}
						echo '</li>';
					}
					echo '</ul>';
				}
				echo '<input type="text" class="inline_edit" size="70" id="spForumsOrder" name="spForumsOrder" />';
				spa_paint_close_fieldset();
			spa_paint_close_panel();
		spa_paint_close_container();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php spa_etext('Save Ordering'); ?>" />
        <?php if ($groupId) { ?>
		<input type="button" class="button-primary" onclick="javascript:jQuery('#group-<?php echo $group->group_id; ?>').html('');jQuery('#grouprow-<?php echo $groupId; ?>').removeClass('inForm');" id="sforder<?php echo $group->group_id; ?>" name="groupordercancel<?php echo $group->group_id; ?>" value="<?php spa_etext('Cancel'); ?>" />
        <?php } ?>

		</div>
<?php
		spa_paint_close_tab();
?>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}

function sp_paint_order_forum($thisForum, $allForums, $depth) {
	# display this forum
	echo "<li id='forum-F$thisForum->forum_id' class='menu-item-depth-$depth'>";
	echo "<div class='forum-list menu-item'>";
	echo "<span class='item-name'>$thisForum->forum_name</span>";
	echo '</div>';
	if ($thisForum->children) {
		$depth++;
		$subForums = unserialize($thisForum->children);
		echo "<ul id='subForumList-$thisForum->forum_id' class='subforumList menu'>";
		foreach ($subForums as $subForum) {
			foreach ($allForums as $whichForum) {
				if ($whichForum->forum_id == $subForum) {
					$thisSubForum = $whichForum;
				}
			}
			sp_paint_order_forum($thisSubForum, $allForums, $depth);
		}
		echo '</ul>';
	} else {
		echo '</li>';
	}
}

?>