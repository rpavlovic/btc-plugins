<?php
/*
 * arpReach Autoresponder Interface
 * Original Author : Fel Jun Palawan
 * Version: $Id: integration.autoresponder.arpreach.php 2306 2014-08-27 22:05:26Z mike $
 */

$__index__ = 'arpreach';

$__ar_options__[$__index__] = 'arpReach';

$__ar_affiliates__[$__index__] = 'http://www.arpreach.com/';
$__ar_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'ar', $__index__ );

if ($data['ARProvider'] == $__index__):
	if ($__INTERFACE__):
		if (function_exists('curl_init')):
			?>
			<form method="post">
				<input type="hidden" name="saveAR" value="saveAR" />
				<h2 class="wlm-integration-steps">Step 1: Copy the Post URL for your List</h2>
				<p><?php _e('<b>arpReach</b> uses the Subscription Form Post URL for its integrations. To get/create one, please follow the steps below:', 'wishlist-member'); ?></p>
				<ol style="margin-left:3em">
					<li><?php _e('Log in to your arpReach account and go to "Autoresponder" &raquo; "Show List". <i>If you have no autoresponder yet, create one.</i>', 'wishlist-member'); ?></li>
					<li><?php _e('Under the "Actions" column of your autoresponder, choose "Subscription forms".', 'wishlist-member'); ?></li>
					<li><?php _e('Create a new subscription form. Make sure to set "Form Type" to "Offer subscribe/unsubscribe" under "Content" &raquo; "Display Options".', 'wishlist-member'); ?></li>
					<li><?php _e('Once you have a subscription form, choose "Get form code" under the "Action" column.', 'wishlist-member'); ?></li>
					<li><?php _e('Once you see the form code, copy the Post URL in the third line that looks like the highlighted text below:', 'wishlist-member'); ?>
						<br /><strong>...form method='post' action='<span style="background:yellow;">http://yourdomain.com/arpreach_folder/a.php/sub/1/5bylw9</span>'....</strong></li>
				</ol>
				<h2 class="wlm-integration-steps">Step 2: Map your Membership Levels to your Lists</h2>
				<p>Paste the Post URL for each corresponding Membership Level below</p>
				<br>
				<table class="widefat">
					<thead>
						<tr>
							<th scope="col"><?php _e('Membership Level', 'wishlist-member'); ?></th>
							<th scope="col"><?php _e('Autoresponder Subscription Form Post URL', 'wishlist-member'); ?>
								<?php echo $this->Tooltip("integration-autoresponder-arpreach-url"); ?>
							</th>
							<th class="num" style="width:22em"><?php _e('Unsubscribe if Removed from Level', 'wishlist-member'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ((array) $wpm_levels AS $levelid => $level): ?>
							<tr class="<?php echo ++$autoresponder_row % 2 ? 'alternate' : ''; ?>">
								<th scope="row"><?php echo $level['name']; ?></th>
								<td><input type="text" name="ar[postURL][<?php echo $levelid; ?>]" value="<?php echo $data[$__index__]['postURL'][$levelid]; ?>" style="width:100%" /></td>
								<?php $arUnsub = ($data[$__index__]['arUnsub'][$levelid] == 1 ? true : false); ?>
								<td class="num"><input type="checkbox" name="ar[arUnsub][<?php echo $levelid; ?>]" value="1" <?php echo $arUnsub ? "checked='checked'" : ""; ?> /></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Update AutoResponder Settings', 'wishlist-member'); ?>" />
				</p>
			</form>
			<?php
			include_once($this->pluginDir . '/admin/tooltips/integration.autoresponder.arpreach.tooltips.php');
		else:
			?>
			<p><?php _e('arpReach requires PHP to have the CURL extension enabled.  Please contact your system administrator.', 'wishlist-member'); ?></p>
		<?php
		endif;
	endif;
endif;
?>
