<?php

namespace Bricksforge;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Form Submissions Handler
 */
class FormSubmissions
{

    /**
     * Menu Location
     */ private $menu_location = 'toplevel';

    /**
     * Menu Name
     */ private $menu_name = 'Submissions';

    /**
     * Menu Position
     */ private $menu_position = 11;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the tool
     */
    public function init()
    {
        if ($this->activated() === true) {
            $this->create_database_table();
            add_action('admin_menu', [$this, 'add_menu']);
            add_action('admin_enqueue_scripts', [$this, 'handle_notifications']);
        }
    }

    /**
     * Check if the tool is activated
     */
    public function activated()
    {
        return get_option('brf_activated_tools') && in_array(11, get_option('brf_activated_tools'));
    }

    public function create_database_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . BRICKSFORGE_SUBMISSIONS_DB_TABLE;

        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            return;
        }

        // Define the table structure
        $table_schema = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            post_id int(11) NOT NULL,
            form_id TEXT DEFAULT NULL,
            timestamp datetime NOT NULL,
            fields TEXT DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";

        // Create the table
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($table_schema);

        // Sanitize the table
        $this->sanitize_submission_database_table();
    }

    /**
     * Sanitize the database table
     */
    public function sanitize_submission_database_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . BRICKSFORGE_SUBMISSIONS_DB_TABLE;

        $rows = $wpdb->get_results("SELECT * FROM $table_name");

        foreach ($rows as $row) {
            $fields = json_decode($row->fields, true);
            $fields = array_map('sanitize_text_field', $fields);

            if (!is_array($fields)) {
                continue;
            }

            foreach ($fields as $key => $value) {
                if (!is_string($value)) {
                    $fields[$key]['value'] = '';
                } else {
                    $fields[$key]['value'] = sanitize_text_field($value);
                }
            }

            $wpdb->update(
                $table_name,
                array('fields' => json_encode($fields)),
                array('id' => $row->id),
                array('%s'),
                array('%d')
            );
        }
    }

    /**
     * Add the submenu to the Bricks menu
     */
    public function add_menu()
    {
        if (get_option('brf_tool_settings') || !in_array(10, get_option('brf_activated_tools'))) {
            // Get get_option('brf_tool_settings') (object) with the key "id" equal to 10
            $settings = array_filter(get_option('brf_tool_settings'), function ($tool) {
                return $tool->id == 11;
            });

            if (count($settings) > 0) {
                // Get the first item of the array
                $settings = array_shift($settings);

                // Get the menu location
                if (isset($settings->settings->location) && !empty($settings->settings->location)) {
                    $this->menu_location = $settings->settings->location;
                }

                // Get the menu name
                if (isset($settings->settings->menuName) && !empty($settings->settings->menuName)) {
                    $this->menu_name = $settings->settings->menuName;
                }

                // Get the menu position
                if (isset($settings->settings->menuPosition) && !empty($settings->settings->menuPosition)) {
                    $this->menu_position = $settings->settings->menuPosition;
                }
            }
        }

        add_menu_page(
            // Page title
            __($this->menu_name, 'bricksforge'),
            // Menu title
            __($this->menu_name, 'bricksforge'),
            // Capability
            'manage_options',
            // Menu slug
            'brf-form-submissions',
            // Callback
            [$this, 'bricks_render_submenu'],
            // Icon
            'dashicons-email-alt',
            // Position
            $this->menu_position
        );
    }

    public function bricks_render_submenu()
    {
        echo '<div id="brf-form-submissions-app"></div>';
    }

    /**
     * Add the notification styles to the menu item
     */
    public function handle_notifications()
    {

        // If the option brf_unread_submissions (array) contains data, get the count and save it as variable
        $unread_submissions = get_option('brf_unread_submissions', array());
        $unread_submissions_count = 0;

        if (is_array($unread_submissions) && count($unread_submissions) > 0) {
            $unread_submissions_count = count($unread_submissions);
        }

        if ($unread_submissions_count === 0) {
            return;
        }

        // Add the styles to the menu item
        wp_add_inline_style('wp-admin', 'body:not(.folded) #toplevel_page_brf-form-submissions:not(.resetted) .wp-menu-image::after { content: attr(data-unread); display: inline-flex; position: absolute; right: 18px; bottom: 0; top: 9px; justify-content: center; align-items: center; background: #FED64E; color: #000; width: 10px; height: 10px; border-radius: 50%; padding: 3px; font-size: 12px; font-weight: 600; }');
        wp_add_inline_style('wp-admin', '#toplevel_page_bricks:not(.resetted) li a[href="admin.php?page=brf-form-submissions"] {position: relative} #toplevel_page_bricks:not(.resetted) li a[href="admin.php?page=brf-form-submissions"]::after { content: attr(data-unread); display: inline-flex; position: absolute; right: 15px; bottom: 0; top: 6.5px; justify-content: center; align-items: center; color: #FED64E; width: 10px; height: 10px; border-radius: 50%; padding: 3px; font-size: 12px; font-weight: 600; }');

?>
        <script>
            window.addEventListener('load', function() {
                const elementTopLevelBricks = document.querySelector(
                    "#toplevel_page_bricks .wp-menu-image"
                );

                const elementTopLevelBricksforge = document.querySelector(
                    "#toplevel_page_brf-form-submissions .wp-menu-image"
                );

                const elementSubLevelBricks = document.querySelector(
                    "#toplevel_page_bricks li a[href='admin.php?page=brf-form-submissions']"
                );

                if (elementTopLevelBricksforge) {
                    elementTopLevelBricksforge.dataset.unread =
                        <?php echo esc_html($unread_submissions_count) ?>;
                }

                if (elementTopLevelBricks) {
                    elementTopLevelBricks.dataset.unread = <?php echo esc_html($unread_submissions_count) ?>;
                }

                if (elementSubLevelBricks) {
                    elementSubLevelBricks.dataset.unread = <?php echo esc_html($unread_submissions_count) ?>;
                }
            });
        </script>
<?php
    }
}
