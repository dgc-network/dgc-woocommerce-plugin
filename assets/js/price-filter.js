jQuery(document).ready(function($) {
	if (typeof dgc_price_filter_params === 'undefined') {
		return false;
	}

	// price slider
	dgcInitPriceSlider = function() {
		$('#dgc-noui-slider').ionRangeSlider({
			type: "double",
			min: 0,
			max: 1000000,
			step: 100,
			onFinish: function (data) {
				// var params = dgcGetUrlVars();

				// remove this parameter if set value is equal to max val
				if (data.to === data.max) {
					history.pushState({}, '', dgcRemoveQueryStringParameter('max-price'));
				} else {
					dgcUpdateQueryStringParameter('max-price', data.to);
				}

        		// remove this parameter if set value is equal to max val
				if (data.from === data.min) {
					history.pushState({}, '', dgcRemoveQueryStringParameter('min-price'));
				} else {
					dgcUpdateQueryStringParameter('min-price', data.from);
				}

				// filter products without reinitializing price slider
				dgcFilterProducts();
			}
		});
	}

    // position currency symbol
	// $(document).bind('update_dgc_slider_vals', function(event, value_holder, value) {
	//     // if WooCommerce Currency Switcher plugin is activated
	//     if (typeof woocs_current_currency !== 'undefined') {
	//     	if (woocs_current_currency.position === 'left') {
	//     		$(value_holder).html(woocs_current_currency.symbol + value);
	//     	} else if (woocs_current_currency.position === 'left_space') {
	//     		$(value_holder).html(woocs_current_currency.symbol + ' ' + value);
	//     	} else if (woocs_current_currency.position === 'right') {
	//     		$(value_holder).html(value + woocs_current_currency.symbol);
	//     	} else if (woocs_current_currency.position === 'right_space') {
	//     		$(value_holder).html(value + ' ' + woocs_current_currency.symbol);
	//     	}
	//     } else {
	// 	    if (dgc_price_filter_params.currency_pos === 'left') {
	// 	    	$(value_holder).html(dgc_price_filter_params.currency_symbol + value);
	// 	    } else if (dgc_price_filter_params.currency_pos === 'left_space') {
	// 	    	$(value_holder).html(dgc_price_filter_params.currency_symbol + ' ' + value);
	// 	    } else if (dgc_price_filter_params.currency_pos === 'right') {
	// 	    	$(value_holder).html(value + dgc_price_filter_params.currency_symbol);
	// 	    } else if (dgc_price_filter_params.currency_pos === 'right_space') {
	// 	    	$(value_holder).html(value + ' ' + dgc_price_filter_params.currency_symbol);
	// 	    }
	//     }
	// });

	// initialize price slider
	dgcInitPriceSlider();
	$(document).on('wcapf:widget_update', dgcInitPriceSlider);
});