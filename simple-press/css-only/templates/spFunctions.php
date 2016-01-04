<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Theme custom function file
#	Theme		:	css-only
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

add_action('init', 'spcssonly_textdomain');

# load the theme textdomain for tranlations
function spcssonly_textdomain() {
	sp_theme_localisation('spcssonly');
}

?>