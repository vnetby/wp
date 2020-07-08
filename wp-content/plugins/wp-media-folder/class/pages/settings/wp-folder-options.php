<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
$tabs_data = array(
    array(
        'id'       => 'general',
        'title'    => __('General', 'wpmf'),
        'icon'     => 'home',
        'sub_tabs' => array(
            'additional_features' => __('Main settings', 'wpmf'),
            'media_filtering'     => __('Media filtering', 'wpmf')
        )
    ),
    array(
        'id'       => 'wordpress_gallery',
        'title'    => __('Wordpress Gallery', 'wpmf'),
        'icon'     => 'image',
        'sub_tabs' => array(
            'gallery_features' => __('Gallery features', 'wpmf'),
            'default_settings' => __('Default settings', 'wpmf')
        )
    ),
    array(
        'id'       => 'gallery_addon',
        'title'    => __('Galleries Addon', 'wpmf'),
        'icon'     => 'add_photo_alternate',
        'sub_tabs' => array(
            'galleryadd_default_settings' => __('Default settings', 'wpmf'),
            'gallery_shortcode_generator' => __('Shortcode generator', 'wpmf'),
            'gallery_social_sharing'      => __('Social sharing', 'wpmf')
        )
    ),
    array(
        'id'       => 'media_access',
        'title'    => __('Access & design', 'wpmf'),
        'icon'     => 'format_color_fill',
        'sub_tabs' => array(
            'user_media_access' => __('Media access', 'wpmf'),
            'file_design'       => __('File Design', 'wpmf')
        )
    ),
    array(
        'id'       => 'files_folders',
        'title'    => __('Rename & Watermark', 'wpmf'),
        'icon'     => 'picture_in_picture_alt',
        'sub_tabs' => array(
            'rename_on_upload' => __('Rename on upload', 'wpmf'),
            'watermark'        => __('Watermark', 'wpmf'),
        )
    ),
    array(
        'id'       => 'ftp_import',
        'title'    => __('Server Import', 'wpmf'),
        'icon'     => 'import_export',
        'sub_tabs' => array()
    ),
    array(
        'id'       => 'sync_media',
        'title'    => __('Server Folder Sync', 'wpmf'),
        'icon'     => 'sync',
        'sub_tabs' => array()
    ),
    array(
        'id'       => 'regenerate_thumbnails',
        'title'    => __('Regenerate Thumb', 'wpmf'),
        'icon'     => 'update',
        'sub_tabs' => array()
    ),
    array(
        'id'       => 'image_compression',
        'title'    => __('Image compression', 'wpmf'),
        'icon'     => 'compare',
        'sub_tabs' => array()
    )
);

if (!is_plugin_active('wp-media-folder-gallery-addon/wp-media-folder-gallery-addon.php')) {
    unset($tabs_data[2]);
}

if (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) {
    $tabs_data[] = array(
        'id'       => 'cloud',
        'title'    => __('Cloud', 'wpmf'),
        'icon'     => 'cloud_queue',
        'sub_tabs' => array(
            'google_drive_box' => __('Google Drive', 'wpmf'),
            'dropbox_box'      => __('Dropbox', 'wpmf'),
            'one_drive_box'    => __('OneDrive', 'wpmf')
        )
    );
}
$tabs_data[] = array(
    'id'       => 'jutranslation',
    'title'    => __('Translation', 'wpmf'),
    'icon'     => 'format_color_text',
    'sub_tabs' => array()
);

