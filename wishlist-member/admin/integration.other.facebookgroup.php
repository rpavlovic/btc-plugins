<?php
return; // this integration is incomplete and needs further research
/*
 * Call Loop Autoresponder Interface
 * Original Author :Andy
 * Version: $Id:  
 */

$__index__ = 'facebookgroup';

$__other_options__[$__index__] = 'Facebook Group';

$__other_affiliates__[$__index__] = '';
$__other_videotutorial__[$__index__] = '';

if ($_GET['other_integration'] == $__index__): if ($__INTERFACE__):
		if($_POST['saveFBGroupSettings'] == 'saveFBGroupSettings') {
			$this->SaveOption('fbgroup_settings', $_POST['fbgroup']);
			echo "<div class='updated fade'>" . __('<p>Your Facebook Group settings have been saved</p>', 'wishlist-member') . "</div>";
			if(isset($_POST['enableFBGroup'])) {
				$this->IntegrationActive('integration.other.facebookgroup.php', true);
			}else{
				$this->IntegrationActive('integration.other.facebookgroup.php', false);
			}
		}
		$fbgroup_settings = (array) $this->GetOption('fbgroup_settings'); 
		$fbgroup_active = $this->IntegrationActive('integration.other.facebookgroup.php');
		?>
		<h2 class="wlm-integration-steps">Facebook Group Setting</h2>
		<form method="post">
			<input type="hidden" name="saveFBGroupSettings" value="saveFBGroupSettings" />
			<p>
				<label><input type="checkbox" name="enableFBGroup" value="1" <?php echo $fbgroup_active ? 'checked="checked"' : ''; ?> /> <?php _e('Enable Facebook Group Integration', 'wishlist-member'); ?></label>
			</p>
			<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="fbAppId">App ID: </label>
							</th>
							<td>
              <input type="text" name="fbgroup[fbAppId]" id="fbAppId" value="<?php echo $fbgroup_settings['fbAppId']; ?>" class="regular-text code"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="fbSecret">App Secret: </label>
							</th>
							<td>
              <input type="text" name="fbgroup[fbSecret]" id="fbSecret" value="<?php echo $fbgroup_settings['fbSecret']; ?>" class="regular-text code"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="fbGroupID">Group ID: </label>
							</th>
							<td>
              <input type="text" name="fbgroup[fbGroupID]" id="fbGroupID" value="<?php echo $fbgroup_settings['fbGroupID']; ?>" class="regular-text code"/>
							</td>
						</tr>
					</tbody>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Update Facebook Group Settings', 'wishlist-member'); ?>" />
			</p>
		</form>
		<?php
	endif;
endif;
?>
