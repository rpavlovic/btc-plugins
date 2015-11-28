<?php
/*
Simple:Press
Admin Forums Global Permission Form
$LastChangedDate: 2014-09-15 19:29:25 -0700 (Mon, 15 Sep 2014) $
$Rev: 11975 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# function to display the add global permission set form. It is hidden until user clicks the add global permission set link
function spa_forums_global_perm_form()
{
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	spjAjaxForm('sfnewglobalpermission', 'sfreloadfb');
    });
</script>
<?php
	spa_paint_options_init();

    $ahahURL = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=globalperm';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfnewglobalpermission" name="sfnewglobalpermission">
<?php
		echo sp_create_nonce('forum-adminform_globalpermissionnew');
		spa_paint_open_tab(spa_text('Forums').' - '.spa_text('Add Global Permission Set'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(spa_text('Add a User Group Permission Set to All Forums'), 'true', 'add-a-user-group-permission-set-to-all-forums');

					spa_paint_select_start(spa_text('Select usergroup'), 'usergroup_id', '');
					spa_display_usergroup_select(false, 0, false);
					spa_paint_select_end();

					spa_paint_select_start(spa_text('Select permission set'), 'role', '');
					spa_display_permission_select(false, 0, false);
					spa_paint_select_end();

					echo '<p>'.spa_text('Caution:  Any current permission sets for the selected usergroup for ANY forum may be overwritten').'</p>';

				spa_paint_close_fieldset();
			spa_paint_close_panel();
			do_action('sph_forums_global_perm_panel');
		spa_paint_close_container();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php spa_etext('Add Global Permission'); ?>" />
		</div>
	<?php spa_paint_close_tab(); ?>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}
?>