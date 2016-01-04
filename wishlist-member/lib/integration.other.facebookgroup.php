<?php
/*
 * FaceBook Group Integration Methods
 * Original Author :Peter Indiola
 * Version: $Id: 
 */

require_once(dirname(__FILE__) . '/../extlib/facebook-php-sdk/facebook.php');

if (!class_exists('WLM_OTHER_INTEGRATION_FBGroup')) {

	class WLM_OTHER_INTEGRATION_FBGroup{


    private $facebook;
    private $fb_user_id;
    private $fb_group_id;

    function __construct() {
			global $WishListMemberInstance;

			$fb_group_settings = (array) $WishListMemberInstance->GetOption('fbgroup_settings');

      $config = array(
        'appId' => $fb_group_settings['fbAppId'],
        'secret' => $fb_group_settings['fbSecret']
      );

      $this->facebook = new Facebook($config);
      $this->fb_user_id = $this->facebook->getUser();
      $this->fb_group_id = $fb_group_settings['fbGroupID'];
    }

    function AddMemberToFBGroup($uid, $data) {
      if($this->fb_user_id) {
        try {
          $response = $this->facebook->api(
            "/{$this->fb_group_id}/members",
            "POST",
            array (
              'member' => $this->fb_user_id
            )
          );
        } catch(FacebookApiException $e) {
          var_dump($e);
        }   
      } else {
        $params = array(
          'scope' => 'user_groups',
          'redirect_url' => get_site_url()
        );
        $login_url = $this->facebook->getLoginUrl($params); 
        header("Location: {$login_url}");
      }

    }
  }

	add_action('wishlistmember_user_registered', array( new WLM_OTHER_INTEGRATION_FBGroup, 'AddMemberToFBGroup'), 9, 2);
}
