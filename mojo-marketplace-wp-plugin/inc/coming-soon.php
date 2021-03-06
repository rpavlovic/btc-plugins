<?php
/**
 * This file adds a coming soon page for new installs
 */

function mm_cs_login_check() {
	if( get_option( 'mm_install_date' ) === date( 'M d, Y' )  && ! get_option( 'mm_coming_soon' ) ){
		update_option( 'mm_coming_soon', 'true' );
	}
}
add_action( 'init', 'mm_cs_login_check', 11 );

function mm_cs_notice_display() {
	if( 'true' === get_option( 'mm_coming_soon', 'false' ) ) {
		?>
		<div class='updated'>
			<p>Your site is currently displaying a "Coming Soon" page. Once you are ready to launch your site <a href='<?php echo add_query_arg( array( 'mm_cs_launch' => true ) );?>'>click here</a>.</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'mm_cs_notice_display' );

function mm_cs_notice_launch_message() {
	?>
		<div class='updated'>
			<p>Congratulations. Your site is now live, <a target='_blank' href='<?php echo get_option( 'siteurl' );?>'>click here</a> to view it.</p>
		</div>
	<?php
}

function mm_cs_notice_launch() {
	if( isset( $_GET['mm_cs_launch'] ) ) {
		update_option( 'mm_coming_soon', 'false' );
		add_action( 'admin_notices', 'mm_cs_notice_launch_message' );
	}
}
add_action( 'admin_init', 'mm_cs_notice_launch' );

function mm_cs_load() {
	if( ! is_user_logged_in() ) {
		$coming_soon = get_option( 'mm_coming_soon', 'false' );
		if( 'true' === $coming_soon ) {
			mm_cs_content();
			die();
		}
	}
}
add_action( 'template_redirect', 'mm_cs_load' );

function mm_cs_meta() {
	$meta = wp_remote_get( 'http://mojomarketplace.com/api/v1/meta/landing_page' );
	if( is_wp_error( $meta ) ) {return;}
	if( isset( $meta['body'] ) && $meta['body'] != "" ) {
		return "<meta name='robots' content='noindex, nofollow' />";
	}
	return;
}

function mm_cs_content() {
	echo mm_minify( "
<!DOCTYPE html>
<html>
<head>
<title>" . get_option( 'blogname' ) . " &mdash; Coming Soon</title>
<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
" . mm_cs_meta() . "
<style type='text/css'>
body{
	background-color: #2D2A25;
	background-image: url( https://mojomarketplace.com/img/mojo-landing-bg.jpg );
	background-position: top right;
	background-repeat: no-repeat;
	font-family: 'Montserrat', sans-serif;
	color: #fff;
}
a{
	color: #fff;
	text-decoration: none;
}
#wrap{
	max-width: 900px;
	margin: 0 auto;
}
#logo{height: auto;width: 204px;padding: 30px 10px 10px 10px;max-width: 90%;}
.cta{
	background-color: #93C933;
	color: #35393A;
	padding: 10px 20px;
	text-decoration: none;
	margin: 10px 0;
	display: inline-block;
	border-radius: 3px;
}
.cta:hover{color: #fff;}
.content{
	margin: 5rem 0;
	font-size: 1.2rem;
	padding: 0 15px;
}
.ghost{ 
	border: 1px solid #fff;
	font-size: 22px;
	margin: 90px auto;
	max-width: 260px;
	padding: 20px 30px;
	text-align: center;
}
h1 span{
	color: #93C933;
}
footer{
	border-top: 1px solid #333;
}
footer a:hover{color: #ccc;}
footer .col{
	padding: 10px 4%;
	display: inline-block;
	vertical-align: top;
	max-width: 100%;
}
footer h2, footer h2 a{
	color: #93C933;
	font-size: 1rem;
	text-decoration: none;
}
footer ul{
	list-style: none;
	padding:0;
}
footer li{
	height: 26px;
}
</style>
</head>
<body>
<div id='wrap'>
	<a target='_blank' href='https://mojomarketplace.com?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=logo'><img src='https://www.mojomarketplace.com/img/mojo-retina-logo.png' id='logo' /></a>
	<div class='content'>
		<h1>I just installed WordPress <span>free</span> at</h1>
		<p>MOJO Marketplace &mdash; a leader in <strong>Themes</strong>, <strong>Plugins</strong>, and <strong>Professional Services</strong>&hellip;</p>
		<div class='ghost'>" . get_option( 'blogname' ) . " coming soon&hellip;</div>
	</div>
	<footer>
		<div class='col'>
			<h2><a href='https://www.mojomarketplace.com/themes/wordpress?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=wordpress_themes'>WordPress Themes</a></h2>
			<ul>
				<li><a target='_blank' href='https://www.mojomarketplace.com/themes/wordpress/blog?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=woocommerce_themes'>WooCommerce Themes</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/themes/wordpress/business?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=responsive_themes'>Responsive WordPress Themes</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/themes/wordpress/photography?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=business_themes'>Business WordPress Themes</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/themes/wordpress/restaurant?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=blog_themes'>Blog WordPress Themes</a></li>
			</ul>
		</div>
		<div class='col'>
			<h2><a href='https://www.mojomarketplace.com/services/all/wordpress?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=wordpress_services'>WordPress Services</a></h2>
			<ul>
				<li><a target='_blank' href='https://www.mojomarketplace.com/item/install-your-wordpress-theme?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=install_theme_service'>Install WordPress Theme</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/item/make-my-wordpress-site-look-like-the-theme-demo?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=theme_demo_service'>Make My Site Look Like the Demo</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/item/backup-your-wordpress-website?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=website_backup_service'>Backup Your WordPress Website</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/item/wordpress-theme-training?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=wp_theme_training_service'>WordPress Theme Training</a></li>
			</ul>
		</div>
		<div class='col'>
			<h2><a href='https://www.mojomarketplace.com/?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=about_mojo'>About MOJO</a></h2>
			<ul>
				<li><a target='_blank' href='https://www.mojomarketplace.com/explore?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=explore_mojo'>Explore MOJO</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/sellers?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=sell_w_mojo'>Sell with MOJO</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/affiliates?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=mojo_affiliates'>MOJO Affiliates</a></li>
				<li><a target='_blank' href='https://www.mojomarketplace.com/how-it-works/faq?utm_source=mojo_wp_plugin&utm_campaign=mojo_wp_plugin&utm_medium=plugin_landing&utm_content=faqs'>FAQs</a></li>
			</ul>
		</div>
	</footer>
</div>

</body>
</html>" );
}