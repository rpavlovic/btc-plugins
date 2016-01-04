<?php
/*
Simple:Press
Admin Permissions Add Auth Form
$LastChangedDate: 2014-09-12 00:25:34 -0700 (Fri, 12 Sep 2014) $
$Rev: 11960 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_permissions_add_auth_form() {
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	spjAjaxForm('sfauthnew', '');
    });
</script>
<?php
	spa_paint_options_init();

    $ahahURL = SFHOMEURL.'index.php?sp_ahah=permissions-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=newauth';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfauthnew" name="sfauthnew">
<?php
		echo sp_create_nonce('forum-adminform_authnew');
		spa_paint_open_tab(spa_text('Permissions').' - '.spa_text('Add New Authorization'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(spa_text('Add New Authorization'), 'true', 'create-new-authorization');
?>
                    <br /><div class="sfoptionerror">
                    <?php spa_etext('Please note, this will create a new singular authorization.'); ?>
                    <?php spa_etext('However, by default, it will not be used by anything in core.'); ?>
                    <?php spa_etext('This authorization could be used for a profile authorization or by a theme or plugin.'); ?>
                    <?php spa_etext('Please see the popup help for more information.'); ?>
                    </div><br />
<?php
    				spa_paint_input(spa_text('Authorization name'), 'auth_name', '');
    				spa_paint_input(spa_text('Authorization description'), 'auth_desc', '');
    	            spa_paint_checkbox(spa_text('Activate authorization'), 'auth_active', true);
    	            spa_paint_checkbox(spa_text('Authorization is ignored for guests'), 'auth_guests', false);
    	            spa_paint_checkbox(spa_text('Authorization requires enabling (recommend false'), 'auth_enabling', false);
				spa_paint_close_fieldset();
			spa_paint_close_panel();
			do_action('sph_perm_add_auth_panel');
		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php spa_etext('Create New Authorization'); ?>" />
	</div>
	<?php spa_paint_close_tab(); ?>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}
?>