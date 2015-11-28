<?php
/*
Simple:Press
New User Email (SPF Option)
$LastChangedDate: 2014-10-26 16:51:39 -0700 (Sun, 26 Oct 2014) $
$Rev: 12025 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# = NEW USER EMAIL REPLACEMENT ================
if (!function_exists('wp_new_user_notification')):
function wp_new_user_notification($user_id, $user_pass='') {
	$user = new WP_User($user_id);
	$sflogin = sp_get_option('sflogin');
	$eol = "\r\n";
	$user_login = $user->user_login;
	$user_email = $user->user_email;
	$message = '';
	$message.= sp_text_noesc('New user registration on your website').': '.get_option('blogname').$eol.$eol;
	$message.= sp_text_noesc('Username').': '.$user_login.$eol;
	$message.= sp_text_noesc('E-mail').': '.$user_email.$eol;
	$message.= sp_text_noesc('Registration IP').': '.sp_get_ip().$eol;

	$address = apply_filters('sph_admin_new_user_email_addrress', get_option('admin_email'), $user_id);
	$subject = apply_filters('sph_admin_new_user_email_subject', get_option('blogname').' '.sp_text_noesc('New User Registration'), $user_id);
	$msg = apply_filters('sph_admin_new_user_email_msg', $message, $user_id);
	sp_send_email($address, $subject, $msg);

	if (empty($user_pass)) return;

	$mailoptions = sp_get_option('sfnewusermail');
	$subject = stripslashes($mailoptions['sfnewusersubject']);
	$body = stripslashes($mailoptions['sfnewusertext']);
	if ((empty($subject)) || (empty($body))) {
		$subject = get_option('blogname').' '.sp_text_noesc('Your username and password').$eol.$eol;
		$body = sp_text_noesc('Username').': '.$user_login.$eol;
		$body.= sp_text_noesc('Password').': '.$user_pass.$eol.$eol;
		$body.= $sflogin['sfloginemailurl'].$eol;
	} else {
		$blogname = get_bloginfo('name');
		$subject = str_replace('%USERNAME%', $user_login, $subject);
		$subject = str_replace('%PASSWORD%', $user_pass, $subject);
		$subject = str_replace('%BLOGNAME%', $blogname, $subject);
		$subject = str_replace('%SITEURL%', sp_url(), $subject);
		$subject = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $subject);
		$body = str_replace('%USERNAME%', $user_login, $body);
		$body = str_replace('%PASSWORD%', $user_pass, $body);
		$body = str_replace('%BLOGNAME%', $blogname, $body);
		$body = str_replace('%SITEURL%', sp_url(), $body);
		$body = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $body);
		$body = str_replace('%NEWLINE%', $eol, $body);
	}
	str_replace('<br />', $eol, $body);

	$address = apply_filters('sph_user_new_user_email_addrress', $user_email, $user_id);
	$subject = apply_filters('sph_user_new_user_email_subject', get_option('blogname').' '.sp_text_noesc('New User Registration'), $user_id);
	$msg = apply_filters('sph_user_new_user_email_msg', $body, $user_id, $user_pass);
	sp_send_email($user_email, $subject, $msg);
}
endif;
?>