<?php
$fields = array(
	'nonce' => array(
		'type'  => 'hidden',
		'name'  => 'nonce',
		'label' => '',
		'value' => wp_create_nonce('eway-do-charge'),
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
		'value' => 'existing',
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
		'type'        => 'hidden',
		'name'        => 'first_name',
		'label'       => 'First Name',
		'placeholder' => "First Name",
		'value'       => $current_user->first_name,
	),
	'last_name' => array(
		'type'        => 'hidden',
		'name'        => 'last_name',
		'label'       => 'Last Name',
		'placeholder' => "Last Name",
		'value'       => $current_user->last_name,
	),
	'email' => array(
		'type'        => 'hidden',
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
);

$heading            = empty($settings['formheading']) ? "Register to %level" : $settings['formheading'];
$heading            = str_replace('%level', $level_name, $heading);
$panel_button_label =  str_replace('%currency', $currency,  $panel_button_label);
$panel_button_label =  str_replace('%amount', $amt,  $panel_button_label);

$data['fields']             = $fields;
$data['heading']            = $heading;
$data['panel_button_label'] = $panel_button_label;
$data['form_action']        = $thankyouurl;
$data['id']                 = $sku;
$data['logo']               = $logo;
$data['showlogin']          = true;