<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WpmfDuplicateFile
 * This class that holds most of the duplicate functionality for Media Folder.
 */
class WpmfDuplicateFile
{

    /**
     * Wpmf_Duplicate_File constructor.
     */
    public function __construct()
    {
        add_action('wp_enqueue_media', array($this, 'enqueueAdminScripts'));
        add_action('wp_ajax_wpmf_duplicate_file', array($this, 'duplicateFile'));
    }

    /**
     * Includes styles and scripts
     *
     * @return void
     */
    public function enqueueAdminScripts()
    {
        wp_enqueue_script('duplicate-image');
        wp_enqueue_style(
            'duplicate-style',
            plugins_url('assets/css/style_duplicate_file.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );
    }

    /**
     * Ajax duplicate attachment
     *
     * @return void
     */
    public function duplicateFile()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to duplicate a file
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'duplicate_file');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $id   = (int) $_POST['id'];
            $post = get_post($id);
            if (empty($post)) {
                wp_send_json(array('status' => false, 'message' => __('This post is not exists', 'wpmf')));
            }

            $args         = array(
                'orderby' => 'name',
                'order'   => 'ASC',
                'fields'  => 'ids'
            );
            $terms_parent = wp_get_object_terms($post->ID, WPMF_TAXO, $args);
            $alt_post     = get_post_meta($id, '_wp_attachment_image_alt', true);
            $file_path    = get_attached_file($id);
            if (!file_exists($file_path)) {
                wp_send_json(array('status' => false, 'message' => __('File does not exist', 'wpmf')));
            }
            $infos_url  = pathinfo($post->guid);
            $mime_type  = get_post_mime_type($id);
            $infos_path = pathinfo($file_path);
            $name       = $infos_path['basename'];
            $filename   = wp_unique_filename($infos_path['dirname'], $name);
            $upload     = copy($file_path, $infos_path['dirname'] . '/' . $filename);
            if ($upload) {
                $attachment = array(
                    'guid'           => $infos_url['dirname'] . '/' . $filename,
                    'post_mime_type' => $mime_type,
                    'post_title'     => $post->post_title,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'post_status'    => 'inherit'
                );

                // insert attachment
                $attach_id   = wp_insert_attachment($attachment, $infos_path['dirname'] . '/' . $filename);
                $attach_data = wp_generate_attachment_metadata($attach_id, $infos_path['dirname'] . '/' . $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
                update_post_meta($attach_id, '_wp_attachment_image_alt', $alt_post);

                // set term
                if (!empty($terms_parent)) {
                    foreach ($terms_parent as $term_id) {
                        wp_set_object_terms($attach_id, $term_id, WPMF_TAXO, true);

                        /**
                         * Duplicate an attachment
                         *
                         * @param integer Attachment ID
                         * @param integer Target folder
                         */
                        do_action('wpmf_duplicate_attachment', $attach_id, $term_id);
                    }
                }
                wp_send_json(array('status' => true, 'message' => __('Duplicated file ', 'wpmf') . $name));
            }
            wp_send_json(array('status' => false, 'message' => __('Error duplicated file', 'wpmf')));
        }
    }
}
