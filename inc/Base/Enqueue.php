<?php

/**
 * @package  SLUdemyWPImporter
 */

namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this,'media_uploader_enqueue'));
    }

    function media_uploader_enqueue()
    {
        wp_enqueue_media();
        wp_register_script('media-uploader',  $this->plugin_url . 'assets/media-uploader.js', array('jquery'));
        wp_enqueue_script('media-uploader');
    }
}
