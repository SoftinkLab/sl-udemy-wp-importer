<div class="wrap">
    <h1>Settings</h1>
    <?php settings_errors(); ?>


    <form method="post" action="options.php">
        <?php
        settings_fields('slui_settings_group');
        do_settings_sections('sl_udemy_wp_importer_settings');
        submit_button();
        ?>
    </form>
</div>