<?php

class WLMAuthorizeNetARB {
  private $wlm;
  public $subscriptions;
  private $forms;

  public function __construct() {
    global $WishListMemberInstance;

    if(empty($WishListMemberInstance)) {
      return;
    }

    add_action('admin_init', array($this, 'include_underscorejs'));
    add_shortcode('wlm_authorizenet_arb_btn', array($this, 'anet_arb_btn'));
    add_action('wp_footer', array($this, 'footer'), 100);

    $this->anet_arb_shortcode_btns();

    add_action('wp_ajax_wlm_anetarb_new-subscription', array($this, 'new_subscription'));
    add_action('wp_ajax_wlm_anetarb_all-subscriptions', array($this, 'get_all_subscriptions'));
    add_action('wp_ajax_wlm_anetarb_save-subscription', array($this, 'save_subscription'));
    add_action('wp_ajax_wlm_anetarb_delete-subscription', array($this, 'delete_subscription'));

    $this->wlm      = $WishListMemberInstance;
    $this->subscriptions = $this->wlm->GetOption('anetarbsubscriptions');
  }

  public function load_js() {
    global $WishListMemberInstance;
    wp_enqueue_script('jquery-fancybox', $WishListMemberInstance->pluginURL.'/js/jquery.fancybox.pack.js', array('jquery'), $WishListMemberInstance->Version, true);
    wp_enqueue_style('jquery-fancybox', $WishListMemberInstance->pluginURL.'/css/jquery.fancybox.css', array(), $WishListMemberInstance->Version);
    wp_enqueue_script('wlm-popup-regform-card-validation', 'https://js.stripe.com/v2/', array('jquery'), $WishListMemberInstance->Version, true);
    wp_enqueue_script('wlm-popup-regform', $WishListMemberInstance->pluginURL.'/js/wlm.popup-regform.js', array('wlm-popup-regform-card-validation'), $WishListMemberInstance->Version, true);
    wp_enqueue_style('wlm-popup-regform-style', $WishListMemberInstance->pluginURL.'/css/wlm.popup-regform.css', array(), $WishListMemberInstance->Version);
  }

  public function include_underscorejs() {
    if(is_admin() && $_GET['page'] == $this->wlm->MenuID && $_GET['wl'] == 'integration') {
      wp_enqueue_script('underscore');
    }
  }

  public function get_all_subscriptions() {
    echo json_encode($this->subscriptions);
    die();
  }

  public function new_subscription() {
    $subscriptions = $this->subscriptions;
    if(empty($subscriptions)) {
      $subscriptions = array();
    }

    //create an id for this button
    $id = strtoupper(substr(sha1( microtime()), 1, 10));

    $subscription = array(
      'id'            => $id,
      'name'          => $_POST['name'],
      'currency'      => 'USD',
      'amount'        => 10,
      'recurring'     => 0,
      'sku'           => $_POST['sku'],
    );

    $this->subscriptions[$id] = $subscription;
    $this->wlm->SaveOption('anetarbsubscriptions', $this->subscriptions);

    echo json_encode($subscription);
    die();
  }

  public function save_subscription() {
    $id = $_POST['id'];
    $subscription = $_POST;
    $this->subscriptions[$id] = $subscription;
    $this->wlm->SaveOption('anetarbsubscriptions', $this->subscriptions);
    echo json_encode($this->subscriptions[$id]);
    die();
  }

  public function delete_subscription() {
    $id = $_POST['id'];
    unset($this->subscriptions[$id]);
    $this->wlm->SaveOption('anetarbsubscriptions', $this->subscriptions);
    die();
  }

  public function anet_arb_btn($atts, $content) {
    $this->load_js();

    $subscriptions   = $this->wlm->GetOption('anetarbsubscriptions');
    $wpm_levels = $this->wlm->GetOption('wpm_levels');
    $atts       = extract( shortcode_atts( array( 'sku'=> null ), $atts ) );
    $subscription    = $subscriptions[$sku];

    $settings              = $this->wlm->GetOption('anetarbthankyou_url');
    $anetarbthankyou     = $this->wlm->GetOption('anetarbthankyou');
    $wpm_scregister        = get_bloginfo('url') . '/index.php/register/';
    $anetarbthankyou_url = $wpm_scregister . $anetarbthankyou;

    include $this->wlm->pluginDir .'/extlib/wlm_authorizenet_arb/form_new_field.php';
    $this->forms[$sku] = wlm_build_payment_form($data);
    return sprintf('<a id="go-regform-%s" class="go-regform" href="#regform-%s">%s</a>', $sku, $sku, $content);
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

  public function anet_arb_shortcode_btns(){
    global $pagenow;
    if(in_array($pagenow, array('post.php', 'post-new.php'))) {
      global $WLMTinyMCEPluginInstanceOnly;
      global $WishListMemberInstance;

      $subscriptions       = $WishListMemberInstance->GetOption('anetarbsubscriptions');
      $wlm_shortcodes = array();
      $str            = __(" Registration Button", "wishlist-member");
      $buy_now_str    = __("Buy Now", "wishlist-member");
      foreach((array) $subscriptions as $id => $p) {
        $wlm_shortcodes[] = array('title' => $p['name'] . $str , 'value' => sprintf("[wlm_authorizenet_arb_btn sku=%s]%s[/wlm_authorizenet_arb_btn]", $id, $buy_now_str));
      }
      $WishListMemberInstance->IntegrationShortcodes['Authorize.Net (ARB) Integration'] = $wlm_shortcodes;
      // $WLMTinyMCEPluginInstanceOnly->RegisterShortcodes("Authorize.Net (ARB) Integration", $wlm_shortcodes, array());
    }
  }
}

$wlm_aurthorizenet_arb_init = new WLMAuthorizeNetARB();
