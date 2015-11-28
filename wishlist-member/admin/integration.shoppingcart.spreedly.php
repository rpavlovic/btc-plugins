<?php
/*
 * Pin Payments Shopping Cart Integration (Formerly known as Spreedly)
 * Original Author : Fel Jun Palawan
 * Version: $Id: integration.shoppingcart.spreedly.php 2389 2014-10-22 15:59:18Z mike $
 */

$__index__ = 'spreedly';
$__sc_options__[$__index__] = 'Pin Payments';
$__sc_affiliates__[$__index__] = '#';
$__sc_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'sc', $__index__ );

if (wlm_arrval($_GET, 'cart') == $__index__) {
	include_once($x = $this->pluginDir . '/extlib/class.spreedly.inc');
	if (!$__INTERFACE__) {
		// BEGIN Initialization
		$spreedlythankyou = $this->GetOption('spreedlythankyou');
		if (!$spreedlythankyou) {
			$this->SaveOption('spreedlythankyou', $spreedlythankyou = $this->MakeRegURL());
		}

		// save POST URL
		if (wlm_arrval($_POST, 'spreedlythankyou')) {
			$_POST['spreedlythankyou'] = trim(wlm_arrval($_POST, 'spreedlythankyou'));
			$wpmx = trim(preg_replace('/[^A-Za-z0-9]/', '', $_POST['spreedlythankyou']));
			if ($wpmx == $_POST['spreedlythankyou']) {
				if ($this->RegURLExists($wpmx, null, 'spreedlythankyou')) {
					echo "<div class='error fade'>" . __('<p><b>Error:</b> Pin Payments Thank You URL (' . $wpmx . ') is already in use by a Membership Level or another Shopping Cart.  Please try a different one.</p>', 'wishlist-member') . "</div>";
				} else {
					$this->SaveOption('spreedlythankyou', $spreedlythankyou = $wpmx);
					echo "<div class='updated fade'>" . __('<p>Thank You URL Changed.&nbsp; Make sure to update Pin Payments with the same Thank You URL to make it work.</p>', 'wishlist-member') . "</div>";
				}
			} else {
				echo "<div class='error fade'>" . __('<p><b>Error:</b> Thank You URL may only contain letters and numbers.</p>', 'wishlist-member') . "</div>";
			}
		}
		// save API Key
		if (wlm_arrval($_POST, 'spreedlytoken')) {
			$_POST['spreedlytoken'] = trim(wlm_arrval($_POST, 'spreedlytoken'));
			$wpmy = trim(preg_replace('/[^A-Za-z0-9]/', '', $_POST['spreedlytoken']));
			if ($wpmy == $_POST['spreedlytoken']) {
				$this->SaveOption('spreedlytoken', $spreedlytoken = $wpmy);
				echo "<div class='updated fade'>" . __('<p>API Token Changed.&nbsp; Make sure that your API Token matches the one specified in your Pin Payments site configuration.</p>', 'wishlist-member') . "</div>";
			} else {
				echo "<div class='error fade'>" . __('<p><b>Error:</b> Pin Payments API Token may only contain letters and numbers.</p>', 'wishlist-member') . "</div>";
			}
		}
		// save Machine Name
		if (wlm_arrval($_POST, 'spreedlyname')) {
			$_POST['spreedlyname'] = trim(wlm_arrval($_POST, 'spreedlyname'));
			$this->SaveOption('spreedlyname', $_POST['spreedlyname']);
			echo "<div class='updated fade'>" . __('<p>Site Name Changed.</p>', 'wishlist-member') . "</div>";
		}

		$spreedlythankyou_url = $wpm_scregister . $spreedlythankyou;
		$spreedlytoken = $this->GetOption('spreedlytoken');
		$spreedlyname = $this->GetOption('spreedlyname');

		$for_approval_registration = $this->GetOption('wlm_for_approval_registration');
		if (!$for_approval_registration) {
			$for_approval_registration = array();
		} else {
			$for_approval_registration = unserialize($for_approval_registration);
		}
		$regurl = WLMREGISTERURL;

		// END Initialization
	} else {
		// START Interface
		$r = array();
		if ($spreedlytoken && $spreedlyname) {
			Spreedly::configure($spreedlyname, $spreedlytoken);
			$r = SpreedlySubscriptionPlan::get_all();
			if (isset($r['ErrorCode'])) {
				if ($r['ErrorCode'] == '401') {
					echo "<div class='error fade'>" . __('<p>Invalid Pin Payments API Credentials.</p>', 'wishlist-member') . "</div>";
				} else {
					echo "<div class='error fade'>" . __("<p>{$r['Response']}</p>", 'wishlist-member') . "</div>";
				}
			}
		}
		?>
		<!-- Spreedly -->
		<p><?php _e('* Pin Payments is formerly known as Spreedly','wishlist-member'); ?></p>
		<blockquote>
			<h2 class="wlm-integration-steps"><?php _e('Step 1. Provide the API Credentials', 'wishlist-member'); ?></h2>
			<blockquote>
				<form method="post">
					<table class="form-table">
						<tr>
							<th scope="row">Short Site Name:</th>
							<td>
								<input type="text" name="spreedlyname" value="<?php echo $spreedlyname ?>" size="40" />
								<?php echo $this->Tooltip("integration-shoppingcart-spreedly-site-name"); ?>
								<br /><span class="small">Can be found in your <strong>Pin Payments Site Configuration &raquo; Short Site Name</strong></span>
							</td>
						</tr>
						<tr>
							<th scope="row">API Authentication Token:</th>
							<td>
								<input type="text" name="spreedlytoken" value="<?php echo $spreedlytoken ?>" size="40" />
								<?php echo $this->Tooltip("integration-shoppingcart-spreedly-token"); ?>
								<br /><span class="small">Can be found in your <strong>Pin Payments Site Configuration &raquo; API Authentication Token</strong></span>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" id="updatecredentials" value="<?php _e('Update API Credentials', 'wishlist-member'); ?>" />
					</p>
				</form>
			</blockquote>
			<h2 class="wlm-integration-steps"><?php _e('Step 2.Set the "Subscribers Changed Notification URL" to the link below:', 'wishlist-member'); ?></h2>
			<p>Can be found in your <strong>Pin Payments Site Configuration &raquo; Subscribers Changed Notification URL</strong></p>
			<p>&nbsp;<a href="<?php echo $spreedlythankyou_url ?>" onclick="return false"><?php echo $spreedlythankyou_url ?></a></p>
			<form method="post">
				<h2 class="wlm-integration-steps"><?php _e('Step 3. Create a Plan for your Membership Level using the details below.', 'wishlist-member'); ?></h2>
				<p>After you created your Plan, reload the page or click the button at the bottom to generate the subscription link in Step 4.</p>
				<blockquote>
					<table class="widefat">
						<thead>
							<tr>
								<th scope="col" width="200"><?php _e('Membership Level', 'wishlist-member'); ?></th>
								<th scope="col"><?php _e('Feature Level', 'wishlist-member'); ?></th>
								<th scope="col"><?php _e('URL a customer is returned to on sale', 'wishlist-member'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$alt = 0;
							foreach ((array) $wpm_levels AS $sku => $level):
								?>
								<tr class="<?php echo $alt++ % 2 ? '' : 'alternate'; ?>" id="wpm_level_row_<?php echo $sku ?>">
									<td><b><?php echo $level['name'] ?></b></td>
									<td><u style="font-size:1.2em"><?php echo $sku ?></u></td>
							<td><u><?php echo $spreedlythankyou_url . "?sku=" . $sku ?></u></td>
							</tr>
		<?php endforeach; ?>
						</tbody>
					</table>
				</blockquote>
				<h2 class="wlm-integration-steps"><?php _e('Step 4. Copy and Paste the Subscription Link below.', 'wishlist-member'); ?></h2>
				<p>Paste the Subscription Link in your Sales page.</p>
				<blockquote>
					<table class="widefat">
						<thead>
							<tr>
								<th scope="col" width="200"><?php _e('Membership Level', 'wishlist-member'); ?></th>
								<th scope="col" class="center"><?php _e('Subscription Link', 'wishlist-member'); ?> <?php echo $this->Tooltip("integration-shoppingcart-spreedly-sub-link"); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$alt = 0;
							$subscription_count = 0;
							foreach ((array) $r AS $id => $data):
								?>
								<?php
								$return_url = $spreedlythankyou_url . "?sku=" . $data->feature_level;
								if (array_key_exists($data->feature_level, $wpm_levels) && $return_url == trim($data->return_url)):
									$subscription_count+=1;
									?>
									<tr class="<?php echo $alt++ % 2 ? '' : 'alternate'; ?>">
										<td><b><?php echo $wpm_levels[$data->feature_level]['name'] ?></b></td>
										<td >
											<?php
											$for_approval_registration[$data->id] = array("level" => $data->feature_level, "name" => "Spreedly");
											echo $regurl . "/" . $data->id;
											?>
										</td>
									</tr>
								<?php else: unset($for_approval_registration[$data->id]); ?>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php
							//save the for_approval_registration
							$this->SaveOption('wlm_for_approval_registration', serialize($for_approval_registration));
							?>
		<?php if (!$spreedlytoken || !$spreedlyname): ?>
								<tr >
									<td colspan="2" style="text-align:center;"><p>Please provide your API Details in Step 1.</p></td>
								</tr>
		<?php elseif (isset($r['ErrorCode'])): ?>
								<tr >
									<td colspan="2" ><p style="text-align:center;color:red;">You have an invalid Pin Payments API credentials</p></td>
								</tr>
		<?php elseif ($subscription_count <= 0): ?>
								<tr >
									<td colspan="2" style="text-align:center;"><p>Please create a Plan using the data above and click the "Refresh Subscription Link" button.</p></td>
								</tr>
		<?php endif; ?>
						</tbody>
					</table>
					<br /><span>Note: <span style="font-style:italic;">You need to setup your Plan in Step 3 first for the subscription link to appear. Click the button below to reload the subscription link.</span><span>
							</blockquote>
							<p class="submit">
								<input type="submit" class="button-secondary" id="refresh" value="<?php _e('Refresh Subscription Link', 'wishlist-member'); ?>" />
							</p>
							</form>
							</blockquote>
							<?php
							include_once($this->pluginDir . '/admin/tooltips/integration.shoppingcart.spreedly.tooltips.php');
							// END Interface
						}
					}
					?>
