<?php
/**
 * MyProtector Platform - Header Component
 */

if (!defined('ABSPATH')) exit;

/**
 * =========================
 * SAFE CONSTANTS
 * =========================
 */
$company_url   = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$support_url   = defined('MYPROTECTOR_SUPPORT_URL') ? MYPROTECTOR_SUPPORT_URL : home_url('/support');
$dashboard_url = home_url('/dashboard');
$login_url     = home_url('/login');
$register_url  = home_url('/register');
$logout_url    = function_exists('wp_logout_url') ? wp_logout_url($company_url) : $company_url;

/**
 * =========================
 * LOGO CONFIGURATION
 * =========================
 * Set MYPROTECTOR_LOGO_URL to your logo image URL
 * Example: define('MYPROTECTOR_LOGO_URL', 'https://example.com/logo.png');
 * 
 * Logo image recommendations:
 * - Format: PNG, SVG, or WebP
 * - Recommended size: 150-200px width, auto height
 * - Should have transparent background for best results
 */
$logo_url = defined('MYPROTECTOR_LOGO_URL') && !empty(MYPROTECTOR_LOGO_URL) 
    ? MYPROTECTOR_LOGO_URL 
    : '';

/**
 * =========================
 * SAFE USER HANDLING
 * =========================
 */
$is_logged_in = function_exists('is_user_logged_in') && is_user_logged_in();

$current_user = null;

if ($is_logged_in && function_exists('wp_get_current_user')) {
    $current_user = wp_get_current_user();
}

/**
 * =========================
 * SAFE DISPLAY NAME
 * =========================
 */
$user_display_name = 'Guest';

if ($current_user instanceof WP_User) {
    $user_display_name = !empty($current_user->display_name)
        ? $current_user->display_name
        : $current_user->user_email;
}
?>

<header class="mp-header">
    <div class="mp-container">
        <div class="mp-header-inner">

            <a href="<?php echo esc_url($company_url); ?>" class="mp-logo">
                <?php if (!empty($logo_url)): ?>
                    <!-- Custom Logo Image -->
                    <img src="<?php echo esc_url($logo_url); ?>" alt="MyProtector Logo" class="mp-logo-image">
                    <div class="mp-logo-text">My<span>Protector</span></div>
                <?php else: ?>
                    <!-- Default Text Logo -->
                    <div class="mp-logo-icon">MP</div>
                    <div class="mp-logo-text">My<span>Protector</span></div>
                <?php endif; ?>
            </a>

            <nav class="mp-nav">
                <a href="<?php echo esc_url($company_url); ?>"
                   class="mp-nav-link <?php echo is_front_page() ? 'active' : ''; ?>">
                    Home
                </a>

                <a href="<?php echo esc_url($company_url . '/businesses'); ?>"
                   class="mp-nav-link">
                    Businesses
                </a>

                <a href="<?php echo esc_url($company_url . '/about'); ?>"
                   class="mp-nav-link">
                    About
                </a>

                <?php if ($is_logged_in): ?>
                    <a href="<?php echo esc_url($dashboard_url); ?>"
                       class="mp-nav-link">
                        Dashboard
                    </a>
                <?php endif; ?>
            </nav>

            <div class="mp-header-actions">

                <?php if ($is_logged_in): ?>
                    <div class="mp-user-menu">
                        <span class="mp-user-name">
                            <?php echo esc_html($user_display_name); ?>
                        </span>

                        <a href="<?php echo esc_url($logout_url); ?>"
                           class="mp-btn mp-btn-ghost">
                            Log Out
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo esc_url($login_url); ?>"
                       class="mp-btn mp-btn-ghost">
                        Log In
                    </a>

                    <a href="<?php echo esc_url($register_url); ?>"
                       class="mp-btn mp-btn-primary">
                        Sign Up
                    </a>
                <?php endif; ?>

            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mp-btn mp-btn-icon mp-btn-ghost mp-mobile-menu-toggle"
                    aria-label="Toggle menu">

                <svg width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>

            </button>

        </div>
    </div>
</header>