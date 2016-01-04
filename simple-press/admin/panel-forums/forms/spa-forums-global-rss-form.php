<?php
/*
Simple:Press
Admin Forums Global RSS Settings Form
$LastChangedDate: 2014-09-15 19:29:25 -0700 (Mon, 15 Sep 2014) $
$Rev: 11975 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# function to display the add global permission set form. It is hidden until user clicks the add global permission set link
function spa_forums_global_rss_form() {

?>
<script type="text/javascript">
    jQuery(document).ready(function() {
    	spjAjaxForm('sfnewglobalrss', 'sfreloadfd');
    });
</script>
<?php
	spa_paint_options_init();

	$ahahURL = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah').'&amp;saveform=globalrss';
?>
	<form action="<?php echo $ahahURL; ?>" method="post" id="sfnewglobalrss" name="sfnewglobalrss">
<?php
		echo sp_create_nonce('forum-adminform_globalrss');
		spa_paint_open_tab(spa_text('Forums').' - '.spa_text('Global RSS Settings'), true);
			spa_paint_open_panel();
			spa_paint_open_fieldset(spa_text('Globally Enable/Disable RSS Feeds'), true, 'global-rss');

				spa_paint_input(spa_text('Replacement external RSS URL for all RSS').'<br />'.spa_text('Default').': <strong>'.sp_build_url('', '', 0, 0, 0, 1).'</strong>', 'sfallrssurl', sp_get_option('sfallRSSurl'));

				$base = SFHOMEURL.'index.php?sp_ahah=forums-loader&amp;sfnonce='.wp_create_nonce('forum-ahah');
				$target = 'sfallrss';
				$image = SFADMINIMAGES;

				$rss_count = spdb_count(SFFORUMS, 'forum_rss_private=0');
				echo spa_text('Enabled Forum RSS feeds').': '.$rss_count.'&nbsp;&nbsp;&nbsp;&nbsp;';
				$rss_count = spdb_count(SFFORUMS, 'forum_rss_private=1');
				echo spa_text('Disabled Forum RSS feeds').': '.$rss_count.'<hr />';
?>
				<input type="button" class="button-secondary" value="<?php echo spa_text('Disable All RSS Feeds'); ?>" onclick="spjLoadForm('globalrssset', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '1', '1');" />
				<input type="button" class="button-secondary" value="<?php echo spa_text('Enable All RSS Feeds'); ?>" onclick="spjLoadForm('globalrssset', '<?php echo $base; ?>', '<?php echo $target; ?>', '<?php echo $image; ?>', '0', '1');" />

				<div class="sfinline-form">  <!-- This row will hold ahah forms for the all rss -->
				    <div id="sfallrss"></div>
				</div>
<?php
			spa_paint_close_fieldset();
			spa_paint_close_panel();
			do_action('sph_forums_global_rss_panel');
		spa_paint_close_container();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php spa_etext('Update Global RSS Settings'); ?>" />
		</div>
	<?php spa_paint_close_tab(); ?>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}
?>