$tabs_data[] = array(
    'id' => 'system_check',
    'title' => __('System Check', 'wpmf'),
    'content' => 'system-check',
    'icon' => 'verified_user',
    'sub_tabs' => array()
)
?>
<div class="ju-main-wrapper">
    <div class="ju-left-panel">
        <div class="ju-logo">
            <a href="https://www.joomunited.com/" target="_blank">
                <img src="<?php echo esc_url(WPMF_PLUGIN_URL . 'assets/wordpress-css-framework/images/logo-joomUnited-white.png') ?>"
                     alt="<?php esc_html_e('JoomUnited logo', 'wpmf') ?>">
            </a>
        </div>
        <div class="ju-menu-search">
            <i class="material-icons ju-menu-search-icon">
                search
            </i>

            <input type="text" class="ju-menu-search-input"
                   placeholder="<?php esc_html_e('Search settings', 'wpmf') ?>"
            >
        </div>
        <ul class="tabs ju-menu-tabs">
            <?php foreach ($tabs_data as $tab) : ?>
                <li class="tab" data-tab-title="<?php echo esc_attr($tab['title']) ?>">
                    <a href="#<?php echo esc_attr($tab['id']) ?>"
                       class="link-tab white-text waves-effect waves-light <?php echo (empty($tab['sub_tabs'])) ? 'no-submenus' : 'with-submenus' ?>"
                    >
                        <i class="material-icons menu-tab-icon"><?php echo esc_html($tab['icon']) ?></i>
                        <span class="tab-title" title="<?php echo esc_attr($tab['title']) ?>"><?php echo esc_html($tab['title']) ?></span>

                        <?php
                        if ($tab['id'] === 'system_check') {
                            if (version_compare(PHP_VERSION, '7.2.0', '<') || !in_array('curl', get_loaded_extensions()) || !function_exists('gd_info')) {
                                echo '<i class="material-icons system-checkbox material-icons-menu-alert" style="float: right;vertical-align: text-bottom;">info</i>';
                            }
                        }
                        ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="ju-right-panel">
        <div class="ju-content-wrapper">
            <div id="profiles-container">
                <form name="form1" id="form_list_size" action="" method="post">
                    <?php foreach ($tabs_data as $tab) : ?>
                        <div class="ju-content-wrapper" id="<?php echo esc_attr($tab['id']) ?>" style="display: none">
                            <?php
                            if (!empty($tab['sub_tabs'])) :
                                ?>
                                <div class="ju-top-tabs-wrapper">
                                    <ul class="tabs ju-top-tabs">
                                        <?php
                                        foreach ($tab['sub_tabs'] as $tab_id => $tab_label) :
                                            ?>

                                            <li class="tab">
                                                <a href="#<?php echo esc_html($tab_id) ?>"
                                                   class="link-tab waves-effect waves-light">
                                                    <?php echo esc_html($tab_label) ?>
                                                </a>
                                            </li>

                                            <?php
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                                <?php
                            endif;
                            ?>
                            <?php if ($tab['id'] !== 'image_compression' && $tab['id'] !== 'cloud') : ?>
                                <div class="wpmf_width_100 top_bar">
                                    <h1><?php echo esc_html($tab['title']) ?></h1>
                                    <?php
                                    require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/submit_button.php';
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
                            if (isset($_POST['btn_wpmf_save']) && $tab['id'] !== 'cloud') {
                                ?>
                                <div class="wpmf_width_100 top_bar saved_infos">
                                    <?php
                                    require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/saved_info.php';
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php include_once(WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/' . $tab['id'] . '.php'); ?>
                            <?php
                            require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/submit_button.php';
                            ?>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" class="setting_tab_value" name="setting_tab_value" value="wpmf-general">
                    <input type="hidden" name="wpmf_nonce"
                           value="<?php echo esc_html(wp_create_nonce('wpmf_nonce')) ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    (function ($) {
        $(function () {
            <?php
            // phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect, Generic.WhiteSpace.ScopeIndent.IncorrectExact, WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
            if (!empty($_POST['wpmf_hash'])) :
            ?>
            $('.ju-top-tabs .link-tab[href="#<?php echo esc_html($_POST['wpmf_hash']) ?>"]').click();
            <?php
            endif;
            // phpcs:enable
            ?>
            jQuery('.wp-color-field-bg').wpColorPicker({width: 180, defaultColor: '#444444'});
            jQuery('.wp-color-field-hv').wpColorPicker({width: 180, defaultColor: '#888888'});
            jQuery('.wp-color-field-font').wpColorPicker({width: 180, defaultColor: '#ffffff'});
            jQuery('.wp-color-field-hvfont').wpColorPicker({width: 180, defaultColor: '#ffffff'});
        });
    })(jQuery);

</script>