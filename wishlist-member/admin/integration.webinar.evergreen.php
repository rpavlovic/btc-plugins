<?php
$webinars = $webinar_settings['evergreen'];
?>
<p><?php _e('Note: Make sure to only have the First Name, Last Name and Email Address as required fields in your Webinar settings.', 'wishlist-member'); ?></p>
<h2 class="wlm-integration-steps">Map your Membership Levels to your Webinars</h2>
<p>Map your membership levels to your webinars by entering the registration URL of your webinar in the fields under the "Registration URL" column.</p>
<form method="post">
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" style="max-width:40%"><?php _e('Membership Level', 'wishlist-member'); ?></th>
				<th scope="col"><?php _e('Registration Auto Link', 'wishlist-member'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($wpm_levels AS $levelid => $level): ?>
				<tr class="<?php echo ++$webinar_row % 2 ? 'alternate' : ''; ?>">
					<th scope="row"><?php echo $level['name']; ?></th>
					<td><input style="width:100%" type="text" name="webinar[evergreen][<?php echo $levelid; ?>]" value="<?php echo $webinars[$levelid]; ?>" size="70" /></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Update Webinar Settings', 'wishlist-member'); ?>" />
	</p>
</form>

<div class="integration-links"
	data-video="<?php echo wlm_video_tutorial ( 'integration', 'wb', $webinar_provider ); ?>"
	data-affiliate="http://wlplink.com/go/evergreen">
