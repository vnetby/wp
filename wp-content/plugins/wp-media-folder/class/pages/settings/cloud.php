<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div id="google_drive_box" class="tab-content">
    <div class="wpmf_width_100 p-tb-20 wpmf_left top_bar">
        <h1 class="wpmf_left"><?php esc_html_e('Google Drive', 'wpmf') ?></h1>
        <?php
        if (isset($googleconfig['googleClientId']) && $googleconfig['googleClientId'] !== ''
            && isset($googleconfig['googleClientSecret']) && $googleconfig['googleClientSecret'] !== '') {
            if (!$googleDrive->checkAuth()) {
                $urlGoogle = $googleDrive->getAuthorisationUrl();
                ?>
                <div class="btn_wpmf_saves">
                    <a id="ggconnect" class="ju-button orange-button waves-effect waves-light btndrive" href="#"
                       onclick="window.location.assign('<?php echo esc_html($urlGoogle); ?>','foo','width=600,height=600');return false;">
                        <?php esc_html_e('Connect Google Drive', 'wpmf') ?></a>
                </div>

                <?php
            } else {
                $url_logout = admin_url('options-general.php?page=option-folder&task=wpmf&function=wpmf_gglogout');
                ?>
                <div class="btn_wpmf_saves">
                    <a id="gg_disconnect"
                       class="ju-button no-background orange-button waves-effect waves-light btndrive"
                       href="<?php echo esc_html($url_logout) ?>">
                        <?php esc_html_e('Disconnect Google Drive', 'wpmf') ?></a>
                </div>
                <?php
            }
        } else {
            require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/submit_button.php';
        }
        ?>
    </div>
    <div class="content-box content-wpmf-general">
        <?php
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (isset($_POST['btn_wpmf_save'])) {
            ?>
            <div class="wpmf_width_100 top_bar saved_infos" style="padding: 20px 0">
                <?php
                require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/saved_info.php';
                ?>
            </div>
            <?php
        }
        ?>

        <div class="wpmf_width_100 ju-settings-option">
            <div class="wpmf_row_full p-d-20">
                <?php
                if (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) {
                    // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
                    echo $html_tabgoogle;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div id="dropbox_box" class="tab-content">
    <div class="wpmf_width_100 p-tb-20 wpmf_left top_bar">
        <h1 class="wpmf_left"><?php esc_html_e('Dropbox', 'wpmf') ?></h1>
        <?php
        if (isset($dropboxconfig['dropboxKey']) && $dropboxconfig['dropboxKey'] !== ''
            && isset($dropboxconfig['dropboxSecret']) && $dropboxconfig['dropboxSecret'] !== '') {
            if ($Dropbox->checkAuth()) {
                try {
                    $urlDropbox = $Dropbox->getAuthorizeDropboxUrl();
                } catch (Exception $e) {
                    $urlDropbox = '';
                }
            }
            if ($Dropbox->checkAuth()) {
                ?>
                <div class="btn_wpmf_saves">
                    <a class="ju-button orange-button waves-effect waves-light btndrive" href="#"
                       onclick="window.open('<?php echo esc_html($urlDropbox); ?>','foo','width=600,height=600');return false;">
                        <?php esc_html_e('Connect Dropbox', 'wpmf') ?></a>
                </div>

                <?php
            } else { ?>
                <div class="btn_wpmf_saves">
                    <a class="ju-button no-background orange-button waves-effect waves-light btndrive"
                       href="<?php echo esc_html(admin_url('options-general.php?page=option-folder&task=wpmf&function=wpmf_dropboxlogout')) ?>">
                        <?php esc_html_e('Disconnect Dropbox', 'wpmf') ?></a>
                </div>
                <?php
            }
        } else {
            require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/submit_button.php';
        }
        ?>
    </div>
    <div class="content-box content-wpmf-general">
        <?php
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (isset($_POST['btn_wpmf_save'])) {
            ?>
            <div class="wpmf_width_100 top_bar saved_infos" style="padding: 20px 0">
                <?php
                require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/saved_info.php';
                ?>
            </div>
            <?php
        }
        ?>

        <div class="wpmf_width_100  ju-settings-option">
            <div class="wpmf_row_full p-d-20">
                <?php
                if (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) {
                    // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
                    echo $html_tabdropbox;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div id="one_drive_box" class="tab-content">
    <div class="wpmf_width_100 p-tb-20 wpmf_left top_bar">
        <h1 class="wpmf_left"><?php esc_html_e('OneDrive', 'wpmf') ?></h1>
        <?php
        $appInfo    = $onedriveDrive->getClient();
        $authUrl    = $onedriveDrive->startWebAuth();
        $btnconnect = '';
        if (!is_wp_error($authUrl)) {
            $btnconnect = '<a class="ju-button orange-button waves-effect waves-light btndrive wpmf_onedrive_login" href="#"
         onclick="window.location.assign(\'' . $authUrl . '\',\'foo\',\'width=600,height=600\');return false;">';
            $btnconnect .= __('Connect OneDrive', 'wpmf');
            $btnconnect .= '</a>';
        }

        $btndisconnect = '<a class="ju-button no-background orange-button waves-effect waves-light btndrive wpmf_onedrive_logout" href="#" >';
        $btndisconnect .= __('Disconnect OneDrive', 'wpmf');
        $btndisconnect .= '</a>';


        $hasToken = $onedriveDrive->loadToken();
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        if (!empty($_GET['error']) && $_GET['error'] === 'access_denied') {
            $onedriveDrive->revokeToken();
            $hasToken = new WP_Error('broke', __("The plugin isn't yet authorized to use your OneDrive!
         Please (re)-authorize the plugin", 'wpmf'));
        }
        $onedrive_config = get_option('_wpmfAddon_onedrive_config');
        if (isset($onedrive_config['OneDriveClientId']) && $onedrive_config['OneDriveClientId'] !== ''
            && isset($onedrive_config['OneDriveClientSecret']) && $onedrive_config['OneDriveClientSecret'] !== '') {
            if (isset($onedrive_config['connected']) && (int) $onedrive_config['connected'] === 1) {
                $client    = $onedriveDrive->startClient();
                $driveInfo = $onedriveDrive->getDriveInfo();
                // phpcs:disable WordPress.Security.EscapeOutput -- Content already escaped in the method
                if ($driveInfo === false) {
                    echo '<div class="btn_wpmf_saves">' . $btnconnect . '</div>';
                } elseif (is_wp_error($driveInfo)) {
                    echo '<div class="btn_wpmf_saves">' . $btnconnect . '</div>';
                } else {
                    echo '<div class="btn_wpmf_saves">' . $btndisconnect . '</div>';
                }
                // phpcs:enable
            } else {
                echo '<div class="btn_wpmf_saves">' . $btnconnect . '</div>'; // phpcs:disable WordPress.Security.EscapeOutput -- Content already escaped in the method
            }
        } else {
            require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/submit_button.php';
        }

        ?>
    </div>

    <div class="content-box content-wpmf-general">
        <?php
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (isset($_POST['btn_wpmf_save'])) {
            ?>
            <div class="wpmf_width_100 top_bar saved_infos" style="padding: 20px 0">
                <?php
                require WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/pages/settings/saved_info.php';
                ?>
            </div>
            <?php
        }
        ?>

        <div class="wpmf_width_100 ju-settings-option">
            <div class="wpmf_row_full p-d-20">
                <?php
                if (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) {
                    // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
                    echo $html_tabonedrive;
                }
                ?>
            </div>
        </div>
    </div>
</div>
