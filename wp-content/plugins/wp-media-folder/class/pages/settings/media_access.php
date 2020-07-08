<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
    <div id="user_media_access" class="tab-content">
        <div class="content-box content-wpmf-media-access">
            <div class="ju-settings-option">
                <div class="wpmf_row_full">
                    <input type="hidden" name="wpmf_active_media" value="0">
                    <label data-alt="<?php esc_html_e('Once user upload some media, he will have a
             personal folder, can be per User or per User Role', 'wpmf'); ?>"
                           class="ju-setting-label text"><?php esc_html_e('Media access by User or User Role', 'wpmf') ?></label>
                    <div class="ju-switch-button">
                        <label class="switch">
                            <input type="checkbox" name="wpmf_active_media"
                                   id="cb_option_active_media" value="1"
                                <?php
                                if (isset($wpmf_active_media) && (int) $wpmf_active_media === 1) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ju-settings-option wpmf_right m-r-0">
                <div class="wpmf_row_full">
                    <label data-alt="<?php esc_html_e('Automatically create a
             folder per User or per WordPress User Role', 'wpmf'); ?>"
                           class="ju-setting-label text"><?php esc_html_e('Folder automatic creation', 'wpmf') ?></label>
                    <label class="line-height-50 wpmf_right p-r-20">
                        <select name="wpmf_create_folder">
                            <option
                                <?php selected($wpmf_create_folder, 'user'); ?> value="user">
                                <?php esc_html_e('By user', 'wpmf') ?>
                            </option>
                            <option
                                <?php selected($wpmf_create_folder, 'role'); ?> value="role">
                                <?php esc_html_e('By role', 'wpmf') ?>
                            </option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="ju-settings-option">
                <h4 data-alt="<?php esc_html_e('Select the root folder to store all user media and
             folders (only if Media by User or User Role is activated above)', 'wpmf'); ?>"
                    class="ju-setting-label text"><?php esc_html_e('User media folder root', 'wpmf') ?></h4>
                <div class="wpmf_row_full">
                    <span id="wpmfjaouser"></span>
                </div>
            </div>
        </div>
    </div>

    <div id="file_design" class="tab-content">
        <div class="content-box content-wpmf-media-access">
            <div class="ju-settings-option">
                <div class="wpmf_row_full">
                    <input type="hidden" name="wpmf_option_singlefile" value="0">
                    <label data-alt="<?php esc_html_e('Apply single file design with below
             parameters when insert file to post / page', 'wpmf'); ?>" class="ju-setting-label text">
                        <?php esc_html_e('Enable single file design', 'wpmf') ?></label>
                    <div class="ju-switch-button">
                        <label class="switch">
                            <input type="checkbox" name="wpmf_option_singlefile"
                                   value="1"
                                <?php
                                if (isset($option_singlefile) && (int) $option_singlefile === 1) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="p-d-20 ju-settings-option wpmf_width_100">
                <h4 style="font-size: 20px"><?php esc_html_e('Color Theme', 'wpmf') ?></h4>
                <div class="wpmf_group_color wpmf_width_100">
                    <div class="ju-settings-option wpmf-no-shadow wpmf_width_20">
                        <label class="wpmf_width_100 p-b-20 wpmf_left text label_text"
                               for="singlebg"><?php esc_html_e('Background color', 'wpmf') ?></label>
                        <label>
                            <input name="wpmf_color_singlefile[bgdownloadlink]" type="text"
                                   value="<?php echo esc_attr($wpmf_color_singlefile->bgdownloadlink) ?>"
                                   class="inputbox input-block-level wp-color-field-bg wp-color-picker">
                        </label>
                    </div>

                    <div class="ju-settings-option wpmf-no-shadow wpmf_width_20">
                        <label class="wpmf_width_100 p-b-20 wpmf_left text label_text"
                               for="singlebg"><?php esc_html_e('Hover color', 'wpmf') ?></label>
                        <label>
                            <input name="wpmf_color_singlefile[hvdownloadlink]" type="text"
                                   value="<?php echo esc_attr($wpmf_color_singlefile->hvdownloadlink) ?>"
                                   class="inputbox input-block-level wp-color-field-hv wp-color-picker">
                        </label>
                    </div>

                    <div class="ju-settings-option wpmf_width_20 wpmf-no-shadow">
                        <label class="wpmf_width_100 p-b-20 wpmf_left text label_text"
                               for="singlebg"><?php esc_html_e('Font color', 'wpmf') ?></label>
                        <label>
                            <input name="wpmf_color_singlefile[fontdownloadlink]" type="text"
                                   value="<?php echo esc_attr($wpmf_color_singlefile->fontdownloadlink) ?>"
                                   class="inputbox input-block-level wp-color-field-font wp-color-picker">
                        </label>
                    </div>

                    <div class="ju-settings-option wpmf_width_20 wpmf-no-shadow">
                        <label class="wpmf_width_100 p-b-20 wpmf_left text label_text"
                               for="singlebg"><?php esc_html_e('Hover font color', 'wpmf') ?></label>
                        <label>
                            <input name="wpmf_color_singlefile[hoverfontcolor]" type="text"
                                   value="<?php echo esc_attr($wpmf_color_singlefile->hoverfontcolor) ?>"
                                   class="inputbox input-block-level wp-color-field-hvfont wp-color-picker">
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php
wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');
?>