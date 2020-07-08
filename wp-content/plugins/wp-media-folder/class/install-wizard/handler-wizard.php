<?php
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Class WpmfHandlerWizard
 */
class WpmfHandlerWizard
{
    /**
     * WpmfHandlerWizard constructor.
     */
    public function __construct()
    {
    }

    /**
     * Save Environment handle
     *
     * @param string $current_step Current step
     *
     * @return void
     */
    public static function saveEvironment($current_step)
    {
        check_admin_referer('wpmf-setup-wizard', 'wizard_nonce');
        /*
         * Do no thing
         */
        $wizard = new WpmfInstallWizard();
        wp_safe_redirect(esc_url_raw($wizard->getNextLink($current_step)));
        exit;
    }

    /**
     * Save Quick configuration handle
     *
     * @param string $current_step Current step
     *
     * @return void
     */
    public static function saveImageConfiguration($current_step)
    {
        check_admin_referer('wpmf-setup-wizard', 'wizard_nonce');

        WP_Filesystem();
        $options = array(
            'wpmf_usegellery' => 1,
            'wpmf_usegellery_lightbox' => 1
        );

        foreach ($options as $name => $value) {
            if (isset($_POST[$name])) {
                update_option($name, $_POST[$name]);
            } else {
                update_option($name, $value);
            }
        }
        $wizard = new WpmfInstallWizard();
        wp_safe_redirect(esc_url_raw($wizard->getNextLink($current_step)));
        exit;
    }

    /**
     * Save Main optimization handle
     *
     * @param string $current_step Current step
     *
     * @return void
     */
    public static function saveAdditionalFeatures($current_step)
    {
        WP_Filesystem();
        check_admin_referer('wpmf-setup-wizard', 'wizard_nonce');
        $options = array(
            'wpmf_option_mediafolder' => 0,
            'wpmf_option_override' => 0,
            'wpmf_option_duplicate' => 0
        );

        if (isset($_POST['hide_remote_video'])) {
            wpmfSetOption('hide_remote_video', $_POST['hide_remote_video']);
        } else {
            wpmfSetOption('hide_remote_video', 1);
        }

        foreach ($options as $name => $value) {
            if (isset($_POST[$name])) {
                update_option($name, $_POST[$name]);
            } else {
                update_option($name, $value);
            }
        }
        $wizard = new WpmfInstallWizard();
        wp_safe_redirect(esc_url_raw($wizard->getNextLink($current_step)));
        exit;
    }
}
