jQuery(document).ready(function($) {
  // dimensions slider
  dgcInitDimensionsSlider = function() {
    $('.dgc-dimensions-slider').each(function () {
      var filter_key = $(this).attr('name');

      $(this).ionRangeSlider({
        type: "double",
        grid: true,
        onFinish: function (data) {
          // remove this parameter if set value is equal to max val
          if (data.to === data.max) {
            history.pushState({}, '', dgcRemoveQueryStringParameter('max-' + filter_key));
          } else {
            dgcUpdateQueryStringParameter('max-' + filter_key, data.to);
          }

          // remove this parameter if set value is equal to max val
          if (data.from === data.min) {
            history.pushState({}, '', dgcRemoveQueryStringParameter('min-' + filter_key));
          } else {
            dgcUpdateQueryStringParameter('min-' + filter_key, data.from);
          }

          // filter products without reinitializing price slider
          dgcFilterProducts();
        }
      });
    });
  }

  // initialize price slider
  dgcInitDimensionsSlider();
	$(document).on('wcapf:widget_update', dgcInitDimensionsSlider);
});