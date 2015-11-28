<?php
/*
 * SamCart Shopping Cart Integration
 * Original Author : Mike Lopez
 * Version: $Id: integration.shoppingcart.samcart.php 2389 2014-10-22 15:59:18Z mike $
 */

$__index__ = 'samcart';
$__sc_options__[$__index__] = 'SamCart';
$__sc_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'sc', $__index__ );

if (wlm_arrval($_GET,'cart') == $__index__) {
	if ($__INTERFACE__) {
		// START Interface
		?>
		<h2 class="wlm-integration-steps"><?php _e('API Credentials','wishlist-member'); ?></h2>
		<p>
			<?php _e('Copy and paste the information below to your SamCart account under<br><strong>Settings &raquo; Membership Portal Integration &raquo; WishList Member</strong>','wishlist-member'); ?>
		</p>
		<h3>Blog URL</h3>
		<blockquote>
		<input type="text" value="<?php echo admin_url(); ?>" size="60" readonly="readonly" onclick="this.select()" />
		</blockquote>

		<h3>API Key</h3>
		<blockquote>
		<input type="text" name="<?php $this->Option('WLMAPIKey'); ?>" value="<?php $this->OptionValue(false, md5(microtime())); ?>" size="60" readonly="readonly" onclick="this.select()" />
		<p><em>Note: You may change your WishList Member API Key by going to Settings &raquo; Configuration &raquo; Miscellaneous</em></p>
		</blockquote>

		<?php
	}
}
?>
