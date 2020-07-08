<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WpmfSingleLightbox
 * This class that holds most of the single lightbox functionality for Media Folder.
 */
class WpmfSingleLightbox
{

    /**
     * Wpmf_Single_Lightbox constructor.
     */
    public function __construct()
    {
        add_action('wp_enqueue_media', array($this, 'loadScript'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScript'));
        add_filter('attachment_fields_to_edit', array($this, 'attachmentFieldsToEdit'), 10, 2);
        add_filter('attachment_fields_to_save', array($this, 'attachmentFieldsToSave'), 10, 2);
        add_filter('image_send_to_editor', array($this, 'imageLightboxSendToEditor'), 10, 8);
        add_action('print_media_templates', array($this, 'printMediaTemplates'));
        add_action('wp_ajax_wpmf_get_thumb_image', array($this, 'getThumbImage'));
    }

    /**
     * Add script to footer
     *
     * @return void
     */
    public function adminFooterImagelightbox()
    {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {

                if (wp && wp.media && wp.media.events) {
                    wp.media.events.on('editor:image-edit', function (data) {
                        data.metadata.wpmf_image_lightbox =
                            data.editor.dom.getAttrib(data.image, 'data-wpmf_image_lightbox');
                        data.metadata.wpmf_size_lightbox =
                            data.editor.dom.getAttrib(data.image, 'data-wpmf_size_lightbox');
                        data.metadata.wpmflightbox = data.editor.dom.getAttrib(data.image, 'data-wpmflightbox');
                    });
                    wp.media.events.on('editor:image-update', function (data) {
                        if (data.metadata.link === 'file' && data.metadata.wpmf_size_lightbox !== 'none') {
                            data.editor.dom.setAttrib(data.image, 'data-wpmflightbox', 1);
                        } else {
                            data.editor.dom.setAttrib(data.image, 'data-wpmflightbox', 0);
                        }
                        $.ajax({
                            url: ajaxurl,
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                action: "wpmf_get_thumb_image",
                                attachment_id: data.metadata.attachment_id,
                                size: data.metadata.wpmf_size_lightbox,
                                wpmf_nonce: wpmf.vars.wpmf_nonce
                            },
                            success: function (res) {
                                if (res.status) {
                                    data.editor.dom.setAttrib(data.image, 'data-wpmf_image_lightbox', res.url_thumb);
                                    data.editor.dom.setAttrib(
                                        data.image,
                                        'data-wpmf_size_lightbox',
                                        data.metadata.wpmf_size_lightbox
                                    );
                                }
                            }
                        });
                    });
                }
            });
        </script>
        <?php
    }

