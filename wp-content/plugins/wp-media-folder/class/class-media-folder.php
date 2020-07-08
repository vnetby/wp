<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WpMediaFolder
 * This class that holds most of the admin functionality for Media Folder.
 */
class WpMediaFolder extends WpmfHelper
{
    /**
     * Id of root Folder
     *
     * @var integer
     */
    public $folderRootId = 0;

    /**
     * Vimeo pattern
     *
     * @var string
     */
    public $vimeo_pattern = '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im';

    /**
     * Wp_Media_Folder constructor.
     */
    public function __construct()
    {
        $root_id            = get_option('wpmf_folder_root_id');
        $this->folderRootId = (int) $root_id;
        add_action('init', array($this, 'includes'));
        add_action('admin_init', array($this, 'adminRedirects'));
        add_action('init', array($this, 'loadPluginTextdomain'), 1);
        $active_media = get_option('wpmf_active_media');
        if (isset($active_media) && (int) $active_media === 1) {
            add_action('init', array($this, 'createUserFolder'));
        }
        if (is_plugin_active('wp-sweep/wp-sweep.php')) {
            add_action('admin_init', array($this, 'updateCountTerm'));
        }

        if (!get_option('_wpmf_import_size_notice_flag', false)) {
            add_action('admin_notices', array($this, 'showNoticeImportSize'), 3);
        }

        if (is_plugin_active('nextgen-gallery/nggallery.php')) {
            if (!get_option('wpmf_import_nextgen_gallery', false)) {
                add_action('admin_notices', array($this, 'showNoticeImportGallery'), 3);
            }
        }

        if (!get_option('wpmf_use_taxonomy', false)) {
            add_option('wpmf_use_taxonomy', 1, '', 'yes');
        }

        add_action('restrict_manage_posts', array($this, 'addImageCategoryFilter'));
        add_action('pre_get_posts', array($this, 'preGetPosts1'));
        add_action('admin_enqueue_scripts', array($this, 'adminPageTableScript'));
        add_action('wp_enqueue_media', array($this, 'mediaPageTableScript'));
        add_action('pre_get_posts', array($this, 'preGetPosts'), 0, 1);
        add_filter('add_attachment', array($this, 'afterUpload'), 0, 1);
        add_filter('media_send_to_editor', array($this, 'addRemoteVideo'), 10, 3);
        add_filter('upload_mimes', array($this, 'svgsUploadMimes'));
        add_filter('wp_check_filetype_and_ext', array($this, 'svgsDisableRealMimeCheck'), 10, 4);
        add_filter('wp_prepare_attachment_for_js', array($this, 'svgsResponseForSvg'), 10, 3);
        add_action('admin_footer', array($this, 'editorFooter'));
        add_action('admin_print_styles', array($this, 'adminInlineStyle'));
        $format_mediatitle = wpmfGetOption('format_mediatitle');
        if ((int) $format_mediatitle === 1) {
            add_action('add_attachment', array($this, 'updateFileTitle'));
        }

        add_filter('attachment_fields_to_edit', array($this, 'attachmentFieldsToEdit'), 10, 2);
        add_filter('attachment_fields_to_save', array($this, 'attachmentFieldsToSave'), 10, 2);
        add_action('wpml_media_create_duplicate_attachment', array($this, 'mediaSetFilesToFolderWpml'), 10, 2);
        add_action('wp_ajax_wpmf', array($this, 'startProcess'));
        add_filter('wpmf_syncMediaExternal', array($this, 'syncMediaExternal'), 10, 2);
    }

    /**
     * Handle redirects to setup/welcome page after install and updates.
     *
     * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
     *
     * @return void
     */
    public function adminRedirects()
    {
        // Setup wizard redirect
        if (is_null(get_option('_wpmf_activation_redirect', null)) && is_null(get_option('wpmf_version', null))) {
            // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
            if ((!empty($_GET['page']) && in_array($_GET['page'], array('wpmf-setup')))) {
                return;
            }

            wp_safe_redirect(admin_url('index.php?page=wpmf-setup'));
            exit;
        }
    }

