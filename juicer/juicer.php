<?php
/**
 * Plugin Name: Juicer
 * Plugin URI: https://wp.juicer.io
 * Description: Embed, curate & aggregate social media feeds from Instagram, Twitter, TikTok, Facebook, LinkedIn, YouTube, Slack, etc. and customize them as you like.
 * Version: 1.12.12
 * Author: saas.group Inc.
 * Author URI: https://saas.group
 * License: GPLv2 or later
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('JUICER_VERSION', '1.12.12');

class Juicer_Feed {
    public function render($args) {
        $defaults = array(
            'name' => 'error',
        );
        $args = wp_parse_args($args, $defaults);

        $map_attributes = generate_attributes($args);

        $attributes = join('&', $map_attributes);

        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'juicerembed-' . $args['name'],
            '//www.juicer.io/embed/' . $args['name'] . '/wp-plugin-1-12.js?nojquery=true&' . $attributes,
            array('jquery'),
            false,
            true
        );

        return '<div class="juicer-feed" data-feed-id="' . htmlspecialchars($args['name']) . '"></div>';
    }
}

function generate_attributes($array) {
    $attrs = array();

    foreach ($array as $key => $val) {
        if ($key == 'name') {
            continue;
        }
        $escaped_val = htmlspecialchars($val);
        if (!empty($escaped_val)) {
            if (strpos($key, "data-") !== false) {
                $escaped_key = str_replace("data-", "", $key);
                array_push($attrs, $escaped_key . '=' . $escaped_val);
            } else {
                array_push($attrs, $key . '=' . $escaped_val);
            }
        }
    }

    return $attrs;
}

function juicer_feed($args) {
    $feed = new Juicer_Feed();
    echo $feed->render($args);
}

function juicer_shortcode($args) {
    extract(shortcode_atts(array(
       'name' => 'error',
    ), $args ) );

    $feed = new Juicer_Feed();
    return $feed->render($args);
}
add_shortcode('juicer', 'juicer_shortcode');


function juicer_activate() {
    setcookie('juicer_welcome', 'true', time() + 3600, '/'); // Cookie expires in 1 hour
}
register_activation_hook(__FILE__, 'juicer_activate');

// Setup menu for admin section
function juicer_set_admin_menu() {
    $hook_suffix = add_menu_page(
        esc_html__('Juicer', 'juicer'),
        esc_html__('Juicer', 'juicer'),
        'manage_options',
        'juicer-settings',
        'juicer_set_settings_page',
        'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjIiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2MiA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQ4LjYwOTUgMTMuNDI4NUM1MS4wMDMzIDEzLjQyODUgNTIuOTMzOCAxMS40OTggNTIuOTMzOCA5LjEwNDIzVjYuMzI0MzJDNTIuODU2NiAzLjkzMDUxIDUxLjAwMzMgMi4wMDAwMiA0OC42MDk1IDIuMDAwMDJDNDYuMjE1NyAyLjAwMDAyIDQ0LjI4NTIgMy45MzA1MSA0NC4yODUyIDYuMzI0MzJWOS4xODE0NUM0NC4yODUyIDExLjQ5OCA0Ni4yMTU3IDEzLjQyODUgNDguNjA5NSAxMy40Mjg1WiIgZmlsbD0iIzlEQTJBNyIvPgo8cGF0aCBkPSJNNTIuODU2OSA0MS4yMjc1VjIzLjAwMzdDNTIuODU2OSAyMC44NDE1IDUwLjkyNjQgMTkuMTQyNyA0OC41MzI2IDE5LjE0MjdDNDYuMTM4OCAxOS4xNDI3IDQ0LjIwODMgMjAuOTE4OCA0NC4yMDgzIDIzLjAwMzdWMzMuNDI4M1YzNS41OTA1QzQ0LjIwODMgMzcuOTg0MyA0Mi4yNzc4IDM5LjkxNDggMzkuODg0IDM5LjkxNDhDMzcuNDkwMiAzOS45MTQ4IDM1LjYzNjkgMzcuOTg0MyAzNS41NTk3IDM1LjY2NzdWMzQuODk1NVYzNC4yMDA1SDM1LjQ4MjVDMzUuMzI4IDMyLjExNTYgMzMuNTUyIDMwLjU3MTIgMzEuNDY3MSAzMC41NzEyQzI5LjMwNDkgMzAuNTcxMiAyNy42MDYxIDMyLjExNTYgMjcuMjIgMzQuMTIzM0gyNy4xNDI4VjM0LjgxODNWMzcuNjc1NEMyNy4xNDI4IDQwLjA2OTIgMjUuMjEyMyA0MS45MjI1IDIyLjgxODUgNDEuOTIyNUMyMC40MjQ3IDQxLjkyMjUgMTguNDk0MiAzOS45OTIgMTguNDk0MiAzNy41OTgyVjMzLjM1MTFDMTMuMzk3NyAzMy4yNzM5IDEwIDM0LjQzMjIgMTAgNDEuMjI3NUMxMCA1Mi43MzMzIDE5LjU3NTIgNjEuOTk5NiAzMS40NjcxIDYxLjk5OTZDNDMuMjgxNyA2Mi4wNzY4IDUyLjg1NjkgNTIuNzMzMyA1Mi44NTY5IDQxLjIyNzVaIiBmaWxsPSIjOURBMkE3Ii8+CjxwYXRoIGQ9Ik0zMS40NjY5IDI0Ljg1N0MzMy44NjA3IDI0Ljg1NyAzNS43OTEyIDIyLjkyNjUgMzUuNzkxMiAyMC41MzI3VjE3LjY3NTVDMzUuNzkxMiAxNS4yODE3IDMzLjg2MDcgMTMuMzUxMiAzMS40NjY5IDEzLjM1MTJDMjkuMDczMSAxMy4zNTEyIDI3LjE0MjYgMTUuMjgxNyAyNy4xNDI2IDE3LjY3NTVWMjAuNTMyN0MyNy4xNDI2IDIyLjkyNjUgMjkuMDczMSAyNC44NTcgMzEuNDY2OSAyNC44NTdaIiBmaWxsPSIjOURBMkE3Ii8+CjxwYXRoIGQ9Ik0yMy41MTMxIDQyLjg0OTNDMjMuMzU4NyA0Mi41NDA1IDIzLjIwNDIgNDIuMzA4OCAyMy4wNDk4IDQxLjk5OTlDMjIuOTcyNiA0MS45OTk5IDIyLjk3MjYgNDEuOTk5OSAyMi44OTU0IDQxLjk5OTlDMjAuNTAxNSA0MS45OTk5IDE4LjU3MTEgNDAuMDY5NCAxOC41NzExIDM3LjY3NTZWMzMuNDI4NUMxNi4xNzcyIDMzLjM1MTMgMTQuMTY5NSAzMy41ODMgMTIuNzAyNCAzNC40MzI0QzEyLjM5MzUgMzUuNDM2MyAxMi4yMzkgMzYuNTk0NiAxMi4yMzkgMzguMDYxN0MxMi4yMzkgNDkuNTY3NSAyMS44MTQzIDU4LjgzMzggMzMuNzA2MSA1OC44MzM4QzM5LjM0MzEgNTguODMzOCA0NC41OTQxIDU3LjA1NzggNDguNDU1MSA1My44MTQ1QzQ5Ljk5OTUgNTEuODg0IDUxLjIzNSA0OS42NDQ3IDUyLjAwNzIgNDcuMTczNkM0My4wNDk3IDUzLjk2OSAyOS44NDUxIDUyLjg4NzkgMjMuNTEzMSA0Mi44NDkzWiIgZmlsbD0iIzlEQTJBNyIvPgo8cGF0aCBkPSJNMjEuMjc0MSA0NS45MzhDMTkuNDIwOCA0My4wMDM2IDE4Ljg4MDMgNDAuNjg3IDE4LjcyNTggMzguNzU2NUMxOC42NDg2IDM4LjQ0NzcgMTguNTcxNCAzOC4wNjE2IDE4LjU3MTQgMzcuNzUyN1YzMy41MDU2QzEzLjM5NzcgMzMuMjczOSAxMCAzNC41MDk1IDEwIDQxLjIyNzZDMTAgNTIuNzMzMyAxOS41NzUyIDYxLjk5OTcgMzEuNDY3MSA2MS45OTk3QzQxLjExOTUgNjEuOTk5NyA0OS4zMDQ4IDU1LjgyMjEgNTIuMDA3NSA0Ny4yNTA3QzQzLjEyNzIgNTYuMjg1NCAyOC4yMjM4IDU2Ljk4MDQgMjEuMjc0MSA0NS45MzhaIiBmaWxsPSIjOURBMkE3Ii8+Cjwvc3ZnPgo=',
        99
    );
}
add_action('admin_menu', 'juicer_set_admin_menu');

// Load custom admin CSS
function load_custom_wp_admin_style() {
    wp_enqueue_style('juicer-admin-css', plugin_dir_url(__FILE__) . 'includes/admin/css/admin.css', array(), JUICER_VERSION);
}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');

// Load custom admin JavaScript and localize
function load_custom_wp_admin_script($hook_suffix) {
    // Check if we're on the Juicer settings page or any admin page
    if ($hook_suffix === 'toplevel_page_juicer-settings' || $hook_suffix === 'index.php') {
        wp_enqueue_script('juicer-admin-js', plugin_dir_url(__FILE__) . 'includes/admin/js/admin.js', array('jquery'), JUICER_VERSION, true);

        // Localize the script with your data
        wp_localize_script('juicer-admin-js', 'juicer_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('juicer_nonce')
        ));
    }
}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_script');


// Setup guide page
function juicer_set_settings_page() {
    include('includes/admin/settings.php');
}

// Plugin setup guide link
function juicer_plugin_action_links($links) {
    $links[] = '<a href="' . admin_url('admin.php?page=juicer-settings') . '">' . __('Setup guide') . '</a>';
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'juicer_plugin_action_links');

// Plugin review notice
// Check for feed existence and set option
function juicer_check_feed_existence() {
    $host_url = home_url();
    $response = wp_remote_get('https://www.juicer.io/api/hosts?hostname=' . $host_url);

    // Default to no feeds existing
    update_option('juicer_feed_exists', false);

    if (!is_wp_error($response)) {
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code !== 404) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            foreach ($data as $item) {
                if (isset($item['feed_id'])) {
                    // Update the option to true as feed exists
                    update_option('juicer_feed_exists', true);
                    break;
                }
            }
        }
    }
}
add_action('admin_init', 'juicer_check_feed_existence');

// Display review notice
function juicer_review_notice() {
    // Get the current screen object to check the hook suffix
    $current_screen = get_current_screen();
    $hook_suffix = $current_screen->base;

    // Check if the current page is one of the allowed pages
    if ($hook_suffix === 'toplevel_page_juicer-settings' || $hook_suffix === 'dashboard') {
        if (get_option('juicer_feed_exists') && juicer_should_show_review_notice()) {
            echo '<div class="notice notice-info is-dismissible" id="juicer-review-notice">';
            echo '<div class="juicer__notice__holder">';
            echo '<div class="juicer__notice__col_1">';
            echo '<img src="' . plugin_dir_url(__FILE__) . 'includes/admin/img/juicer-icon.svg" width="24" >';
            echo '</div>';
            echo '<div class="juicer__notice__col_2">';
            echo '<strong class="juicer__notice__title">Thanks for using Juicer!</strong>';
            echo '<p class="juicer__notice__text">If you enjoy our plugin, would you consider leaving us a <strong>5-star review</strong> on Wordpress.org?</p>';
            echo '<div class="juicer__notice__buttons">';
            echo '<a href="https://wordpress.org/plugins/juicer/#reviews" target="_blank" id="juicer-love-it" class="button button-primary">Sure, I’d love to <img src="' . plugin_dir_url(__FILE__) . 'includes/admin/img/wp-outbound-link-icon-white.svg" width="16" height="16" ></a> ';
            echo '<button id="juicer-maybe-later" class="juicer-notice__links">Maybe later</button> ';
            echo '<button id="juicer-never-show" class="juicer-notice__links">Never show this again</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
}
add_action('admin_notices', 'juicer_review_notice');


// AJAX handler for dismissing the review notice
function juicer_dismiss_review_notice() {
    check_ajax_referer('juicer_nonce', 'security');
    if (current_user_can('manage_options')) {
        if (isset($_POST['dismiss_type'])) {
            $dismiss_type = sanitize_text_field($_POST['dismiss_type']);
            if ($dismiss_type === 'permanent') {
                // Never show again
                update_option('juicer_review_permanently_dismissed', 'yes');
                wp_send_json_success('Permanently dismissed.');
            } elseif ($dismiss_type === 'temporary') {
                // Dismiss for 7 days
                update_option('juicer_review_dismissed', time() + 7 * DAY_IN_SECONDS);
                wp_send_json_success('Temporarily dismissed.');
            } else {
                wp_send_json_error('Invalid dismiss type.');
            }
        } else {
            wp_send_json_error('Dismiss type not set.');
        }
    } else {
        wp_send_json_error('Permission denied.');
    }
    wp_die();
}
add_action('wp_ajax_juicer_dismiss_review_notice', 'juicer_dismiss_review_notice');

// Check if the review notice should be displayed
function juicer_should_show_review_notice() {
    if (get_option('juicer_review_permanently_dismissed') === 'yes') {
        return false;
    }
    $dismissed_until = get_option('juicer_review_dismissed');
    if ($dismissed_until && time() < $dismissed_until) {
        return false;
    }
    return true;
}

// Register the Elementor widget
function register_juicer_elementor_widget($widgets_manager) {
    if (defined('ELEMENTOR_VERSION') && class_exists('Elementor\Widget_Base')) {
        require_once plugin_dir_path(__FILE__) . 'includes/elementor/elementor-widget.php';
        $widgets_manager->register(new \Elementor_Juicer_Widget());
    }
}
add_action('elementor/widgets/register', 'register_juicer_elementor_widget');

// Enqueue custom CSS for Elementor editor
function enqueue_juicer_elementor_editor_styles() {
    wp_enqueue_style(
        'juicer-elementor-editor',
        plugin_dir_url(__FILE__) . 'includes/elementor/juicer-elementor.css',
        array(),
        JUICER_VERSION
    );
}
add_action('elementor/frontend/after_enqueue_styles', 'enqueue_juicer_elementor_editor_styles');
add_action('elementor/editor/after_enqueue_styles', 'enqueue_juicer_elementor_editor_styles');

function enqueue_daterangepicker() {
    wp_enqueue_script('moment-js', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '2.29.1', true);
    wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array('jquery', 'moment-js'), '3.1', true);
    wp_enqueue_style('daterangepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', array(), '3.1');
    wp_enqueue_script('juicer-daterangepicker-init', plugin_dir_url(__FILE__) . 'includes/elementor/daterangepicker-init.js', array('jquery', 'daterangepicker'), '1.0', true);
}
add_action('elementor/editor/after_enqueue_scripts', 'enqueue_daterangepicker');
?>
