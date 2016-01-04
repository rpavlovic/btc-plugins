<?php
/*
 * Shopping Cart Integration
 * Original Author : Mike Lopez
 * Version: $Id: shoppingcart.php 2389 2014-10-22 15:59:18Z mike $
 */
if (!isset($_GET['cart'])) {
	$_GET['cart'] = $this->GetOption('lastcartviewed');
}
$this->SaveOption('lastcartviewed', $_GET['cart']);
$__integrations__ = glob($this->pluginDir . '/admin/integration.shoppingcart.*.php');
$__INTERFACE__ = false;
foreach ((array) $__integrations__ AS $__integration__) {
	include($__integration__);
}
?>
<form method="get">
	<table class="form-table">
		<?php
		parse_str($this->QueryString('cart'), $fields);
		foreach ((array) $fields AS $field => $value) {
			echo "<input type='hidden' name='{$field}' value='{$value}' />";
		}
		?>
		<tr>
			<td scope="row" colspan="3" style="padding-left:0">
				<p><?php _e('Integrate 3rd party shopping carts with WishList Member.', 'wishlist-member'); ?></p>
			</td>
			<td>
				<?php if (!empty($__sc_videotutorial__[wlm_arrval($_GET,'cart')])): ?>
					<p class="alignright" style="margin-top:0"><a href="<?php echo $__sc_videotutorial__[wlm_arrval($_GET,'cart')]; ?>" target="_blank"><?php _e('Watch Integration Video Tutorial', 'wishlist-member'); ?></a></p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Select System', 'wishlist-member'); ?></th>
			<td width="1" style="white-space:nowrap">
				<select name="cart">
					<option value=""><?php _e('None', 'wishlist-member'); ?></option>
					<?php
					// sort by Name
					natcasesort($__sc_options__);

					// Generic integration always goes last
					if (isset($__sc_options__['generic'])) {
						$x = $__sc_options__['generic'];
						unset($__sc_options__['generic']);
						$__sc_options__['generic'] = $x;
					}

					// display dropdown options
					$provider_name = '';
					foreach ((array) $__sc_options__ AS $key => $value) {
						$selected = (wlm_arrval($_GET,'cart') == $key) ? ' selected="true" ' : '';
						echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
						if($selected) $provider_name = $value;
					}
					?>
				</select> <?php echo $this->Tooltip("shoppingcart-tooltips-Select-shoppingcart-System"); ?>

			</td>
			<td>
				<p class="submit" style="margin:0;padding:0"><input type="submit" class="button-secondary" value="<?php _e('Select Shopping Cart', 'wishlist-member'); ?>" /></p>
			</td>
			<td style="text-align:right">
				<?php if (isset($__sc_affiliates__[wlm_arrval($_GET,'cart')])): ?>
					<a href="<?php echo $__sc_affiliates__[wlm_arrval($_GET,'cart')]; ?>" target="_blank"><?php printf(__('Learn more about %1$s', 'wishlist-member'), $__sc_options__[wlm_arrval($_GET,'cart')]); ?></a>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</form>
<hr />
<blockquote>
	<?php
	$__INTERFACE__ = true;
	foreach ((array) $__integrations__ AS $__integration__) {
		include($__integration__);
	}

	if (!isset($__sc_options__[wlm_arrval($_GET,'cart')])) {
		echo '<p>';
		_e('Please select a shopping cart to configure from the dropdown list above.', 'wishlist-member');
		echo '</p>';
	}
	?>
</blockquote>

<?php
include_once($this->pluginDir . '/admin/tooltips/shoppingcart.tooltips.php');
?>
