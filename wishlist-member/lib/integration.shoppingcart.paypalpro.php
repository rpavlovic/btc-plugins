<?php


if(extension_loaded('curl')) {
	global $WishListMemberInstance;
	include_once($WishListMemberInstance->pluginDir . '/extlib/paypal/PPAutoloader.php');
	PPAutoloader::register();
}

if (!class_exists('WLM_INTEGRATION_PAYPALPRO')) {
	class WLM_INTEGRATION_PAYPALPRO {
		private $settings;
		private $wlm;

		private $thankyou_url;
		private $pp_settings;
		public function __construct() {
			global $WishListMemberInstance;
			$this->wlm      = $WishListMemberInstance;
			$this->products = $this->wlm->GetOption('paypalproproducts');

			$settings           = $this->wlm->GetOption('paypalprothankyou_url');
			$paypalprothankyou  = $this->wlm->GetOption('paypalprothankyou');
			$wpm_scregister     = get_bloginfo('url') . '/index.php/register/';
			$this->thankyou_url = $wpm_scregister . $paypalprothankyou;


			$pp_settings = $this->wlm->GetOption('paypalprosettings');


			$index = 'live';
			if($pp_settings['sandbox_mode']) {
				$index = 'sandbox';
			}

			$this->pp_settings = array(
				'acct1.UserName'  => $pp_settings[$index]['api_username'],
				'acct1.Password'  => $pp_settings[$index]['api_password'],
				'acct1.Signature' => $pp_settings[$index]['api_signature'],
				'mode'            => $pp_settings['sandbox_mode']? 'sandbox' : 'live',
				'gateway'         => $pp_settings['sandbox_mode']? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com',
			);


		}
		public function paypalpro($that) {
			$action = strtolower(trim($_GET['action']));

			switch ($action) {
				case 'purchase-express':
					try {
						$this->purchase_express($_GET['id']);
					} catch (Exception $e) {
					}

					break;
				case 'purchase-direct':
					$this->purchase_direct($_GET['id']);
					break;
				case 'confirm':
					try {
						$this->confirm($_GET['id'], $_GET['token'], $_GET['PayerID']);
					} catch (Exception $e) {
					}

					break;
				case 'ipn':
					$this->ipn($_GET['id']);
				default:
					# code...
					break;
			}
		}
		public function ipn($id = null) {
			//$products = $this->products;
			//$product = $products[$id];


			$ipn_message = new PPIPNMessage(null, $this->pp_settings);
			$raw_data    = $ipn_message->getRawData();

			if(!$ipn_message->validate()) {
				return false;
			}

			foreach($raw_data as $key => $value) {
				//error_log("IPN: $key => $value");
			}
			//error_log("-----------------------------end ipn------------------------------");

			$txn_id           = isset($raw_data['parent_txn_id'])? $raw_data['parent_txn_id'] : $raw_data['txn_id'];
			$txn_id           = isset($raw_data['recurring_payment_id'])? $raw_data['recurring_payment_id'] : $txn_id;
			$_POST['sctxnid'] = $txn_id;

			switch ($raw_data['txn_type']) {
				//anything related to recurring, we follow
				//the profiles status
				case 'recurring_payment_profile_created':
				case 'subscr_signup':
				case 'recurring_payment':
				case 'recurring_payment_skipped':
				case 'subscr_modify':
				case 'subscr_payment':
				case 'recurring_payment_profile_cancel':
				case 'recurring_payment_expired':
				case 'recurring_payment_failed':
				case 'recurring_payment_suspended_due_to_max_failed_payment':
				case 'recurring_payment_suspended':
				case 'subscr_cancel':
				case 'subscr_eot':
				case 'subscr_failed':
					switch ($raw_data['profile_status']) {
						case 'Active':
							$this->wlm->ShoppingCartReactivate();
							break;
						case 'Suspended':
						case 'Cancelled':
							$this->wlm->ShoppingCartDeactivate();
							break;
						default:
							//ignore
							break;
					}
					//were done
					return;
				break;
			}

			// this is a one time payment
			switch($raw_data['payment_status']) {
				case 'Completed':
					if (isset($raw_data['echeck_time_processed'])) {
						$this->wlm->ShoppingCartReactivate(1);
					} else {
						$this->wlm->ShoppingCartRegistration(null, false);
						$this->wlm->CartIntegrationTerminate();
					}
					break;
				case 'Canceled-Reversal':
					$this->wlm->ShoppingCartReactivate();
					break;
				case 'Processed':
					$this->wlm->ShoppingCartReactivate('Confirm');
					break;
				case 'Expired':
				case 'Failed':
				case 'Refunded':
				case 'Reversed':
					$this->wlm->ShoppingCartDeactivate();
					break;

			}
		}
		public function confirm($id, $token, $payer_id) {
			$products = $this->products;
			$product = $products[$id];
			if(empty($product)) {
				return;
			}

			$paypal_service  = new PayPalAPIInterfaceServiceService($this->pp_settings);

			$ec_details_req_type = new GetExpressCheckoutDetailsRequestType($token);
			$ec_detail_req = new GetExpressCheckoutDetailsReq();
			$ec_detail_req->GetExpressCheckoutDetailsRequest = $ec_details_req_type;

			$ec_resp = $paypal_service->GetExpressCheckoutDetails($ec_detail_req);

			if(!$ec_resp && $ec_resp->Ack != 'Success') {
				throw new Exception("Paypal Request Failed");
			}

			//we now have the payer info
			$payer_info = $ec_resp->GetExpressCheckoutDetailsResponseDetails->PayerInfo;

			if($product['recurring']) {
				$order_total = new BasicAmountType($product['currency'], $product['init_amount']);
			} else {
				$order_total = new BasicAmountType($product['currency'], $product['amount']);
			}

			$payment_details             = new PaymentDetailsType();
			$payment_details->OrderTotal = $order_total;
			$payment_details->NotifyURL  = $this->thankyou_url.'?action=ipn&id='.$id;


			$do_ec_details = new DoExpressCheckoutPaymentRequestDetailsType();
			$do_ec_details->PayerID = $payer_id;
			$do_ec_details->Token = $token;
			$do_ec_details->PaymentDetails[0] = $payment_details;

			$do_ec_request = new DoExpressCheckoutPaymentRequestType();
			$do_ec_request->DoExpressCheckoutPaymentRequestDetails = $do_ec_details;


			if($order_total->value > 0) {
				$do_ec = new DoExpressCheckoutPaymentReq();
				$do_ec->DoExpressCheckoutPaymentRequest = $do_ec_request;

				$do_ec_resp = $paypal_service->DoExpressCheckoutPayment($do_ec);
				if(!$do_ec_resp || $do_ec_resp->Ack != 'Success') {
					throw new Exception("Paypal Checkout Error Has Occured");
				}

				//we now have a payment info. Yeehaaa
				$payment_info = current($do_ec_resp->DoExpressCheckoutPaymentResponseDetails->PaymentInfo);

				$accept_statuses = array('Completed', 'In-Progress', 'Pending', 'Processed');
				if(!in_array($payment_info->PaymentStatus, $accept_statuses)) {
					throw new Exception("Paypal Payment Checkout Failed");
				}
			}


			if($product['recurring']) {
				//create a recurring payment profile
				$schedule_details                          = new ScheduleDetailsType();
				$payment_billing_period                   = new BillingPeriodDetailsType();
				$payment_billing_period->BillingFrequency = $product['recur_billing_frequency'];
				$payment_billing_period->BillingPeriod    = $product['recur_billing_period'];
				$payment_billing_period->Amount           = new BasicAmountType($product['currency'], $product['recur_amount']);
				$schedule_details->PaymentPeriod           = $payment_billing_period;
				$schedule_details->Description             = sprintf("%s %s", $product['name'], __("Subscription", "wishlist-member"));

				$recur_profile_details = new RecurringPaymentsProfileDetailsType();
				$recur_profile_details->BillingStartDate = date(DATE_ATOM, strtotime(sprintf("+%s %s", $product['recur_billing_frequency'], $product['recur_billing_period'])));

				$create_recur_paypay_profile_details = new CreateRecurringPaymentsProfileRequestDetailsType();
				$create_recur_paypay_profile_details->Token  = $token;
				$create_recur_paypay_profile_details->ScheduleDetails = $schedule_details;
				$create_recur_paypay_profile_details->RecurringPaymentsProfileDetails = $recur_profile_details;

				$create_recur_profile = new CreateRecurringPaymentsProfileRequestType();
				$create_recur_profile->CreateRecurringPaymentsProfileRequestDetails = $create_recur_paypay_profile_details;

				$create_recur_profile_req =  new CreateRecurringPaymentsProfileReq();
				$create_recur_profile_req->CreateRecurringPaymentsProfileRequest = $create_recur_profile;
				$create_profile_resp = $paypal_service->CreateRecurringPaymentsProfile($create_recur_profile_req);

				if(!$create_profile_resp || $create_profile_resp->Ack != 'Success') {
					throw new Exception("Could not create recurring profile");
				}
			}


			$address = array();
			$address['company']       = $payer_info->PayerBusiness;
			$address['address1']      = $payer_info->Address->Street1;
			$address['address2']      = $payer_info->Address->Street2;
			$address['city']          = $payer_info->Address->CityName;
			$address['state']         = $payer_info->Address->StateOrProvince;
			$address['zip']           = $payer_info->Address->PostalCode;
			$address['country']       = $payer_info->Address->CountryName;

			$_POST['wpm_useraddress'] = $address;
			$_POST['lastname']        = $payer_info->PayerName->LastName;
			$_POST['firstname']       = $payer_info->PayerName->FirstName;
			$_POST['action']          = 'wpm_register';
			$_POST['wpm_id']          = $product['sku'];
			$_POST['username']        = $payer_info->Payer;
			$_POST['email']           = $payer_info->Payer;
			$_POST['password1']       = $_POST['password2'] = $this->wlm->PassGen();
			$_POST['sctxnid']         = $product['recurring']? $create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileID :
			$payment_info->TransactionID;


			$pending_statuses = array('In-Progress', 'Pending');
			if(in_array($payment_info->PaymentStatus, $pending_statuses) || $create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileStatus == 'PendingProfile') {
				$this->wlm->ShoppingCartRegistration(null, null, 'Paypal Pending');
			} else {
				$this->wlm->ShoppingCartRegistration();
			}


		}
		public function purchase_express($id) {
			$products = $this->products;
			$product = $products[$id];
			if(empty($product)) {
				return;
			}



			$paypal_service  = new PayPalAPIInterfaceServiceService($this->pp_settings);
			$payment_details = new PaymentDetailsType();


			if($product['recurring']) {
				$item_details                = new PaymentDetailsItemType();
				$item_details->Name          = $product['name'];
				$item_details->Amount        = $product['init_amount'];
				$item_details->Quantity      = 1;
				$payment_details->OrderTotal = new BasicAmountType($product['currency'], $product['init_amount']);

				$billing_agreement  = new BillingAgreementDetailsType('RecurringPayments');
				$billing_agreement->BillingAgreementDescription = sprintf("%s %s", $product['name'], __("Subscription", "wishlist-member"));

			} else {
				$item_details           = new PaymentDetailsItemType();
				$item_details->Name     = $product['name'];
				$item_details->Amount   = $product['amount'];
				$item_details->Quantity = 1;
				$payment_details->OrderTotal = new BasicAmountType($product['currency'], $product['amount']);
			}


			$payment_details->PaymentDetailsItem[$i] = $item_details;

			$ec_req_details                     = new SetExpressCheckoutRequestDetailsType();
			$ec_req_details->NoShipping         = 0;
			$ec_req_details->ReqConfirmShipping = 0;
			$ec_req_details->SolutionType       = 'Sole';
			$ec_req_details->ReturnURL          = $this->thankyou_url.'?action=confirm&id='.$id;
			$ec_req_details->CancelURL          = $this->thankyou_url.'?action=confirm&id='.$id;
			$ec_req_details->LandingPage        = 'Billing';

			$ec_req_details->PaymentDetails[0]  = $payment_details;

			if(isset($billing_agreement)) {
				$ec_req_details->BillingAgreementDetails = array($billing_agreement);
			}

			$ec_req_type = new SetExpressCheckoutRequestType();
			$ec_req_type->SetExpressCheckoutRequestDetails = $ec_req_details;


			$ec_req = new SetExpressCheckoutReq();
			$ec_req->SetExpressCheckoutRequest = $ec_req_type;


			$ec_res = $paypal_service->SetExpressCheckout($ec_req);


			if($ec_res && $ec_res->Ack == 'Success') {
				$next_loc = sprintf("%s/webscr?cmd=_express-checkout&token=%s", $this->pp_settings['gateway'], $ec_res->Token);
				wp_redirect($next_loc);
				die();
			} else {
				//var_dump($ec_res);
			}

		}

		public function purchase_direct_recurring($product) {
			//create a recurring payment profile
			$person_name = new PersonNameType();
			$person_name->FirstName = $_POST['first_name'];
			$person_name->LastName  = $_POST['last_name'];

			$payer = new PayerInfoType();
			$payer->Payer     = $_POST['email'];
			$payer->PayerName = $person_name;

			$card_details = new CreditCardDetailsType();
			$card_details->CreditCardNumber = $_POST['cc_number'];
			$card_details->CreditCardType   = $_POST['cc_type'];
			$card_details->ExpMonth         = $_POST['cc_expmonth'];
			$card_details->ExpYear          = $_POST['cc_expyear'] + 2000;
			$card_details->CVV2             = $_POST['cc_cvc'];
			$card_details->CardOwner        = $payer;

			$activation_details = new ActivationDetailsType();
			$activation_details->FailedInitialAmountAction = 'ContinueOnFailure';


			$activation_details->InitialAmount = new BasicAmountType($product['currency'], $product['init_amount']);
			if(empty($product['init_amount'])) {
				$activation_details->InitialAmount = new BasicAmountType($product['currency'], $product['recur_amount']);
			}

			$schedule_details                         = new ScheduleDetailsType();
			$payment_billing_period                   = new BillingPeriodDetailsType();
			$payment_billing_period->BillingFrequency = $product['recur_billing_frequency'];
			$payment_billing_period->BillingPeriod    = $product['recur_billing_period'];
			$payment_billing_period->Amount           = new BasicAmountType($product['currency'], $product['recur_amount']);
			$schedule_details->PaymentPeriod          = $payment_billing_period;
			$schedule_details->Description            = sprintf("%s %s", $product['name'], __("Subscription", "wishlist-member"));
			$schedule_details->ActivationDetails 		= $activation_details;

			$recur_profile_details = new RecurringPaymentsProfileDetailsType();
			// adjust the start billing date to the next period
			$recur_profile_details->BillingStartDate = date(DATE_ATOM, strtotime(sprintf("+%s %s", $product['recur_billing_frequency'], $product['recur_billing_period'])));

			$create_recur_paypay_profile_details = new CreateRecurringPaymentsProfileRequestDetailsType();
			$create_recur_paypay_profile_details->Token  = $token;
			$create_recur_paypay_profile_details->ScheduleDetails = $schedule_details;
			$create_recur_paypay_profile_details->RecurringPaymentsProfileDetails = $recur_profile_details;
			$create_recur_paypay_profile_details->CreditCard = $card_details;

			$create_recur_profile = new CreateRecurringPaymentsProfileRequestType();
			$create_recur_profile->CreateRecurringPaymentsProfileRequestDetails = $create_recur_paypay_profile_details;

			$create_recur_profile_req =  new CreateRecurringPaymentsProfileReq();
			$create_recur_profile_req->CreateRecurringPaymentsProfileRequest = $create_recur_profile;

			$paypal_service  = new PayPalAPIInterfaceServiceService($this->pp_settings);
			$create_profile_resp = $paypal_service->CreateRecurringPaymentsProfile($create_recur_profile_req);

			if($create_profile_resp->Ack != 'Success' && $create_profile_resp->Ack != 'SuccessWithWarning') {
				return false;
			}

			if($create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileStatus == 'ActiveProfile') {
				return array(
					'status' => 'active',
					'id'     => $create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileID
				);
			}

			return array(
				'status' => 'pending',
				'id'     => $create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileID
			);

		}
		public function purchase_direct_once($product) {

			$item_details           = new PaymentDetailsItemType();
			$item_details->Name     = $product['name'];
			$item_details->Amount   = $product['amount'];
			$item_details->Quantity = 1;

			$payment_details = new PaymentDetailsType();
			$payment_details->OrderTotal = new BasicAmountType($product['currency'], $product['amount']);
			$payment_details->NotifyURL = $this->thankyou_url.'?action=ipn&id='.$id;
			$payment_details->PaymentDetailsItem[$i] = $item_details;

			$person_name = new PersonNameType();
			$person_name->FirstName = $_POST['first_name'];
			$person_name->LastName  = $_POST['last_name'];

			$payer = new PayerInfoType();
			$payer->Payer     = $_POST['email'];
			$payer->PayerName = $person_name;

			$card_details = new CreditCardDetailsType();
			$card_details->CreditCardNumber = $_POST['cc_number'];
			$card_details->CreditCardType   = $_POST['cc_type'];
			$card_details->ExpMonth         = $_POST['cc_expmonth'];
			$card_details->ExpYear          = $_POST['cc_expyear'] + 2000;
			$card_details->CVV2             = $_POST['cc_cvc'];
			$card_details->CardOwner        = $payer;

			$dd_req_details = new DoDirectPaymentRequestDetailsType();
			$dd_req_details->CreditCard = $card_details;
			$dd_req_details->PaymentDetails = $payment_details;

			$do_direct_req = new DoDirectPaymentReq();
			$do_direct_req->DoDirectPaymentRequest = new DoDirectPaymentRequestType($dd_req_details);

			$paypal_service  = new PayPalAPIInterfaceServiceService($this->pp_settings);
			$resp = $paypal_service->DoDirectPayment($do_direct_req);

			if($resp->Ack == 'Success' || $resp->Ack == 'SuccessWithWarning') {
				return array(
					'status' =>  'active',
					'id' => $resp->TransactionID
				);
			}
		}
		public function purchase_direct($id) {
			$products = $this->products;
			$product = $products[$id];

			if(empty($product)) {
				return;
			}

			if($product['recurring']) {
				$status = $this->purchase_direct_recurring($product);
			} else {
				$status = $this->purchase_direct_once($product);
			}

			$_POST['lastname']  = $_POST['last_name'];
			$_POST['firstname'] = $_POST['first_name'];
			$_POST['action']    = 'wpm_register';
			$_POST['wpm_id']    = $product['sku'];
			$_POST['username']  = $_POST['email'];
			$_POST['email']     = $_POST['email'];
			$_POST['sctxnid']   = $status['id'];
			$_POST['password1'] = $_POST['password2'] = $this->wlm->PassGen();

			// Paypal will mark the profile as pending
			// When there is an initial amount because the charge event is delayed.
			// We will ignore the pending status because this will cause
			// users to see the 'pending/forapproval' error when the ipn
			// get's delayed. Which is usually the case because of the delay
			// when charging
			$this->wlm->ShoppingCartRegistration();
		}
	}
}
