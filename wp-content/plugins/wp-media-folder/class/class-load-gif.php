<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WpmfLoadGif
 * This class that holds most of the frontend functionality for load gif file
 */
class WpmfLoadGif
{
    /**
     * WpmfLoadGif constructor.
     */
    public function __construct()
    {
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        }
        add_filter('the_content', array($this, 'gifReplace'));
    }

    /**
     * Cancel gif file load on front end
     *
     * @param string $content Current post content
     *
     * @return mixed
     */
    public function gifReplace($content)
    {
        if (preg_match_all('/<img [^<>]+ \/>/i', $content, $matches)) {
            if (isset($matches[0]) && is_array($matches[0])) {
                foreach ($matches[0] as $img) {
                    $dom = new DOMDocument();
                    $dom->loadHTML($img);
                    $src          = $dom->getElementsByTagName('img')->item(0)->getAttribute('src');
                    $still_attach = preg_replace('/\.gif$/', '_still_tmp.jpeg', $src);
                    $alt          = $dom->getElementsByTagName('img')->item(0)->getAttribute('alt');
                    $width        = $dom->getElementsByTagName('img')->item(0)->getAttribute('width');
                    $class        = $dom->getElementsByTagName('img')->item(0)->getAttribute('class');
                    if (empty($src)) {
                        return $content;
                    }
                    $infos = pathinfo($src);
                    if ($infos['extension'] === 'gif') {
                        $output = '<div class="gif_wrap ' . $width . '">
                        <a href="javascript:void(0);" class="gif_link_wrap ' . $width . '"
                         title="Click to play" rel="nofollow"></a>
                        <span class="play_gif ' . $width . '">GIF</span>
                        <img src="' . $still_attach . '"
                         class="_showing frame no-lazy ' . $class . '" alt="' . $alt . '">
                   </div>
                   <img src="' . $still_attach . '" class="_hidden no-lazy" alt="' . $alt . '" style="display:none;">';

                        $content = str_replace($img, $output, $content);
                    }
                }
            }
        }
        // otherwise returns the database content
        return $content;
    }

    /**
     * Load script
     *
     * @return void
     */
    public function enqueue()
    {
        if (!is_admin()) {
            wp_register_script(
                'wpmf_play_gifs',
                plugins_url('assets/js/gif/play_gif.js', dirname(__FILE__)),
                array('jquery'),
                WPMF_VERSION,
                true
            );
            wp_enqueue_script('wpmf_play_gifs');
            wp_register_script(
                'wpmf_spin',
                plugins_url('assets/js/gif/spin.js', dirname(__FILE__)),
                array('jquery'),
                '1.0',
                true
            );
            wp_enqueue_script('wpmf_spin');
            wp_register_script(
                'wpmf_spinjQuery',
                plugins_url('assets/js/gif/jquery.spin.js', dirname(__FILE__)),
                array('jquery'),
                '1.0',
                true
            );
            wp_enqueue_script('wpmf_spinjQuery');
        }
    }
}
