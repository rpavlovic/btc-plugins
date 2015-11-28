<?php
$wpm_levels = $this->GetOption('wpm_levels');
?>
<div style='display: none !important;' id='wlmtnmcelbox'>
	<div class="media-modal wp-core-ui" style="display: none !important;">
		<a class="media-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>
		<div class="media-frame-title"><h1>Private Tags</h1></div>
		<div class="media-modal-content">
			<!-- Main Contend Starts -->
			<div class="wlmtnmcelbox-content">

				<!-- Options -->
				<div class="options-holder">
						<p class="modal-field-label">
							<input type='checkbox' value='1' name='wlmtnmcelbox-reverse' class='wlmtnmcelbox-reverse' /> Reverse Private Tags
						</p>
						<p class="modal-field-label">Membership Levels:</p>
						<select class="wlmtnmcelbox-levels" multiple="multiple" data-placeholder=' ' >
						<option value="all">Select All</option>
						<?php foreach( $wpm_levels as $sku => $level ){
							if (is_numeric($sku)){
								
								$levelname=$level['name'];
								$levelname=str_replace("%","&#37;",$levelname);
								//$levelname = htmlentities($levelname, ENT_QUOTES);
								
								
								?>
								<option value="<?php echo $sku; ?>"><?php echo trim($levelname); ?></option>
								<?php 
								}
							}
						?>
						</select>
						<p class="modal-field-label">Content:</p>
						<textarea name="wlmtnmcelbox-content-text" class="wlmtnmcelbox-content-text"></textarea>
				</div>
				<!-- Options Ends -->

				<!-- Preview -->
				<div class="wlmtnmcelbox-preview">
					<div class="wlmtnmcelbox-preview-msg" >
						<input tab="display_details" type="button" class="button button-primary wlmtnmcelbox-insertcode" value="<?php _e("Insert Mergecode", "wishlist-member")?>" />
						Shortcode Preview:
					</div>
					<textarea name="wlmtnmcelbox-preview-text"  class="wlmtnmcelbox-preview-text"></textarea>
				</div>
				<!-- Preview Ends -->
			</div>
			<!-- Main Contend Ends -->
		</div>

	</div>
	<div class="media-modal-backdrop" style="display: none !important;"></div>
</div>