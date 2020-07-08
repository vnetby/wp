(function ($) {
    var status_regenthumbs = false;
    var current_page_regenthumbs = 1;
    $(document).ready(function ($) {
        /* click 'Regenerate all image thumbnails' button */
        $('.btn_regenerate_thumbnails').on('click', function () {
            status_regenthumbs = true;
            if (status_regenthumbs) {
                $(this).html(wpmfoption.l18n.continue).hide();
                $('.btn_stop_regenerate_thumbnails').show();
                wpmf_regenthumbs(current_page_regenthumbs);
            }
        });

        /* stop regenerate thumbnails */
        $('.btn_stop_regenerate_thumbnails').on('click', function () {
            status_regenthumbs = false;
            $('.btn_regenerate_thumbnails').show();
            $(this).hide();
        });

        /**
         * Regenerate thumbnails
         * @param paged current page
         */
        var wpmf_regenthumbs = function (paged) {
            if (!status_regenthumbs) {
                return;
            }
            $('.process_gennerate_thumb_full').show();
            $('.img_thumbnail').show();
            $('.right_wrap_render_thumbnail').removeClass('wpmf_width_100 wpmf-no-margin');
            $('.btn_regenerate_thumbnails ').removeClass('wpmf_width_100');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: "wpmf_regeneratethumbnail",
                    paged: paged,
                    wpmf_nonce: wpmf.vars.wpmf_nonce
                },
                success: function (res) {
                    var w = $('.process_gennerate_thumb').data('w');
                    /* Check status and set progress bar */
                    if (res.status === 'ok') {
                        current_page_regenthumbs = 1;
                        $('.btn_regenerate_thumbnails').html(wpmfoption.l18n.regenerate_all_image_lb).show();
                        $('.process_gennerate_thumb').data('w', 0).css('width', '100%');
                        $('.process_gennerate_thumb_full span').html('100%');
                        $('.btn_stop_regenerate_thumbnails').hide();
                    }

                    /* Check status and set progress bar */
                    if (res.status === 'limit') {
                        current_page_regenthumbs = parseInt(paged) + 1;
                        if (typeof res.percent !== "undefined") {
                            var new_w = parseFloat(w) + parseFloat(res.percent);
                            if (new_w > 100)
                                new_w = 100;
                            $('.process_gennerate_thumb_full').show();
                            $('.process_gennerate_thumb').data('w', new_w).css('width', new_w + '%');
                            $('.process_gennerate_thumb_full span').html(parseInt(new_w) + '%');
                        }
                        wpmf_regenthumbs(current_page_regenthumbs);
                    }

                    if (typeof res.url !== "undefined" && typeof res.url[0] !== "undefined") {
                        $('.img_thumbnail').attr('src', res.url[0]);
                    }
                    $('.result_gennerate_thumb').show().append(res.success);
                }
            });
        };
    });
})(jQuery);