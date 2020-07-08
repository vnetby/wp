<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="content-box content-wpmf-media-sync">
    <div class="ju-settings-option btnoption">
        <div class="wpmf_row_full">
            <input type="hidden" name="wpmf_option_sync_media" value="0">
            <label data-alt="<?php esc_html_e('Activate the sync from External folder to WordPress media library', 'wpmf') ?>"
                   class="ju-setting-label text"><?php esc_html_e('Activate the sync', 'wpmf') ?></label>
            <div class="ju-switch-button">
                <label class="switch">
                    <input type="checkbox" id="cb_option_sync_media"
                           name="wpmf_option_sync_media" value="1"
                        <?php
                        if (isset($option_sync_media) && (int) $option_sync_media === 1) {
                            echo 'checked';
                        }
                        ?>
                    >
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="ju-settings-option btnoption wpmf_right m-r-0">
        <div class="wpmf_row_full">
            <input type="hidden" name="wpmf_option_sync_media_external" value="0">
            <label data-alt="<?php esc_html_e('Also activate the sync from
             WordPress media library to external folders', 'wpmf') ?>"
                   class="ju-setting-label text"><?php esc_html_e('Activate 2 ways sync', 'wpmf') ?></label>
            <div class="ju-switch-button">
                <label class="switch">
                    <input type="checkbox" id="cb_option_sync_media_external"
                           name="wpmf_option_sync_media_external" value="1"
                        <?php
                        if (isset($sync_media_ex) && (int) $sync_media_ex === 1) {
                            echo 'checked';
                        }
                        ?>
                    >
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="ju-settings-option btnoption">
        <div class="wpmf_row_full p-lr-20">
            <label data-alt="<?php esc_html_e('Launch an automatic synchronization between the media folders selected below, each X minutes', 'wpmf') ?>" class="setting-label-bold p-r-20"><?php esc_html_e('Sync delay', 'wpmf') ?></label>
            <label>
                <input type="text" name="input_time_sync" class="input_time_sync"
                       value="<?php echo esc_attr($time_sync) ?>">
            </label>
            <label class="setting-label-bold"><?php esc_html_e('minutes', 'wpmf') ?></label>
        </div>
    </div>

    <div class="ju-settings-option wpmf_width_100 btnoption">
        <div class="wpmf_row_full">
            <div>
                <div class="wrap_dir_name_ftp wpmf_left">
                    <div id="wpmf_foldertree_sync"></div>

                </div>

                <div class="wrap_dir_name_categories wpmf_left">
                    <div id="wpmf_foldertree_categories"></div>

                </div>
            </div>
            <div class="time_sync p-lr-20">
                <div class="input_dir">
                    <label>
                        <input type="text" name="dir_name_ftp" class="input_sync dir_name_ftp wpmf_left" readonly
                               value="">
                    </label>
                    <label>
                        <input type="text" name="dir_name_categories" class="input_sync dir_name_categories wpmf_left"
                               readonly
                               data-id_category="0" value="">
                    </label>
                </div>

                <button type="button"
                        class="m-t-10 ju-button no-background orange-button waves-effect waves-light btn_addsync_media"><?php esc_html_e('Add', 'wpmf') ?></button>
                <button type="button"
                        class="m-t-10 ju-button no-background orange-button waves-effect waves-light btn_deletesync_media"><?php esc_html_e('Delete selected', 'wpmf') ?></button>
            </div>
        </div>
    </div>

    <div class="ju-settings-option wpmf_width_100 btnoption">
        <table class="wp-list-table widefat striped wp-list-table-sync">
            <tr>
                <td style="width: 1%"><label for="cb-select-all"></label><input id="cb-select-all" type="checkbox"></td>
                <td style="width: 40%"><?php esc_html_e('Directory FTP', 'wpmf') ?></td>
                <td style="width: 40%"><?php esc_html_e('Folder category', 'wpmf') ?></td>
            </tr>
            <?php if (!empty($wpmf_list_sync_media)) : ?>
                <?php foreach ($wpmf_list_sync_media as $k => $v) : ?>
                    <tr data-id="<?php echo esc_html($k) ?>">
                        <td>
                            <label for="cb-select-<?php echo esc_html($k) ?>"></label>
                            <input id="cb-select-<?php echo esc_html($k) ?>"
                                   type="checkbox" name="post[]" value="<?php echo esc_html($k) ?>">
                        </td>
                        <td><?php echo esc_html($v['folder_ftp']) ?></td>
                        <td><?php echo esc_html($this->breadcrumb_category[$k]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>
