<?php
add_thickbox(); 
$__index__ = 'ontraport';
$__ar_options__[$__index__] = 'Ontraport';
$__ar_affiliates__[$__index__] = 'http://ontraport.com/';
$__ar_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'ar', $__index__ );

if ($data['ARProvider'] == $__index__):
	if ($__INTERFACE__):
		?>

		
		<form method="post">
			<input type="hidden" name="saveAR" value="saveAR" />

			<h2 class="wlm-integration-steps">Step 1: Enter your API Settings</h2>
			<p><a class="thickbox" href="#TB_inline?width=300&height=550&inlineId=divinstructions">Click here for instructions on how to get App ID and API Key</a></p>
			<table class="form-table">
				<tr>
					<th>API ID</th>
					<td><input size="50" type="text" name="ar[app_id]" value="<?php echo $data['ontraport']['app_id']?>"/></td>
				</tr>
				<tr>
					<th>API Key</th>
					<td><input size="50" type="text" name="ar[api_key]" value="<?php echo $data['ontraport']['api_key']?>"/></td>
				</tr>
			</table>
			
			<h2 class="wlm-integration-steps">Step 2: Select Membership Levels to Enable</h2>
			<p>A contact will be added to Ontraport's contacts once a member is added to enabled levels</p>
			
			<table class="widefat">
				<thead>
					<tr>
						<th scope="col"><?php _e('Membership Level', 'wishlist-member'); ?> </th>
						<th class="num" scope="col"><?php _e("Enable", "wishlist-member"); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ((array) $wpm_levels AS $levelid => $level): ?>
						<tr class="<?php echo ++$autoresponder_row % 2 ? 'alternate' : ''; ?>">
							<th scope="row"><?php echo $level['name']; ?></th>
							<td class="num">
								<?php $checked = $data['ontraport']['addenabled'][$levelid] == 'yes' ? 'checked="checked"' : null ?>
									<input <?php echo $checked ?> type="checkbox" name="ar[addenabled][<?php echo $levelid ?>]" value="yes">
							</td>
						<?php endforeach; ?>
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Update AutoResponder Settings', 'wishlist-member'); ?>" />
			</p>
		</form>
	
<!-- ---------------------------------------------------------->
<!-- INSTRUCTIONS MODAL DIV -->
<!-- ---------------------------------------------------------->
<div id="divinstructions" style="display:none;">
			<p>Use of the Contacts API requires an API App ID and API Key. These can be generated within your Ontraport account by going to <b>Admin &raquo; API Settings and Key Manager. </b> </p> 
			<a href="https://officeautopilot.zendesk.com/attachments/token/jaequxtukdq2gu9/?name=apikeymanager.png" target="_blank" >
				<img src="https://officeautopilot.zendesk.com/attachments/token/jaequxtukdq2gu9/?name=apikeymanager.png" width="600px"> 
			</a><br><br>
			<p> Also, be sure to select the appropriate permissions for the key you are about to generate (for example, if a key does not have "add" permissions selected, it will not be able to be used to add type requests. </p>
			<a href="https://officeautopilot.zendesk.com/attachments/token/d1ksowg6erumsvf/?name=2013-01-29_1242.png" target="_blank" >
				<img src="https://officeautopilot.zendesk.com/attachments/token/d1ksowg6erumsvf/?name=2013-01-29_1242.png" width="600px"> 
			</a><br><br>
</div>

		<?php
	endif;
endif;
?>



