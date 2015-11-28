(function ( $ ) {

	// WishListLighBox plugin with support for tabs
	// See membereship levels advanced search for usage
	$.fn.WishListLightBox = function( options ) {
		// This is the easiest way to have default options.
		var settings = $.extend({
			// These are the defaults.
			trigger  : null,
			autoopen : false
		}, options );

		var self = this;

		self.backdrop = self.next('.media-modal-backdrop');

		self.open_modal = function() {
			self.backdrop.show();
			self.show();
			self.focus();
			$('body').css('overflow','hidden');
		}
		self.close_modal = function() {
			self.backdrop.hide();
			self.hide();
			$('body').css('overflow','auto');
		}

		self.open_tab = function (tab) {
			self.find('.media-router a').removeClass('active');
			self.find('a[href='+tab+']').addClass('active');
			self.find('.panel').hide();
			$(tab).show();
		}

		self.oncancel = function(settings) {
			if('function' === typeof settings.oncancel) {
				settings.oncancel();
			}
		}
		self.init = function(settings) {
			if(settings.trigger != null) {
				settings.trigger.on('click', function(ev) {
					self.open_modal();
				});
			}

			// we need to set a tab index. Otherwise this
			// div will not recieve focus, thus will not fire
			// keydown events

			self.attr('tabindex', 0);

			// handle tab clicks
			self.on('click', '.media-router a',  function(ev) {
				self.open_tab( $(this).attr('href'));
			});

			// handle close click
			self.on('click', '.media-modal-close', function(ev) {
				self.close_modal();
				self.oncancel(settings);
			});

			// clicking outside the form closes the box
			self.backdrop.on('click', function(ev) {
				self.close_modal();
				self.oncancel(settings);
			});

			// escape closes the box
			self.on('keydown', function(event) {
				if ( 27 === event.which ) {
					self.close_modal();
					self.oncancel(settings);
				}
			});

			var tab = $(location).attr('hash');
			// open the correct tab if needed
			if($(tab).length > 0 && $.contains( self, $(tab))) {
				self.open_tab(tab);
			} else {
				self.find('.media-router a').get(0).click();
			}

			if(settings.autoopen) {
				self.open_modal();
			}
		}
		self.init(settings);
		return self;
	}

}(jQuery));

jQuery(document).ready(function($) {
	//for loading wlm feeds on dashboard
	jQuery(function($) {
		data = {
			action: 'wlm_feeds'
		}
		$.ajax({
			type: 'POST',
			url: admin_main_js.wlm_feed_url,
			data: data,
			success: function(response) {
				if($.trim(response) != ""){
					$('.wlrss-widget').html(response);
					$('#wlrss-postbox').show();
				}
			}
		});
	});

	//for email broadcast
	jQuery("#send-mail-queue").click(function(){
		var container = jQuery("#send-mail-queue-modal");
		var mails = [];
		var mailcount = 0;
		jQuery("#send-mail-queue-modal").modal({
	                overlayCss:{
	                	backgroundColor:'#222'
	            	},
	                containerCss:{
	                	backgroundColor:'#fff',
	                	border:'1px solid #ccc',
	                	padding:'8px',
	                	width: '400px'
	                },
	                onClose: function () {
						jQuery.modal.close();
						window.location.assign(document.URL);
					}
		});
		//get the number of emails in queue
		var data = {
			WishListMemberAction: 'EmailBroadcast',
			EmailBroadcastAction: 'GetEmailQueue'
		}

		jQuery.ajax({
			type: 'POST',
			url: admin_main_js.wlm_broacast_url,
			data: data,
			success: function(response) {
				mails = jQuery.parseJSON(response);
				if(mails){
					mailcount = mails.length;
					container.find(".email-queue-count-holder").html(mailcount);
					container.find(".modal-sent-percent").html("0%");

					var sent = 0;
					var failed = 0;
					var pcnt = 0;
					jQuery.each( mails, function( key, value ) {

						data = {
							WishListMemberAction: 'EmailBroadcast',
							EmailBroadcastAction: 'SendEmailQueue',
							QueueID: value
						}

						jQuery.ajax({
							type: 'POST',
							url: admin_main_js.wlm_broacast_url,
							data: data,
							success: function(response) {

								if(response == true || response == "true" ){
									sent++;
								}else{
									failed++;
								}
							},
							error:function() {
								failed++;
							},
							complete:function(){
								pcnt = (sent + failed) / mailcount;
								pcnt = pcnt * 100;
								pcnt = Math.round(pcnt);
								container.find(".modal-sent-count").html(sent);
								container.find(".modal-failed-count").html(failed);
								container.find(".modal-sent-percent").html(pcnt +"%");
								if(pcnt >= 100){
									container.find(".simplemodal-close").show();
									container.find(".waiting-msg").hide();
								}
							}
						});
					});

				}else{
					alert("No Emails found in queue.");
				}
			},
			error:function() {
				alert("An Error occured while processing the email queue. Please refresh the page and try again.");
			}
		});

	});

});