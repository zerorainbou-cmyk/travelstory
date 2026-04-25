(function($){
	"use strict";

	$(window).on('elementor/frontend/init', function () {
        $('.ovabrw-search .ovabrw-search-form').each( function() {
            const that = $(this);

            // Guests picker
            const guestspicker = that.find('.ovabrw-guestspicker');

            // Guests picker controls
            let guestsPickerControl = $(this).find('.guestspicker-control')
            guestspicker.on('click', function() {
                guestsPickerControl = $(this).closest('.guestspicker-control').toggleClass('active');
            });

            $(window).click( function(e) {
                const guestsPickerContent = $('.ovabrw-guestspicker-content');
                if ( !guestspicker.is(e.target) && guestspicker.has(e.target).length === 0 && !guestsPickerContent.is(e.target) && guestsPickerContent.has(e.target).length === 0 ) {
                    guestsPickerControl.removeClass('active');
                }
            });

            const minus = that.find('.minus');
            minus.on('click', function() {
                gueststotal($(this), 'sub');
            });

            const plus = that.find('.plus');
            plus.on('click', function() {
                gueststotal($(this), 'sum');
            });

            // select 2
            $('#brw-destinations-select-box, .brw_custom_taxonomy_dropdown').select2({ 
                width: '100%'
            });

        });

        function gueststotal( that, cal ) {
            const guestsButton = that.closest('.guests-button');

            // Guest input
            const input = guestsButton.find('input[type="text"]');

            // Guest data
            let value = input.val();
            const min = input.attr('min');
            const max = input.attr('max');

            if ( cal == 'sub' && parseInt(value) > parseInt(min) ) {
                input.val(parseInt(value) - 1);
            }

            if ( cal == 'sum' && parseInt(value) < parseInt(max) ) {
                input.val(parseInt(value) + 1);
            }

            const guestsPickerControl = that.closest('.guestspicker-control');

            // Adults
            const adults = parseInt(guestsPickerControl.find('.ovabrw_adults').val()) || 0;
            
            // Children
            const children = parseInt(guestsPickerControl.find('.ovabrw_childrens').val()) || 0;

            // Babies
            const babies = parseInt(guestsPickerControl.find('.ovabrw_babies').val()) || 0;

            // Guests total
            const gueststotal = guestsPickerControl.find('.gueststotal');
            if ( gueststotal ) {
                gueststotal.text(adults + children + babies);
            }
        }

	});
})(jQuery);