<?php

/**
 * @package  SLUdemyWPImporter
 */

namespace Inc\Pages;

use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\SettingsApi;
use Inc\Base\BaseController;

class Admin extends BaseController{
    public $settings;
    public $callbacks;
    public $pages = array();
    public $subpages = array();

    public function register()
    {
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
        $this->setPages();
        $this->setSettings();
        $this->setSections();
        $this->setFields();
        $this->settings->addPages($this->pages)->withSubPage('Import')->addSubPages($this->subpages)->register();
    }

    public function setPages()
    {
        $this->pages = array(
            array(
                'page_title' => 'Udemy - Wordpress Importer',
                'menu_title' => 'Udemy Importer',
                'capability' => 'manage_options',
                'menu_slug' => 'sl_udemy_wp_importer',
                'callback' => array($this->callbacks, 'adminDashboardImport'),
                'icon_url' => 'dashicons-admin-links',
                'position' => 110
            )
        );

        $this->subpages = array(
            array(
                'parent_slug' => 'sl_udemy_wp_importer',
                'page_title' => 'Settings',
                'menu_title' => 'Settings',
                'capability' => 'manage_options',
                'menu_slug' => 'sl_udemy_wp_importer_settings',
                'callback' => array($this->callbacks, 'adminDashboardSettings')
            )
        );
    }

    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'slui_settings_group',
                'option_name' => 'slui_client_id',
                'callback' => array($this->callbacks, 'sluiOptionsGroup')
            ),
            array(
                'option_group' => 'slui_settings_group',
                'option_name' => 'slui_client_secret',
                'callback' => array($this->callbacks, 'sluiOptionsGroup')
            ),
            array(
                'option_group' => 'slui_settings_group',
                'option_name' => 'slui_price_markup',
                'callback' => array($this->callbacks, 'sluiOptionsGroup')
            ),
            array(
                'option_group' => 'slui_settings_group',
                'option_name' => 'slui_import_active',
                'callback' => array($this->callbacks, 'sluiOptionsGroup')
            ),
            array(
                'option_group' => 'slui_settings_group',
                'option_name' => 'slui_insert_video',
                'callback' => array($this->callbacks, 'sluiOptionsGroup')
            ),
            array(
                'option_group' => 'slui_settings_group',
                'option_name' => 'slui_video_id',
                'callback' => array($this->callbacks, 'sluiOptionsGroup')
            )
        );
        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'slui_admin_index',
                'title' => 'Udemy API Details',
                'callback' => array($this->callbacks, 'sluiAdminSection'),
                'page' => 'sl_udemy_wp_importer_settings'
            ),
            array(
                'id' => 'slui_admin_import',
                'title' => 'Import Settings',
                'callback' => array($this->callbacks, 'sluiImportSection'),
                'page' => 'sl_udemy_wp_importer_settings'
            )
        );
        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array(
            array(
                'id' => 'slui_client_id',
                'title' => 'Client ID',
                'callback' => array($this->callbacks, 'sluiClientIdSet'),
                'page' => 'sl_udemy_wp_importer_settings',
                'section' => 'slui_admin_index',
                'args' => array(
                    'label_for' => 'slui_client_id',
                    'class' => 'example-class'
                )
                ),
            array(
                'id' => 'slui_client_secret',
                'title' => 'Client Secret',
                'callback' => array($this->callbacks, 'sluiClientSecretSet'),
                'page' => 'sl_udemy_wp_importer_settings',
                'section' => 'slui_admin_index',
                'args' => array(
                    'label_for' => 'slui_client_secret',
                    'class' => 'example-class'
                )
                ),
            array(
                'id' => 'slui_price_markup',
                'title' => 'Price Markup (%)',
                'callback' => array($this->callbacks, 'sluiPriceMarkupSet'),
                'page' => 'sl_udemy_wp_importer_settings',
                'section' => 'slui_admin_import',
                'args' => array(
                    'label_for' => 'slui_price_markup',
                    'class' => 'example-class'
                )
                ), 
            array(
                'id' => 'slui_import_active',
                'title' => 'Import as Active Course',
                'callback' => array($this->callbacks, 'sluiImportStateSet'),
                'page' => 'sl_udemy_wp_importer_settings',
                'section' => 'slui_admin_import',
                'args' => array(
                    'label_for' => 'slui_import_active',
                    'class' => 'example-class'
                )
                ),
            array(
                'id' => 'slui_insert_video',
                'title' => 'Insert Video to Course',
                'callback' => array($this->callbacks, 'sluiInsertVideoSet'),
                'page' => 'sl_udemy_wp_importer_settings',
                'section' => 'slui_admin_import',
                'args' => array(
                    'label_for' => 'slui_insert_video',
                    'class' => 'example-class'
                )
                ),
            array(
                'id' => 'slui_video_id',
                'title' => 'Select Video',
                'callback' => array($this->callbacks, 'sluiSelectVideoSet'),
                'page' => 'sl_udemy_wp_importer_settings',
                'section' => 'slui_admin_import',
                'args' => array(
                    'label_for' => 'slui_video_id',
                    'class' => 'example-class'
                )
            )
        );
        $this->settings->setFields($args);
    }
}