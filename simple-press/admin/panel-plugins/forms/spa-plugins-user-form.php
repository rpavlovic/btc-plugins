<?php
/*
Simple:Press
Admin plugins user form
$LastChangedDate: 2015-01-21 14:28:47 -0800 (Wed, 21 Jan 2015) $
$Rev: 12383 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_plugins_user_form($admin, $save, $form, $reload) {
	global $spAPage;

    if ($form) {
?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
            	jQuery('#sfpluginsuser').ajaxForm({
            		target: '#sfmsgspot',
            		success: function() {
            			<?php if (!empty($reload)) echo "jQuery('#".$reload."').click();"; ?>
            			jQuery('#sfmsgspot').fadeIn();
            			jQuery('#sfmsgspot').fadeOut(6000);
            		}
            	});
            });
        </script>
<?php
    	spa_paint_options_init();
        $ahahURL = SFHOMEURL.'index.php?sp_ahah=plugins-loader&amp;sfnonce='.wp_create_nonce('forum-ahah')."&amp;saveform=plugin&amp;func=$save";
    	echo '<form action="'.$ahahURL.'" method="post" id="sfpluginsuser" name="sfpluginsuser">';
    	echo sp_create_nonce('forum-adminform_userplugin');
    }

    call_user_func($admin);

    if ($form) {
?>
    	<div class="sfform-submit-bar">
    	   <input type="submit" class="button-primary" value="<?php spa_etext('Update'); ?>" />
    	</div>
        <?php spa_paint_close_tab(); ?>
        </form>

    	<div class="sfform-panel-spacer"></div>
<?php
    }
}
?>