    /**
     * Includes WP Media Folder setup
     *
     * @return void
     */
    public function includes()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (!empty($_GET['page'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
            switch ($_GET['page']) {
                case 'wpmf-setup':
                    require_once WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/install-wizard/install-wizard.php';
                    break;
            }
        }
    }

    /**
     * Create user folder
     *
     * @return void
     */
    public function createUserFolder()
    {
        // insert term when user login and enable option 'Display only media by User/User'
        global $current_user;
        $user_roles = $current_user->roles;
        $role       = array_shift($user_roles);

        /**
         * Filter check capability of current user when create user folder
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'create_user_folder');
        if ($role !== 'administrator' && $wpmf_capability) {
            $wpmf_create_folder = get_option('wpmf_create_folder');
            if ($wpmf_create_folder === 'user') {
                $slug       = sanitize_title($current_user->data->user_login) . '-wpmf';
                $check_term = get_term_by('slug', $slug, WPMF_TAXO);
                if (empty($check_term)) {
                    $wpmf_checkbox_tree = get_option('wpmf_checkbox_tree');
                    if (!empty($wpmf_checkbox_tree)) {
                        $current_parrent = get_term($wpmf_checkbox_tree, WPMF_TAXO);
                        if (!empty($current_parrent)) {
                            $parent = $wpmf_checkbox_tree;
                        } else {
                            $parent = 0;
                        }
                    } else {
                        $parent = 0;
                    }
                    $inserted = wp_insert_term(
                        $current_user->data->user_login,
                        WPMF_TAXO,
                        array('parent' => $parent, 'slug' => $slug)
                    );
                    if (!is_wp_error($inserted)) {
                        wp_update_term($inserted['term_id'], WPMF_TAXO, array('term_group' => $current_user->data->ID));
                    }
                }
            } elseif ($wpmf_create_folder === 'role') {
                $slug       = sanitize_title($role) . '-wpmf-role';
                $check_term = get_term_by('slug', $slug, WPMF_TAXO);
                if (empty($check_term)) {
                    wp_insert_term($role, WPMF_TAXO, array('parent' => 0, 'slug' => $slug));
                }
            }
        }
    }

    /**
     * Sync media from media library to server
     *
     * @param integer $folderID      Id folder on media library
     * @param integer $attachment_id Id of file
     *
     * @return mixed
     */
    public function syncMediaExternal($folderID = 0, $attachment_id = 0)
    {
        $lists      = get_option('wpmf_list_sync_media');
        $folder_ftp = '';
        if (isset($lists[$folderID])) {
            $folder_ftp = $lists[$folderID]['folder_ftp'];
        }

        $file_path = get_attached_file($attachment_id);

        $filename = pathinfo($file_path);
        if (file_exists($file_path) && file_exists($folder_ftp)) {
            copy($file_path, $folder_ftp . '/' . $filename['basename']);
        }

        return $folderID;
    }

    /**
     * Run ajax
     *
     * @return void
     */
    public function startProcess()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_REQUEST['task'])) {
            switch ($_REQUEST['task']) {
                case 'import':
                    $this->importCategories();
                    break;
                case 'add_folder':
                    $this->addFolder();
                    break;
                case 'edit_folder':
                    $this->editFolder();
                    break;
                case 'delete_folder':
                    $this->deleteFolder();
                    break;
                case 'move_file':
                    $this->moveFile();
                    break;
                case 'move_folder':
                    $this->moveFolder();
                    break;
                case 'get_terms':
                    $this->getTerms();
                    break;
                case 'get_assign_tree':
                    $this->getAssignTree();
                    break;
                case 'gallery_get_image':
                    $this->galleryGetImage();
                    break;
                case 'set_object_term':
                    $this->setObjectTerm();
                    break;
                case 'create_remote_video':
                    $this->createRemoteVideo();
                    break;
                case 'get_user_media_tree':
                    $this->getUserMediaTree();
                    break;
                case 'set_folder_color':
                    $this->setFolderColor();
                    break;
                case 'delete_file':
                    $this->deleteFile();
                    break;
                case 'reorderfile':
                    $this->reorderFile();
                    break;
                case 'reorderfolder':
                    $this->reorderfolder();
                    break;
                case 'import_order':
                    $this->importOrder();
                    break;
                case 'save_folder_cover':
                    $this->saveFolderCover();
                    break;
                case 'update_link':
                    $this->updateLink();
                    break;
                case 'getcountfiles':
                    $this->getCountFilesInFolder();
                    break;
                case 'get_exclude_folders':
                    $this->getExcludeFolders();
                    break;
            }
        }
    }

    /**
     * Sync folders/files structure in all languages
     *
     * @param integer $attachment_id            ID of media file
     * @param integer $duplicated_attachment_id ID of duplicate media file
     *
     * @return void
     */
    public function mediaSetFilesToFolderWpml($attachment_id, $duplicated_attachment_id)
    {
        $terms = get_the_terms($attachment_id, WPMF_TAXO);
        if (!empty($terms)) {
            foreach ($terms as $term) {
                wp_set_object_terms($duplicated_attachment_id, $term->term_id, WPMF_TAXO, true);
                $this->addSizeFiletype($duplicated_attachment_id);

                /**
                 * Set attachment folder after upload with WPML plugin
                 *
                 * @param integer Attachment ID
                 * @param integer Target folder
                 * @param array   Extra informations
                 *
                 * @ignore Hook already documented
                 */
                do_action('wpmf_attachment_set_folder', $duplicated_attachment_id, $term->term_id, array('trigger' => 'upload'));
            }
        }
    }

    /**
     * Filters the attachment data prepared for JavaScript.
     * Base on /wp-includes/media.php
     *
     * @param array          $response   Array of prepared attachment data.
     * @param integer|object $attachment Attachment ID or object.
     * @param array          $meta       Array of attachment meta data.
     *
     * @return mixed $response
     */
    public function svgsResponseForSvg($response, $attachment, $meta)
    {
        if ($response['mime'] === 'image/svg+xml' && empty($response['sizes'])) {
            $svg_path = get_attached_file($attachment->ID);
            if (!file_exists($svg_path)) {
                // If SVG is external, use the URL instead of the path
                $svg_path = $response['url'];
            }
            $dimensions        = $this->svgsGetDimensions($svg_path);
            $response['sizes'] = array(
                'full' => array(
                    'url'         => $response['url'],
                    'width'       => $dimensions->width,
                    'height'      => $dimensions->height,
                    'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
                )
            );
        }

        return $response;
    }

    /**
     * Get dimension svg file
     *
     * @param string $svg Path of svg
     *
     * @return object width and height
     */
    public function svgsGetDimensions($svg)
    {
        $svg = simplexml_load_file($svg);
        if ($svg === false) {
            $width  = '0';
            $height = '0';
        } else {
            $attributes = $svg->attributes();
            $width      = (string) $attributes->width;
            $height     = (string) $attributes->height;
        }

        return (object) array('width' => $width, 'height' => $height);
    }

    /**
     * Add inline style and html
     *
     * @return void
     */
    public function adminInlineStyle()
    {
        global $pagenow, $current_screen;
        if (is_admin() && ($pagenow === 'customize.php' || (isset($current_screen) && $current_screen->base === 'toplevel_page_wptm'))) {
            $this->editorFooter();
        }
    }

    /**
     * Update count children of a folder
     *
     * @return void
     */
    public function updateCountTerm()
    {
        global $wpdb;
        $terms = get_categories(array('taxonomy' => WPMF_TAXO, 'hide_empty' => false));
        if (!empty($terms)) {
            foreach ($terms as $term) {
                // get count file in folder
                $count_object = $this->getCountFiles($term->term_id);
                // get count subfolder in folder
                $term_child       = get_term_children($term->term_id, WPMF_TAXO);
                $count_term_child = count($term_child);
                $count            = $count_object + $count_term_child;
                $wpdb->update($wpdb->term_taxonomy, compact('count'), array('term_taxonomy_id' => $term->term_id));
            }
        }
    }

    /**
     * Filters list of allowed mime types and file extensions.
     *
     * @param array $mimes Mime types keyed by the file extension regex corresponding to
     *                     those types. 'swf' and 'exe' removed from full list. 'htm|html' also
     *                     removed depending on '$user' capabilities.
     *
     * @return array $mimes
     */
    public function svgsUploadMimes($mimes = array())
    {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Filters the "real" file type of the given file.
     *
     * @param array  $data     File data array containing 'ext', 'type', and
     *                         'proper_filename' keys.
     * @param string $file     Full path to the file.
     * @param string $filename The name of the file (may differ from $file due to
     *                         $file being in a tmp directory).
     * @param array  $mimes    Key is the file extension with value as the mime type.
     *
     * @return array
     */
    public function svgsDisableRealMimeCheck($data, $file, $filename, $mimes)
    {
        $wp_filetype = wp_check_filetype($filename, $mimes);

        $ext             = $wp_filetype['ext'];
        $type            = $wp_filetype['type'];
        $proper_filename = $data['proper_filename'];

        return compact('ext', 'type', 'proper_filename');
    }

    /**
     * Ajax get gallery image
     *
     * @return void
     */
    public function galleryGetImage()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user when get gallery image
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'gallery_sort_image');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        if (!empty($_POST['ids']) && isset($_POST['wpmf_orderby']) && isset($_POST['wpmf_order'])) {
            $ids          = $_POST['ids'];
            $wpmf_orderby = $_POST['wpmf_orderby'];
            $wpmf_order   = $_POST['wpmf_order'];
            if ($wpmf_orderby === 'title' || $wpmf_orderby === 'date') {
                $wpmf_orderby = 'post_' . $wpmf_orderby;
                // query attachment by orderby and order
                $args  = array(
                    'posts_per_page' => - 1,
                    'post_type'      => 'attachment',
                    'post__in'       => $ids,
                    'post_status'    => 'any',
                    'orderby'        => $wpmf_orderby,
                    'order'          => $wpmf_order
                );
                $query = new WP_Query($args);
                $posts = $query->get_posts();
                wp_send_json($posts);
            }
        }
        wp_send_json(false);
    }

    /**
     * Load plugin text domain
     *
     * @return void
     */
    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(
            'wpmf',
            false,
            dirname(plugin_basename(WPMF_FILE)) . '/languages/'
        );
    }

    /**
     * Load styles
     *
     * @return void
     */
    public function loadAssets()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        if (!get_option('_wpmf_import_order_notice_flag', false)) {
            wp_enqueue_script(
                'import-custom-order',
                plugins_url('assets/js/import_custom_order.js', dirname(__FILE__)),
                array('jquery'),
                WPMF_VERSION
            );
        }

        $design = wpmfGetOption('folder_design');
        if ($design === 'material_design') {
            // load style for material folder design
            wp_enqueue_style(
                'wpmf-folder-design',
                plugins_url('/assets/css/material/folder_design.css', dirname(__FILE__)),
                array(),
                WPMF_VERSION
            );
        }

        wp_enqueue_style(
            'wpmf-jaofiletree',
            plugins_url('/assets/css/jaofiletree.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-style',
            plugins_url('/assets/css/style.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );
        wp_enqueue_style(
            'wpmf-material',
            plugins_url('/assets/css/material.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );
        wp_enqueue_style(
            'wpmf-mdl',
            plugins_url('/assets/css/modal-dialog/mdl-jquery-modal-dialog.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );
        wp_enqueue_style(
            'wpmf-deep_orange',
            plugins_url('/assets/css/modal-dialog/material.deep_orange-amber.min.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-google-icon',
            plugins_url('/assets/css/google-icon.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );

        wp_enqueue_style(
            'wpmf-style-qtip',
            plugins_url('/assets/css/jquery.qtip.css', dirname(__FILE__)),
            array(),
            WPMF_VERSION
        );
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'wpmf-material',
            plugins_url('/assets/js/modal-dialog/material.min.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );
        wp_enqueue_script(
            'wpmf-mdl',
            plugins_url('/assets/js/modal-dialog/mdl-jquery-modal-dialog.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_register_script(
            'wpmf-base',
            plugins_url('/assets/js/script.js', dirname(__FILE__)),
            array('jquery', 'plupload'),
            WPMF_VERSION
        );
        wp_enqueue_script('wpmf-base');

        $hide_tree = wpmfGetOption('hide_tree');
        if (isset($hide_tree) && (int) $hide_tree === 1) {
            wp_register_script(
                'wpmf-folder-tree',
                plugins_url('/assets/js/folder_tree.js', dirname(__FILE__)),
                array('wpmf-base'),
                WPMF_VERSION
            );
            wp_enqueue_script('wpmf-folder-tree');
        }

        wp_register_script(
            'wpmf-folder-snackbar',
            plugins_url('/assets/js/snackbar.js', dirname(__FILE__)),
            array('wpmf-base'),
            WPMF_VERSION
        );
        wp_enqueue_script('wpmf-folder-snackbar');

        wp_register_script(
            'duplicate-image',
            plugins_url('assets/js/duplicate-image.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION,
            true
        );
        wp_register_script(
            'wpmf-media-filters',
            plugins_url('/assets/js/media-filters.js', dirname(__FILE__)),
            array('jquery', 'plupload'),
            WPMF_VERSION
        );
        wp_enqueue_script('wpmf-media-filters');

        wp_enqueue_script(
            'wpmf-script-qtip',
            plugins_url('/assets/js/jquery.qtip.min.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION,
            true
        );
        wp_enqueue_script(
            'wpmf-assign-tree',
            plugins_url('/assets/js/assign_image_folder_tree.js', dirname(__FILE__)),
            array('jquery'),
            WPMF_VERSION
        );

        $params = $this->localizeScript();
        wp_localize_script('wpmf-base', 'wpmf', $params);
        wp_enqueue_script('wplink');
        wp_enqueue_style('editor-buttons');
    }

    /**
     * Load style and script
     *
     * @return void
     */
    public function adminPageTableScript()
    {
        global $pagenow;
        /**
         * Filter check capability of current user when load assets
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'load_script_style');
        if ($wpmf_capability) {
            if ($pagenow === 'upload.php') {
                $this->loadAssets();
            } elseif ($pagenow === 'media-new.php') {
                // Add current folder to hidden fields on media-new.php page
                add_filter('upload_post_params', function ($params) {
                    // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
                    if (isset($_GET['wpmf-folder'])) {
                        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
                        $params['wpmf_folder'] = (int) $_GET['wpmf-folder'];
                    }
                    return $params;
                }, 1, 1);
            }
        }
    }

    /**
     * Includes styles and some scripts
     *
     * @return void
     */
    public function mediaPageTableScript()
    {
        /**
         * Filter check capability of current user to load assets
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'load_script_style');
        if ($wpmf_capability) {
            $this->loadAssets();
        }
    }

    /**
     * Localize a script.
     * Works only if the script has already been added.
     *
     * @return array
     */
    public function localizeScript()
    {
        global $pagenow;
        $design           = wpmfGetOption('folder_design');
        $option_override  = get_option('wpmf_option_override');
        $option_duplicate = get_option('wpmf_option_duplicate');

        if ($pagenow === 'upload.php') {
            $categorytype = $this->getFiletype();
            // get count file archive
            $count_zip = $this->countExt('application');
            // get count file pdf
            $count_pdf = $this->countExt('application/pdf');
        } else {
            $categorytype = '';
            $count_zip    = 0;
            $count_pdf    = 0;
        }

        // get some options
        $terms               = $this->getAttachmentTerms();
        $parents_array       = $this->getParrentsArray($terms['attachment_terms']);
        $usegellery          = get_option('wpmf_usegellery');
        $get_plugin_active   = json_encode(get_option('active_plugins'));
        $option_media_remove = get_option('wpmf_option_media_remove');
        $option_search       = get_option('wpmf_option_searchall');

        $s_dimensions = get_option('wpmf_selected_dimension');
        $size         = json_decode($s_dimensions);
        $s_weights    = get_option('wpmf_weight_selected');
        $weight       = json_decode($s_weights);
        // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        if (isset($_GET['attachment_size'])) {
            $attachment_size = $_GET['attachment_size'];
        } else {
            $attachment_size = '';
        }

        if (isset($_GET['attachment_weight'])) {
            $attachment_weight = $_GET['attachment_weight'];
        } else {
            $attachment_weight = '';
        }

        // get param sort media
        if (isset($_GET['orderby']) && isset($_GET['order'])) {
            $media_order = $_GET['orderby'] . '|' . $_GET['order'];
        } else {
            if (isset($_GET['media-order-media'])) {
                $media_order = $_GET['media-order-media'];
            } else {
                $media_order = 'all';
            }
        }

        if (isset($_GET['wpmf-display-media-filters']) && $_GET['wpmf-display-media-filters'] === 'yes') {
            $display_own_media = 'yes';
        } else {
            $display_own_media = 'all';
        }

        // get param sort folder
        if (isset($_GET['folder_order'])) {
            $folder_order = $_GET['folder_order'];
        } else {
            $folder_order = 'all';
        }

        $wpmf_order_f = isset($_GET['folder_order']) ? $_GET['folder_order'] : '';
        // phpcs:enable
        $option_countfiles = get_option('wpmf_option_countfiles');
        $option_hoverimg   = get_option('wpmf_option_hoverimg');
        $root_media_root   = get_term_by('id', $this->folderRootId, WPMF_TAXO);
        if (empty($root_media_root)) {
            $root_media = 0;
        } else {
            $root_media = $root_media_root->term_id;
        }
        $hide_tree         = wpmfGetOption('hide_tree');
        $hide_remote_video = wpmfGetOption('hide_remote_video');
        // get colors folder option
        $colors_option = wpmfGetOption('folder_color');

        // get default gallery config
        $gallery_configs = wpmfGetOption('gallery_settings');
        $l18n            = $this->translation();
        $vars            = array(
            'site_url'              => site_url(),
            'folder_design'         => $design,
            'override'              => (int) $option_override,
            'duplicate'             => (int) $option_duplicate,
            'wpmf_file'             => $categorytype,//
            'wpmfcount_zip'         => $count_zip,//
            'wpmfcount_pdf'         => $count_pdf, //
            'wpmf_categories'       => $terms['attachment_terms'],
            'wpmf_categories_order' => $terms['attachment_terms_order'],
            'parents_array'         => $parents_array, //
            'taxo'                  => WPMF_TAXO,
            'wpmf_role'             => $terms['role'],
            'wpmf_active_media'     => (int) $terms['wpmf_active_media'],
            'term_root_username'    => $terms['term_root_username'],
            'term_root_id'          => $terms['term_root_id'],
            'wpmf_pagenow'          => $terms['wpmf_pagenow'],
            'base'                  => $terms['base'],
            'usegellery'            => (int) $usegellery,
            'get_plugin_active'     => $get_plugin_active,//
            'wpmf_post_type'        => $terms['wpmf_post_type'],
            'wpmf_current_userid'   => get_current_user_id(),
            'wpmf_post_mime_type'   => $terms['post_mime_types'],//
            'wpmf_type'             => $terms['post_type'],//
            'usefilter'             => (int) $terms['useorder'],
            'wpmf_remove_media'     => (int) $option_media_remove,
            'wpmf_search'           => (int) $option_search,
            'ajaxurl'               => admin_url('admin-ajax.php'),
            'wpmf_size'             => $size,
            'size'                  => $attachment_size,
            'wpmf_weight'           => $weight,
            'weight'                => $attachment_weight,
            'wpmf_order_media'      => $media_order,
            'folder_order'          => $folder_order,
            'display_own_media'     => $display_own_media,
            'wpmf_order_f'          => $wpmf_order_f,
            'option_countfiles'     => (int) $option_countfiles,
            'option_hoverimg'       => (int) $option_hoverimg,
            'root_media_root'       => $root_media,
            'parent'                => $terms['parent'],
            'colors'                => $colors_option,
            'hide_tree'             => $hide_tree,
            'hide_remote_video'     => $hide_remote_video,
            'gallery_configs'       => $gallery_configs,
            'wpmf_nonce'            => wp_create_nonce('wpmf_nonce')
        );

        return array('l18n' => $l18n, 'vars' => $vars);
    }

    /**
     * Get translation string
     *
     * @return array
     */
    public function translation()
    {
        $l18n = array(
            'change_folder'         => __('Change Folders', 'wpmf'),
            'create_folder'         => __('Create Folder', 'wpmf'),
            'media_folder'          => __('Media Library', 'wpmf'),
            'promt'                 => __('New folder name:', 'wpmf'),
            'edit_file_lb'          => __('Please enter a new name for the item:', 'wpmf'),
            'edit_media'            => __('Edit media', 'wpmf'),
            'title_media'           => __('Title', 'wpmf'),
            'caption_media'         => __('Caption', 'wpmf'),
            'alt_media'             => __('Alternative Text', 'wpmf'),
            'desc_media'            => __('Description', 'wpmf'),
            'new_folder'            => __('New folder', 'wpmf'),
            'new_folder_tree'       => __('NEW FOLDER', 'wpmf'),
            'alert_add'             => __('A folder already exists here with the same name. Please try with another name, thanks :)', 'wpmf'),
            'alert_delete_file'     => __('Are you sure to want to delete this file?', 'wpmf'),
            'update_file_msg'       => __('Update failed. Please try with another name, thanks :)', 'wpmf'),
            'alert_delete'          => __('Are you sure to want to delete this folder?', 'wpmf'),
            'alert_delete_all'      => __('This folder contains other sub folders and files. Are you sure want to delete it ?', 'wpmf'),
            'alert_delete1'         => __('this folder contains sub-folder or file, delete sub-folders or file before', 'wpmf'),
            'display_own_media'     => __('Display only my own medias', 'wpmf'),
            'create_gallery_folder' => __('Create a gallery from folder', 'wpmf'),
            'home'                  => __('Home', 'wpmf'),
            'youarehere'            => __('You are here', 'wpmf'),
            'back'                  => __('Back', 'wpmf'),
            'dragdrop'              => __('Drag and Drop me hover a folder', 'wpmf'),
            'smallview'             => __('Small View', 'wpmf'),
            'pdf'                   => __('PDF', 'wpmf'),
            'zip'                   => __('Zip & archives', 'wpmf'),
            'other'                 => __('Other', 'wpmf'),
            'link_to'               => __('Link To', 'wpmf'),
            'folder_cover'          => __('Folder cover', 'wpmf'),
            'error_replace'         => __('To replace a media and keep the link to this media working,it must be in the same format, ie. jpg > jpg, zip > zip� Thanks!', 'wpmf'),
            'uploaded_to_this'      => __('Uploaded to this ', 'wpmf'),
            'mimetype'              => __('All media items', 'wpmf'),
            'replace'               => __('Send new file and replace', 'wpmf'),
            'duplicate_text'        => __('Duplicate', 'wpmf'),
            'wpmf_undo'             => __('Undo.', 'wpmf'),
            'wpmf_undo_remove'      => __('Folder removed.', 'wpmf'),
            'wpmf_undo_movefile'    => __('File(s) moved.', 'wpmf'),
            'wpmf_undo_movefolder'  => __('Moved a folder.', 'wpmf'),
            'wpmf_undo_editfolder'  => __('Folder name updated', 'wpmf'),
            'wpmf_file_replace'     => __('File replaced!', 'wpmf'),
            'wpmf_fileupload'       => __('File upload on the way...', 'wpmf'),
            'wpmf_undofilter'       => __('Filter applied', 'wpmf'),
            'wpmf_remove_filter'    => __('Media filters removed', 'wpmf'),
            'cancel'                => __('Cancel', 'wpmf'),
            'create'                => __('Create', 'wpmf'),
            'save'                  => __('Save', 'wpmf'),
            'ok'                    => __('OK', 'wpmf'),
            'delete'                => __('Delete', 'wpmf'),
            'remove'                => __('Remove', 'wpmf'),
            'get_url_file'          => __('Get URL', 'wpmf'),
            'edit_folder'           => __('Edit Folder', 'wpmf'),
            'change_color'          => __('Change color', 'wpmf'),
            'edit_file'             => __('Edit', 'wpmf'),
            'information'           => __('Information', 'wpmf'),
            'cannot_copy'           => __('Cannot copy text', 'wpmf'),
            'unable_copy'           => __('Unable to copy.', 'wpmf'),
            'clear_filters'         => __('Clear filters and sorting', 'wpmf'),
            'label_filter_order'    => __('Filter or order media', 'wpmf'),
            'label_remove_filter'   => __('Remove all filters', 'wpmf'),
            'wpmf_remove_file'      => __('Media removed', 'wpmf'),
            'wpmf_addfolder'        => __('Folder added', 'wpmf'),
            'wpmf_media_uploaded'   => __('New media uploaded', 'wpmf'),
            'video_uploaded'        => __('New video uploaded', 'wpmf'),
            'assign_tree_label'     => __('Media folders selection', 'wpmf'),
            'label_assign_tree'     => __('Select the folders where the media belong', 'wpmf'),
            'label_apply'           => __('Apply', 'wpmf'),
            'folder_selection'      => __('New folder selection applied to media', 'wpmf'),
            'all_size_label'        => __('All sizes', 'wpmf'),
            'all_weight_label'      => __('All weight', 'wpmf'),
            'order_folder_label'    => __('Sort folder', 'wpmf'),
            'order_img_label'       => __('Sort media', 'wpmf'),
            'sort_media'            => __('Default sorting', 'wpmf'),
            'media_type'            => __('Media type', 'wpmf'),
            'date'                  => __('Date', 'wpmf'),
            'lang_size'             => __('Size', 'wpmf'),
            'lang_weight'           => __('Weight', 'wpmf'),
            'remote_video'          => __('Add a Youtube, Vimeo or Dailymotion video URL', 'wpmf'),
            'remote_video_lb_box'   => __('Copy Youtube, Vimeo or Dailymotion video URL', 'wpmf'),
            'remote_video_tooltip'  => __('Youtube video', 'wpmf'),
            'upload'                => __('UPLOAD', 'wpmf'),
            'filesize_label'        => __('File size:', 'wpmf'),
            'dimensions_label'      => __('Dimensions:', 'wpmf'),
            'no_media_label'        => __('No', 'wpmf'),
            'yes_media_label'       => __('Yes', 'wpmf'),
            'sort_label'            => __('Sorting / Filtering', 'wpmf'),
            'order_folder'          => array(
                'name-ASC'  => __('Name (Ascending)', 'wpmf'),
                'name-DESC' => __('Name (Descending)', 'wpmf'),
                'id-ASC'    => __('ID (Ascending)', 'wpmf'),
                'id-DESC'   => __('ID (Descending)', 'wpmf'),
                'custom'    => __('Custom order', 'wpmf'),
            ), // List of available ordering type for folders
            'order_media'           => array(
                'date|asc'      => __('Date (Ascending)', 'wpmf'),
                'date|desc'     => __('Date (Descending)', 'wpmf'),
                'title|asc'     => __('Title (Ascending)', 'wpmf'),
                'title|desc'    => __('Title (Descending)', 'wpmf'),
                'size|asc'      => __('Size (Ascending)', 'wpmf'),
                'size|desc'     => __('Size (Descending)', 'wpmf'),
                'filetype|asc'  => __('File type (Ascending)', 'wpmf'),
                'filetype|desc' => __('File type (Descending)', 'wpmf'),
                'custom'        => __('Custom order', 'wpmf'),
            ), // List of available ordering type for attachements
            'colorlists'            => array(
                '#ac725e' => __('Chocolate ice cream', 'wpmf'),
                '#d06b64' => __('Old brick red', 'wpmf'),
                '#f83a22' => __('Cardinal', 'wpmf'),
                '#fa573c' => __('Wild strawberries', 'wpmf'),
                '#ff7537' => __('Mars orange', 'wpmf'),
                '#ffad46' => __('Yellow cab', 'wpmf'),
                '#42d692' => __('Spearmint', 'wpmf'),
                '#16a765' => __('Vern fern', 'wpmf'),
                '#7bd148' => __('Asparagus', 'wpmf'),
                '#b3dc6c' => __('Slime green', 'wpmf'),
                '#fbe983' => __('Desert sand', 'wpmf'),
                '#fad165' => __('Macaroni', 'wpmf'),
                '#92e1c0' => __('Sea foam', 'wpmf'),
                '#9fe1e7' => __('Pool', 'wpmf'),
                '#9fc6e7' => __('Denim', 'wpmf'),
                '#4986e7' => __('Rainy sky', 'wpmf'),
                '#9a9cff' => __('Blue velvet', 'wpmf'),
                '#b99aff' => __('Purple dino', 'wpmf'),
                '#8f8f8f' => __('Mouse', 'wpmf'),
                '#cabdbf' => __('Mountain grey', 'wpmf'),
                '#cca6ac' => __('Earthworm', 'wpmf'),
                '#f691b2' => __('Bubble gum', 'wpmf'),
                '#cd74e6' => __('Purple rain', 'wpmf'),
                '#a47ae2' => __('Toy eggplant', 'wpmf'),
            ), // colorlists
            'placegolder_color'     => __('Custom color #8f8f8f', 'wpmf'),
            'bgcolorerror'          => __('Change background folder has failed', 'wpmf'),
            'search_folder'         => __('Search folders...', 'wpmf'),
            'copy_url'              => __('Media URL copied!', 'wpmf'),
            'reload_media'          => __('Refresh media library', 'wpmf'),
            'msg_upload_folder'     => __('You are uploading media to folder: ', 'wpmf'),
        );

        return $l18n;
    }

    /**
     * Get parrents folder array
     *
     * @param array $attachment_terms All wpmf categories
     *
     * @return array
     */
    public function getParrentsArray($attachment_terms)
    {
        $wcat    = isset($_GET['wcat']) ? $_GET['wcat'] : '0'; // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        $parents = array();
        $pCat    = (int) $wcat;
        while ((int) $pCat !== 0) {
            $parents[] = $pCat;
            $pCat      = (int) $attachment_terms[$pCat]['parent_id'];
        }

        $parents_array = array_reverse($parents);
        return $parents_array;
    }

    /**
     * Get all params
     *
     * @return array
     */
    public function getAttachmentTerms()
    {
        global $pagenow, $current_user, $current_screen;
        if (isset($current_screen->base)) {
            $base = $current_screen->base;
        } else {
            $base = '';
        }
        // Retrieve cover images
        $cover_images   = get_option('wpmf_field_bgfolder');
        $cf_count_files = get_option('wpmf_option_countfiles');

        // get categories
        $attachment_terms = array();
        $terms            = get_categories(
            array(
                'hide_empty'                   => false,
                'taxonomy'                     => WPMF_TAXO,
                'pll_get_terms_not_translated' => 1
            )
        );
        $terms            = $this->parentSort($terms);
        $term_rootId      = 0;

        $attachment_terms_order = array();
        $wpmf_create_folder     = get_option('wpmf_create_folder');
        $wpmf_active_media      = get_option('wpmf_active_media');
        $user_roles             = $current_user->roles;
        $role                   = array_shift($user_roles);
        $term_root_username     = '';
        $term_slug              = '';
        /* role == administrator or disable option 'Display only media by User/User' */
        $parent = 0;
        if ($role === 'administrator' || (int) $wpmf_active_media === 0) {
            $attachment_terms[]       = array(
                'id'        => 0,
                'label'     => __('Select a folder', 'wpmf'),
                'slug'      => '',
                'parent_id' => 0
            );
            $attachment_terms_order[] = '0';
        } else { // role != administrator or enable option 'Display only media by User/User'
            if ($wpmf_create_folder === 'user') {
                $term_root_username = $current_user->user_login;
                $wpmf_checkbox_tree = get_option('wpmf_checkbox_tree');
                if (!empty($wpmf_checkbox_tree)) {
                    $parent = $wpmf_checkbox_tree;
                }
            } elseif ($wpmf_create_folder === 'role') {
                $term_root_username = $role;
            }
            $wpmfterm = $this->termRoot();
            if (!empty($wpmfterm)) {
                $term_rootId                    = $wpmfterm['term_rootId'];
                $term__label                    = $wpmfterm['term_label'];
                $term__parent                   = $wpmfterm['term_parent'];
                $term_slug                      = $wpmfterm['term_slug'];
                $attachment_terms[$term_rootId] = array(
                    'id'        => $term_rootId,
                    'label'     => $term__label,
                    'slug'      => $term_slug,
                    'parent_id' => $term__parent
                );
                $attachment_terms_order[]       = $term_rootId;
            } else {
                $attachment_terms[]       = array(
                    'id'        => 0,
                    'label'     => __('Select a folder', 'wpmf'),
                    'slug'      => '',
                    'parent_id' => 0
                );
                $attachment_terms_order[] = 0;
                $term_slug                = '';
            }
        }

        /* role != administrator or enable option 'Display only media by User/User' */
        if (isset($wpmf_active_media) && (int) $wpmf_active_media === 1 && $role !== 'administrator') {
            $wpmfterm = $this->termRoot();
            if (!empty($wpmfterm)) {
                $term_rootId = $wpmfterm['term_rootId'];
            } else {
                $term_rootId = 0;
            }

            $current_role = $this->getRoles(get_current_user_id());
            $ts           = get_term_children($term_rootId, WPMF_TAXO);
            foreach ($terms as $term) {
                if ((int) $term->term_id === (int) $this->folderRootId) {
                    continue;
                }

                // get custom order folder
                $order = $this->getOrderFolder($term->term_id);
                if ($wpmf_create_folder === 'user') {
                    if ((int) $term->term_group === get_current_user_id()) {
                        if (in_array($term->term_id, $ts)) {
                            $attachment_terms[$term->term_id] = array(
                                'id'          => $term->term_id,
                                'label'       => $term->name,
                                'slug'        => $term->slug,
                                'parent_id'   => $term->category_parent,
                                'depth'       => $term->depth,
                                'term_group'  => $term->term_group,
                                'cover_image' => isset($cover_images[$term->term_id]) ? $cover_images[$term->term_id] : '',
                                'order'       => $order
                            );
                            if (isset($cf_count_files) && (int) $cf_count_files === 1) {
                                $attachment_terms[$term->term_id]['files_count'] = $this->getCountFiles($term->term_id);
                            }
                            $attachment_terms_order[] = $term->term_id;
                        }
                    }
                } else {
                    $crole = $this->getRoles($term->term_group);
                    if ($current_role === $crole && $term_slug !== $term->slug) {
                        $attachment_terms[$term->term_id] = array(
                            'id'          => $term->term_id,
                            'label'       => $term->name,
                            'slug'        => $term->slug,
                            'parent_id'   => $term->category_parent,
                            'depth'       => $term->depth,
                            'term_group'  => $term->term_group,
                            'cover_image' => isset($cover_images[$term->term_id]) ? $cover_images[$term->term_id] : '',
                            'order'       => $order
                        );
                        if (isset($cf_count_files) && (int) $cf_count_files === 1) {
                            $attachment_terms[$term->term_id]['files_count'] = $this->getCountFiles($term->term_id);
                        }
                        $attachment_terms_order[] = $term->term_id;
                    }
                }
            }
        } else { // role == administrator or disable option 'Display only media by User/User'
            $current_role = 'administrator';
            foreach ($terms as $term) {
                if ((int) $term->term_id === (int) $this->folderRootId) {
                    continue;
                }
                // get custom order folder
                $order                            = $this->getOrderFolder($term->term_id);
                $attachment_terms[$term->term_id] = array(
                    'id'          => $term->term_id,
                    'label'       => $term->name,
                    'slug'        => $term->slug,
                    'parent_id'   => $term->category_parent,
                    'depth'       => $term->depth,
                    'term_group'  => $term->term_group,
                    'cover_image' => isset($cover_images[$term->term_id]) ? $cover_images[$term->term_id] : '',
                    'order'       => $order
                );
                if (isset($cf_count_files) && (int) $cf_count_files === 1) {
                    $attachment_terms[$term->term_id]['files_count'] = $this->getCountFiles($term->term_id);
                }
                $attachment_terms_order[] = $term->term_id;
            }
        }

        $post_mime_types = get_post_mime_types();
        $useorder        = get_option('wpmf_useorder');

        // get post type
        global $post;
        if (!empty($post) && !empty($post->post_type)) {
            $post_type = $post->post_type;
        } else {
            $post_type = '';
        }

        if (in_array('js_composer/js_composer.php', get_option('active_plugins'))) {
            $wpmf_post_type = 1;
        } else {
            $wpmf_post_type = 0;
        }

        return array(
            'role'                   => $current_role,
            'wpmf_active_media'      => (int) $wpmf_active_media,
            'term_root_username'     => $term_root_username,
            'term_root_id'           => (int) $term_rootId,
            'attachment_terms'       => $attachment_terms,
            'attachment_terms_order' => $attachment_terms_order,
            'wpmf_pagenow'           => $pagenow,
            'base'                   => $base,
            'post_mime_types'        => $post_mime_types,
            'useorder'               => (int) $useorder,
            'post_type'              => $post_type,
            'wpmf_post_type'         => $wpmf_post_type,
            'parent'                 => $parent
        );
    }

    /**
     * Get custom order folder
     *
     * @param integer $term_id Id of folder
     *
     * @return integer|mixed
     */
    public function getOrderFolder($term_id)
    {
        $order = get_term_meta($term_id, 'wpmf_order', true);
        if (empty($order)) {
            $order = 0;
        }
        return $order;
    }

    /**
     * Show notice of import size for files
     *
     * @return void
     */
    public function showNoticeImportSize()
    {
        global $wpdb;
        $total = $wpdb->get_var('SELECT COUNT(posts.ID) as total FROM ' . $wpdb->prefix . 'posts as posts
               WHERE   posts.post_type = "attachment"');

        if ($total > 10000) {
            wp_enqueue_script(
                'wpmfimport-size',
                plugins_url('/assets/js/import_size_filetype.js', dirname(__FILE__)),
                array('jquery'),
                WPMF_VERSION
            );

            echo '<div class="error" id="wpmf_error">'
                 . '<p>'
                 . esc_html__('Your website has a large image library (>10000 images).
                 WP Media Folder needs to index all of them to run smoothly.
                  It may take few minutes... keep cool :)', 'wpmf')
                 . '<a href="#" class="button button-primary"
                 style="margin: 0 5px;" data-page="0" id="wmpfImportsize">
                 ' . esc_html__('Import size and filetype now', 'wpmf') . ' 
                 <span class="spinner" style="display:none"></span></a>'
                 . '</p>'
                 . '</div>';
        }
    }

    /**
     * Add NextGEN galleries notice
     *
     * @return void
     */
    public function showNoticeImportGallery()
    {
        /**
         * Filter check capability of current user to show notice import gallery
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('manage_options'), 'notice_import_gallery');
        if ($wpmf_capability) {
            echo '<div class="error" id="wpmf_error">'
                 . '<p>'
                 . esc_html__('You\'ve just installed WP Media Folder,
            to save your time we can import your nextgen gallery into WP Media Folder', 'wpmf')
                 . '<a href="#" class="button button-primary"
            style="margin: 0 5px;" id="wmpfImportgallery">
            ' . esc_html__('Sync/Import NextGEN galleries', 'wpmf') . '</a> or
             <a href="#" style="margin: 0 5px;" class="button wmpfNoImportgallery">
             ' . esc_html__('No thanks ', 'wpmf') . '</a>
             <span class="spinner" style="display:none; margin:0; float:none"></span>'
                 . '</p>'
                 . '</div>';
        }
    }

    /**
     * Show notice of import category
     *
     * @return void
     */
    public function showNotice()
    {
        /**
         * Filter check capability of current user to show notice import category
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('manage_options'), 'notice_import_category');
        if ($wpmf_capability) {
            wp_enqueue_script(
                'wpmfimport-category',
                plugins_url('/assets/js/import_category.js', dirname(__FILE__)),
                array('jquery'),
                WPMF_VERSION
            );

            $params = $this->localizeScript();
            wp_localize_script('wpmfimport-category', 'wpmf', $params);

            echo '<div class="error" id="wpmf_error">'
                 . '<p>'
                 . esc_html__('Thanks for using WP Media Folder!
                 Save time by transforming post categories into media folders automatically. More info', 'wpmf')
                 . '<a href="#" class="button button-primary"
                 style="margin: 0 5px;" id="wmpfImportBtn">
                 ' . esc_html__('Import categories now', 'wpmf') . ' 
                 <span class="spinner" style="display:none"></span></a> or 
                 <a href="#" 
                  style="margin: 0 5px;" class="button wmpfNoImportBtn">
                  ' . esc_html__('No thanks ', 'wpmf') . ' <span class="spinner" style="display:none"></span></a>'
                 . '</p>'
                 . '</div>';
        }
    }

    /**
     * This function do import wordpress category default
     *
     * @return void
     */
    public static function importCategories()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        $option_import_taxo = get_option('_wpmf_import_notice_flag');
        if (isset($option_import_taxo) && $option_import_taxo === 'yes') {
            die();
        }
        if ($_POST['doit'] === 'true') {
            // get all term taxonomy 'category'
            $terms = get_categories(array(
                'taxonomy'   => 'category',
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => false,
                'child_of'   => 0
            ));

            $termsRel = array('0' => 0);
            // insert wpmf-category term
            foreach ($terms as $term) {
                $inserted = wp_insert_term(
                    $term->name,
                    WPMF_TAXO,
                    array('slug' => wp_unique_term_slug($term->slug, $term))
                );
                if (is_wp_error($inserted)) {
                    wp_send_json($inserted->get_error_message());
                }
                $termsRel[$term->term_id] = array('id' => $inserted['term_id'], 'name' => $term->name);
            }
            // update parent wpmf-category term
            foreach ($terms as $term) {
                wp_update_term($termsRel[$term->term_id]['id'], WPMF_TAXO, array('parent' => $termsRel[$term->parent]['id']));

                /**
                 * Create a folder when importing categories
                 *
                 * @param integer Created folder ID
                 * @param string  Created folder name
                 * @param integer Parent folder ID
                 * @param array   Extra informations
                 *
                 * @ignore Hook already documented
                 */
                do_action('wpmf_create_folder', $termsRel[$term->term_id]['id'], $termsRel[$term->term_id]['name'], $termsRel[$term->parent], array('trigger' => 'import_categories'));
            }

            //update attachments
            $attachments = get_posts(array('posts_per_page' => - 1, 'post_type' => 'attachment'));
            foreach ($attachments as $attachment) {
                $terms      = wp_get_post_terms($attachment->ID, 'category');
                $termsArray = array();
                foreach ($terms as $term) {
                    $termsArray[] = $termsRel[$term->term_id]['id'];
                }
                if ($termsArray !== null) {
                    wp_set_post_terms($attachment->ID, $termsArray, WPMF_TAXO);

                    /**
                     * Set attachment folder after categories import
                     *
                     * @param integer Attachment ID
                     * @param integer Target folder
                     * @param array   Extra informations
                     *
                     * @ignore Hook already documented
                     */
                    do_action('wpmf_attachment_set_folder', $attachment->ID, $termsArray, array('trigger' => 'import_categories'));
                }
            }
        }
        if ($_POST['doit'] === 'true') {
            update_option('_wpmf_import_notice_flag', 'yes');
        } else {
            update_option('_wpmf_import_notice_flag', 'no');
        }
        die();
    }

    /**
     * Display or retrieve the HTML dropdown list of categories.
     *
     * @return void
     */
    public function addImageCategoryFilter()
    {
        // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        global $pagenow;
        if ($pagenow === 'upload.php') {
            $wpmf_active_media = get_option('wpmf_active_media');
            if (!function_exists('get_userdata')) {
                require_once(ABSPATH . 'wp-includes/pluggable.php');
            }
            $user_data  = get_userdata(get_current_user_id());
            $user_roles = $user_data->roles;
            $role       = array_shift($user_roles);

            $term_rootId = 0;
            $term_label  = __('Select a folder', 'wpmf');
            if ($role !== 'administrator' && (int) $wpmf_active_media === 1) {
                $wpmfterm           = $this->termRoot();
                $term_rootId        = $wpmfterm['term_rootId'];
                $term_label         = $wpmfterm['term_label'];
                $this->folderRootId = $term_rootId;
            }

            $root_media_root = get_term_by('id', $this->folderRootId, WPMF_TAXO);
            // get cookie last access folder
            if (isset($_COOKIE['lastAccessFolder_' . site_url()])) {
                $selected = $_COOKIE['lastAccessFolder_' . site_url()];
            } else {
                if (isset($_GET['wcat'])) {
                    $selected = $_GET['wcat'];
                } else {
                    if ($role !== 'administrator' && (int) $wpmf_active_media === 1) {
                        $selected = $term_rootId;
                    } else {
                        $selected = 0;
                    }
                }
            }

            if ($role !== 'administrator' && (int) $wpmf_active_media === 1) {
                $dropdown_options = array(
                    'exclude'           => $root_media_root->term_id,
                    'show_option_none'  => $term_label,
                    'option_none_value' => $term_rootId,
                    'hide_empty'        => false,
                    'hierarchical'      => true,
                    'orderby'           => 'name',
                    'taxonomy'          => WPMF_TAXO,
                    'class'             => 'wpmf-categories',
                    'name'              => 'wcat',
                    'child_of'          => $root_media_root->term_id,
                    'id'                => 'wpmf-media-category',
                    'selected'          => (int) $selected
                );
            } else {
                $dropdown_options = array(
                    'exclude'           => $root_media_root->term_id,
                    'show_option_none'  => __('Select a folder', 'wpmf'),
                    'option_none_value' => 0,
                    'hide_empty'        => false,
                    'hierarchical'      => true,
                    'orderby'           => 'name',
                    'taxonomy'          => WPMF_TAXO,
                    'class'             => 'wpmf-categories',
                    'name'              => 'wcat',
                    'id'                => 'wpmf-media-category',
                    'selected'          => (int) $selected
                );
            }

            wp_dropdown_categories($dropdown_options);
        }
        // phpcs:enable
    }

    /**
     * Query post in media list view
     *
     * @param object $query Params use to query attachment
     *
     * @return object $query
     */
    public function preGetPosts1($query)
    {
        global $pagenow;
        if ($pagenow !== 'upload.php') {
            if (empty($_REQUEST['query']['wpmf_nonce'])
                || !wp_verify_nonce($_REQUEST['query']['wpmf_nonce'], 'wpmf_nonce')) {
                return $query;
            }
        }

        if (!isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'attachment') {
            return $query;
        }
        global $pagenow;
        $option_search = get_option('wpmf_option_searchall');
        if ($pagenow === 'upload.php') {
            $current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $redirect    = false;
            if (isset($_GET['s']) && $_GET['s'] === '') {
                $current_url = remove_query_arg('s', $current_url);
                $redirect    = true;
            }
            if ($redirect) {
                wp_redirect($current_url);
                ob_end_flush();
                exit();
            }
            if (isset($_GET['page']) && $_GET['page'] === 'wp-retina-2x') {
                return $query;
            }

            if ((int) $option_search === 0 || (empty($_GET['s']) && (int) $option_search === 1)) {
                // get last access folder
                $cookie_name = str_replace('.', '_', 'lastAccessFolder_' . site_url());
                if (isset($_COOKIE[$cookie_name])) {
                    $selected = $_COOKIE[$cookie_name];
                } else {
                    if (isset($_GET['wcat']) && (int) $_GET['wcat'] !== 0) {
                        $selected = $_GET['wcat'];
                    } else {
                        $selected = 0;
                    }
                }

                if (isset($selected) && (int) $selected !== 0) {
                    // list view , query post with term_id != 0
                    $query->tax_query->queries[]    = array(
                        'taxonomy'         => WPMF_TAXO,
                        'field'            => 'term_id',
                        'terms'            => (int) $selected,
                        'include_children' => false
                    );
                    $query->query_vars['tax_query'] = $query->tax_query->queries;
                } else {
                    // grid view , query post with term_id != 0
                    $wpmf_active_media = get_option('wpmf_active_media');
                    if (!function_exists('get_userdata')) {
                        require_once(ABSPATH . 'wp-includes/pluggable.php');
                    }
                    $user_data  = get_userdata(get_current_user_id());
                    $user_roles = $user_data->roles;
                    $role       = array_shift($user_roles);
                    if ((int) $wpmf_active_media === 1 && $role !== 'administrator') {
                        $wpmfterm                       = $this->termRoot();
                        $term_rootId                    = $wpmfterm['term_rootId'];
                        $query->tax_query->queries[]    = array(
                            'taxonomy'         => WPMF_TAXO,
                            'field'            => 'term_id',
                            'terms'            => (int) $term_rootId,
                            'include_children' => false
                        );
                        $query->query_vars['tax_query'] = $query->tax_query->queries;
                    } else {
                        $terms = get_categories(array('hide_empty' => false, 'taxonomy' => WPMF_TAXO));
                        $cats  = array();
                        foreach ($terms as $term) {
                            if (!empty($term->term_id)) {
                                $cats[] = $term->term_id;
                            }
                        }

                        if (in_array($this->folderRootId, $cats)) {
                            $key = array_search($this->folderRootId, $cats);
                            unset($cats[$key]);
                        }
                        if (count($terms) !== 1) {
                            if (isset($query->tax_query)) {
                                $ob_in_root = get_objects_in_term($this->folderRootId, WPMF_TAXO);
                                if (count($ob_in_root) > 0) {
                                    $args = array(
                                        'relation' => 'OR',
                                        array(
                                            'taxonomy' => WPMF_TAXO,
                                            'field'    => 'term_id',
                                            'terms'    => $cats,
                                            'operator' => 'NOT IN'
                                        ),
                                        array(
                                            'taxonomy' => WPMF_TAXO,
                                            'field'    => 'term_id',
                                            'terms'    => $this->folderRootId,
                                            'operator' => 'IN'
                                        )
                                    );
                                } else {
                                    $args = array(
                                        array(
                                            'taxonomy' => WPMF_TAXO,
                                            'field'    => 'term_id',
                                            'terms'    => $cats,
                                            'operator' => 'NOT IN'
                                        )
                                    );
                                }

                                $query->set('tax_query', $args);
                            }
                        }
                    }
                }
            }

            if (isset($_GET['wpmf-display-media-filters']) && $_GET['wpmf-display-media-filters'] === 'yes') {
                $id_author = get_current_user_id();
                $query->set('author', $id_author);
            }
        }
        return $query;
    }

    /**
     * Add editor layout to footer
     *
     * @return void
     */
    public function editorFooter()
    {
        if (!class_exists('_WP_Editors', false)) {
            require_once ABSPATH . 'wp-includes/class-wp-editor.php';
            _WP_Editors::wp_link_dialog();
        }
    }

    /**
     * Query post in media gird view and ifame
     *
     * @param object $query Params use to query attachment
     *
     * @return object $query
     */
    public function preGetPosts($query)
    {
        global $pagenow;
        if ($pagenow !== 'upload.php') {
            if (empty($_REQUEST['query']['wpmf_nonce'])
                || !wp_verify_nonce($_REQUEST['query']['wpmf_nonce'], 'wpmf_nonce')) {
                return $query;
            }
        }

        $option_search = get_option('wpmf_option_searchall');
        if (!isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'attachment') {
            return $query;
        }

        if (isset($_REQUEST['query']['orderby']) && $_REQUEST['query']['orderby'] !== 'menu_order ID') {
            $taxonomies = apply_filters('attachment-category', get_object_taxonomies('attachment', 'objects'));
            if (!$taxonomies) {
                return $query;
            }

            foreach ($taxonomies as $taxonomyname => $taxonomy) :
                if ($taxonomyname === WPMF_TAXO) {
                    if ((int) $option_search === 0 || (empty($_REQUEST['query']['s']) && (int) $option_search === 1)) {
                        if (isset($_REQUEST['query']['wpmf_taxonomy']) && $_REQUEST['query']['term_slug']) {
                            $query->set(
                                'tax_query',
                                array(
                                    array(
                                        'taxonomy'         => $taxonomyname,
                                        'field'            => 'slug',
                                        'terms'            => $_REQUEST['query']['term_slug'],
                                        'include_children' => false
                                    )
                                )
                            );
                        } elseif (isset($_REQUEST[$taxonomyname]) && is_numeric($_REQUEST[$taxonomyname])
                                  && intval($_REQUEST[$taxonomyname]) !== 0) {
                            $term = get_term_by('id', $_REQUEST[$taxonomyname], $taxonomyname);
                            if (is_object($term)) {
                                set_query_var($taxonomyname, $term->slug);
                            }
                        } elseif (isset($_REQUEST['query']['wpmf_taxonomy']) && $_REQUEST['query']['term_slug'] === '') {
                            $terms     = get_categories(
                                array(
                                    'taxonomy'     => $taxonomyname,
                                    'hide_empty'   => false,
                                    'hierarchical' => false
                                )
                            );
                            $unsetTags = array();
                            foreach ($terms as $term) {
                                $unsetTags[] = $term->slug;
                            }

                            $root_media_root = get_term_by('id', $this->folderRootId, WPMF_TAXO);
                            if (in_array($root_media_root->slug, $unsetTags)) {
                                $key = array_search($root_media_root->slug, $unsetTags);
                                unset($unsetTags[$key]);
                            }

                            if (count($terms) !== 1) {
                                $ob_in_root = get_objects_in_term($this->folderRootId, WPMF_TAXO);
                                if (count($ob_in_root) > 0) {
                                    $query->set(
                                        'tax_query',
                                        array(
                                            'relation' => 'OR',
                                            array(
                                                'taxonomy'         => $taxonomyname,
                                                'field'            => 'slug',
                                                'terms'            => $unsetTags,
                                                'operator'         => 'NOT IN',
                                                'include_children' => false
                                            ),
                                            array(
                                                'taxonomy'         => $taxonomyname,
                                                'field'            => 'slug',
                                                'terms'            => $root_media_root->slug,
                                                'include_children' => false
                                            )
                                        )
                                    );
                                } else {
                                    $query->set(
                                        'tax_query',
                                        array(
                                            array(
                                                'taxonomy'         => $taxonomyname,
                                                'field'            => 'slug',
                                                'terms'            => $unsetTags,
                                                'operator'         => 'NOT IN',
                                                'include_children' => false
                                            )
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
            endforeach;
        }

        global $current_user;
        $user_roles         = $current_user->roles;
        $role               = array_shift($user_roles);
        $wpmf_create_folder = get_option('wpmf_create_folder');
        $wpmf_active_media  = get_option('wpmf_active_media');
        $id_author          = get_current_user_id();

        if ($role === 'administrator') {
            // role administrator when checked checkbox 'Display only my own media'
            if (isset($_POST['query']) && isset($_POST['query']['wpmf_display_media'])
                && $_POST['query']['wpmf_display_media'] === 'yes') {
                $query->query_vars['author'] = $id_author;
            }
        } elseif (isset($wpmf_active_media) && (int) $wpmf_active_media === 1) {
            // role != administrator when enable option 'Display only media by User/User'
            if ($wpmf_create_folder === 'user') {
                $query->query_vars['author'] = $id_author;
            } else {
                $current_role = $this->getRoles(get_current_user_id());
                $user_query   = new WP_User_Query(array('role' => $current_role));
                $user_lists   = $user_query->get_results();
                $user_array   = array();

                foreach ($user_lists as $user) {
                    $user_array[] = $user->data->ID;
                }

                $query->query_vars['author__in'] = $user_array;
            }
        }

        return $query;
    }

    /**
     * Get count files in folder
     *
     * @param integer $term_id Id of folder
     *
     * @return integer
     */
    public function getCountFiles($term_id)
    {
        global $wpdb;
        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
            if (is_plugin_active('wpml-media-translation/plugin.php')) {
                $my_current_lang = apply_filters('wpml_current_language', null);
                $count           = $wpdb->get_var($wpdb->prepare(
                    'SELECT COUNT(t1.object_id) FROM ' . $wpdb->prefix . 'term_relationships as t1
             INNER JOIN ' . $wpdb->prefix . 'term_taxonomy as t2 ON t1.term_taxonomy_id = t2.term_taxonomy_id
             INNER JOIN ' . $wpdb->prefix . 'terms as terms ON terms.term_id = t2.term_id
              INNER JOIN ' . $wpdb->prefix . 'posts as t4 ON t1.object_id = t4.ID
              INNER JOIN ' . $wpdb->prefix . 'icl_translations as t3 ON t1.object_id = t3.element_id 
              WHERE terms.term_id = %d AND t2.taxonomy = %s AND t3.language_code = %s',
                    array($term_id, WPMF_TAXO, $my_current_lang)
                ));
            } else {
                $count = $wpdb->get_var($wpdb->prepare(
                    'SELECT COUNT(t1.object_id) FROM ' . $wpdb->prefix . 'term_relationships as t1
             INNER JOIN ' . $wpdb->prefix . 'term_taxonomy as t2 ON t1.term_taxonomy_id = t2.term_taxonomy_id
             INNER JOIN ' . $wpdb->prefix . 'terms as terms ON terms.term_id = t2.term_id
              INNER JOIN ' . $wpdb->prefix . 'posts as t4 ON t1.object_id = t4.ID
              WHERE terms.term_id = %d AND t2.taxonomy = %s',
                    array($term_id, WPMF_TAXO)
                ));
            }
        } elseif (is_plugin_active('polylang/polylang.php')) {
            global $polylang;
            $all_objects = get_objects_in_term($term_id, WPMF_TAXO);
            if ($polylang->curlang) {
                $my_current_lang = $polylang->curlang->slug;
                $lang_term       = get_term_by('slug', $my_current_lang, 'language');
                $lang_object     = get_objects_in_term($lang_term->term_id, 'language', array('post_type' => 'attachment'));
                $count           = array_intersect($all_objects, $lang_object);
                return count($count);
            } else {
                return count($all_objects);
            }
        } else {
            $count = $wpdb->get_var($wpdb->prepare(
                'SELECT COUNT(t1.object_id) FROM ' . $wpdb->prefix . 'term_relationships as t1
             INNER JOIN ' . $wpdb->prefix . 'term_taxonomy as t2 ON t1.term_taxonomy_id = t2.term_taxonomy_id
             INNER JOIN ' . $wpdb->prefix . 'terms as terms ON terms.term_id = t2.term_id
             INNER JOIN ' . $wpdb->prefix . 'posts as t3 ON t1.object_id = t3.ID
              WHERE terms.term_id = %d AND t2.taxonomy = %s',
                array($term_id, WPMF_TAXO)
            ));
        }

        return (int) $count;
    }

    /**
     * Set file to current folder after upload files
     *
     * @param integer $attachment_id Id of attachment
     *
     * @return void
     */
    public function afterUpload($attachment_id)
    {
        // Get the parent folder from the post request
        // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        if (isset($_POST['wpmf_folder'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
            $parent = (int) $_POST['wpmf_folder'];
        } else {
            $parent = 0;
        }

        $post_upload = get_post($attachment_id);

        // only set object to term when upload files from media library screen
        if (!empty($post_upload) && !strpos($post_upload->post_content, 'wpmf-nextgen-image')
            && !strpos($post_upload->post_content, '[wpmf-ftp-import]')) {
            if ($parent) {
                wp_set_object_terms($attachment_id, $parent, WPMF_TAXO, true);

                /**
                 * Set attachmnent folder after upload
                 *
                 * @param integer Attachment ID
                 * @param integer Target folder
                 * @param array   Extra informations
                 *
                 * @ignore Hook already documented
                 */
                do_action('wpmf_attachment_set_folder', $attachment_id, $parent, array('trigger' => 'upload'));
            }
        }

        if (!empty($attachment_id)) {
            $this->addSizeFiletype($attachment_id);
            // add custom order position
            if (!metadata_exists('post', $attachment_id, 'wpmf_order')) {
                add_post_meta($attachment_id, 'wpmf_order', 0);
            }
        }
    }

    /**
     * Get size and file type of a file
     *
     * @param integer $pid Id of attachment
     *
     * @return array
     */
    public function getSizeFiletype($pid)
    {
        $wpmf_size_filetype = array();
        $meta               = get_post_meta($pid, '_wp_attached_file');
        $upload_dir         = wp_upload_dir();
        // get path file
        $path_attachment = $upload_dir['basedir'] . '/' . $meta[0];
        if (file_exists($path_attachment)) {
            // get size
            $size = filesize($path_attachment);
            // get file type
            $categorytype = wp_check_filetype($path_attachment);
            $ext          = $categorytype['ext'];
        } else {
            $size = 0;
            $ext  = '';
        }
        $wpmf_size_filetype['size'] = $size;
        $wpmf_size_filetype['ext']  = $ext;

        return $wpmf_size_filetype;
    }

    /**
     * Add meta size and file type of a file
     *
     * @param integer $attachment_id Id of attachment
     *
     * @return void
     */
    public function addSizeFiletype($attachment_id)
    {
        $wpmf_size_filetype = $this->getSizeFiletype($attachment_id);
        $size               = $wpmf_size_filetype['size'];
        $ext                = $wpmf_size_filetype['ext'];
        if (!get_post_meta($attachment_id, 'wpmf_size')) {
            add_post_meta($attachment_id, 'wpmf_size', $size);
        }

        if (!get_post_meta($attachment_id, 'wpmf_filetype')) {
            add_post_meta($attachment_id, 'wpmf_filetype', $ext);
        }
    }

    /**
     * Add a new folder via ajax
     *
     * @return void
     */
    public function addFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to add a folder
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'add_folder');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        if (isset($_POST['name']) && $_POST['name'] !== '') {
            $term = esc_attr(trim($_POST['name']));
        } else {
            $term = __('New folder', 'wpmf');
        }
        $termParent = (int) $_POST['parent'] | 0;
        $id_author  = get_current_user_id();
        // insert new term
        $inserted = wp_insert_term($term, WPMF_TAXO, array('parent' => $termParent));
        if (is_wp_error($inserted)) {
            wp_send_json(array('status' => false, 'error' => $inserted->get_error_message()));
        } else {
            // update term_group for new term
            $updateted = wp_update_term($inserted['term_id'], WPMF_TAXO, array('term_group' => $id_author));
            $termInfos = get_term($updateted['term_id'], WPMF_TAXO);

            // Retrieve the updated folders hierarchy
            $terms = $this->getAttachmentTerms();

            /**
             * Create a folder from media library
             * This hook is also used when syncing and importing files from FTP, creating user and role based folders
             * and importing from Nextgen Gallery
             *
             * @param integer Created folder ID
             * @param string  Created folder name
             * @param integer Parent folder ID
             * @param array   Extra informations
             */
            do_action('wpmf_create_folder', $inserted['term_id'], $term, $termParent, array('trigger' => 'media_library_action'));

            // Send back all needed informations in json format
            wp_send_json(array(
                'status'           => true,
                'term'             => $termInfos,
                'categories'       => $terms['attachment_terms'],
                'categories_order' => $terms['attachment_terms_order']
            ));
        }
    }

    /**
     * Change a folder name from ajax request
     *
     * @return void
     */
    public function editFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to edit a folder
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'edit_folder');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }

        $term_name = esc_attr($_POST['name']);
        if (!$term_name) {
            wp_send_json(false);
        }

        // Retrieve term informations
        $term = get_term((int) $_POST['id'], WPMF_TAXO);

        // check duplicate name
        $siblings = get_categories(
            array(
                'taxonomy' => WPMF_TAXO,
                'fields'   => 'names',
                'get'      => 'all',
                'parent'   => $term->parent
            )
        );

        if (in_array($term_name, $siblings)) {
            // Another folder with the same name exists
            wp_send_json(false);
        }

        $updated_term = wp_update_term((int) $_POST['id'], WPMF_TAXO, array('name' => $term_name));
        if ($updated_term instanceof WP_Error) {
            wp_send_json($updated_term->get_error_messages());
        } else {
            // Retrieve more information than wp_update_term function returns
            $updated_term = get_term($updated_term['term_id'], WPMF_TAXO);

            /**
             * Update folder name
             *
             * @param integer Folder ID
             * @param string  Updated name
             */
            do_action('wpmf_update_folder_name', $_POST['id'], $term_name);

            wp_send_json($updated_term);
        }
    }

    /**
     * Delete folder via ajax
     *
     * @return void
     */
    public function deleteFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to delete a folder
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'delete_folder');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }

        global $wpdb;
        $option_media_remove  = get_option('wpmf_option_media_remove');
        $wpmf_list_sync_media = get_option('wpmf_list_sync_media');
        $wpmf_ao_lastRun      = get_option('wpmf_ao_lastRun');
        $colors_option        = wpmfGetOption('folder_color');

        if ((int) $option_media_remove === 1) {
            // delete all subfolder and subfile
            $childs   = get_term_children((int) $_POST['id'], WPMF_TAXO);
            $childs[] = (int) $_POST['id'];
            $i        = 0;
            foreach ($childs as $child) {
                $childs_media = $wpdb->get_results($wpdb->prepare(
                    'SELECT t1.object_id as object_id FROM ' . $wpdb->prefix . 'term_relationships as t1
             INNER JOIN ' . $wpdb->prefix . 'term_taxonomy as t2 ON t1.term_taxonomy_id = t2.term_id
             INNER JOIN ' . $wpdb->prefix . 'posts as t3 ON t1.object_id = t3.ID
              WHERE t1.term_taxonomy_id = %d AND t2.taxonomy = %s',
                    array($child, WPMF_TAXO)
                ));
                foreach ($childs_media as $media) {
                    if ($i >= 20) {
                        wp_send_json(array(
                            'status' => false,
                            'msg'    => 'limit'
                        ));
                    } else {
                        $i ++;
                        wp_delete_attachment($media->object_id);
                    }
                }

                // Retrieve the term before deleting it
                $term = get_term($child, WPMF_TAXO);

                // remove element $child in option 'wpmf_list_sync_media' , 'wpmf_ao_lastRun'
                wp_delete_term($child, WPMF_TAXO);

                /**
                 * Delete a folder
                 *
                 * @param WP_Term Folder, this term is not available anymore as it as been deleted
                 */
                do_action('wpmf_delete_folder', $term);

                if (isset($wpmf_list_sync_media[$child])) {
                    unset($wpmf_list_sync_media[$child]);
                }
                if (isset($wpmf_ao_lastRun[$child])) {
                    unset($wpmf_ao_lastRun[$child]);
                }
                if (isset($colors_option[$child])) {
                    unset($colors_option[$child]);
                }

                // update option 'wpmf_list_sync_media' , 'wpmf_ao_lastRun'
                update_option('wpmf_list_sync_media', $wpmf_list_sync_media);
                update_option('wpmf_ao_lastRun', $wpmf_ao_lastRun);
                wpmfSetOption('folder_color', $colors_option);
            }

            // Retrieve the updated folders hierarchy
            $terms = $this->getAttachmentTerms();

            // Send full json response
            wp_send_json(array(
                'status'           => true,
                'fids'             => $childs,
                'categories'       => $terms['attachment_terms'],
                'categories_order' => $terms['attachment_terms_order']
            ));
        } else {
            // delete current folder
            $childs         = get_term_children((int) $_POST['id'], WPMF_TAXO);
            $object_in_term = $this->getCountFiles($_POST['id']);
            if ((is_array($childs) && count($childs) > 0) || (int) $object_in_term > 0) {
                // Folder not empty
                wp_send_json(array('status' => false)); // todo : send error message in response
            }

            // remove element $_POST['id'] in option 'wpmf_list_sync_media' , 'wpmf_ao_lastRun'
            if (isset($wpmf_list_sync_media[(int) $_POST['id']])) {
                unset($wpmf_list_sync_media[(int) $_POST['id']]);
            }

            if (isset($wpmf_ao_lastRun[(int) $_POST['id']])) {
                unset($wpmf_ao_lastRun[(int) $_POST['id']]);
            }

            if (isset($colors_option[(int) $_POST['id']])) {
                unset($colors_option[(int) $_POST['id']]);
            }
            update_option('wpmf_list_sync_media', $wpmf_list_sync_media);
            update_option('wpmf_ao_lastRun', $wpmf_ao_lastRun);
            wpmfSetOption('folder_color', $colors_option);

            // Retrieve the term before deleting it
            $term = get_term((int) $_POST['id'], WPMF_TAXO);

            if (wp_delete_term((int) $_POST['id'], WPMF_TAXO)) {
                /**
                 * Delete a folder
                 *
                 * @param WP_Term Folder
                 *
                 * @ignore Hook already documented
                 */
                do_action('wpmf_delete_folder', $term);

                // Retrieve the updated folders hierarchy
                $terms = $this->getAttachmentTerms();

                wp_send_json(array(
                    'status'           => true,
                    'categories'       => $terms['attachment_terms'],
                    'categories_order' => $terms['attachment_terms_order']
                ));
            } else {
                wp_send_json(array('status' => false)); // todo : send error message in response
            }
        }
    }

    /**
     * Move file compatiple with WPML plugin
     *
     * @param integer $id               Id of attachment
     * @param integer $current_category Id of current folder
     * @param integer $id_category      Id of new folder
     *
     * @return void
     */
    public function moveFileWpml($id, $current_category, $id_category)
    {
        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
            global $sitepress;
            $trid = $sitepress->get_element_trid($id, 'post_attachment');
            if ($trid) {
                $translations = $sitepress->get_element_translations($trid, 'post_attachment', true, true, true);
                foreach ($translations as $translation) {
                    if ((int) $translation->element_id !== (int) $id) {
                        if ($current_category !== 'no') {
                            wp_remove_object_terms(
                                (int) $translation->element_id,
                                (int) $current_category,
                                WPMF_TAXO
                            );
                        } else {
                            wp_set_object_terms(
                                (int) $translation->element_id,
                                (int) $id_category,
                                WPMF_TAXO,
                                true
                            );
                        }

                        if ($id_category !== 'no') {
                            wp_set_object_terms(
                                (int) $translation->element_id,
                                (int) $id_category,
                                WPMF_TAXO,
                                true
                            );

                            /**
                             * Set attachmnent folder after moving file with WPML plugin
                             *
                             * @param integer Attachment ID
                             * @param integer Target folder
                             * @param array   Extra informations
                             *
                             * @ignore Hook already documented
                             */
                            do_action('wpmf_attachment_set_folder', $translation->element_id, $id_category, array('trigger' => 'move_attachment'));
                        } else {
                            wp_remove_object_terms(
                                (int) $translation->element_id,
                                (int) $current_category,
                                WPMF_TAXO
                            );
                        }

                        // reset order of file
                        update_post_meta(
                            (int) $translation->element_id,
                            'wpmf_order',
                            0
                        );
                    }
                }
            }
        }
    }

    /**
     * Move a file via ajax from a category to another
     *
     * @return void
     */
    public function moveFile()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to move the files
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'move_file');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        $return = true;
        // check current folder
        if ((int) $_POST['current_category'] === 0) {
            $current_category = $this->folderRootId;
        } else {
            $current_category = (int) $_POST['current_category'];
        }
        foreach (array_unique($_POST['ids']) as $id) {
            // compability with WPML plugin
            $this->moveFileWpml($id, $current_category, $_POST['id_category']);

            wp_remove_object_terms((int) $id, $current_category, WPMF_TAXO);
            if ((int) $_POST['id_category'] === 0
                || wp_set_object_terms((int) $id, (int) $_POST['id_category'], WPMF_TAXO, true)) {
                /**
                 * Set attachment folder after moving an attachment to a folder in the media manager
                 * This hook is also used when importing attachment to categories, after an attachment upload and
                 * when assigning multiple folder to an attachment
                 *
                 * @param integer       Attachment ID
                 * @param integer|array Target folder or array of target folders
                 * @param array         Extra informations
                 */
                do_action('wpmf_attachment_set_folder', $id, (int) $_POST['id_category'], array('trigger' => 'move_attachment'));

                // reset order
                update_post_meta(
                    (int) $id,
                    'wpmf_order',
                    0
                );
            } else {
                $return = false;
            }
        }

        // todo : update the galleries with this file (new WpmfDisplayGallery()->updateGallery('move', $_POST['ids']);)
        wp_send_json(array('status' => $return));
    }

    /**
     * Move a folder via ajax
     *
     * @return void
     */
    public function moveFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to move a folder
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'move_folder');
        if (!$wpmf_capability) {
            wp_send_json(array('status' => false));
        }

        // Check that the folder we move into is not a child of the folder we're moving
        $wpmf_childs = $this->getFolderChild($_POST['id'], array());
        if (in_array((int) $_POST['id_category'], $wpmf_childs)) {
            wp_send_json(array('status' => false, 'wrong' => 'wrong'));
        }

        /*
         * Check if there is another folder with the same name
         * in the folder we moving into
         */
        $term     = get_term($_POST['id_category']);
        $siblings = get_categories(
            array(
                'taxonomy' => WPMF_TAXO,
                'fields'   => 'names',
                'get'      => 'all',
                'parent'   => (int) $_POST['id_category']
            )
        );
        if (in_array($term->name, $siblings)) {
            wp_send_json(array('status' => false));
        }

        $r = wp_update_term((int) $_POST['id'], WPMF_TAXO, array('parent' => (int) $_POST['id_category']));
        if ($r instanceof WP_Error) {
            wp_send_json(array('status' => false));
        } else {
            // Retrieve the updated folders hierarchy
            $terms = $this->getAttachmentTerms();

            /**
             * Move a folder from media library
             * This hook is also used when role folder option is changed
             *
             * @param integer Folder moved ID
             * @param string  Destination folder ID
             * @param array   Extra informations
             */
            do_action('wpmf_move_folder', $_POST['id'], $_POST['id_category'], array('trigger' => 'media_library_action'));
            wp_send_json(
                array(
                    'status'           => true,
                    'categories'       => $terms['attachment_terms'],
                    'categories_order' => $terms['attachment_terms_order']
                )
            );
        }
    }

    /**
     * Ajax get term to display folder tree
     * todo : this function use in WPMF addon
     *
     * @return void
     */
    public function getTerms()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to get categories list
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'get_terms');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }

        global $current_user;
        $dir = '/';
        if (!empty($_POST['dir'])) {
            $dir = $_POST['dir'];
            if ($dir[0] === '/') {
                $dir = '.' . $dir . '/';
            }
        }
        $dir  = str_replace('..', '', $dir);
        $dirs = array();
        $id   = 0;
        if (!empty($_POST['id'])) {
            $id = (int) $_POST['id'];
        }
        // todo: cookie are not used anymore to order folders
        // get orderby and order
        if (isset($_COOKIE['wpmf_folder_order'])) {
            $sortbys = explode('-', $_COOKIE['wpmf_folder_order']);
            $orderby = $sortbys[0];
            $order   = $sortbys[1];
        } else {
            $orderby = 'name';
            $order   = 'ASC';
        }

        // Retrieve the terms in a given taxonomy or list of taxonomies.
        $categorys          = get_categories(
            array(
                'taxonomy'   => WPMF_TAXO,
                'orderby'    => $orderby,
                'order'      => $order,
                'parent'     => $id,
                'hide_empty' => false
            )
        );
        $wpmf_active_media  = get_option('wpmf_active_media');
        $wpmf_create_folder = get_option('wpmf_create_folder');
        $user_roles         = $current_user->roles;
        $role               = array_shift($user_roles);
        $current_role       = $this->getRoles(get_current_user_id());
        foreach ($categorys as $category) {
            if ($role !== 'administrator' && isset($wpmf_active_media)
                && (int) $wpmf_active_media === 1) {
                $child      = get_term_children((int) $category->term_id, WPMF_TAXO);
                $countchild = count($child);
                if ($wpmf_create_folder === 'user') {
                    if ((int) $category->term_group === (int) get_current_user_id()) {
                        $dirs[] = array(
                            'type'        => 'dir',
                            'dir'         => $dir,
                            'file'        => $category->name,
                            'id'          => $category->term_id,
                            'parent_id'   => $category->parent,
                            'count_child' => $countchild,
                            'term_group'  => $category->term_group
                        );
                    }
                } else {
                    $crole = $this->getRoles($category->term_group);
                    if ($current_role === $crole) {
                        $dirs[] = array(
                            'type'        => 'dir',
                            'dir'         => $dir,
                            'file'        => $category->name,
                            'id'          => $category->term_id,
                            'parent_id'   => $category->parent,
                            'count_child' => $countchild,
                            'term_group'  => $category->term_group
                        );
                    }
                }
            } else {
                $child      = get_term_children((int) $category->term_id, WPMF_TAXO);
                $countchild = count($child);
                $dirs[]     = array(
                    'type'        => 'dir',
                    'dir'         => $dir,
                    'file'        => $category->name,
                    'id'          => $category->term_id,
                    'parent_id'   => $category->parent,
                    'count_child' => $countchild,
                    'term_group'  => $category->term_group
                );
            }
        }

        if (count($dirs) < 0) {
            wp_send_json('not empty');
        } else {
            wp_send_json($dirs);
        }
    }

    /**
     * Sort parents before children
     * http://stackoverflow.com/questions/6377147/sort-an-array-placing-children-beneath-parents
     *
     * @param array   $objects List folder
     * @param array   $result  Result
     * @param integer $parent  Parent of folder
     * @param integer $depth   Depth of folder
     *
     * @return array           output
     */
    public function parentSort(array $objects, array &$result = array(), $parent = 0, $depth = 0)
    {
        foreach ($objects as $key => $object) {
            if ((int) $object->parent === (int) $parent) {
                $object->depth = $depth;
                array_push($result, $object);
                unset($objects[$key]);
                $this->parentSort($objects, $result, $object->term_id, $depth + 1);
            }
        }
        return $result;
    }

    /**
     * Get current user role
     *
     * @param integer $userId Id of user
     *
     * @return mixed|string
     */
    public function getRoles($userId)
    {
        if (!function_exists('get_userdata')) {
            require_once(ABSPATH . 'wp-includes/pluggable.php');
        }
        $userdata = get_userdata($userId);
        if (!empty($userdata->roles)) {
            $role = array_shift($userdata->roles);
        } else {
            $role = '';
        }
        return $role;
    }

    /**
     * Get info root folder
     *
     * @return array
     */
    public function termRoot()
    {
        global $current_user;
        $wpmf_checkbox_tree = get_option('wpmf_checkbox_tree');
        $wpmf_create_folder = get_option('wpmf_create_folder');
        if ($wpmf_create_folder === 'user') {
            if (!empty($wpmf_checkbox_tree)) {
                $current_parrent = get_term($wpmf_checkbox_tree, WPMF_TAXO);
                if (!empty($current_parrent)) {
                    $parent = $wpmf_checkbox_tree;
                } else {
                    $parent = 0;
                }
            } else {
                $parent = 0;
            }
        } else {
            $parent = 0;
        }

        $term_roots = get_categories(array('taxonomy' => WPMF_TAXO, 'parent' => $parent, 'hide_empty' => false));
        $wpmfterm   = array();

        $user_roles = $current_user->roles;
        $role       = array_shift($user_roles);
        if (count($term_roots) > 0) {
            if ($wpmf_create_folder === 'user') {
                foreach ($term_roots as $term) {
                    if ($term->name === $current_user->user_login && (int) $term->term_group === (int) get_current_user_id()) {
                        $wpmfterm['term_rootId'] = $term->term_id;
                        $wpmfterm['term_label']  = $term->name;
                        $wpmfterm['term_parent'] = $term->parent;
                        $wpmfterm['term_slug']   = $term->slug;
                    }
                }
            } else {
                foreach ($term_roots as $term) {
                    if ($term->name === $role && strpos($term->slug, '-wpmf-role')) {
                        $wpmfterm['term_rootId'] = $term->term_id;
                        $wpmfterm['term_label']  = $term->name;
                        $wpmfterm['term_parent'] = $term->parent;
                        $wpmfterm['term_slug']   = $term->slug;
                    }
                }
            }
        }
        return $wpmfterm;
    }

    /**
     * Get children categories
     *
     * @param integer $id_parent Parent of attachment
     * @param array   $lists     List childrens folder
     *
     * @return array
     */
    public function getFolderChild($id_parent, $lists)
    {
        if (empty($lists)) {
            $lists = array();
        }
        $folder_childs = get_categories(
            array(
                'taxonomy'   => WPMF_TAXO,
                'parent'     => (int) $id_parent,
                'hide_empty' => false
            )
        );
        if (count($folder_childs) > 0) {
            foreach ($folder_childs as $child) {
                $lists[] = $child->term_id;
                $lists   = $this->getFolderChild($child->term_id, $lists);
            }
        }

        return $lists;
    }

    /**
     * Get count file by type
     *
     * @param string $app Mime type of post
     *
     * @return integer|null|string
     */
    public function countExt($app)
    {
        global $wpdb;
        if ($app === 'application/pdf') {
            $count = $wpdb->get_var($wpdb->prepare(
                'SELECT COUNT(ID) FROM ' . $wpdb->prefix . 'posts WHERE post_type = %s AND post_mime_type= %s ',
                array('attachment', 'application/pdf')
            ));
        } else {
            $post_mime_type = array(
                'application/zip',
                'application/rar',
                'application/ace',
                'application/arj',
                'application/bz2',
                'application/cab',
                'application/gzip',
                'application/iso',
                'application/jar',
                'application/lzh',
                'application/tar',
                'application/uue',
                'application/xz',
                'application/z',
                'application/7-zip'
            );
            $post_types     = "'" . implode("', '", $post_mime_type) . "'";
            $count          = $wpdb->get_var($wpdb->prepare(
                'SELECT COUNT(ID) FROM ' . $wpdb->prefix . 'posts WHERE post_type = %s AND post_mime_type IN (%s) ',
                array('attachment', $post_types)
            ));
        }

        return $count;
    }

    /**
     * Get current filter file type
     *
     * @return string
     */
    public function getFiletype()
    {
        // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- No action, nonce is not required
        if (isset($_GET['attachment-filter'])) {
            if ($_GET['attachment-filter'] === 'wpmf-zip' || $_GET['attachment-filter'] === 'wpmf-pdf'
                || $_GET['attachment-filter'] === 'wpmf-other') {
                $categorytype = $_GET['attachment-filter'];
            } else {
                $categorytype = '';
            }
        } else {
            $categorytype = '';
        }
        // phpcs:enable
        return $categorytype;
    }

    /**
     * Get folder tree
     *
     * @return void
     */
    public function getUserMediaTree()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to get categories user media
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'get_user_media');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        global $current_user;
        $dir = '/';
        if (!empty($_POST['dir'])) {
            $dir = $_POST['dir'];
            if ($dir[0] === '/') {
                $dir = '.' . $dir . '/';
            }
        }
        $dir  = str_replace('..', '', $dir);
        $dirs = array();
        $id   = 0;
        if (!empty($_POST['id'])) {
            $id = (int) $_POST['id'];
        }

        // Retrieve the terms in a given taxonomy or list of taxonomies.
        $categories             = get_categories(
            array(
                'taxonomy'   => WPMF_TAXO,
                'orderby'    => 'name',
                'order'      => 'ASC',
                'parent'     => $id,
                'hide_empty' => false
            )
        );
        $wpmf_active_media      = get_option('wpmf_active_media');
        $wpmf_create_folder     = get_option('wpmf_create_folder');
        $user_roles             = $current_user->roles;
        $role                   = array_shift($user_roles);
        $current_role           = $this->getRoles(get_current_user_id());
        $user_media_folder_root = get_option('wpmf_checkbox_tree');
        $current_parrent        = get_term((int) $user_media_folder_root, WPMF_TAXO);
        if (empty($current_parrent)) {
            $user_media_folder_root = 0;
        }

        if (empty($user_media_folder_root)) {
            $user_media_folder_root = 0;
        }

        foreach ($categories as $category) {
            if ((int) $category->term_id === (int) $user_media_folder_root) {
                $checked  = true;
                $pchecked = false;
            } else {
                $checked  = false;
                $pchecked = $this->userMediaCheckChecked($category->term_id, $user_media_folder_root);
            }
            if ($role !== 'administrator' && isset($wpmf_active_media) && (int) $wpmf_active_media === 1) {
                $child      = get_term_children((int) $category->term_id, WPMF_TAXO);
                $countchild = count($child);
                if ($wpmf_create_folder === 'user') {
                    if ((int) $category->term_group === (int) get_current_user_id()) {
                        $dirs[] = array(
                            'type'        => 'dir',
                            'dir'         => $dir,
                            'file'        => $category->name,
                            'id'          => $category->term_id,
                            'parent_id'   => $category->parent,
                            'count_child' => $countchild,
                            'term_group'  => $category->term_group,
                            'checked'     => $checked,
                            'pchecked'    => $pchecked
                        );
                    }
                } else {
                    $role = $this->getRoles($category->term_group);
                    if ($current_role === $role) {
                        $dirs[] = array(
                            'type'        => 'dir',
                            'dir'         => $dir,
                            'file'        => $category->name,
                            'id'          => $category->term_id,
                            'parent_id'   => $category->parent,
                            'count_child' => $countchild,
                            'term_group'  => $category->term_group,
                            'checked'     => $checked,
                            'pchecked'    => $pchecked
                        );
                    }
                }
            } else {
                $child      = get_term_children((int) $category->term_id, WPMF_TAXO);
                $countchild = count($child);
                $dirs[]     = array(
                    'type'        => 'dir',
                    'dir'         => $dir,
                    'file'        => $category->name,
                    'id'          => $category->term_id,
                    'parent_id'   => $category->parent,
                    'count_child' => $countchild,
                    'term_group'  => $category->term_group,
                    'checked'     => $checked,
                    'pchecked'    => $pchecked
                );
            }
        }

        if (count($dirs) < 0) {
            wp_send_json(array('status' => false));
        } else {
            wp_send_json(array('dirs' => $dirs, 'user_media_folder_root' => $user_media_folder_root, 'status' => true));
        }
    }

    /**
     * Get assign folder tree
     *
     * @return void
     */
    public function getAssignTree()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to get categories assign media
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'get_assign_media');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        global $current_user, $wpdb;
        $dir = '/';
        if (!empty($_POST['dir'])) {
            $dir = $_POST['dir'];
            if ($dir[0] === '/') {
                $dir = '.' . $dir . '/';
            }
        }
        $dir  = str_replace('..', '', $dir);
        $dirs = array();
        $id   = 0;
        if (!empty($_POST['id'])) {
            $id = (int) $_POST['id'];
        }

        // Retrieve the terms in a given taxonomy or list of taxonomies.
        $categories         = get_categories(
            array(
                'taxonomy'   => WPMF_TAXO,
                'orderby'    => 'name',
                'order'      => 'ASC',
                'parent'     => $id,
                'hide_empty' => false
            )
        );
        $wpmf_active_media  = get_option('wpmf_active_media');
        $wpmf_create_folder = get_option('wpmf_create_folder');
        $user_roles         = $current_user->roles;
        $role               = array_shift($user_roles);
        $current_role       = $this->getRoles(get_current_user_id());
        $term_of_file       = wp_get_object_terms(
            (int) $_POST['attachment_id'],
            WPMF_TAXO,
            array(
                'orderby' => 'name',
                'order'   => 'ASC',
                'fields'  => 'ids'
            )
        );
        // check image in root
        $root_media_root = get_term_by('name', __('WP Media Folder Root', 'wpmf'), WPMF_TAXO);
        if (empty($term_of_file) || (!empty($term_of_file) && in_array($root_media_root->term_id, $term_of_file))) {
            $root_check = true;
        } else {
            $root_check = false;
        }

        foreach ($categories as $category) {
            if (in_array($category->term_id, $term_of_file)) {
                $checked  = true;
                $pchecked = false;
            } else {
                $checked  = false;
                $pchecked = $this->checkChecked($category->term_id, (array) $term_of_file);
            }
            if ($role !== 'administrator' && isset($wpmf_active_media) && (int) $wpmf_active_media === 1) {
                $countchild = $wpdb->get_var($wpdb->prepare('SELECT COUNT(term_id) FROM ' . $wpdb->prefix . 'term_taxonomy 
               WHERE parent = %d', array((int) $category->term_id)));
                if ($wpmf_create_folder === 'user') {
                    if ((int) $category->term_group === (int) get_current_user_id()) {
                        $dirs[] = array(
                            'type'        => 'dir',
                            'dir'         => $dir,
                            'file'        => $category->name,
                            'id'          => $category->term_id,
                            'parent_id'   => $category->parent,
                            'count_child' => $countchild,
                            'term_group'  => $category->term_group,
                            'checked'     => $checked,
                            'pchecked'    => $pchecked
                        );
                    }
                } else {
                    $role = $this->getRoles($category->term_group);
                    if ($current_role === $role) {
                        $dirs[] = array(
                            'type'        => 'dir',
                            'dir'         => $dir,
                            'file'        => $category->name,
                            'id'          => $category->term_id,
                            'parent_id'   => $category->parent,
                            'count_child' => $countchild,
                            'term_group'  => $category->term_group,
                            'checked'     => $checked,
                            'pchecked'    => $pchecked
                        );
                    }
                }
            } else {
                $countchild = $wpdb->get_var($wpdb->prepare('SELECT COUNT(term_id) FROM ' . $wpdb->prefix . 'term_taxonomy 
               WHERE parent = %d', array((int) $category->term_id)));
                $dirs[]     = array(
                    'type'        => 'dir',
                    'dir'         => $dir,
                    'file'        => $category->name,
                    'id'          => $category->term_id,
                    'parent_id'   => $category->parent,
                    'count_child' => $countchild,
                    'term_group'  => $category->term_group,
                    'checked'     => $checked,
                    'pchecked'    => $pchecked
                );
            }
        }

        if (count($dirs) < 0) {
            wp_send_json(array('status' => false));
        } else {
            wp_send_json(array('dirs' => $dirs, 'root_check' => $root_check, 'status' => true));
        }
    }

    /**
     * Get status folder
     *
     * @param integer $term_id      Id of folder
     * @param array   $term_of_file Parent of file
     *
     * @return boolean
     */
    public function checkChecked($term_id, $term_of_file = array())
    {
        $childs   = get_term_children((int) $term_id, WPMF_TAXO);
        $pchecked = false;
        foreach ($childs as $child) {
            if (in_array($child, (array) $term_of_file)) {
                $pchecked = true;
                break;
            } else {
                $pchecked = false;
                $this->checkChecked($child, $term_of_file);
            }
        }
        return $pchecked;
    }

    /**
     * Get status folder
     *
     * @param integer $term_id Id of folder
     * @param integer $termID  Id of folder
     *
     * @return boolean
     */
    public function userMediaCheckChecked($term_id, $termID)
    {
        $childs   = get_term_children((int) $term_id, WPMF_TAXO);
        $pchecked = false;
        foreach ($childs as $child) {
            if ((int) $child === (int) $termID) {
                $pchecked = true;
                break;
            } else {
                $pchecked = false;
                $this->checkChecked($child, $termID);
            }
        }
        return $pchecked;
    }

    /**
     * Set file to multiple folders
     *
     * @return void
     */
    public function setObjectTerm()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to update file to category
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'update_object_term');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        if (isset($_POST['attachment_id']) && $_POST['attachment_id'] !== '') {
            $attachment_ids = explode(',', $_POST['attachment_id']);
            foreach ($attachment_ids as $attachment_id) {
                // set file to list folder checked
                if (isset($_POST['wpmf_term_ids_check'])) {
                    $wpmf_term_ids_check = explode(',', $_POST['wpmf_term_ids_check']);
                    foreach ($wpmf_term_ids_check as $term_id) {
                        // compability with WPML plugin
                        $this->moveFileWpml($attachment_id, 'no', $term_id);
                        wp_set_object_terms((int) $attachment_id, (int) $term_id, WPMF_TAXO, true);
                    }
                    /**
                     * Assign multiple folders to an attachment
                     *
                     * @param integer Attachment ID
                     * @param array   Target folders
                     * @param array   Extra informations
                     *
                     * @ignore Hook already documented
                     */
                    do_action('wpmf_attachment_set_folder', $attachment_id, $wpmf_term_ids_check, array('trigger' => 'set_multiple_folders'));
                }

                // unset file to list folder checked
                if (isset($_POST['wpmf_term_ids_notcheck'])) {
                    $wpmf_term_ids_notcheck = explode(',', $_POST['wpmf_term_ids_notcheck']);
                    foreach ($wpmf_term_ids_notcheck as $term_id) {
                        // compability with WPML plugin
                        $this->moveFileWpml($attachment_id, $term_id, 'no');
                        wp_remove_object_terms((int) $attachment_id, (int) $term_id, WPMF_TAXO);
                    }
                }
            }
            wp_send_json(true);
        } else {
            wp_send_json(false);
        }
    }

    /**
     * Update file title to database
     *
     * @param integer $pid Id of attachment
     *
     * @return void
     */
    public function updateFileTitle($pid)
    {
        global $wpdb;
        $options_format_title = get_option('wpmf_options_format_title');
        $post                 = get_post($pid);
        if (!empty($post)) {
            $title = $post->post_title;

            /* create array character from settings */
            $character = array();
            if (isset($options_format_title['tilde']) && $options_format_title['tilde']) {
                $character[] = '~';
            }
            if (isset($options_format_title['underscore']) && $options_format_title['underscore']) {
                $character[] = '_';
            }
            if (isset($options_format_title['period']) && $options_format_title['period']) {
                $character[] = '.';
            }
            if (isset($options_format_title['plus']) && $options_format_title['plus']) {
                $character[] = '+';
            }
            if (isset($options_format_title['hyphen']) && $options_format_title['hyphen']) {
                $character[] = '-';
            }

            if (isset($options_format_title['hash']) && $options_format_title['hash']) {
                $character[] = '#';
            }

            if (isset($options_format_title['ampersand']) && $options_format_title['ampersand']) {
                $character[] = '@';
            }

            if (isset($options_format_title['number']) && $options_format_title['number']) {
                for ($i = 0; $i <= 9; $i ++) {
                    $character[] = $i;
                }
            }

            if (isset($options_format_title['square_brackets']) && $options_format_title['square_brackets']) {
                $character[] = '[]';
            }

            if (isset($options_format_title['round_brackets']) && $options_format_title['round_brackets']) {
                $character[] = '()';
            }

            if (isset($options_format_title['curly_brackets']) && $options_format_title['curly_brackets']) {
                $character[] = '{}';
            }

            /* Replace character to space */
            if (!empty($character)) {
                $title = str_replace($character, ' ', $title);
            }

            $title  = preg_replace('/\s+/', ' ', $title);
            $capita = $options_format_title['capita'];

            /* Capitalize Title. */
            switch ($capita) {
                case 'cap_all':
                    $title = ucwords($title);
                    break;
                case 'all_upper':
                    $title = strtoupper($title);
                    break;
                case 'cap_first':
                    $title = ucfirst(strtolower($title));
                    break;
                case 'all_lower':
                    $title = strtolower($title);
                    break;
                case 'dont_alter':
                    break;
            }

            /**
             * Manipulate file title before saving it into database
             *
             * @param string File title
             *
             * @return string
             */
            $title = apply_filters('wpmf_set_file_title', $title);

            // update _wp_attachment_image_alt
            if (isset($options_format_title['alt']) && $options_format_title['alt']) {
                update_post_meta($pid, '_wp_attachment_image_alt', $title);
            }

            // update post
            $field  = array(
                'post_title' => $title
            );
            $format = array('%s');
            if (isset($options_format_title['description']) && $options_format_title['description']) {
                $field['post_content'] = $title;
                $format[]              = '%s';
            }

            if (isset($options_format_title['caption']) && $options_format_title['caption']) {
                $field['post_excerpt'] = $title;
                $format[]              = '%s';
            }

            $wpdb->update(
                $wpdb->posts,
                $field,
                array('ID' => $pid),
                $format,
                array('%d')
            );
        }
    }

    /**
     * Get dailymotion video ID from URL
     *
     * @param string $url URL of video
     *
     * @return mixed|string
     */
    public function getDailymotionVideoIdFromUrl($url = '')
    {
        $id = strtok(basename($url), '_');
        return $id;
    }

    /**
     * Get vimeo video ID from URL
     *
     * @param string $url URl of video
     *
     * @return mixed|string
     */
    public function getVimeoVideoIdFromUrl($url = '')
    {
        $regs = array();
        $id   = '';
        if (preg_match($this->vimeo_pattern, $url, $regs)) {
            $id = $regs[3];
        }

        return $id;
    }

    /**
     * Get vimeo video infos
     *
     * @param string $url URL of video
     *
     * @return boolean|mixed
     */
    public function getVimeoVideo($url = '')
    {
        $id = $this->getVimeoVideoIdFromUrl($url);
        $id = (int) trim($id);
        if ($id === '') {
            return false;
        }
        $apiData = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.php'));
        if (is_array($apiData) && count($apiData) > 0) {
            $videoInfo = $apiData[0];
            return $videoInfo;
        }
        return false;
    }

    /**
     * Get youtube infos
     *
     * @param string $url Youtube URL
     *
     * @return array|mixed|object
     */
    public function getYoutube($url)
    {
        $youtube = 'http://www.youtube.com/oembed?url=' . $url . '&format=json';
        $curl = curl_init($youtube);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }


    /**
     * Ajax create remote video
     *
     * @return void
     */
    public function createRemoteVideo()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to create remote video
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'remote_video');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        if (isset($_POST['wpmf_remote_link'])) {
            $url     = $_POST['wpmf_remote_link'];
            $title   = '';
            $ext     = '';
            $content = '';

            if (!preg_match($this->vimeo_pattern, $url, $output_array)
                && !preg_match('/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/', $url, $match)
                && !preg_match('/\b(?:dailymotion)\.com\b/i', $url, $vresult)) {
                wp_send_json(
                    array(
                        'status' => false,
                        'msg'    => __('Sorry, not a youtube, vimeo or dailymotion URL', 'wpmf')
                    )
                );
            } elseif (preg_match($this->vimeo_pattern, $url, $output_array)) {
                // for vimeo
                $vimeo_infos = $this->getVimeoVideo($_POST['wpmf_remote_link']);
                // get thumbnail of video
                $content = file_get_contents($vimeo_infos['thumbnail_large']);
                if (empty($content)) {
                    wp_send_json(array('status' => false, 'msg' => __('Sorry, this video not found', 'wpmf')));
                }

                $title          = sanitize_title($vimeo_infos['title']);
                $info_thumbnail = pathinfo($vimeo_infos['thumbnail_large']); // get info thumbnail
                $ext            = $info_thumbnail['extension'];
            } elseif (preg_match('/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/', $url, $match)) {
                // for youtube
                // get thumbnail of video
                $parts = parse_url($url);
                parse_str($parts['query'], $query);
                $content = file_get_contents('http://img.youtube.com/vi/' . $query['v'] . '/maxresdefault.jpg');
                if (empty($content)) {
                    $content = file_get_contents('http://img.youtube.com/vi/' . $query['v'] . '/default.jpg');
                }

                if (empty($content)) {
                    wp_send_json(array('status' => false, 'msg' => __('Sorry, this video not found', 'wpmf')));
                }

                $infos = $this->getYoutube($url);
                if (isset($infos['status']) && $infos['status'] === 'fail') {
                    wp_send_json(array('status' => false, 'msg' => $infos['reason']));
                }

                if (empty($infos['thumbnail_url'])) {
                    wp_send_json(array('status' => false, 'msg' => __('Sorry, this video not found', 'wpmf')));
                }

                $info_thumbnail = pathinfo($infos['thumbnail_url']); // get info thumbnail
                $title          = sanitize_title($infos['title']); // get title video
                $ext            = $info_thumbnail['extension'];
            } elseif (preg_match('/\b(?:dailymotion)\.com\b/i', $url, $vresult)) {
                // for dailymotion
                $id   = $this->getDailymotionVideoIdFromUrl($url);
                $info = json_decode(
                    file_get_contents(
                        'http://www.dailymotion.com/services/oembed?format=json&url=http://www.dailymotion.com/embed/video/' . $id
                    ),
                    true
                );
                if (empty($info)) {
                    wp_send_json(array('status' => false, 'msg' => __('Sorry, this video not found', 'wpmf')));
                }

                // get thumbnail content of video
                $content        = file_get_contents($info['thumbnail_url']);
                $info_thumbnail = pathinfo($info['thumbnail_url']); // get info thumbnail
                $title          = sanitize_title($info['title']); // get title video
                $ext            = $info_thumbnail['extension'];
            }

            $upload_dir = wp_upload_dir();
            // create wpmf_remote_video folder
            if (!file_exists($upload_dir['basedir'] . '/wpmf_remote_video')) {
                if (!mkdir($upload_dir['basedir'] . '/wpmf_remote_video')) {
                    wp_send_json(array('status' => false, 'msg' => __('Failed to create folders...', 'wpmf')));
                }
            }

            // upload  thumbnail to wpmf_remote_video folder
            $upload_folder = $upload_dir['basedir'] . '/wpmf_remote_video';
            $upload        = file_put_contents($upload_folder . '/' . $title . '.' . $ext, $content);

            // upload images
            if ($upload) {
                if (($ext === 'jpg')) {
                    $mimetype = 'image/jpeg';
                } else {
                    $mimetype = 'image/' . $ext;
                }
                $attachment = array(
                    'guid'           => $upload_dir['baseurl'] . '/' . $title . '.' . $ext,
                    'post_mime_type' => $mimetype,
                    'post_title'     => $title,
                    'post_excerpt'   => $url,
                    'post_content'   => 'wpmf_remote_video'
                );

                $image_path = $upload_folder . '/' . $title . '.' . $ext;
                $attach_id  = wp_insert_attachment($attachment, $image_path);

                $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
                wp_update_attachment_metadata($attach_id, $attach_data);
                update_post_meta($attach_id, 'wpmf_remote_video_link', $url);
                // create image in folder
                $current_folder_id = $_POST['folder_id'];
                wp_set_object_terms((int) $attach_id, (int) $current_folder_id, WPMF_TAXO, false);

                /**
                 * Create remote video file
                 *
                 * @param integer       Created attachment ID
                 * @param integer|array Target        folder
                 * @param array         Extra informations
                 *
                 * @ignore Hook already documented
                 */
                do_action('wpmf_add_attachment', $attach_id, $current_folder_id, array('type' => 'remove_video'));
                wp_send_json(array('status' => true, 'msg' => __('Upload success!', 'wpmf')));
            }
        }
        wp_send_json(array('status' => false, 'msg' => __('Upload errors...', 'wpmf')));
    }

    /**
     * Create attachment fields
     * Based on /wp-admin/includes/media.php
     *
     * @param array   $form_fields An array of attachment form fields.
     * @param WP_Post $post        The WP_Post attachment object.
     *
     * @return mixed
     */
    public function attachmentFieldsToEdit($form_fields, $post)
    {
        $remote_video = get_post_meta($post->ID, 'wpmf_remote_video_link');
        if (!empty($remote_video)) {
            $form_fields['wpmf_remote_video_link'] = array(
                'label' => __('Remote video', 'wpmf'),
                'input' => 'html',
                'html'  => '<input type="text" class="text"
                 id="attachments-' . $post->ID . '-wpmf_remote_video_link"
                  name="attachments[' . $post->ID . '][wpmf_remote_video_link]"
                   value="' . get_post_meta($post->ID, 'wpmf_remote_video_link', true) . '">'
            );
        }
        return $form_fields;
    }

    /**
     * Add video html to editor
     *
     * @param string  $html       HTML markup for a media item sent to the editor.
     * @param integer $id         The first key from the $_POST['send'] data.
     * @param array   $attachment Array of attachment metadata.
     *
     * @return mixed
     */
    public function addRemoteVideo($html, $id, $attachment)
    {
        $remote_video = get_post_meta($id, 'wpmf_remote_video_link');
        if (!empty($remote_video)) {
            $html = $remote_video;
        }
        return $html;
    }

    /**
     * Save attachment fields
     * Based on /wp-admin/includes/media.php
     *
     * @param array $post       An array of post data.
     * @param array $attachment An array of attachment metadata.
     *
     * @return mixed $post
     */
    public function attachmentFieldsToSave($post, $attachment)
    {
        if (isset($attachment['wpmf_remote_video_link'])) {
            $url = $attachment['wpmf_remote_video_link'];
            // get thumbnail of video
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $content    = file_get_contents('http://img.youtube.com/vi/' . $query['v'] . '/sddefault.jpg');
            $upload_dir = wp_upload_dir();

            // check if link is a valid youtube link
            $regex_pattern = '/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/';
            if (!preg_match($regex_pattern, $url, $match)) {
                return $post;
            }

            // get thumbnail of video
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $check_img = file_get_contents('http://img.youtube.com/vi/' . $query['v'] . '/sddefault.jpg');
            if (empty($check_img)) {
                return $post;
            }

            $filepath = get_attached_file($post['ID']);
            $infos    = pathinfo($filepath);
            $metadata = wp_get_attachment_metadata($post['ID']);

            // upload  thumbnail to wpmf_remote_video folder
            unlink($filepath);
            $upload_folder = $upload_dir['basedir'] . '/wpmf_remote_video';
            file_put_contents($upload_folder . '/' . $infos['basename'], $content);
            if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $size => $sizeinfo) {
                    $intermediate_file = str_replace(basename($filepath), $sizeinfo['file'], $filepath);
                    // This filter is documented in wp-includes/functions.php
                    $intermediate_file = apply_filters('wp_delete_file', $intermediate_file);
                    unlink(path_join($upload_dir['basedir'], $intermediate_file));
                }
            }

            $this->createThumbs($filepath, $infos['extension'], $metadata, $post['ID']);
            update_post_meta($post['ID'], 'wpmf_remote_video_link', esc_url_raw($attachment['wpmf_remote_video_link']));
        }
        return $post;
    }

    /**
     * Ajax set folder color
     *
     * @return void
     */
    public function setFolderColor()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        $colors_option = wpmfGetOption('folder_color');
        if (isset($_POST['folder_id']) && isset($_POST['color'])) {
            if (empty($colors_option)) {
                $colors_option                      = array();
                $colors_option[$_POST['folder_id']] = $_POST['color'];
            } else {
                $colors_option[$_POST['folder_id']] = $_POST['color'];
            }
            wpmfSetOption('folder_color', $colors_option);
            wp_send_json(array('status' => true));
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Ajax delete file
     *
     * @return void
     */
    public function deleteFile()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['id'])) {
            wp_delete_attachment((int) $_POST['id']);
            wp_send_json(array('status' => true));
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Ajax custom order for file
     *
     * @return void
     */
    public function reorderFile()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['order'])) {
            $orders = (array) json_decode(stripslashes_deep($_POST['order']));
            if (is_array($orders) && !empty($orders)) {
                foreach ($orders as $position => $id) {
                    update_post_meta(
                        (int) $id,
                        'wpmf_order',
                        (int) $position
                    );
                }
            }
        }
    }

    /**
     * Ajax custom order for folder
     *
     * @return void
     */
    public function reorderfolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['order'])) {
            $orders = (array) json_decode(stripslashes_deep($_POST['order']));
            if (is_array($orders) && !empty($orders)) {
                foreach ($orders as $position => $id) {
                    update_term_meta(
                        (int) $id,
                        'wpmf_order',
                        (int) $position
                    );
                }
            }
        }
    }

    /**
     * Save folder cover
     *
     * @return void
     */
    public function saveFolderCover()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (isset($_POST['folder_id'])) {
            if (!empty($_POST['post_id'])) {
                // Retrieve previous image covers
                $cover_images = get_option('wpmf_field_bgfolder');

                // Define array if not yet any cover image defined
                if (empty($cover_images)) {
                    $cover_images = array();
                }

                // Retrieve the current folder the post is in
                $current_folder_id = (int) $_POST['folder_id'];
                if (isset($cover_images[$current_folder_id])
                    && (int) $cover_images[$current_folder_id][0] === (int) $_POST['post_id']) {
                    unset($cover_images [$current_folder_id]);
                    $msg    = 'unset';
                    $params = array(0, '');
                } else {
                    // Retrieve the thumbnail image
                    $image_thumb = wp_get_attachment_image_src($_POST['post_id'], 'thumbnail');
                    // Affect post ID and image thumbnail to the folder
                    $params                           = array(
                        (int) $_POST['post_id'],
                        $image_thumb[0]
                    );
                    $cover_images[$current_folder_id] = $params;
                    $msg                              = 'update';
                }
                update_option('wpmf_field_bgfolder', $cover_images);
                wp_send_json(
                    array(
                        'status' => true,
                        'msg'    => $msg,
                        'params' => $params
                    )
                );
            }
        }
        wp_send_json(array('status' => false));
    }

    /**
     * Import custom order
     *
     * @return void
     */
    public function importOrder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to import order of file
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('manage_options'), 'import_order_file');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        global $wpdb;
        $limit       = 50;
        $offset      = (int) $_POST['current_import_page'] * $limit;
        $attachments = $wpdb->get_results($wpdb->prepare('SELECT ID FROM ' . $wpdb->prefix . 'posts as posts
               WHERE   posts.post_type     = %s LIMIT %d OFFSET %d', array('attachment', $limit, $offset)));
        $i           = 0;
        foreach ($attachments as $attachment) {
            if (!get_post_meta($attachment->ID, 'wpmf_order')) {
                update_post_meta($attachment->ID, 'wpmf_order', 0);
            }

            $i ++;
        }
        if ($i >= $limit) {
            wp_send_json(array('status' => false, 'page' => (int) $_POST['current_import_page']));
        } else {
            update_option('_wpmf_import_order_notice_flag', 'yes');
            wp_send_json(array('status' => true));
        }
    }

    /**
     * Ajax update link for attachment
     *
     * @return void
     */
    public function updateLink()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        /**
         * Filter check capability of current user to update link image
         *
         * @param boolean The current user has the given capability
         * @param string  Action name
         *
         * @return boolean
         *
         * @ignore Hook already documented
         */
        $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('upload_files'), 'update_link');
        if (!$wpmf_capability) {
            wp_send_json(false);
        }
        $attachment_id = $_POST['id'];
        update_post_meta($attachment_id, '_wpmf_gallery_custom_image_link', esc_url_raw($_POST['link']));
        update_post_meta($attachment_id, '_gallery_link_target', $_POST['link_target']);
        $link   = get_post_meta($attachment_id, '_wpmf_gallery_custom_image_link');
        $target = get_post_meta($attachment_id, '_gallery_link_target');
        wp_send_json(array('link' => $link, 'target' => $target));
    }

    /**
     * Ajax get count files in a folder
     *
     * @return void
     */
    public function getCountFilesInFolder()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        if (!empty($_POST['term_id'])) {
            $count = $this->getCountFiles($_POST['term_id']);
            wp_send_json(
                array('status' => true, 'count' => $count)
            );
        }
        wp_send_json(
            array('status' => false)
        );
    }

    /**
     * Get exclude folders on watermark
     *
     * @return void
     */
    public function getExcludeFolders()
    {
        if (empty($_POST['wpmf_nonce'])
            || !wp_verify_nonce($_POST['wpmf_nonce'], 'wpmf_nonce')) {
            die();
        }

        $exclude_folders = wpmfGetOption('watermark_exclude_folders');
        wp_send_json(
            array('status' => true, 'folders' => array_unique($exclude_folders))
        );
    }
}
