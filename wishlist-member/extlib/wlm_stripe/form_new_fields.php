<?php
$fields = array(
	'nonce' => array(
		'type'  => 'hidden',
		'name'  => 'nonce',
		'label' => '',
		'value' => wp_create_nonce('stripe-do-charge'),
		'class' => ''
	),
	'stripe_action' => array(
		'type'  => 'hidden',
		'name'  => 'stripe_action',
		'label' => '',
		'value' => 'charge',
		'class' => ''
	),
	'charge_type' => array(
		'type'  => 'hidden',
		'name'  => 'charge_type',
		'label' => '',
		'value' => 'new',
		'class' => '',
	),
	'subscription' => array(
		'type'  => 'hidden',
		'name'  => 'subscription',
		'label' => '',
		'value' => $settings['subscription'],
		'class' => ''
	),
	'redirect_to' => array(
		'type'  => 'hidden',
		'name'  => 'redirect_to',
		'label' => '',
		'value' => get_permalink(),
		'class' => ''
	),
	'sku' => array(
		'type'  => 'hidden',
		'name'  => 'sku',
		'label' => '',
		'value' => $sku,
		'class' => ''
	),
	//name fields
	'first_name' => array(
		'type'        => 'text',
		'name'        => 'first_name',
		'label'       => 'First Name',
		'placeholder' => "First Name",
		'value'       => $current_user->first_name,
	),
	'last_name' => array(
		'type'        => 'text',
		'name'        => 'last_name',
		'label'       => 'Last Name',
		'placeholder' => "Last Name",
		'value'       => $current_user->last_name,
	),
	'email' => array(
		'type'        => 'text',
		'name'        => 'email',
		'label'       => 'Email',
		'placeholder' => "Email",
		'value'       => $current_user->user_email,
	),
	'coupon' => array(
		'type'        => 'text',
		'name'        => 'coupon',
		'label'       => 'Coupon Code',
		'placeholder' => "Coupon Code",
		'class'       => 'stripe-coupon',
		'value'       => "",
	),
	//card fields
	'cc_number' => array(
		'type'        => 'card',
		'name'        => 'cc_number',
		'label'       => __('Card Number:', "wishlist-member"),
		'placeholder' => "●●●● ●●●● ●●●● ●●●●",
		'value'       => "",
	),
	'cc_expmonth' => array(
		'type'        => 'card',
		'name'        => 'cc_expmonth',
		'label'       => __('Expires:', "wishlist-member"),
		'placeholder' => "",
		'value'       => "",
	),
	'cc_expmonth' => array(
		'type'        => 'card',
		'name'        => 'cc_expyear',
		'label'       => __('Expires:', "wishlist-member"),
		'placeholder' => "",
		'value'       => "",
	),
	'cc_cvc' => array(
		'type'        => 'card',
		'name'        => 'cc_cvc',
		'label'       => __('Code:', "wishlist-member"),
		'placeholder' => "",
		'value'       => "",
	)
);

$data['fields']             = $fields;
$data['heading']            = $heading;
$data['panel_button_label'] = $panel_btn_label ." " . $currency ." ". $amt;
$data['form_action']        = $stripethankyou_url;
$data['id']                 = $sku;
$data['logo']               = $logo;
$data['showlogin']          = true;
?>
<!--
<?php if (isset($_GET['status']) && $_GET['status'] == 'fail') echo sprintf(__("<br/>If you continue to have trouble registering, please contact <em><a style='color: red' href='mailto:%s'>%s</a></em>"), $stripesettings['supportemail'], $stripesettings['supportemail']) ?>
-->