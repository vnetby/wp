<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div id="rename_on_upload" class="tab-content">
    <div class="content-box content-wpmf-files-folders">
        <div class="ju-settings-option">
            <div class="wpmf_row_full">
                <input type="hidden" name="wpmf_media_rename" value="0">
                <label data-alt="<?php esc_html_e('Tag available: {sitename} - {foldername} - {date} - {original name} .
             Note: # will be replaced by increasing numbers', 'wpmf') ?>"
                       class="ju-setting-label text"><?php esc_html_e('Activate media rename on upload', 'wpmf') ?>
                </label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" name="wpmf_media_rename" value="1"
                            <?php
                            if (isset($wpmf_media_rename) && (int) $wpmf_media_rename === 1) {
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
                <label for="wpmf_patern" class="ju-setting-label text"
                       data-alt="<?php esc_html_e('Tag avaiable: {sitename} - {foldername} - {date} - {original name} .
                    Note: # will be replaced by increasing numbers', 'wpmf') ?>" style="min-width: 50px">
                    <?php esc_html_e('Pattern', 'wpmf') ?>
                </label>
                <label class="line-height-50">
                    <input type="text" name="wpmf_patern"
                           id="wpmf_patern" class="regular-text wpmf_width_60" value="<?php echo esc_attr($wpmf_pattern); ?>">
                </label>

            </div>
        </div>

        <h3 class="title_h3"><?php esc_html_e('Format Media Titles', 'wpmf'); ?></h3>
        <h4 class="title_h4">
            <label data-alt="<?php esc_html_e('Remove characters automatically on media upload', 'wpmf'); ?>"
                   class="text"><?php esc_html_e('Remove Characters', 'wpmf') ?>
            </label>
        </h4>

        <div class="ju-settings-option wpmf-no-shadow">
            <div class="wpmf_row_full">
                <input type="hidden" name="format_mediatitle" value="0">
                <label data-alt="<?php esc_html_e('Additionally to the file rename on upload pattern, you can apply rename options below', 'wpmf') ?>"
                       class="ju-setting-label text"><?php esc_html_e('Activate format media title', 'wpmf') ?>
                </label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" name="format_mediatitle" value="1"
                            <?php
                            if (isset($format_mediatitle) && (int) $format_mediatitle === 1) {
                                echo 'checked';
                            }
                            ?>
                        >
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="wpmf_row_full wpmf-no-margin">
            <div class="wrap_left">
                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <input type="hidden" name="wpmf_options_format_title[hyphen]" value="0">
                        <div class="pure-checkbox">
                            <input id="wpmf_hyphen" type="checkbox" name="wpmf_options_format_title[hyphen]"
                                <?php checked($opts_format_title['hyphen'], 1) ?> value="1">
                            <label for="wpmf_hyphen" class="ju-setting-label"><?php esc_html_e('Hyphen', 'wpmf') ?>
                                -</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[period]" value="0">
                            <input id="wpmf_period" type="checkbox"
                                   name="wpmf_options_format_title[period]"
                                <?php checked($opts_format_title['period'], 1) ?> value="1">
                            <label for="wpmf_period" class="ju-setting-label"><?php esc_html_e('Period', 'wpmf') ?>
                                .</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[plus]" value="0">
                            <input id="wpmf_plus" type="checkbox"
                                   name="wpmf_options_format_title[plus]"
                                <?php checked($opts_format_title['plus'], 1) ?> value="1">
                            <label for="wpmf_plus" class="ju-setting-label"><?php esc_html_e('Plus', 'wpmf') ?> +</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[ampersand]" value="0">
                            <input id="wpmf_ampersand" type="checkbox"
                                   name="wpmf_options_format_title[ampersand]"
                                <?php checked($opts_format_title['ampersand'], 1) ?> value="1">
                            <label for="wpmf_ampersand" class="ju-setting-label"><?php esc_html_e('Ampersand', 'wpmf') ?>
                                @</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[square_brackets]" value="0">
                            <input id="wpmf_square_brackets" type="checkbox"
                                   name="wpmf_options_format_title[square_brackets]"
                                <?php checked($opts_format_title['square_brackets'], 1) ?> value="1">
                            <label for="wpmf_square_brackets"
                                   class="ju-setting-label"><?php esc_html_e('Square brackets', 'wpmf') ?> []</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[curly_brackets]" value="0">
                            <input id="wpmf_curly_brackets" type="checkbox"
                                   name="wpmf_options_format_title[curly_brackets]"
                                <?php checked($opts_format_title['curly_brackets'], 1) ?> value="1">
                            <label for="wpmf_curly_brackets"
                                   class="ju-setting-label"><?php esc_html_e('Curly brackets', 'wpmf') ?> {}</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[underscore]" value="0">
                            <input id="wpmf_underscore" type="checkbox"
                                   name="wpmf_options_format_title[underscore]"
                                <?php checked($opts_format_title['underscore'], 1) ?> value="1">
                            <label for="wpmf_underscore" class="ju-setting-label"><?php esc_html_e('Underscore', 'wpmf') ?>
                                _</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wrap_right">
                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[tilde]" value="0">
                            <input id="wpmf_tilde" type="checkbox"
                                   name="wpmf_options_format_title[tilde]"
                                <?php checked($opts_format_title['tilde'], 1) ?> value="1">
                            <label for="wpmf_tilde" class="ju-setting-label"><?php esc_html_e('Tilde', 'wpmf') ?> ~</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[hash]" value="0">
                            <input id="wpmf_hash" type="checkbox"
                                   name="wpmf_options_format_title[hash]"
                                <?php checked($opts_format_title['hash'], 1) ?> value="1">
                            <label for="wpmf_hash" class="ju-setting-label"><?php esc_html_e('Hash/pound', 'wpmf') ?>
                                #</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[number]" value="0">
                            <input id="wpmf_number" type="checkbox"
                                   name="wpmf_options_format_title[number]"
                                <?php checked($opts_format_title['number'], 1) ?> value="1">
                            <label for="wpmf_number" class="ju-setting-label"><?php esc_html_e('All numbers', 'wpmf') ?>
                                0-9</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[round_brackets]" value="0">
                            <input id="wpmf_round_brackets" type="checkbox"
                                   name="wpmf_options_format_title[round_brackets]"
                                <?php checked($opts_format_title['round_brackets'], 1) ?> value="1">
                            <label for="wpmf_round_brackets"
                                   class="ju-setting-label"><?php esc_html_e('Round brackets', 'wpmf') ?> ()</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[alt]" value="0">
                            <input id="wpmf_alt" type="checkbox"
                                   name="wpmf_options_format_title[alt]"
                                <?php checked($opts_format_title['alt'], 1) ?> value="1">
                            <label for="wpmf_alt"
                                   class="ju-setting-label"><?php esc_html_e('Copy title to Alternative Text Field?', 'wpmf') ?>
                                (-)</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[description]" value="0">
                            <input id="wpmf_description" type="checkbox"
                                   name="wpmf_options_format_title[description]"
                                <?php checked($opts_format_title['description'], 1) ?> value="1">
                            <label for="wpmf_description"
                                   class="ju-setting-label"><?php esc_html_e('Copy title to Description Field?', 'wpmf') ?>
                                (.)</label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf_width_100">
                    <div class="wpmf_row_full p-lr-20">
                        <div class="pure-checkbox">
                            <input type="hidden" name="wpmf_options_format_title[caption]" value="0">
                            <input id="wpmf_caption" type="checkbox"
                                   name="wpmf_options_format_title[caption]"
                                <?php checked($opts_format_title['caption'], 1) ?> value="1">
                            <label for="wpmf_caption"
                                   class="ju-setting-label"><?php esc_html_e('Copy title to Caption Field?', 'wpmf') ?>
                                (_)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ju-settings-option wpmf_width_100 p-lr-20 wpmf-no-shadow">
            <div class="wpmf_row_full">
                <h2>
                    <label data-alt="<?php esc_html_e('Add capital letters automatically on media upload', 'wpmf'); ?>"
                           class="text"><?php esc_html_e('Automatic capitalization', 'wpmf') ?>
                    </label>
                </h2>

                <label>
                    <select name="wpmf_options_format_title[capita]" class="wpmf_width_100">
                        <option <?php selected($opts_format_title['capita'], 'cap_all') ?>
                                value="cap_all"><?php esc_html_e('Capitalize All Words', 'wpmf'); ?></option>
                        <option <?php selected($opts_format_title['capita'], 'cap_first') ?>
                                value="cap_first"><?php esc_html_e('Capitalize First Word Only', 'wpmf'); ?></option>
                        <option <?php selected($opts_format_title['capita'], 'all_lower') ?>
                                value="all_lower"><?php esc_html_e('All Words Lower Case', 'wpmf'); ?></option>
                        <option <?php selected($opts_format_title['capita'], 'all_upper') ?>
                                value="all_upper"><?php esc_html_e('All Words Upper Case', 'wpmf'); ?></option>
                        <option <?php selected($opts_format_title['capita'], 'dont_alter') ?>
                                value="dont_alter"><?php esc_html_e('Don\'t Alter (title text isn\'t modified in any way)', 'wpmf'); ?></option>
                    </select>
                </label>
            </div>
        </div>
    </div>
</div>

<div id="watermark" class="tab-content">
    <div class="content-box content-wpmf-files-folders">
        <div class="ju-settings-option wpmf_width_100 p-lr-20">
            <div class="ju-settings-option wpmf_width_40 wpmf-no-shadow wpmf-no-padding wpmf-no-margin">
                <div class="wpmf_row_full">
                    <input type="hidden" name="wpmf_option_image_watermark" value="0">
                    <b data-alt="<?php esc_html_e('Watermark will be applied only after saving the
             settings and regenerate the thumnails (hit the regenerate thumnails button)', 'wpmf'); ?>"
                       class="ju-setting-label text wpmf-no-padding"><?php esc_html_e('Images watermark', 'wpmf') ?></b>
                    <div class="ju-switch-button">
                        <label class="switch">
                            <input type="checkbox" id="cb_option_image_watermark"
                                   name="wpmf_option_image_watermark" value="1"
                                <?php
                                if (isset($option_image_watermark) && (int) $option_image_watermark === 1) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ju-settings-option wpmf_width_100 wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <label data-alt="<?php esc_html_e('Select a picture that will be applied over your images', 'wpmf') ?>"
                        class="wpmf_width_100 p-b-20 wpmf_left text label_text">
                        <?php esc_html_e('Select an image', 'wpmf') ?>
                    </label>
                    <label for="wpmf_watermark_image"></label>
                    <input type="text" readonly name="wpmf_watermark_image"
                           id="wpmf_watermark_image" class="regular-text wpmf_width_70"
                           value="<?php echo esc_attr($watermark_image); ?>">
                    <input type="hidden" name="wpmf_watermark_image_id"
                           id="wpmf_watermark_image_id" class="regular-text"
                           value="<?php echo esc_attr($watermark_image_id); ?>">
                    <div class="min-w-0 ju-button waves-effect waves-light wpmf_watermark_select_image"><?php esc_html_e('Select', 'wpmf') ?></div>
                    <div class="min-w-0 ju-button waves-effect waves-light wpmf_watermark_clear_image"><?php esc_html_e('Clear', 'wpmf') ?></div>
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <label data-alt="<?php esc_html_e('Select the watermark image position', 'wpmf'); ?>" class="wpmf_width_100 p-b-20 wpmf_left text label_text">
                        <?php esc_html_e('Watermark position', 'wpmf') ?>
                    </label>
                    <label>
                        <select name="wpmf_watermark_position" class="wpmf_width_50">
                            <option
                                <?php selected($watermark_position, 'center'); ?>
                                    value="center"><?php esc_html_e('Center', 'wpmf') ?></option>
                            <option
                                <?php selected($watermark_position, 'bottom_left'); ?>
                                    value="bottom_left"><?php esc_html_e('Bottom Left', 'wpmf') ?></option>
                            <option
                                <?php selected($watermark_position, 'bottom_right'); ?>
                                    value="bottom_right"><?php esc_html_e('Bottom Right', 'wpmf') ?></option>
                            <option
                                <?php selected($watermark_position, 'top_right'); ?>
                                    value="top_right"><?php esc_html_e('Top Right', 'wpmf') ?></option>
                            <option
                                <?php selected($watermark_position, 'top_left'); ?>
                                    value="top_left"><?php esc_html_e('Top Left', 'wpmf') ?></option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow wpmf-no-padding">
                <div class="wpmf_row_full">
                    <label data-alt="<?php esc_html_e('Make a watermark fit each photo size, resize the width of the watermark', 'wpmf'); ?>"
                        class="wpmf_width_100 p-b-20 wpmf_left text label_text">
                        <?php esc_html_e('Set size of watermark from picture', 'wpmf') ?>
                    </label>
                    <div class="wrap_apply">
                        <div>
                            <label>
                                <input type="number" class="small-text"
                                       name="watermark_image_scaling"
                                       value="<?php echo esc_attr($watermark_image_scaling) ?>">
                            </label>
                            <label><?php esc_html_e('%', 'wpmf') ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ju-settings-option wpmf_width_100 wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <label data-alt="<?php esc_html_e('Watermark image margin', 'wpmf'); ?>" class="wpmf_width_100 p-b-20 wpmf_left text label_text">
                        <?php esc_html_e('Image margin', 'wpmf') ?>
                    </label>
                    <div>
                        <div class="ju-settings-option wpmf-no-shadow wpmf_width_25 wpmf-no-margin">
                            <label class="wtm-label-small-text label_text"><?php esc_html_e('Top', 'wpmf') ?></label>
                            <label>
                                <input type="number" class="small-text"
                                       name="watermark_margin[top]"
                                       value="<?php echo esc_attr($watermark_margin['top']) ?>">
                            </label>
                            <label><?php esc_html_e('px', 'wpmf') ?></label>
                        </div>
                        <div class="ju-settings-option wpmf-no-shadow wpmf_width_25 wpmf-no-margin">
                            <label class="wtm-label-small-text label_text"><?php esc_html_e('Right', 'wpmf') ?></label>
                            <label>
                                <input type="number" class="small-text"
                                       name="watermark_margin[right]"
                                       value="<?php echo esc_attr($watermark_margin['right']) ?>">
                            </label>
                            <label><?php esc_html_e('px', 'wpmf') ?></label>
                        </div>
                        <div class="ju-settings-option wpmf-no-shadow wpmf_width_25 wpmf-no-margin">
                            <label class="wtm-label-small-text label_text"><?php esc_html_e('Bottom', 'wpmf') ?></label>
                            <label>
                                <input type="number" class="small-text"
                                       name="watermark_margin[bottom]"
                                       value="<?php echo esc_attr($watermark_margin['bottom']) ?>">
                            </label>
                            <label><?php esc_html_e('px', 'wpmf') ?></label>
                        </div>
                        <div class="ju-settings-option wpmf-no-shadow wpmf_width_25 wpmf-no-margin">
                            <label class="wtm-label-small-text label_text"><?php esc_html_e('Left', 'wpmf') ?></label>
                            <label>
                                <input type="number" class="small-text"
                                       name="watermark_margin[left]"
                                       value="<?php echo esc_attr($watermark_margin['left']) ?>">
                            </label>
                            <label><?php esc_html_e('px', 'wpmf') ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ju-settings-option wpmf_width_100 p-lr-20">
            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 data-alt="<?php esc_html_e('Select the size where you want to apply the watermark', 'wpmf'); ?>"
                        class="text title_h4 font-size-16 color-7f8a98"><?php esc_html_e('Apply watermark on', 'wpmf') ?></h4>
                        <div class="ju-settings-option wpmf-no-shadow wpmf_width_100">
                            <div class="pure-checkbox ju-setting-label line-height-30">
                                <input type="hidden" name="wpmf_image_watermark_apply[all_size]" value="0">
                                <input id="wpmf_watermark_position_all" type="checkbox"
                                       name="wpmf_image_watermark_apply[all_size]"
                                    <?php checked($watermark_apply['all_size'], 1) ?> value="1">
                                <label for="wpmf_watermark_position_all"><?php esc_html_e('All sizes', 'wpmf') ?></label>
                            </div>
                        </div>

                        <?php
                        $sizes = apply_filters('image_size_names_choose', array(
                            'thumbnail' => __('Thumbnail', 'wpmf'),
                            'medium'    => __('Medium', 'wpmf'),
                            'large'     => __('Large', 'wpmf'),
                            'full'      => __('Full Size', 'wpmf'),
                        ));
                        foreach ($sizes as $ksize => $vsize) :
                            ?>
                            <div class="ju-settings-option wpmf-no-shadow wpmf_width_100">
                                <div class="pure-checkbox ju-setting-label line-height-30">
                                    <input type="hidden" name="wpmf_image_watermark_apply[<?php echo esc_html($ksize) ?>]"
                                           value="0">
                                    <?php if (isset($watermark_apply[$ksize]) && (int) $watermark_apply[$ksize] === 1) : ?>
                                        <input title="" id="wpmf_watermark_position_<?php echo esc_html($ksize) ?>"
                                               type="checkbox"
                                               name="wpmf_image_watermark_apply[<?php echo esc_html($ksize) ?>]"
                                               checked value="1">
                                    <?php else : ?>
                                        <input id="wpmf_watermark_position_<?php echo esc_html($ksize) ?>"
                                               type="checkbox"
                                               name="wpmf_image_watermark_apply[<?php echo esc_html($ksize) ?>]" value="1">
                                    <?php endif; ?>
                                    <label for="wpmf_watermark_position_<?php echo esc_html($ksize) ?>"><?php echo esc_html($vsize) ?></label>
                                </div>
                            </div>

                        <?php endforeach; ?>
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 data-alt="<?php esc_html_e('Exclude Folders', 'wpmf'); ?>"
                        class="text title_h4 font-size-16 color-7f8a98"><?php esc_html_e('Exclude Folders', 'wpmf') ?></h4>
                    <div class="wrap_apply">
                        <div class="watermark_exclude_folders">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="wpmf_row_full">
            <label class="ju-setting-label text"></label>
            <button type="button" class="ju-button no-background orange-button waves-effect waves-light wpmf_watermark_regeneration"
            ><?php esc_html_e('Thumbnail regeneration', 'wpmf') ?></button>
            <button type="button"
                    class="ju-button orange-button no-background waves-effect waves-light btn_stop_watermark"
            ><?php esc_html_e('Stop the process', 'wpmf') ?></button>
            <div class="process_watermark_thumb_full">
                <div class="process_watermark_thumb" data-w="0"></div>
            </div>
        </div>

    </div>
</div>