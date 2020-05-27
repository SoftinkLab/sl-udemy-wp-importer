<?php

/**
 * @package SLUdemyWPImporter
 */

namespace Inc\Base;

class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();

        // Set default settings
        if (!get_option('slui_price_markup')){
            update_option('slui_price_markup', 15);
        }

        if (!get_option('slui_import_active')) {
            update_option('slui_import_active', true);
        }
    }
}
