(function($) {
    var App = {
        toggleSocialbar: function() {
            $toggleIcon = $('<div class="toggle-icon"></div>');

            $toggleIcon.insertAfter($('.tpl-socail-fl .tpl-social-share-bar'));
            $width = $('.tpl-socail-fl .tpl-social-share-bar').width();

            $toggleIcon.on('click', function() {
                $('.tpl-social-share-bar').parent('.tpl-socail-fl').toggleClass('hide-bar');
            })
        }
    }

    App.init = function() {
        this.toggleSocialbar();
    }

	$(function() {
		App.init();
	});
})(jQuery);