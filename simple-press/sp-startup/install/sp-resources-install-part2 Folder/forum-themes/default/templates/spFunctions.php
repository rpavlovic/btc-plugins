<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Theme custom function file
#	Theme		:	Default
#	File		:	custom functions
#	Author		:	Simple:Press
#
#	The 'functions' file can be used for custom functions & is loaded with each template
#
# --------------------------------------------------------------------------------------

# A small javascript routine has been used to replace standard browser tooltips with
# more appealing graphics. You can turn this off by setting SP_TOOLTIPS to false.

if (!defined('SP_TOOLTIPS')) define('SP_TOOLTIPS', true);

# ------------------------------------------------------------------------------------------

# add support for child overlays
add_theme_support('sp-theme-child-overlays');

add_action('init', 'spDefault_textdomain');

# load the theme textdomain for tranlations
function spDefault_textdomain() {
	sp_theme_localisation('spDefault');
}

?>