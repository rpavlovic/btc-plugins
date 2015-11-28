
<div id="regform-<?php echo $id ?>" class="regform">
	<div class="regform-container">
		<div class="regform-header">
			<?php if (!empty($logo)): ?>
				<img class="regform-logo" src="<?php echo $logo ?>"></img>
			<?php endif; ?>
			<h2><?php echo $heading?></h2>

			<?php if(!is_user_logged_in()): ?>
			<p>
				Existing users please <a href="" class="regform-open-login">login</a> before purchasing
			</p>
			<?php endif; ?>
			<a class="regform-close" href="javascript:void(0)">x</a>
		</div>


		<div class="regform-error">
			<p>
			<?php if (isset($_GET['status']) && $_GET['status'] == 'fail') echo __("An error has occured while processing payment, please try again", "wishlist-member") ?>
			<?php if (!empty($_GET['reason'])) echo '<br/>Reason: ' . strip_tags(wlm_arrval($_GET,'reason'))  ?>
			</p>
		</div>

		<div class="regform-new">
			<form action="<?php echo $form_action ?>" class="regform-form" method="post">
			<?php
			foreach($fields as $f) {
				switch ($f['type']) {
					case 'hidden':
						echo sprintf('<input type="hidden" name="%s" value="%s"/>%s', $f['name'], $f['value'], "\n");
						break;
					case 'text':
						echo sprintf('<div class="txt-fld two-col-input"><label for="%1$s">%2$s</label><input id=""'
						.' class="regform-%1$s %5$s" name="%1$s" type="text" placeholder="%3$s" value="%4$s" /></div>',
						$f['name'],
						$f['label'],
						$f['placeholder'],
						$f['value'],
						$f['class']);

					default:
						# code...
						break;
				}
			}
			?>


			<?php if($fields['cc_type']): ?>
			<div class="txt-fld two-col-input">
				<label for="">Card Type:</label>
					<select name="cc_type">
						<option value="Visa" selected="selected">Visa</option>
						<option value="MasterCard">MasterCard</option>
						<option value="Discover">Discover</option>
						<option value="Amex">American Express</option>
					</select>
			</div>
			<?php endif; ?>

			<?php if($fields['cc_number']): //treating card fields as special?>
			<div class="txt-fld two-col-input">
				<label for="">Card Number:</label>
				<input autocomplete="false" placeholder="●●●● ●●●● ●●●● ●●●●" class="regform-cardnumber" name="cc_number" type="text" />
			</div>
			<?php endif; ?>

			<?php if($fields['cc_expmonth'] || $fields['cc_expyear'] || $fields['cc_cvc']): ?>
			<div class="widefield">
				<?php if($fields['cc_expmonth'] || $fields['cc_expyear']): ?>
				<div class="txt-fld expires two-col-input">
					<label for="">Expires:</label>
					<input autocomplete="false" placeholder="MM" maxlength="2"  class="regform-expmonth" name="cc_expmonth" type="text" />
					<input autocomplete="false" placeholder="YY" maxlength="2"  class="regform-expyear"  name="cc_expyear" type="text" />
				</div>
				<?php endif; ?>

				<?php if($fields['cc_cvc']): ?>
				<div class="txt-fld code two-col-input">
					<label for="">Card Code:</label>
					<input autocomplete="false" maxlength="4" placeholder="CVC" id="" class="regform-cvc" name="cc_cvc" type="text" />
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="btn-fld">
				<button class="regform-button"><?php echo $panel_button_label ?><span class="regform-waiting">...</span> &nbsp;<?php echo $currency?> <?php echo $amt ?> </button>
			</div>
			</form>
		</div>

		<?php if(!is_user_logged_in()): ?>
		<div class="regform-login">
			<form method="post" action="<?php echo get_bloginfo('wpurl')?>/wp-login.php">
				<div class="txt-fld">
					<label for="">Username:</label>
					<input id="" class="regform-username" name="log" type="text" />
				</div>
				<div class="txt-fld">
					<label for="">Password:</label>
					<input id="" class="regform-password" name="pwd" type="password" />
				</div>
				<input type="hidden" name="wlm_redirect_to" value="<?php echo get_permalink()?>#regform-<?php echo $id ?>" />
				<div class="btn-fld">
					<div><a href="" class="regform-close-login">Cancel</a></div>
					<button class="regform-button"><?php echo __("Login", "wishlist-member")?></button>
				</div>
			</form>
		</div>
		<?php endif; ?>
	</div>
</div>
