/*!
 * strength.js
 * Original author: @aaronlumsden
 * Further changes, comments: @aaronlumsden
 * Licensed under the MIT license
 */
;(function ( $, window, document, undefined ) {

	var pluginName = "strength",
		defaults = {
			strengthClass: 'strength',
			strengthMeterClass: 'strength_meter',
			strengthButtonClass: 'button_strength',
			strengthButtonText: 'Show Password',
			strengthButtonTextToggle: 'Hide Password'
		};

	// $('<style>body { background-color: red; color: white; }</style>').appendTo('head');

	function Plugin( element, options ) {
		this.element = element;
		this.$elem = $(this.element);
		this.options = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	Plugin.prototype = {

		init: function() {


			var characters = 0;
			var capitalletters = 0;
			var loweletters = 0;
			var number = 0;
			var special = 0;

			var upperCase= new RegExp('[A-Z]');
			var lowerCase= new RegExp('[a-z]');
			var numbers = new RegExp('[0-9]');
			var specialchars = new RegExp('([!,%,&,@,#,$,^,*,?,_,~])');

			function GetPercentage(a, b) {
				return ((b / a) * 100);
			}

			function check_strength(thisval,thisid){
				if (thisval.length > 8) { characters = 1; } else { characters = 0; };
				if (thisval.match(upperCase)) { capitalletters = 1} else { capitalletters = 0; };
				if (thisval.match(lowerCase)) { loweletters = 1}  else { loweletters = 0; };
				if (thisval.match(numbers)) { number = 1}  else { number = 0; };

				var total = characters + capitalletters + loweletters + number + special;
				var totalpercent = GetPercentage(7, total).toFixed(0);

				if (!thisval.length) {total = -1;}

				get_total(total,thisid);
			}

			function get_total(total,thisid){

				var wrap = $('div .password-strength'),
					thismeter = $('div[data-meter="'+thisid+'"]'),
					tooltip = $('div .tooltip.strength');

				if (total <= 1) {
					wrap.addClass('tooltipActive');
					thismeter.removeClass();
					thismeter.addClass('veryweak');
					tooltip.show();
					tooltip.find('.label-strength').html('Very weak');
				} else if (total == 2){
					thismeter.removeClass();
					thismeter.addClass('weak');
					tooltip.show();
					tooltip.find('.label-strength').html('Weak');
				} else if(total == 3){
					thismeter.removeClass();
					thismeter.addClass('medium');
					tooltip.show();
					tooltip.find('.label-strength').html('Medium');
				} else {
					thismeter.removeClass();
					thismeter.addClass('strong');
					tooltip.show();
					tooltip.find('.label-strength').html('Strong');
				}

				if (total == -1) { thismeter.removeClass(); tooltip.hide(); wrap.removeClass('tooltipActive');}
			}





			var isShown = false;

			thisid = this.$elem.attr('id');

			this.$elem.addClass(this.options.strengthClass).attr('data-password',thisid).after('<input style="display:none" class="'+this.options.strengthClass+'" data-password="'+thisid+'" type="text" name="" value=""><div class="'+this.options.strengthMeterClass+'"><div data-meter="'+thisid+'"></div></div>');

			this.$elem.bind('keyup keydown', function(event) {
				thisval = $('#'+thisid).val();
				$('input[type="text"][data-password="'+thisid+'"]').val(thisval);
				check_strength(thisval,thisid);

			});

			$('input[type="text"][data-password="'+thisid+'"]').bind('keyup keydown', function(event) {
				thisval = $('input[type="text"][data-password="'+thisid+'"]').val();
				console.log(thisval);
				$('input[type="password"][data-password="'+thisid+'"]').val(thisval);
				check_strength(thisval,thisid);

			});
		},

		yourOtherFunction: function(el, options) {
			// some logic
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function ( options ) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin( this, options ));
			}
		});
	};

})( jQuery, window, document );