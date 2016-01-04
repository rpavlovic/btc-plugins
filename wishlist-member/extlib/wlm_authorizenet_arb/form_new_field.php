<?php
$fields = array(
	'nonce' => array(
		'type'  => 'hidden',
		'name'  => 'nonce',
		'label' => '',
		'value' => wp_create_nonce('anetarb-do-charge'),
		'class' => ''
	),
	'regform_action' => array(
		'type'  => 'hidden',
		'name'  => 'regform_action',
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
		'placeholder' => "First Name",
		'value'       => $current_user->last_name,
	),
	'email' => array(
		'type'        => 'text',
		'name'        => 'email',
		'label'       => 'Email',
		'placeholder' => "Email",
		'value'       => $current_user->user_email,
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
		'name'        => 'cc_expyear',
		'label'       => __('Expires:', "wishlist-member"),
		'placeholder' => "",
		'value'       => "",
	),
	'cc_type' => array(
		'type'        => 'card',
		'name'        => 'cc_type',
		'label'       => __('Card Type:', "wishlist-member"),
		'placeholder' => "",
		'value'       => "",
	),
);

$level_name         = $wpm_levels[$subscription['sku']]['name'];
$heading            = empty($settings['formheading']) ? "Register to %level" : $settings['formheading'];
$heading            = str_replace('%level', $level_name, $heading);
$panel_button_label = 'Pay %waiting';
$panel_button_label =  str_replace('%waiting', '<span class="regform-waiting">...</span> ',  $panel_button_label);

$data['fields']             = $fields;
$data['heading']            = $heading;
$data['panel_button_label'] = $panel_button_label;
$data['form_action']        = $anetarbthankyou_url.'?action=purchase-direct&id='.$sku;
$data['id']                 = $sku;
$data['logo']               = $logo;
$data['showlogin']          = true;
