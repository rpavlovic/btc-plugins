<?php

$__index__ = 'maropost';
$__ar_options__[$__index__] = 'Maropost';
$__ar_affiliates__[$__index__] = 'http://wlplink.com/go/maropost';
$__ar_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'ar', $__index__ );

if ($data['ARProvider'] == $__index__):
	if ($__INTERFACE__):
		$class_file = $this->pluginDir . '/extlib/maropost/maropost.php';
		include $class_file;
		$ac = false;

		$lists = array();
		if(!empty($data['maropost']['account_id']) && !empty($data['maropost']['auth_token'])) {
			$ac = new WPMaropost($data['maropost']['account_id'], $data['maropost']['auth_token']);
			$lists = $ac->get_lists();
		}
		?>
		<form method="post">
			<input type="hidden" name="saveAR" value="saveAR" />

			<h2 class="wlm-integration-steps">Step 1: Enter your API Settings</h2>
			<p>You may obtain your Account ID and Auth Token by logging in to your MaroPost account</p>
			<ul style="list-style:disc;margin-left:3em">
				<li>Go to Account &raquo; API Documentation to get your Account ID</li>
				<li>Go to Account &raquo; Accounts Page to get your Auth Key</li>
			</ul>
			<table class="form-table">
				<tr>
					<th>Account ID</th>
					<td><input size="5" type="text" name="ar[account_id]" value="<?php echo $data['maropost']['account_id']?>"/></td>
				</tr>
				<tr>
					<th>Auth Token</th>
					<td><input size="50" type="text" name="ar[auth_token]" value="<?php echo $data['maropost']['auth_token']?>"/></td>
				</tr>


			</table>
			<p class="submit">
				<input type="submit" class="button-secondary" value="<?php _e('Update API Settings', 'wishlist-member'); ?>" />
			</p>
			<h2 class="wlm-integration-steps">Step 2: Map your Membership Levels to your Lists</h2>
			<p>Map your membership levels to your email lists by selecting a list from the dropdowns provided under the "List" column.</p>
			<table class="widefat">
				<thead>
					<th>Membership Level</th>
					<th>List</th>
					<th class="num" style="width:22em">Unsubscribe if Removed from Level</th>
				</thead>
				<?php foreach($wpm_levels as $i => $l): ?>
					<tr class="<?php echo ++$autoresponder_row % 2 ? 'alternate' : ''; ?>">
						<th scope="row"><?php echo $l['name']?></th>
						<td style="overflow:visible;">
							<select name="ar[maps][<?php echo $i?>][]" multiple class="chosen-select" style="width: 150px;" data-placeholder="Select Lists">
								<option></option>
								<?php foreach($lists as $l): ?>
								<?php $selected = in_array($l->id, $data['maropost']['maps'][$i])? 'selected="selected"' : null ?>
								<option <?php echo $selected?> value="<?php echo $l->id?>"><?php echo $l->name?></option>
							<?php endforeach; ?>
							</select>
						</td>
						<td class="num">
							<input <?php if($data['maropost'][$i]['autoremove'] == 1) echo 'checked="checked"'?> type="checkbox" name="ar[<?php echo $i?>][autoremove]" value="1"/>
						</td>
					</tr>
				<?php endforeach; ?>

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Update AutoResponder Settings', 'wishlist-member'); ?>" />
			</p>
		</form>
		<script type="text/javascript">
		jQuery(function($) {
			$('.chosen-select').chosen({disable_search: true});
		});
		</script>
		<style type="text/css">
		.chosen-container-multi .chosen-choices {
			background-color: #fff;
			  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(1%, #eeeeee), color-stop(15%, #ffffff));
			  background-image: -webkit-linear-gradient(#eeeeee 1%, #ffffff 15%);
			  background-image: -moz-linear-gradient(#eeeeee 1%, #ffffff 15%);
			  background-image: -o-linear-gradient(#eeeeee 1%, #ffffff 15%);
			  background-image: linear-gradient(#eeeeee 1%, #ffffff 15%);
		}
		</style>
		<?php
	endif;
endif;
?>

