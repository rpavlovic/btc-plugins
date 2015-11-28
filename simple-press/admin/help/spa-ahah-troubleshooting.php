<?php
/*
Simple:Press
Help and Troubleshooting
$LastChangedDate: 2014-11-28 03:18:46 -0800 (Fri, 28 Nov 2014) $
$Rev: 12112 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ahah_support();
include_once SF_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

spa_paint_open_tab(spa_text('Help and Troubleshooting'));
	spa_paint_open_panel();
		spa_paint_open_fieldset(spa_text('Help and Troubleshooting'), false);
?>
		<div class="codex">
			<img class="spLeft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<p class="codex-head">Codex articles <br />you may find useful</p>
			<div class="clearleft"></div>

			<p class="codex-sub">Something wrong after the install?</p>
			<p>If you have some problems displaying the forums - or some of the features do not seem to be
			working then the answer is usually something simple. You can see if there is a solution in
			our <a href="http://simple-press.com/documentation/codex/faq/troubleshooting/" ><b><u>Troubleshooting FAQ</u></b></a>.</p>
			<div class="clearleft"></div>

			<p class="codex-sub">Trouble getting started?</p>
			<p>Our simple and quick <a href="http://simple-press.com/documentation/codex/getting-started/" ><b><u>Getting
			Started Guide</u></b></a> may be all you need to get your forums up and running.</p>
			<div class="clearleft"></div>

			<p class="codex-sub">How to display Simple:Press in your language</p>
			<p>Find out how to <a href="http://simple-press.com/documentation/codex/installation/installation-information/localization/" >
			<b><u>Localise your Forums</u></b></a>. Remember you will need to download and install SP theme and
			plugin language files as well as the core files.</p>
			<div class="clearleft"></div>

			<p class="codex-sub">How do I install a Simple:Press Theme?</p>
			<p>An introduction to <a href="http://simple-press.com/documentation/codex/themes/theme-basics/using-themes/" >
			<b><u>Installing and Using Themes</u></b></a>.
			<div class="clearleft"></div>

			<p class="codex-sub">How do I install a Simple:Press Plugin?</p>
			<p>An introduction to <a href="http://simple-press.com/documentation/codex/plugins/using-plugins/" >
			<b><u>Installing and Using Plugins</u></b></a>.
			<div class="clearleft"></div>

			<p class="codex-sub">Need to know How To...?</p>
			<p>Maybe the answer is in our Frequently Asked <a href="http://simple-press.com/documentation/codex/faq/how-to/" >
			<b><u>How To</u></b></a> section.</p>
		</div>
<?php
		spa_paint_close_fieldset();

		spa_paint_open_fieldset(spa_text('Premium Support'), false);
?>
		<div class="codex">
			<img class="spLeft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<p class="codex-head">Premium <br />Support</p>
			<div class="clearleft"></div>

			<p class="codex-sub">Want that extra level of support?<br /><br /></p>
			<p>Premium support gains you full access to our forums where our user-praised response times will
			help you get the best out of Simple:Press.</p>
			<p>You will also be able to access and download all of our latest Simple:Press plugins and additional
			themes as they become available.</p>
			<p>And for those who want to get into the code to perform some serious customisation - our Codex will
			provide details of the full Simple:Press API  - currently under construction.</p>
			<p>For membership details and plans please visit our <a href="http://simple-press.com/membership/" >
			<b><u>Membership</u></b></a> page.</p>
		</div>
<?php
		spa_paint_close_fieldset();

		spa_paint_open_fieldset(spa_text('Custom Themes and Plugins'), false);
?>
		<div class="codex">
			<img class="spLeft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<p class="codex-head">Customised <br />Themes and Plugins</p>
			<div class="clearleft"></div>

			<p class="codex-sub">Need something a little special?<br /><br /></p>
			<p>If you need your Simple:Press forum to integrate visually more closely with your WordPress theme - or just want to stand
			out with something unique - then we do offer a custom theme service for those who do not wish to do it themselves.</p>
			<p>For more information please visit our <a href="http://simple-press.com/custom-simplepress-themes-for-every-need/" >
			<b><u>Custom Themes</u></b></a> page.<br /><br /></p>

			<p>If you require some special features or functionality that we do not currently offer then we may be able to
			help with a customised plugin.</p>
			<p>For more information please visit our <a href="http://simple-press.com/custom-simplepress-plugin-development-services/" >
			<b><u>Custom Plugins</u></b></a> page.<br /><br /></p>
		</div>
<?php
		spa_paint_close_fieldset();

		# Open the XML file for the next sections
		$c = wp_remote_get('http://simple-press.com/downloads/simple-press/simple-press.xml');
        if (is_wp_error($c) || wp_remote_retrieve_response_code($c) != 200) {
			echo '<p>'.spa_text('Unable to communicate with Simple Press server').'</p>';
			spa_paint_close_panel();
			spa_paint_close_container();
			spa_paint_close_tab();
			die();
		}

		spa_paint_open_fieldset(spa_text('Simple:Press Free Themes'), false, '', false);
?>
		<div class="codex">
			<img class="spLeft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<p class="codex-head">Freely Available <br />Simple:Press Themes</p>
			<div class="clearleft"></div>
<?php
			$l = new SimpleXMLElement($c['body']);
			if (!empty($l)) {
				$list = $l->themes;
				$i = 0;
				foreach ($list->theme as $theme) {
					if ($theme->display == 'yes' && $theme->type == 'free') {
						echo '<p class="codex-sub">'.$theme->name.'</p>';
						echo '<p>'.$theme->description.'</p>';
						$i++;
					}
				}
			} else {
				echo '<p>'.spa_text('Unable to communicate with Simple Press server').'</p>';
				spa_paint_close_panel();
				spa_paint_close_container();
				spa_paint_close_tab();
				die();
			}
?>
		</div>
<?php
		spa_paint_close_fieldset();

		spa_paint_open_fieldset(spa_text('Simple:Press Member Themes'), false, '', false);
?>
		<div class="codex">
			<img class="spLeft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<p class="codex-head">Members Only <br />Simple:Press Themes</p>
			<div class="clearleft"></div>
<?php
			$list = $l->themes;
			$i = 0;
			foreach ($list->theme as $theme) {
				if ($theme->display == 'yes' && $theme->type == 'premium') {
					echo '<p class="codex-sub">'.$theme->name.'</p>';
					echo '<p>'.$theme->description.'</p>';
					$i++;
				}
			}
?>
		</div>
<?php
		spa_paint_close_fieldset();
	spa_paint_close_panel();

	spa_paint_open_panel();
		spa_paint_open_fieldset(spa_text('Simple:Press Plugins'), false);
?>
		<div class="codex">
			<img class="spLeft" src="<?php echo SFCOMMONIMAGES; ?>sp-small-megaphone.png" alt="" title="" />
			<p class="codex-head">Available <br />Simple:Press Plugins</p>
			<div class="clearleft"></div>
<?php
			$list = $l->plugins;
			$i = 0;
			foreach ($list->plugin as $plugin) {
				if ($plugin->display == 'yes') {
					# Split the column as necessary
					if ($i == 18) {
						echo '</div>';
						spa_paint_close_fieldset();
						spa_paint_close_panel();
						spa_paint_tab_right_cell();
						spa_paint_open_panel();
						spa_paint_open_fieldset(spa_text('Simple:Press Plugins - continued'), false);
						echo '<div class="codex">';
					}

					echo '<p class="codex-sub">'.$plugin->name.'</p>';
					echo '<p>'.$plugin->description.'</p>';
					$i++;
				}
			}
?>
		</div>
<?php
		spa_paint_close_fieldset();
	spa_paint_close_panel();
	spa_paint_close_container();
spa_paint_close_tab();

die();
?>