<?php

/**
 * @package  SLUdemyWPImporter
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class AdminCallbacks extends BaseController
{
    // Load Pages
    public function adminDashboardImport()
    {
        return require_once("$this->plugin_path/templates/import.php");
    }
    public function adminDashboardSettings()
    {
        return require_once("$this->plugin_path/templates/admin.php");
    }

    // Admin Settings
    public function sluiOptionsGroup($input)
    {
        return $input;
    }
    public function sluiAdminSection()
    {
        echo 'Please enter your udemy api credentials here.';
    }
    public function sluiImportSection()
    {
        echo 'Settings related to your course import.';
    }
    public function sluiClientIdSet()
    {
        $value = esc_attr(get_option('slui_client_id'));
        echo '<input type="text" class="regular-text" name="slui_client_id" value="' . $value . '" placeholder="">';
    }
    public function sluiClientSecretSet()
    {
        $value = esc_attr(get_option('slui_client_secret'));
        echo '<input type="text" class="regular-text" name="slui_client_secret" value="' . $value . '" placeholder="">';
    }

    public function sluiPriceMarkupSet()
    {
        $value = esc_attr(get_option('slui_price_markup'));
        echo '<input type="text" class="regular-text" name="slui_price_markup" value="' . $value . '" placeholder="">';
    }

    public function sluiImportStateSet()
    {
        $value = esc_attr(get_option('slui_import_active'));
        if ($value){
            echo '<input type="checkbox" name="slui_import_active" checked placeholder="">';
        }else{
            echo '<input type="checkbox" name="slui_import_active" placeholder="">';
        }
        
    }
    public function sluiInsertVideoSet()
    {
        $value = esc_attr(get_option('slui_insert_video'));
        if ($value) {
            echo '<input type="checkbox" name="slui_insert_video" checked placeholder="">';
        } else {
            echo '<input type="checkbox" name="slui_insert_video" placeholder="">';
        }
    }
    public function sluiSelectVideoSet()
    {
        $value = esc_attr(get_option('slui_video_id'));
        echo '
        <input id="background_image" type="text" name="background_image" value="' . get_the_title($value) . '" readonly/>
        <input type="hidden" id="slui_video_id" name="slui_video_id" value="' . $value . '" />
        <input id="upload_image_button" type="button" class="button-primary" value="Select Video" />
        ';
    }
}