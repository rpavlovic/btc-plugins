jQuery(document).ready(function($) {

	wlmtnmcelbox_vars.shortcode_text = jQuery('#wlmtnmcelbox').find('.wlmtnmcelbox-preview-text');
	wlmtnmcelbox_vars.insertcode_button = jQuery('#wlmtnmcelbox').find('.wlifcon-modal-shortcodes-insertcode');
	wlmtnmcelbox_vars.reverse = jQuery('#wlmtnmcelbox').find('.wlmtnmcelbox-reverse');
	wlmtnmcelbox_vars.content_text = jQuery('#wlmtnmcelbox').find('.wlmtnmcelbox-content-text');
	wlmtnmcelbox_vars.content_levels = jQuery('#wlmtnmcelbox').find('.wlmtnmcelbox-levels');

	wlmtnmcelbox_vars.show_lightbox = function() {
		jQuery('#wlmtnmcelbox').show();
		jQuery('#wlmtnmcelbox').find('.media-modal').show();
		jQuery('#wlmtnmcelbox').find('.media-modal-backdrop').show();
		var t = "";
		if ( typeof tinyMCE == 'object' ) {
			t = tinyMCE.activeEditor.selection.getContent();
		}
		wlmtnmcelbox_vars.content_text.val(t);
		wlmtnmcelbox_vars.shortcode_text.val('');
	}

	wlmtnmcelbox_vars.hide_shortcodes = function() {
		jQuery('#wlmtnmcelbox').hide();
		jQuery('#wlmtnmcelbox').find('.media-modal').hide();
		jQuery('#wlmtnmcelbox').find('.media-modal-backdrop').hide();
		wlmtnmcelbox_vars.shortcode_text.val('');
		wlmtnmcelbox_vars.content_text.val('')
	}

	wlmtnmcelbox_vars.update_preview = function() {
		var selected = "";
		jQuery(".wlmtnmcelbox-levels :selected").each(function(){
			selected = selected + (selected == "" ? "":"|") + jQuery.trim(jQuery(this).html());
		});
		if ( selected == "") {
			wlmtnmcelbox_vars.shortcode_text.val('');
		 	return;
		}
		var reverse = wlmtnmcelbox_vars.reverse.attr('checked') ? '!' : 'wlm_';
		var text = "[" +reverse +"private '" +selected +"']" +wlmtnmcelbox_vars.content_text.val() +"[/" +reverse +"private]";
		wlmtnmcelbox_vars.shortcode_text.val(text);
	}

	jQuery('#wlmtnmcelbox').find('.media-modal-close').on('click', function(ev) {
		ev.preventDefault();
		wlmtnmcelbox_vars.hide_shortcodes();
	});

	jQuery('body').on('keydown', function(event) {
		if ( 27 === event.which ) {
			wlmtnmcelbox_vars.hide_shortcodes();
		}
	});

	wlmtnmcelbox_vars.content_levels.chosen({width:'100%', display_disabled_options:false, });
	wlmtnmcelbox_vars.content_levels.chosen().change(function(){
		$str_selected = jQuery(this).val();
		if( $str_selected != null ){
			$pos = $str_selected.lastIndexOf("all");
			if($pos >= 0){
				jQuery(this).find('option').each(function() {
					if(jQuery(this).val() == "all"){
						jQuery(this).prop("selected",false);
					}else{
						jQuery(this).prop("selected","selected");
					}
					jQuery(this).trigger("chosen:updated");
				});
			}
		}
		wlmtnmcelbox_vars.update_preview();
	});

	wlmtnmcelbox_vars.reverse.on('click', wlmtnmcelbox_vars.update_preview );

	wlmtnmcelbox_vars.content_text.on('keyup', wlmtnmcelbox_vars.update_preview );

	jQuery('#wlmtnmcelbox .wlmtnmcelbox-insertcode').on('click', function(e) {
		var text = wlmtnmcelbox_vars.shortcode_text.val().replace(/\r\n|\r|\n/g,"<br/>");
	    if (tinyMCE && tinyMCE.activeEditor && text != '') {
	   		tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
	   		wlmtnmcelbox_vars.shortcode_text.val('');
	    }
	    wlmtnmcelbox_vars.hide_shortcodes();
	});
});
