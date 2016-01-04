<?php


$__index__ = 'paypalpro';
$__sc_options__[$__index__] = 'PayPal Pro';
$__sc_affiliates__[$__index__] = '#';
$__sc_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'sc', $__index__ );

if (wlm_arrval($_GET, 'cart') == $__index__) {
	if (!$__INTERFACE__) {
		// BEGIN Initialization
		$paypalprothankyou = $this->GetOption('paypalprothankyou');
		if (!$paypalprothankyou) {
			$this->SaveOption('paypalprothankyou', $paypalprothankyou = $this->MakeRegURL());
		}

		// save POST URL
		if (wlm_arrval($_POST, 'paypalprothankyou')) {
			$_POST['paypalprothankyou'] = trim(wlm_arrval($_POST, 'paypalprothankyou'));
			$wpmx = trim(preg_replace('/[^A-Za-z0-9]/', '', $_POST['paypalprothankyou']));
			if ($wpmx == $_POST['paypalprothankyou']) {
				if ($this->RegURLExists($wpmx, null, 'paypalprothankyou')) {
					echo "<div class='error fade'>" . __('<p><b>Error:</b> stripe Thank You URL (' . $wpmx . ') is already in use by a Membership Level or another Shopping Cart.  Please try a different one.</p>', 'wishlist-member') . "</div>";
				} else {
					$this->SaveOption('paypalprothankyou', $paypalprothankyou = $wpmx);
					echo "<div class='updated fade'>" . __('<p>Thank You URL Changed.&nbsp; Make sure to update stripe with the same Thank You URL to make it work.</p>', 'wishlist-member') . "</div>";
				}
			} else {
				echo "<div class='error fade'>" . __('<p><b>Error:</b> Thank You URL may only contain letters and numbers.</p>', 'wishlist-member') . "</div>";
			}
		}

		if (isset($_POST['paypalprosettings'])) {
			$paypalprosettings = $_POST['paypalprosettings'];
			$this->SaveOption('paypalprosettings', $paypalprosettings);
		}

		$paypalprothankyou_url = $wpm_scregister . $paypalprothankyou;
		$paypalprosettings = $this->GetOption('paypalprosettings');
		// END Initialization
	} else {
		// START Interface
		$xposts = $this->GetPayPerPosts(array('post_title', 'post_type'));
		$post_types = get_post_types('', 'objects');

		$level_names = array();
		foreach($wpm_levels as $sku => $level) {
			$level_names[$sku] = $level['name'];
		}

		foreach ($xposts AS $post_type => $posts) {
			foreach ((array) $posts AS $post) {
				$level_names['payperpost-' . $post->ID] = $post->post_title;
			}
		}

		$currencies = array('USD', 'AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','RUB','SGD','SEK','CHF','TWD','THB','TRY');

		?>
		<style type="text/css">
		.col-edit { display: none;}
		</style>
		<form method="post" id="stripe_form">
			<h2 class="wlm-integration-steps">Step 1. Enter your API Credentials</h2>
			<p>You will find your API credentials in your PayPal account under <strong>Profile &raquo; My Selling Tools &raquo; API Access &raquo; View API Signature</strong></p>

			<h3 class="wlm-integration-steps">Live API Credentials</h3>
			<table class="form-table" style="margin-left:2em">
				<tr>
					<th>API Username</th>
					<td><input type="text" style="width: 300px" name="paypalprosettings[live][api_username]" value="<?php echo $paypalprosettings['live']['api_username'] ?>"><br/></td>
				</tr>
				<tr>
					<th>API Password</th>
					<td><input type="text" style="width: 300px" name="paypalprosettings[live][api_password]" value="<?php echo $paypalprosettings['live']['api_password']  ?>"><br/></td>
				</tr>
				<tr>
					<th>API Signature</th>
					<td><input type="text" style="width: 300px" name="paypalprosettings[live][api_signature]" value="<?php echo $paypalprosettings['live']['api_signature']  ?>"><br/></td>
				</tr>

				<tr>
					<th>Sandbox Mode</th>
					<td>
						<label><input type="checkbox" class="sandbox_mode" name="paypalprosettings[sandbox_mode]" value="1" <?php if($paypalprosettings['sandbox_mode'] == 1) echo "checked='checked'"?>> Enable</label>
					</td>
				</tr>
			</table>

			<div class="sandbox-mode">
				<h3 class="wlm-integration-steps">Sandbox API Credentials</h3>
				<table class="form-table">
					<tr>
						<th>API Username</th>
						<td><input type="text" style="width: 300px" name="paypalprosettings[sandbox][api_username]" value="<?php echo $paypalprosettings['sandbox']['api_username'] ?>"><br/></td>
					</tr>
					<tr>
						<th>API Password</th>
						<td><input type="text" style="width: 300px" name="paypalprosettings[sandbox][api_password]" value="<?php echo $paypalprosettings['sandbox']['api_password']  ?>"><br/></td>
					</tr>
					<tr>
						<th>API Signature</th>
						<td><input type="text" style="width: 300px" name="paypalprosettings[sandbox][api_signature]" value="<?php echo $paypalprosettings['sandbox']['api_signature']  ?>"><br/></td>
					</tr>
				</table>
			</div>

			<input type="submit" name="submit" value="Update API Keys" class="button-secondary"/>
			</form>

			<?php
			try {
				// test api connection?
			} catch (Exception $e) {
				if (!empty($stripeapikey)) {
					?>
					<div class="error fade"><p><strong>Unable to connect stripe api. Stripe reason:</strong> <em>"<?php echo $e->getMessage() ?>"</em></p></div>

					<?php
				}
			}
			?>
			<h2 class="wlm-integration-steps">Step 2. Setup your Products</h2>
			<br>
			<table class="widefat product-list">
				<thead>
					<tr>
						<th scope="col" width="300"><?php _e('Product Name', 'wishlist-member'); ?></th>
						<th scope="col"><?php _e('Checkout Type', 'wishlist-member'); ?></th>
						<th scope="col"><?php _e('Recurring', 'wishlist-member'); ?></th>
						<th scope="col"><?php _e('Currency', 'wishlist-member'); ?></th>
						<th scope="col"><?php _e('Amount', 'wishlist-member'); ?></th>
						<th scope="col"><?php _e('Membership Level', 'wishlist-member'); ?></th>
					</tr>
				</thead>

				<tbody>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="100">
							<em><?php _e('Loading...','wishlist-member'); ?></em>
						</td>
					</tr>
				</tfoot>
			</table>

			<p><?php _e('To add a new product, select a membership level below and click "Add New Product"','wishlist-member'); ?></p>
			<div style="float:left">
				<select name="sku" class="new-product-level">
					<optgroup label="Membership Levels">
						<?php foreach($wpm_levels as $sku => $l): ?>
						<option value="<?php echo $sku?>"><?php echo $l['name']?></option>
						<?php endforeach; ?>
					</optgroup>

					<?php foreach ($xposts AS $post_type => $posts) : ?>
					<optgroup label="<?php echo $post_types[$post_type]->labels->name; ?>">
						<?php foreach ((array) $posts AS $post): ?>
						<option value="payperpost-<?php echo $post->ID?>"><?php echo $post->post_title?></option>
						<?php endforeach; ?>
					</optgroup>
					<?php endforeach; ?>
				</select>
				<button href="<?php echo $paypalprothankyou_url?>?action=new-product" class="button-secondary new-product"><?php _e('Add New Product','wishlist-member'); ?></button>
				<span class="spinner"></span>
			</div>

			<script type="text/template" id='product-row'>
				<tr id="product-<%=obj.id%>">
					<td class="column-title col-info col-name">
						<strong><a class="row-title"><%= obj.name %></a></strong>
						<div class="row-actions">
							<span class="edit"><a href="#" rel="<%=obj.id%>" class="edit-product">Edit</a> | </span>
							<span class="delete"><a href="#" rel="<%=obj.id%>" class="delete-product">Delete</a></span>
						</div>
					</td>
					<td class="col-info col-checkout-type"><% if(obj.checkout_type == 'express-checkout') print("Express Checkout"); else if(obj.checkout_type == 'direct-charge') print ("Direct Payment"); %></td>
					<td class="col-info col-recurring"><% if(obj.recurring == 1) print("YES"); else print ("NO"); %></td>
					<td class="col-info col-currency"><%=obj.currency%></td>
					<td class="col-info col-amount"><%=obj.amount%></td>
					<td class="col-info col-sku">
						<%= obj.name %>
					</td>


					<td class="col-edit col-name">
						<input class="form-val"  size="40" type="text" name="name" value="<%= obj.name %>"/>
					</td>
					<td class="col-edit col-checkout-type">
						<select class="form-val" name="checkout_type">
							<option <% if(obj.checkout_type == 'express-checkout') print ('selected="selected"') %> value="express-checkout">Express Checkout</option>
							<option <% if(obj.checkout_type == 'direct-charge') print ('selected="selected"') %> value="direct-charge">Direct Payment</option>
						</select>
					</td>
					<td class="col-edit col-recurring">
						<input type="checkbox" class="form-val"  name="recurring" value="1" <% if(obj.recurring == 1) print('checked=checked') %>/>
					</td>
					<td class="col-edit col-currency">
						<select class="form-val" name="currency">
							<?php foreach($currencies as $c): ?>
							<option <% if(obj.currency == '<?php echo $c?>') print ('selected="selected"') %> name="<?php echo $c?>"><?php echo $c?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td class="col-edit col-amount">
						<div class="recurring">
							<table>
								<tr>
									<td>Initial Amount:</td>
									<td><input class="form-val" type="text" name="init_amount" value="<%=obj.init_amount%>"/> <br/></td>
								</tr>
								<tr>
									<td>Recurring Amount:</td>
									<td><input class="form-val" type="text" name="recur_amount" value="<%=obj.recur_amount%>"/> <br/></td>
								</tr>
								<tr>
									<td>Billing Cycle</td>
									<td>
										<select class="form-val" name="recur_billing_frequency">
										<?php for($i=0; $i<30; $i++): ?>
											<option <% if(obj.recur_billing_frequency == '<?php echo $i+1?>') print ('selected="selected"') %> value="<?php echo $i+1?>"><?php echo $i+1?></option>
										<?php endfor; ?>
										</select>

										<select class="form-val" name="recur_billing_period">
											<option <% if(obj.recur_billing_period == 'Day') print ('selected="selected"') %> value="Day">Day</option>
											<option <% if(obj.recur_billing_period == 'Week') print ('selected="selected"') %> value="Week">Week</option>
											<option <% if(obj.recur_billing_period == 'Month') print ('selected="selected"') %> value="Month">Month</option>
											<option <% if(obj.recur_billing_period == 'Year') print ('selected="selected"') %> value="Year">Year</option>
										</select>
									</td>
								</tr>


							</table>
						</div>
						<div class="onetime">
							<input class="form-val" type="text" name="amount" value="<%=obj.amount%>"/>
						</div>

					</td>
					<td class="col-edit col-sku">
						<select name="sku" class="form-val">
							<optgroup label="Membership Levels">
								<?php foreach($wpm_levels as $sku => $l): ?>
								<option <% if(obj.sku == '<?php echo $sku?>') print('selected="selected"')%> value="<?php echo $sku?>"><?php echo $l['name']?></option>
								<?php endforeach; ?>
							</optgroup>

							<?php foreach ($xposts AS $post_type => $posts) : ?>
							<optgroup label="<?php echo $post_types[$post_type]->labels->name; ?>">
								<?php foreach ((array) $posts AS $post): ?>
								<option <% if(obj.sku == 'payperpost-<?php echo $post->ID?>') print('selected="selected"')%> value="payperpost-<?php echo $post->ID?>"><?php echo $post->post_title?></option>
								<?php endforeach; ?>
							</optgroup>
							<?php endforeach; ?>
						</select>

						<hr/>
						<p class="form-actions" style="float:left">
							<input class="form-val" type="hidden" name="id" value="<%=obj.id%>"/>
							<button class="button-primary save-product">Save</button>
							<button class="button-secondary cancel-edit">Cancel</button>
							<span class="spinner"></span></div>
						</p>
					</td>

				</tr>
			</script>




		<style type="text/css">
			#logo-preview img { width: 90px; height: 40px;}
			.sandbox-mode { display: none; }
		</style>
		<script type="text/javascript">
				var level_names = JSON.parse('<?php echo json_encode($level_names)?>');
				var send_to_editor = function(html) {
					imgurl = jQuery('img', html).attr('src');
					var el = jQuery('#stripe-logo');
					el.val(imgurl);
					tb_remove();
					//also update the img preview
					jQuery('#logo-preview').html('<img src="' + imgurl + '">');
				}

				jQuery(function($) {
					$('.dropmenu').on('click', function(ev) {
						ev.preventDefault();
						$('li.dropme ul').not( $(this).parent()).hide();
						console.log($(this).parent().find('ul'));
					});

					function update_fields(el, tr) {
						if (el.val() == 1) {
							tr.find('.amount').find('input').attr('disabled', true).val('');
							tr.find('.plans').find('select').removeAttr('disabled');
						} else {
							tr.find('.plans').find('select').attr('disabled', true).val('');
							tr.find('.amount').find('input').removeAttr('disabled');
						}
					}

					function update_sandbox_tbl(cb) {
						if(cb.prop('checked')) {
							$('.sandbox-mode').show('slow');
						} else {
							$('.sandbox-mode').hide('slow');
						}

					}

					$('.sandbox_mode').on('change', function(ev) {
						update_sandbox_tbl($(this));
					});

					/** table handler **/

					var table_handler = {};


					table_handler.toggle_recurring = function(id) {
						var row = $('#product-' + id);
						var el = row.find('input[name=recurring]');
						if(el.prop('checked')) {
							row.find('.recurring').show();
							row.find('.onetime').hide();
						} else {
							row.find('.recurring').hide();
							row.find('.onetime').show();
						}
					}
					table_handler.remove_row = function(id) {
						$('#product-' + id).remove();
						self.table.find('tr').each(function(i, e) {
							$(e).removeClass('alternate');
							if(i % 2 == 0) {
								$(e).addClass('alternate');
							}
						});
						table_handler.toggle_header();
					}

					table_handler.render_row = function(obj) {
						var cnt      = self.table.find('tr').length;
						var template = $("#product-row").html();
						var str      = _.template(template, {'obj': obj} );
						var el       = $('#product-' + obj.id);


						if(el.length > 0) {
							el.replaceWith(str);
						} else {
							self.table.find('tbody').eq(0).append(str);
						}

						table_handler.toggle_recurring(obj.id);

						if(cnt % 2 == 0) {
							self.table.find('tr').eq(cnt).addClass('alternate');
						}

						table_handler.toggle_header();
					}
					table_handler.end_edit = function(id) {
						$('#product-' + id).find('td.col-info').show();
						$('#product-' + id).find('td.col-edit').hide();
					}
					table_handler.edit_row = function(id) {
						$('#product-' + id).find('td.col-info').hide();
						$('#product-' + id).find('td.col-edit').show();
					}
					table_handler.fetch = function() {
						$.post(ajaxurl + '?action=wlm_pp_all-products', {}, function(res) {
							var obj = JSON.parse(res);
							for(i in obj) {
								table_handler.render_row(obj[i]);
							}
							self.table.find('tfoot td').html('<em>No products to display. Please add a product below.</em>')
						});
					}
					table_handler.edit_product = function(id) {
						table_handler.edit_row(id);
					}
					table_handler.delete_product = function(id) {
						$('#product-' + id + ' td').toggleClass('wlm-ajax-red', true, 1000);
						$.post(ajaxurl + '?action=wlm_pp_delete-product', {id: id}, function(res) {
							table_handler.remove_row(id);
						});
					}
					table_handler.save_product = function(id) {
						var row = $('#product-' + id);
						row.find('.spinner').show();

						var data = {};
						row.find('.form-val').each(function(i, e) {
							var el = $(e);
							data[el.prop('name')] = $(el).is(':checkbox')?  ( $(el).is(':checked')? 1 : 0 )  : el.val();
						});


						$.post(ajaxurl + '?action=wlm_pp_save-product', data, function(res) {
							row.find('.spinner').hide();
							var obj = JSON.parse(res);
							table_handler.render_row(obj);
							table_handler.end_edit(id);
							$('#product-'+obj.id).toggleClass('wlm-ajax-green', true, 100);
							$('#product-'+obj.id).toggleClass('wlm-ajax-green', false, 3000);
						});


					}
					table_handler.new_product = function() {
						var data = {
							'name' : $('.new-product-level option:selected').html(),
							'sku'  : $('.new-product-level').val()
						};
						$('.new-product').next('.spinner').show();
						$('.new-product').attr('disabled','disabled');
						$.post(ajaxurl + '?action=wlm_pp_new-product', data, function(res) {
							var obj = JSON.parse(res);
							var template = $("#product-row").html();
							table_handler.render_row(obj);
							$('.new-product').next('.spinner').hide();
							$('.new-product').removeAttr('disabled');
							$('#product-'+obj.id).toggleClass('wlm-ajax-green', true, 100);
							$('#product-'+obj.id).toggleClass('wlm-ajax-green', false, 3000);
							table_handler.edit_product(obj.id);
						});
					}
					table_handler.init = function(table) {
						self.table = table;

						$('.new-product').on('click', function(ev) {
							ev.preventDefault();
							table_handler.new_product();
						});

						$('.product-list').on('click', '.delete-product', function(ev) {
							ev.preventDefault();
							table_handler.delete_product( $(this).attr('rel'));
						});

						$('.product-list').on('click', '.edit-product', function(ev) {
							ev.preventDefault();
							table_handler.edit_product( $(this).attr('rel'));
						});

						$('.product-list').on('click', '.save-product', function(ev) {
							ev.preventDefault();
							var id = $(this).parent().find('input[name=id]').val();
							table_handler.save_product(id);
						});

						$('.product-list').on('click', '.cancel-edit', function(ev) {
							ev.preventDefault();
							var id = $(this).parent().find('input[name=id]').val();
							table_handler.end_edit(id);
						});

						$('.product-list').on('change', '.col-recurring input', function(ev) {
							ev.preventDefault();
							var id = $(this).parent().parent().find('input[name=id]').val();
							table_handler.toggle_recurring(id);
						});

						table_handler.fetch();

						table_handler.toggle_header();
					}

					table_handler.toggle_header = function() {
						if(self.table.find('tbody tr').length < 1) {
							self.table.find('thead').hide();
							self.table.find('tfoot').show();
						} else {
							self.table.find('thead').show();
							self.table.find('tfoot').hide();
						}
					}

					table_handler.init($('.product-list'));



					/* end table handler **/


					update_sandbox_tbl($('.sandbox_mode'));
				});
		</script>
		<?php
		include_once($this->pluginDir . '/admin/tooltips/integration.shoppingcart.stripe.tooltips.php');
		// END Interface
	}
}
?>