    /**
     * Add script to footer
     *
     * @return void
     */
    public function loadScript()
    {
        wp_enqueue_script(
            'wpmf-singleimage-lightbox',
            plugins_url('/assets/js/single_image_lightbox/image_lightbox.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );
        add_action('admin_footer', array($this, 'adminFooterImagelightbox'), 11);
        add_action('wp_footer', array($this, 'adminFooterImagelightbox'), 11);
    }

    /**
     * Load styles and scripts
     *
     * @return void
     */
    public function enqueueScript()
    {
        wp_enqueue_style(
            'wpmf-material-design-iconic-font.min',
            plugins_url('/assets/css/material-design-iconic-font.min.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );
        wp_enqueue_script(
            'wpmf-gallery-popup',
            plugins_url('/assets/js/display-gallery/jquery.magnific-popup.min.js', dirname(__FILE__)),
            array('jquery'),
            '0.9.9',
            true
        );
        wp_enqueue_script(
            'wpmf-singleimage-lightbox',
            plugins_url('/assets/js/single_image_lightbox/single_image_lightbox.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );
        wp_enqueue_style(
            'wpmf-singleimage-popup-style',
            plugins_url('/assets/css/display-gallery/magnific-popup.css', dirname(__FILE__)),
            array(),
            '0.9.9'
        );
    }

    /**
     * Ajax get thumbnail for image
     *
     * @return void
     */
    public function getThumbImage()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['attachment_id']) && isset($_POST['size'])) {
            $image_src = wp_get_attachment_image_src($_POST['attachment_id'], $_POST['size']);
            $url_image = $image_src[0];
            wp_send_json(array('status' => true, 'url_thumb' => $url_image));
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Add media templates
     *
     * @return void
     */
    public function printMediaTemplates()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        if (isset($_GET['action']) && $_GET['action'] === 'elementor') {
            return;
        }
        ?>

        <script type="text/html" id="tmpl-image-wpmf">
            <label class="setting wpmf_size_lightbox">
                <span><?php esc_html_e('Lightbox size', 'wpmf'); ?></span>
                <select class="wpmf_size_lightbox" name="wpmf_size_lightbox" data-setting="wpmf_size_lightbox">
                    <option value="none"><?php esc_html_e('None', 'wpmf') ?></option>
                    <?php
                    $sizes = apply_filters('image_size_names_choose', array(
                        'none'      => __('None', 'wpmf'),
                        'thumbnail' => __('Thumbnail', 'wpmf'),
                        'medium'    => __('Medium', 'wpmf'),
                        'large'     => __('Large', 'wpmf'),
                        'full'      => __('Full Size', 'wpmf'),
                    ));
                    ?>

                    <?php foreach ($sizes as $k => $v) : ?>
                        <option value="<?php echo esc_html($k) ?>"><?php echo esc_html($v) ?></option>
                    <?php endforeach; ?>

                </select>
            </label>
        </script>
        <?php
    }

    /**
     * Filters the image HTML markup to send to the editor when inserting an image.
     *
     * @param string       $html    The image HTML markup to send.
     * @param integer      $id      The attachment id.
     * @param string       $caption The image caption.
     * @param string       $title   The image title.
     * @param string       $align   The image alignment.
     * @param string       $url     The image source URL.
     * @param string|array $size    Size of image. Image size or array of width and height values
     * @param string       $alt     The image alternative, or alt, text.
     *
     * @return string $html
     */
    public function imageLightboxSendToEditor($html, $id, $caption, $title, $align, $url, $size, $alt = '')
    {
        // check link to option, if value not empty do set attribute
        if (isset($url) && $url !== '') {
            $url_attachment = wp_get_attachment_url($id);
            $size           = get_post_meta($id, 'wpmf_image_lightbox', true);
            if (empty($size)) {
                $size = 'large';
            }
            $image_src = wp_get_attachment_image_src($id, $size);
            $url_image = $image_src[0];
            $dom       = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            $tags = $dom->getElementsByTagName('img');
            foreach ($tags as $tag) {
                $tag->setAttribute('data-wpmf_image_lightbox', $url_image);
                $tag->setAttribute('data-wpmf_size_lightbox', $size);
                if ($url_attachment !== $url || $size === 'none') {
                    $tag->setAttribute('data-wpmflightbox', 0);
                } else {
                    $tag->setAttribute('data-wpmflightbox', 1);
                }
            }
            $html = $dom->saveHTML();
        }

        return $html;
    }

    /**
     * Create lightbox size field
     * Based on /wp-admin/includes/media.php
     *
     * @param array   $form_fields An array of attachment form fields.
     * @param WP_Post $post        The WP_Post attachment object.
     *
     * @return mixed
     */
    public function attachmentFieldsToEdit($form_fields, $post)
    {
        $value = get_post_meta($post->ID, 'wpmf_image_lightbox', true);
        if (empty($value)) {
            $value = 'large';
        }
        $sizes  = apply_filters('image_size_names_choose', array(
            'thumbnail' => __('Thumbnail', 'wpmf'),
            'medium'    => __('Medium', 'wpmf'),
            'large'     => __('Large', 'wpmf'),
            'full'      => __('Full Size', 'wpmf'),
        ));
        $option = '';
        $option .= '<option value="none">' . __('None', 'wpmf') . '</option>';
        foreach ($sizes as $k => $v) {
            if ($value === $k) {
                $option .= '<option selected value="' . $k . '">' . $v . '</option>';
            } else {
                $option .= '<option value="' . $k . '">' . $v . '</option>';
            }
        }
        $form_fields['wpmf_image_lightbox'] = array(
            'label' => __('Lightbox size', 'wpmf'),
            'input' => 'html',
            'html'  => '
                        <select name="attachments[' . $post->ID . '][wpmf_image_lightbox]"
                         id="attachments[' . $post->ID . '][wpmf_image_lightbox]">
                                ' . $option . '
                        </select>'
        );

        return $form_fields;
    }

    /**
     * Save lightbox size field
     * Based on /wp-admin/includes/media.php
     *
     * @param array $post       An array of post data.
     * @param array $attachment An array of attachment metadata.
     *
     * @return mixed $post
     */
    public function attachmentFieldsToSave($post, $attachment)
    {
        if (isset($attachment['wpmf_image_lightbox'])) {
            update_post_meta($post['ID'], 'wpmf_image_lightbox', $attachment['wpmf_image_lightbox']);
        }
        return $post;
    }
}
