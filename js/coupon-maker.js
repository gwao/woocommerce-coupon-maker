/*global jQuery, document, window, couponMaker, console*/

(function ($) {
    "use strict";

    var serialArrayToHash = function (array) {
        var i, data, hash;
        hash = {};
        for (i = 0; i < array.length; i += 1) {
            data = array[i];
            hash[data.name] = data.value;
        }
        return hash;
    };

    $(document).ready(function () {
        var $form = $("#coupon-maker"),
            $notifier = $form.find('.notifier'),
            $formBody = $form.find('.form'),
            $couponElements = $form.find(".form-control, .btn"),
            formData,
            handleCouponMaker = function (e) {
                formData = serialArrayToHash($form.serializeArray());
                formData.action = 'coupon_maker';

                $couponElements.prop("disabled", true);
                $.ajax({
                    type: 'POST',
                    url: couponMaker.ajaxURL,
                    data: formData,
                    dataType: 'json',
                    success: function (data, status) {
                        $notifier.addClass('alert-success').text(data.message).removeClass('hidden');
                        $couponElements.prop("disabled", false);
                    },
                    error: function (xhr, status) {
                        $couponElements.prop("disabled", false);
                    }
                });
                e.preventDefault();
            };

        $form.submit(handleCouponMaker);
    });

}(jQuery));
