<?php
/**
 * Plugin Name: MyProtector Platform
 * Plugin URI: https://myprotector.com
 * Description: Trust verification platform for businesses - Protect, Verify, Trust. A comprehensive review and trust signal system for WordPress.
 * Version: 1.0.0
 * Author: MyProtector Team
 * Author URI: https://myprotector.com
 * License: Proprietary
 * Text Domain: myprotector-platform
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load configuration
require_once __DIR__ . '/config.php';

// Autoloader
require_once __DIR__ . '/myprotector-platform-loader.php';

// Register activation hook
register_activation_hook(__FILE__, function () {
    require_once __DIR__ . '/Core/Activator.php';
    \MyProtector\Core\Activator::activate();

    // FIX: Directly create pages during activation without depending on module loading
    // This bypasses the module system which may not be fully loaded during activation
    $pages = [
        'home'      => ['title' => 'MyProtector Home', 'slug' => 'home'],
        'businesses' => ['title' => 'Businesses', 'slug' => 'businesses'],
        'login'     => ['title' => 'Login', 'slug' => 'login'],
        'register'  => ['title' => 'Register', 'slug' => 'register'],
        'dashboard' => ['title' => 'Dashboard', 'slug' => 'dashboard'],
        'about'     => ['title' => 'About', 'slug' => 'about'],
        'contact'   => ['title' => 'Contact', 'slug' => 'contact'],
    ];
    
    foreach ($pages as $key => $page) {
        $existing = get_page_by_path($page['slug']);
        if (!$existing) {
            wp_insert_post([
                'post_title'   => $page['title'],
                'post_name'    => $page['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ]);
        }
    }
    
    // Flag rewrite rules to be flushed
    update_option('mp_flush_rewrite_rules', true);
});

// Register deactivation hook
register_deactivation_hook(__FILE__, function () {
    require_once __DIR__ . '/Core/Deactivator.php';
    \MyProtector\Core\Deactivator::deactivate();
});

// Bootstrap the plugin
use MyProtector\Core\MyProtector;

function myprotector(): MyProtector {
    return MyProtector::getInstance();
}

// Load Frontend UI Module
$frontend_ui = plugin_dir_path(__FILE__) . 'Modules/FrontendUI/FrontendUI.php';

if (file_exists($frontend_ui)) {
    require_once $frontend_ui;
}

// Initialize
myprotector()->run();