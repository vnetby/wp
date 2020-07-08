(function ($) {
    if (typeof ajaxurl === "undefined") {
        ajaxurl = wpmf.vars.ajaxurl;
    }

    $(document).ready(function () {
        /**
         * Import nextgen gallery
         * @param doit true or false
         * @param button
         */
        var importWpmfgallery = function (doit, button) {
            jQuery(button).closest("p").find(".spinner").show().css({"visibility": "visible"});
            jQuery.post(ajaxurl, {
                action: "import_gallery",
                doit: doit,
                wpmf_nonce: wpmf.vars.wpmf_nonce
            }, function (response) {
                if (response === "error time") {
                    jQuery("#wmpfImportgallery").click();
                } else {
                    jQuery(button).closest("div#wpmf_error").hide();
                    if (doit === true) {
                        jQuery("#wpmf_error").after("<div class='updated'> <p><strong>NextGEN galleries successfully imported in WP Media Folder</strong></p></div>");
                    }
                }
            });
        };

        /**
         * import nextgen gallery
         */
        $('#wmpfImportgallery').on('click', function () {
            var $this = $(this);
            importWpmfgallery(true, $this);
        });

        /**
         * cancel import gallery button
         */
        $('.wmpfNoImportgallery').on('click', function () {
            var $this = $(this);
            importWpmfgallery(false, $this);
        });
    });
}(jQuery));