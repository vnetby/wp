<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="content-box content-wpmf-regen-thumbnail">
    <div class="ju-settings-option wpmf_width_100 p-d-20">
        <div style="width: 30%;text-align: center;float:left; max-height:500px; overflow: hidden;">
            <img class="img_thumbnail" src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/default.png') ?>">
        </div>


        <div class="right_wrap_render_thumbnail wpmf_width_100 wpmf-no-margin">
            <button type="button"
                    class="ju-button orange-button waves-effect wpmf_width_100 waves-light btn_regenerate_thumbnails stop"><?php esc_html_e('Regenerate all image thumbnails', 'wpmf') ?></button>
            <button type="button"
                    class="ju-button orange-button no-background waves-effect waves-light btn_stop_regenerate_thumbnails"
            ><?php esc_html_e('Stop the process', 'wpmf') ?></button>

            <div class="process_gennerate_thumb_full" style="">
                <div class="process_gennerate_thumb" data-w="0"></div>
                <span>0%</span>
            </div>

            <div class="result_gennerate_thumb">
                <h3><?php esc_html_e('Information', 'wpmf') ?></h3>
            </div>
        </div>
    </div>
</div>
