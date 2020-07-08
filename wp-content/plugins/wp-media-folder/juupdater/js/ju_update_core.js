(function ($) {
    if (typeof ajaxurl === "undefined") {
        ajaxurl = updaterparams.ajaxurl;
    }
    $(document).ready(function () {
        if (typeof updaterparams.ju_base !== "undefined" && typeof updaterparams.ju_content_url !== "undefined") {
            if (typeof updaterparams.token !== "undefined" && updaterparams.token !== '') {

            } else {
                var listplugins = [
                    "wp-media-folder",
                    "wp-media-folder-addon",
                    "wp-media-folder-gallery-addon",
                    "wp-file-download",
                    "wp-file-download-addon",
                    "wp-team-display",
                    "wp-table-manager",
                    "wp-latest-post",
                    "wp-latest-posts-addon",
                    "wp-frontpage-news-pro-addon",
                    "wp-meta-seo-addon",
                    "wp-speed-of-light-addon"
                ];

                $('#update-plugins-table').find('tr input[type="checkbox"][name="checked[]"]').each(function () {
                    var ju_plugin_file = $(this).val();
                    var slug = ju_plugin_file.substr(ju_plugin_file.indexOf('/') + 1, ju_plugin_file.indexOf('.') - ju_plugin_file.indexOf('/') - 1);
                    if($.inArray(slug,listplugins) !== -1){
                        var link = updaterparams.ju_base + "index.php?option=com_juupdater&view=login&tmpl=component&site=" + updaterparams.ju_content_url + "&TB_iframe=true&width=300&height=305";
                        $(this).closest('tr').find('td.plugin-title').append('<p>Please <a class="thickbox ju_update" href="' + link + '">login</a> use your account on JoomUnited.com to update the plugin</p>');
                    }
                });
            }
        }

        var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
        var eventer = window[eventMethod];
        var messageEvent = eventMethod === "attachEvent" ? "onmessage" : "message";

        // Listen to message from child window
        eventer(messageEvent, function (e) {

            var res = e.data;
            if (typeof res !== "undefined" && typeof res.type !== "undefined" && res.type === "joomunited_login") {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'ju_add_token',
                        'token': res.token,
                        'wpmf_nonce': updaterparams.wpmf_nonce
                    },
                    success: function () {
                        window.location.assign(document.URL);
                    }
                });
            }
        }, false);
    });
}(jQuery));