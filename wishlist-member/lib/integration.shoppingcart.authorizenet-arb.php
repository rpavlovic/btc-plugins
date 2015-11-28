<?php
if (!class_exists('WLM_INTEGRATION_AUTHORIZENET_ARB')) {

  class WLM_INTEGRATION_AUTHORIZENET_ARB {

    private $settings;
    private $wlm;

		private $thankyou_url;
		private $anetarb_settings;

    public function __construct() {
			global $WishListMemberInstance;
      $this->wlm      = $WishListMemberInstance;
			$this->subscriptions = $this->wlm->GetOption('anetarbsubscriptions');

			$settings           = $this->wlm->GetOption('anetarbthankyou_url');
			$anetarbthankyou    = $this->wlm->GetOption('anetarbthankyou');
			$wpm_scregister     = get_bloginfo('url') . '/index.php/register/';
			$this->thankyou_url = $wpm_scregister . $anetarbthankyou;


			$anetarb_settings = $this->wlm->GetOption('anetarbsettings');

			$this->anetarb_settings = array(
        'acct.api_login_id'   => $anetarb_settings['api_login_id'],
        'acct.transaction_key'   => $anetarb_settings['api_transaction_key'],
        'mode'            => $anetarb_settings['sandbox_mode']? 'sandbox' : null,
        'gateway'         => $anetarb_settings['sandbox_mode']? 'https://test.authorize.net/gateway/transact.dll' : 'https://secure.authorize.net/gateway/transact.dll',
			);

      include $this->wlm->pluginDir .'/extlib/wlm_authorizenet_arb/authnet_aim.php';
      include $this->wlm->pluginDir .'/extlib/wlm_authorizenet_arb/authnet_arb.php';
    }

		public function authorizenet_arb($that) {
			$action = strtolower(trim($_REQUEST['action']));
			$valid_actions = array('purchase-direct', 'silent-post');
			if (!in_array($action, $valid_actions)) {
				echo __("Permission Denied", "wishlist-member");
				die();
			}

			if (($action != 'silent-post' && $action != 'purchase-direct') && !wp_verify_nonce($_REQUEST['nonce'], "anetarb-do-$action")) {
				echo __("Permission Denied", "wishlist-member");
				die();
			}

      switch ($action) {
        case 'purchase-direct':
					$this->purchase_direct($_GET['id']);
          break;
        case 'silent-post':
          $this->silent_post();
          break;
        default:
          break;
      }
		}

    public function purchase_direct($id) {
			$subscriptions = $this->subscriptions;
			$subscription = $subscriptions[$id];

			if(empty($subscription)) {
				return;
			}


      $sandbox = $this->anetarb_settings['mode'] == 'sandbox' ? true : false;
      $payment = new AuthnetAIM($sandbox);
			$creditcard = $_POST['cc_number'];
			$expiration = $_POST['cc_expmonth'].$_POST['cc_expyear'];
			$cvv = $_POST['cc_cvc'];
      $total = $subscription['init_amount'];
      $invoice = null;
      $tax = null;
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $email = $_POST['email'];
      $payment->transaction($creditcard, $expiration, $total, $cvv, $invoice, $tax);

      $login = $this->anetarb_settings['acct.api_login_id'];
      $key = $this->anetarb_settings['acct.transaction_key'];
      $payment->setParameter("x_login", $login);
      $payment->setParameter("x_tran_key", $key);
      $payment->setParameter("x_first_name", $first_name);
      $payment->setParameter("x_last_name", $last_name);
      $payment->setParameter("x_email", $email);
      $subscription_name = $subscription['name'];

      $payment->process();

      if ($payment->isApproved()) {
        // Instanciate our ARB class
        $arb = new AuthnetARB($sandbox, $login, $key);

        // Set recurring billing variables

        // Set recurring billing parameters
        $arb->setParameter('amount', $total);
        $arb->setParameter('cardNumber', $creditcard);
        $arb->setParameter('expirationDate', $expiration);
        $arb->setParameter('firstName', $first_name);
        $arb->setParameter('lastName', $last_name);
        //$arb->setParameter('address', $address);
        //$arb->setParameter('city', $city);
        //$arb->setParameter('state', $state);
        //$arb->setParameter('zip', $zip);
        $arb->setParameter('customerEmail', $email);
        $arb->setParameter('subscrName', $subscription_name);


        // Create the recurring billing subscription
        $arb->createAccount();

        // If successful let's get the subscription ID
        if ($arb->isSuccessful()) {
          $arb_id = $arb->getSubscriberID();
          $status = array(
            'status' => 'active',
            'id'     => $arb_id
          );
        } else {
          //var_dump('Fail:' . $arb->getResponse());
        }
      } else {
        //var_dump($payment->getResultResponseFull());
      }
			$_POST['lastname']  = $last_name;
			$_POST['firstname'] = $first_name;
			$_POST['action']    = 'wpm_register';
			$_POST['wpm_id']    = $subscription['sku'];
			$_POST['username']  = $email;
			$_POST['email']     = $email;
			$_POST['sctxnid']   = $status['id'];
			$_POST['password1'] = $_POST['password2'] = $this->wlm->PassGen();

			$this->wlm->ShoppingCartRegistration();
    }

    private function silent_post() {
			$this->wlm->SyncMembership();
      $subscription_id = (int) $_POST['x_subscription_id'];

      if ($subscription_id) {
        // Get the response code. 1 is success, 2 is decline, 3 is error
        $response_code = (int) $_POST['x_response_code'];

        // Get the reason code. 8 is expired card.
        $reason_code = (int) $_POST['x_response_reason_code'];

        $_POST['sctxnid'] = $subscription_id;
        switch ($response_code) {
          case 1:
            break;
          case 2:
          case 3:
          case 8:
            $this->wlm->ShoppingCartDeactivate();
            break;
          default:
            break;
        }
      }
    }

  }
}
