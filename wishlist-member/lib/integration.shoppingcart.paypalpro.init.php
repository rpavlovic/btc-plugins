<?php
class WlmPaypalProInit {
	private $forms;
	private $wlm;
	private $products;

	public function load_popup() {
		global $WishListMemberInstance;
		wp_enqueue_script('jquery-fancybox', $WishListMemberInstance->pluginURL.'/js/jquery.fancybox.pack.js', array('jquery'), $WishListMemberInstance->Version, true);
		wp_enqueue_style('jquery-fancybox', $WishListMemberInstance->pluginURL.'/css/jquery.fancybox.css', array(), $WishListMemberInstance->Version);
		wp_enqueue_script('wlm-popup-regform-card-validation', 'https://js.stripe.com/v2/', array('jquery'), $WishListMemberInstance->Version, true);
		wp_enqueue_script('wlm-popup-regform', $WishListMemberInstance->pluginURL.'/js/wlm.popup-regform.js', array('wlm-popup-regform-card-validation'), $WishListMemberInstance->Version, true);
		wp_enqueue_style('wlm-popup-regform-style', $WishListMemberInstance->pluginURL.'/css/wlm.popup-regform.css', array(), $WishListMemberInstance->Version);

	}
	public function __construct() {
		add_action('admin_init', array($this, 'use_underscore'));
		add_shortcode( 'wlm_paypalpro_btn', array($this, 'paypalprobtn'));
		add_action('wp_footer', array($this, 'footer'), 100);

		$this->paypalpro_shortcode_btns();


		add_action('wp_ajax_wlm_pp_new-product', array($this, 'new_product'));
		add_action('wp_ajax_wlm_pp_all-products', array($this, 'get_all_products'));
		add_action('wp_ajax_wlm_pp_save-product', array($this, 'save_product'));
		add_action('wp_ajax_wlm_pp_delete-product', array($this, 'delete_product'));

		global $WishListMemberInstance;

		if(empty($WishListMemberInstance)) {
			return;
		}
		$this->wlm      = $WishListMemberInstance;
		$this->products = $WishListMemberInstance->GetOption('paypalproproducts');
	}
	public function footer() {
		foreach((array) $this->forms as $f) {
			echo $f;
		}
?>
<script type="text/javascript">
jQuery(function($) {
<?php
	if(!empty($this->forms) && is_array($this->forms)) {
		$skus = array_keys($this->forms);
		foreach($skus as $sku) {
			echo sprintf("$('#regform-%s .regform-form').PopupRegForm();", $sku);
		}
	}
?>
});
</script>
<?php
	}
	public function use_underscore() {
		global $WishListMemberInstance;
		if(is_admin() && $_GET['page'] == $WishListMemberInstance->MenuID && $_GET['wl'] == 'integration') {
			wp_enqueue_script('underscore');
		}
	}

	public function paypalprobtn( $atts, $content) {
		global $WishListMemberInstance;
		$this->load_popup();
		$products   = $WishListMemberInstance->GetOption('paypalproproducts');
		$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
		$atts       = extract( shortcode_atts( array( 'sku'=> null ), $atts ) );
		$product    = $products[$sku];


		$settings              = $WishListMemberInstance->GetOption('paypalprothankyou_url');
		$paypalprothankyou     = $WishListMemberInstance->GetOption('paypalprothankyou');
		$wpm_scregister        = get_bloginfo('url') . '/index.php/register/';
		$paypalprothankyou_url = $wpm_scregister . $paypalprothankyou;
		if($product['checkout_type'] == 'direct-charge') {
			include $WishListMemberInstance->pluginDir .'/extlib/wlm_paypal/form_new_fields.php';
			$this->forms[$sku] = wlm_build_payment_form($data);
			return sprintf('<a id="go-regform-%s" class="go-regform" href="#regform-%s">%s</a>', $sku, $sku, $content);
		} else {
			return sprintf('<a id="" href="%s?action=purchase-express&id=%s">%s</a>', $paypalprothankyou_url, $sku, $content);
		}


		return sprintf('<a href="%s?action=purchase&id=%s">BUY</a>', $paypalprothankyou_url, $sku);

	}
	public function paypalpro_shortcode_btns() {
		global $pagenow;
		if(in_array($pagenow, array('post.php', 'post-new.php'))) {
			global $WishListMemberInstance;

			$products       = $WishListMemberInstance->GetOption('paypalproproducts');
			$wlm_shortcodes = array();
			$str            = __(" Registration Button", "wishlist-member");
			$buy_now_str    = __("Buy Now", "wishlist-member");
			foreach((array) $products as $id => $p) {
				$wlm_shortcodes[] = array('title' => $p['name'] . $str , 'value' => sprintf("[wlm_paypalpro_btn sku=%s]%s[/wlm_paypalpro_btn]", $id, $buy_now_str));
			}
			$WishListMemberInstance->IntegrationShortcodes['PayPal Pro Integration'] = $wlm_shortcodes;
		}
	}


	//ajax methods

	public function delete_product() {
		$id = $_POST['id'];
		unset($this->products[$id]);
		$this->wlm->SaveOption('paypalproproducts', $this->products);
	}
	public function save_product() {

		$id = $_POST['id'];
		$product = $_POST;
		$this->products[$id] = $product;
		$this->wlm->SaveOption('paypalproproducts', $this->products);
		echo json_encode($this->products[$id]);
		die();
	}

	public function get_all_products() {
		$products = $this->products;
		echo json_encode($products);
		die();
	}

	public function new_product() {
		$products = $this->products;
		if(empty($products)) {
			$products = array();
		}

		//create an id for this button
		$id = strtoupper(substr(sha1( microtime()), 1, 10));

		$product = array(
			'id'            => $id,
			'name'          => $_POST['name'],
			'currency'      => 'USD',
			'amount'        => 10,
			'recurring'     => 0,
			'sku'           => $_POST['sku'],
			'checkout_type' => 'express-checkout'
		);

		$this->products[$id] = $product;
		$this->wlm->SaveOption('paypalproproducts', $this->products);

		echo json_encode($product);
		die();
	}
}


$wlm_paypalpro_init = new WlmPaypalProInit();





