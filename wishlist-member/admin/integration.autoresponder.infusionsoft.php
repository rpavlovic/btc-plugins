<?php
/*
 * Infusionsoft Autoresponder API
 * Original Author : Fel Jun Palawan
 * Version: $Id: integration.autoresponder.infusionsoft.php 2462 2014-11-17 17:49:17Z mike $
 */

/*
  GENERAL PROGRAM NOTES: (This script was based on Mike's Autoresponder integrations.)
  Purpose: This is the UI part of the code. This is displayed as the admin area for Infusionsoft Integration in WLM Dashboard.
  Location: admin/
  Calling program : integration.autoresponder.php
  Logic Flow:
  1. integration.autoresponder.php displays this script (integration.autoresponder.infusionsoft.php)
  and displays current or default settings
  2. on user update, this script submits value to integration.autoresponder.php, which in turn save the value
  3. after saving the values, integration.autoresponder.php call this script again with $wpm_levels contains the membership levels and $data contains the Infusionsoft Integration settings for each membership level.
 */

$__index__ = 'infusionsoft';
$__ar_options__[$__index__] = 'Infusionsoft';
$__ar_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'ar', $__index__ );

require_once($this->pluginDir . '/lib/integration.autoresponder.infusionsoft.php');

if ($data['ARProvider'] == $__index__):

		if (wlm_arrval($_POST,'update_ifauto')) {

			$tagsSelections = array();
			foreach ((array) $wpm_levels AS $sku => $level){
				$n = 'auto_istag_add_app'.$sku;
				if(isset($_POST[$n])){
					$tagsSelections[$sku] = $_POST[$n];
				}
			}
			$istags = maybe_serialize($tagsSelections);
			$this->SaveOption('auto_istags_add_app',$istags);

			$tagsSelections = array();
			foreach ((array) $wpm_levels AS $sku => $level){
				$n = 'auto_istag_add_rem'.$sku;
				if(isset($_POST[$n])){
					$tagsSelections[$sku] = $_POST[$n];
				}
			}
			$istags = maybe_serialize($tagsSelections);
			$this->SaveOption('auto_istags_add_rem',$istags);

			$tagsSelections = array();
			foreach ((array) $wpm_levels AS $sku => $level){
				$n = 'auto_istag_remove_app'.$sku;
				if(isset($_POST[$n])){
					$tagsSelections[$sku] = $_POST[$n];
				}
			}
			$istags = maybe_serialize($tagsSelections);
			$this->SaveOption('auto_istags_remove_app',$istags);

			$tagsSelections = array();
			foreach ((array) $wpm_levels AS $sku => $level){
				$n = 'auto_istag_remove_rem'.$sku;
				if(isset($_POST[$n])){
					$tagsSelections[$sku] = $_POST[$n];
				}
			}
			$istags = maybe_serialize($tagsSelections);
			$this->SaveOption('auto_istags_remove_rem',$istags);

			$tagsSelections = array();
			foreach ((array) $wpm_levels AS $sku => $level){
				$n = 'auto_istag_cancelled_app'.$sku;
				if(isset($_POST[$n])){
					$tagsSelections[$sku] = $_POST[$n];
				}
			}
			$istags = maybe_serialize($tagsSelections);
			$this->SaveOption('auto_istags_cancelled_app',$istags);

			$tagsSelections = array();
			foreach ((array) $wpm_levels AS $sku => $level){
				$n = 'auto_istag_cancelled_rem'.$sku;
				if(isset($_POST[$n])){
					$tagsSelections[$sku] = $_POST[$n];
				}
			}
			$istags = maybe_serialize($tagsSelections);
			$this->SaveOption('auto_istags_cancelled_rem',$istags);							
		}

	$isapikey = $data[$__index__]['iskey'];
	$ismachine = $data[$__index__]['ismname'];

	$isTagsCategory = array();
	$isTags = array();
	if (class_exists('WLM_AUTORESPONDER_INFUSIONSOFT_INIT')) {
		if($isapikey && $ismachine){
			$WLM_AUTORESPONDER_INFUSIONSOFT_INIT = new WLM_AUTORESPONDER_INFUSIONSOFT_INIT;
			$isTagsCategory = $WLM_AUTORESPONDER_INFUSIONSOFT_INIT->getTagsCategory($this,$ismachine,$isapikey);
			$isTags = $WLM_AUTORESPONDER_INFUSIONSOFT_INIT->getTags($this,$ismachine,$isapikey);
			$isTagsCategory[0] = "- No Category -";
			asort($isTagsCategory);			
		}
		$this->SaveOption('auto_isapikey', $isapikey);
		$this->SaveOption('auto_ismachine', $ismachine);
	}
	$tag_placeholder = count($isTags) > 0 ? "Select tags...":"No tags available";

		$auto_istags_add_app = $this->GetOption('auto_istags_add_app');
		if($auto_istags_add_app) $auto_istags_add_app = maybe_unserialize($auto_istags_add_app);
		else $auto_istags_add_app = array();

		$auto_istags_add_rem = $this->GetOption('auto_istags_add_rem');
		if($auto_istags_add_rem) $auto_istags_add_rem = maybe_unserialize($auto_istags_add_rem);
		else $auto_istags_add_rem = array();

		$auto_istags_remove_app = $this->GetOption('auto_istags_remove_app');
		if($auto_istags_remove_app) $auto_istags_remove_app = maybe_unserialize($auto_istags_remove_app);
		else $auto_istags_remove_app = array();

		$auto_istags_remove_rem = $this->GetOption('auto_istags_remove_rem');
		if($auto_istags_remove_rem) $auto_istags_remove_rem = maybe_unserialize($auto_istags_remove_rem);
		else $auto_istags_remove_rem = array();

		$auto_istags_cancelled_app = $this->GetOption('auto_istags_cancelled_app');
		if($auto_istags_cancelled_app) $auto_istags_cancelled_app = maybe_unserialize($auto_istags_cancelled_app);
		else $auto_istags_cancelled_app = array();

		$auto_istags_cancelled_rem = $this->GetOption('auto_istags_cancelled_rem');
		if($auto_istags_cancelled_rem) $auto_istags_cancelled_rem = maybe_unserialize($auto_istags_cancelled_rem);
		else $auto_istags_cancelled_rem = array();
		

	if ($__INTERFACE__):
		?>
		<form method="post">
			<input type="hidden" name="saveAR" value="saveAR" />
			<h2 class="wlm-integration-steps"><?php _e('Step 1. Enter your Machine Name and Encrypted Key', 'wishlist-member'); ?></h2>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Machine Name: ', 'wishlist-member'); ?></th>
					<td>
						<input type="text" name="ar[ismname]" value="<?php echo $data[$__index__]['ismname']; ?>" size="60" />
						<?php echo $this->Tooltip("integration-autoresponder-infusionsoft-tooltips-machine-name"); ?>
						<br />
						<small><b><span style="background:#ffff00">machinename</span></b>.infusionsoft.com</small><br />
						<?php _e('Please note, do not include .infusionsoft.com in the Machine Name field ', 'wishlist-member'); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Encrypted Key: ', 'wishlist-member'); ?></th>
					<td>
						<input type="text" name="ar[iskey]" value="<?php echo $data[$__index__]['iskey']; ?>" size="60" />
						<?php echo $this->Tooltip("integration-autoresponder-infusionsoft-tooltips-api-key"); ?>
						<p>The Encrypted Key can be found by going to Admin &raquo; Settings &raquo; Application</p>			
					</td>
				</tr>
			</table><br />
			<h2 class="wlm-integration-steps">Step 2: Map your Membership Levels to your Lists</h2>
			<p><?php _e('Provide the Follow-Up Sequence Id a user will be added to when added to a membership level.', 'wishlist-member'); ?></p>
			<p><em>Your Follow-Up Sequences are found in Marketing &raquo; Legacy &raquo; View Follow-Up Sequences. The "Follow-Up Sequence Id" will be in the "Id" column.</em></p>
			<table class="widefat">
				<thead>
					<tr>
						<th scope="col" width="28%"><?php _e('Level', 'wishlist-member'); ?></th>
						<th scope="col" width="28%"><?php _e('Sequence Id', 'wishlist-member'); ?>
							<?php echo $this->Tooltip("integration-autoresponder-infusionsoft-tooltips-sequence-id"); ?>
						</th>
						<th class="num" style="width:22em"><?php _e('Unsubscribe if Removed from Level', 'wishlist-member'); ?></th>
						<th scope="col" style="width:20em">&nbsp;</th>			
					</tr>
				</thead>
				<tbody>
					<?php foreach ((array) $wpm_levels AS $levelid => $level): ?>
						<tr class="<?php echo ++$autoresponder_row % 2 ? 'alternate' : ''; ?>">
							<td><?php echo $level['name']; ?></td>
							<td><input type="text" name="ar[isCID][<?php echo $levelid; ?>]" value="<?php echo $data[$__index__]['isCID'][$levelid]; ?>" size="20" /></td>
							<?php $isUnsub = ($data[$__index__]['isUnsub'][$levelid] == 1 ? true : false); ?>
							<td class="num"><input type="checkbox" name="ar[isUnsub][<?php echo $levelid; ?>]" value="1" <?php echo $isUnsub ? "checked='checked'" : ""; ?> /></td>						
							<td><a class="if_edit_tag_level ifshow" href="javascript:void(0);">[+] Edit Level Tag Settings</a></td>		
						</tr>
						<tr class="<?php echo $autoresponder_row % 2 ? 'alternate' : ''; ?> hidden">
							<td style="z-index:0;overflow:visible;">
								<p><b>When Added:</b></p>
								<p>
								Apply tags:<br />
								 <select name="auto_istag_add_app<?php echo $levelid; ?>[]" data-placeholder='<?php echo $tag_placeholder; ?>' style="width:300px;" class='chzn-select' multiple="multiple" >
								<?php
									foreach($isTagsCategory as $catid=>$name){
										if(isset($isTags[$catid]) && count($isTags[$catid]) > 0){
											asort($isTags[$catid]);
											echo "<optgroup label='{$name}'>";
											foreach($isTags[$catid] as $id=>$d){
												$selected = "";
												if(isset($auto_istags_add_app[$levelid]) && in_array($d['Id'],$auto_istags_add_app[$levelid])){
													$selected = "selected='selected'";
												}
												
												echo "<option value='{$d['Id']}' {$selected}>{$d['Name']}</option>";
											}
										echo "</optgroup>";
										}
									}			
								?>
								</select>
								</p>
								<p>
								Remove tags:<br />
								<select name="auto_istag_add_rem<?php echo $levelid; ?>[]" data-placeholder='<?php echo $tag_placeholder; ?>' style="width:300px;" class='chzn-select' multiple="multiple" >
								<?php
									foreach($isTagsCategory as $catid=>$name){
										if(isset($isTags[$catid]) && count($isTags[$catid]) > 0){
											asort($isTags[$catid]);
											echo "<optgroup label='{$name}'>";
											foreach($isTags[$catid] as $id=>$d){
												$selected = "";
												if(isset($auto_istags_add_rem[$levelid]) && in_array($d['Id'],$auto_istags_add_rem[$levelid])){
													$selected = "selected='selected'";
												}
												
												echo "<option value='{$d['Id']}' {$selected}>{$d['Name']}</option>";
											}
										echo "</optgroup>";
										}
									}			
								?>
								</select>
								</p>								
							</td>
							<td style="z-index:0;overflow:visible;">
								<p><b>When Removed:</b></p>
								<p>
								Apply tags:<br />
								 <select name="auto_istag_remove_app<?php echo $levelid; ?>[]" data-placeholder='<?php echo $tag_placeholder; ?>' style="width:300px;" class='chzn-select' multiple="multiple" >
								<?php
									foreach($isTagsCategory as $catid=>$name){
										if(isset($isTags[$catid]) && count($isTags[$catid]) > 0){
											asort($isTags[$catid]);
											echo "<optgroup label='{$name}'>";
											foreach($isTags[$catid] as $id=>$d){
												$selected = "";
												if(isset($auto_istags_remove_app[$levelid]) && in_array($d['Id'],$auto_istags_remove_app[$levelid])){
													$selected = "selected='selected'";
												}
												
												echo "<option value='{$d['Id']}' {$selected}>{$d['Name']}</option>";
											}
										echo "</optgroup>";
										}
									}			
								?>
								</select>
								</p>
								<p>
								Remove tags:<br />
								<select name="auto_istag_remove_rem<?php echo $levelid; ?>[]" data-placeholder='<?php echo $tag_placeholder; ?>' style="width:300px;" class='chzn-select' multiple="multiple" >
								<?php
									foreach($isTagsCategory as $catid=>$name){
										if(isset($isTags[$catid]) && count($isTags[$catid]) > 0){
											asort($isTags[$catid]);
											echo "<optgroup label='{$name}'>";
											foreach($isTags[$catid] as $id=>$d){
												$selected = "";
												if(isset($auto_istags_remove_rem[$levelid]) && in_array($d['Id'],$auto_istags_remove_rem[$levelid])){
													$selected = "selected='selected'";
												}
												
												echo "<option value='{$d['Id']}' {$selected}>{$d['Name']}</option>";
											}
										echo "</optgroup>";
										}
									}			
								?>
								</select>
								</p>			
							</td>
							<td style="z-index:0;overflow:visible;">
								<p><b>When Cancelled:</b></p>
								<p>
								Apply tags:<br />
								 <select name="auto_istag_cancelled_app<?php echo $levelid; ?>[]" data-placeholder='<?php echo $tag_placeholder; ?>' style="width:300px;" class='chzn-select' multiple="multiple" >
								<?php
									foreach($isTagsCategory as $catid=>$name){
										if(isset($isTags[$catid]) && count($isTags[$catid]) > 0){
											asort($isTags[$catid]);
											echo "<optgroup label='{$name}'>";
											foreach($isTags[$catid] as $id=>$d){
												$selected = "";
												if(isset($auto_istags_cancelled_app[$levelid]) && in_array($d['Id'],$auto_istags_cancelled_app[$levelid])){
													$selected = "selected='selected'";
												}
												
												echo "<option value='{$d['Id']}' {$selected}>{$d['Name']}</option>";
											}
										echo "</optgroup>";
										}
									}			
								?>
								</select>
								</p>
								<p>
								Remove tags:<br />
								<select name="auto_istag_cancelled_rem<?php echo $levelid; ?>[]" data-placeholder='<?php echo $tag_placeholder; ?>' style="width:300px;" class='chzn-select' multiple="multiple" >
								<?php
									foreach($isTagsCategory as $catid=>$name){
										if(isset($isTags[$catid]) && count($isTags[$catid]) > 0){
											asort($isTags[$catid]);
											echo "<optgroup label='{$name}'>";
											foreach($isTags[$catid] as $id=>$d){
												$selected = "";
												if(isset($auto_istags_cancelled_rem[$levelid]) && in_array($d['Id'],$auto_istags_cancelled_rem[$levelid])){
													$selected = "selected='selected'";
												}
												
												echo "<option value='{$d['Id']}' {$selected}>{$d['Name']}</option>";
											}
										echo "</optgroup>";
										}
									}			
								?>
								</select>
								</p>			
							</td>
							<td scope="col" >&nbsp;</td>															
						</tr>						
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="submit">
				<input name="update_ifauto" class="button-primary" type="submit" value="<?php _e('Update Infusionsoft Settings', 'wishlist-member'); ?>" />
			</p>
		</form>
		<?php
		include_once($this->pluginDir . '/admin/tooltips/integration.autoresponder.infusionsoft.tooltips.php');
	endif;
endif;
