<?php
/**
 * MyProtector Platform - Role Installer (FIXED)
 *
 * Standalone role registration for easy integration
 *
 * @package MyProtector\Core
 * @version 1.0.1
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Install Roles
 * Call this function on plugin activation
 */
function mp_install_roles(): void {
    require_once __DIR__ . '/RoleManager.php';
    \MyProtector\Core\RoleManager::registerRoles();
}

/**
 * Uninstall Roles
 * Call this function on plugin uninstall (if option selected)
 */
function mp_uninstall_roles(): void {
    require_once __DIR__ . '/RoleManager.php';
    \MyProtector\Core\RoleManager::removeRoles();
}

/**
 * Check User Role
 */
function mp_get_user_role(?int $user_id = null): ?string {
    require_once __DIR__ . '/RoleManager.php';

    if (!function_exists('get_user_by')) {
        return null;
    }

    $user = $user_id
        ? get_user_by('id', $user_id)
        : (function_exists('wp_get_current_user') ? wp_get_current_user() : null);

    return \MyProtector\Core\RoleManager::getUserRole($user);
}

/**
 * Safe helper: get current user (always guarded)
 */
function mp_current_user(): ?WP_User {
    if (!function_exists('wp_get_current_user')) {
        return null;
    }

    $user = wp_get_current_user();

    return ($user instanceof WP_User && $user->exists()) ? $user : null;
}

/**
 * Check if Current User is Admin
 */
function mp_is_admin(): bool {
    $user = mp_current_user();

    if (!$user) return false;

    require_once __DIR__ . '/RoleManager.php';
    return \MyProtector\Core\RoleManager::isAdmin($user);
}

/**
 * Check if Current User is Support
 */
function mp_is_support(): bool {
    $user = mp_current_user();

    if (!$user) return false;

    require_once __DIR__ . '/RoleManager.php';
    return \MyProtector\Core\RoleManager::isSupport($user);
}

/**
 * Check if Current User is Business
 */
function mp_is_business(): bool {
    $user = mp_current_user();

    if (!$user) return false;

    require_once __DIR__ . '/RoleManager.php';
    return \MyProtector\Core\RoleManager::isBusiness($user);
}

/**
 * Check if Current User is Reseller
 */
function mp_is_reseller(): bool {
    $user = mp_current_user();

    if (!$user) return false;

    require_once __DIR__ . '/RoleManager.php';
    return \MyProtector\Core\RoleManager::isReseller($user);
}

/**
 * Check if Current User is Individual
 */
function mp_is_individual(): bool {
    $user = mp_current_user();

    if (!$user) return false;

    require_once __DIR__ . '/RoleManager.php';
    return \MyProtector\Core\RoleManager::isIndividual($user);
}

/**
 * Check if User Has Capability
 */
function mp_user_can(string $capability, ?int $user_id = null): bool {
    if (!function_exists('user_can')) {
        return false;
    }

    if ($user_id) {
        return user_can($user_id, $capability);
    }

    return function_exists('current_user_can') ? current_user_can($capability) : false;
}

/**
 * Get Dashboard URL based on role
 */
function mp_get_dashboard_url(): string {
    $user = mp_current_user();

    if (!$user) {
        return home_url('/');
    }

    if (mp_is_admin() || mp_is_support()) {
        return admin_url('admin.php?page=myprotector');
    }

    if (mp_is_business()) {
        return home_url('/dashboard/business');
    }

    if (mp_is_reseller()) {
        return home_url('/dashboard/reseller');
    }

    return home_url('/dashboard');
}

/**
 * Assign Role to User
 */
function mp_assign_role(int $user_id, string $role): bool {
    if (!function_exists('get_user_by')) {
        return false;
    }

    $user = get_user_by('id', $user_id);

    if (!$user) {
        return false;
    }

    $valid_roles = [
        'mp_individual',
        'mp_business',
        'mp_reseller',
        'mp_support',
        'mp_admin'
    ];

    if (!in_array($role, $valid_roles, true)) {
        return false;
    }

    // Remove existing custom roles
    foreach ($valid_roles as $r) {
        $user->remove_role($r);
    }

    // Add new role
    $user->add_role($role);

    return true;
}

/**
 * Get Users by Role
 */
function mp_get_users_by_role(string $role, int $limit = -1): array {
    if (!function_exists('get_users')) {
        return [];
    }

    return get_users([
        'role'   => $role,
        'number' => $limit,
        'fields' => ['ID', 'user_email', 'display_name', 'user_registered'],
    ]);
}

/**
 * Get Role Display Name
 */
function mp_get_role_display_name(string $role): string {
    $names = [
        'administrator' => 'Administrator',
        'mp_admin'      => 'MyProtector Admin',
        'mp_support'    => 'Customer Support',
        'mp_business'   => 'Business Owner',
        'mp_reseller'   => 'Reseller',
        'mp_individual' => 'Individual',
    ];

    return $names[$role] ?? 'Unknown Role';
}

/**
 * Get Role Badge Color
 */
function mp_get_role_badge_color(string $role): string {
    $colors = [
        'administrator' => '#e91e63',
        'mp_admin'      => '#e91e63',
        'mp_support'    => '#2196f3',
        'mp_business'   => '#4caf50',
        'mp_reseller'   => '#ff9800',
        'mp_individual' => '#9e9e9e',
    ];

    return $colors[$role] ?? '#9e9e9e';
}

/**
 * Capability filter for custom MP roles
 */
add_filter('map_meta_cap', function ($caps, $cap, $user_id, $args) {

    if (!function_exists('get_user_by')) {
        return $caps;
    }

    if (strpos($cap, 'mp_') === 0) {
        $user = get_user_by('id', $user_id);

        if ($user && $user->has_cap($cap)) {
            return ['read'];
        }

        return ['do_not_allow'];
    }

    return $caps;

}, 10, 4);