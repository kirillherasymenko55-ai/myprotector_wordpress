<?php
/**
 * MyProtector Platform - Data Repository
 * 
 * Handles all database queries for frontend display
 * 
 * @package MyProtector\Core
 */

namespace MyProtector\Core;

if (!defined('ABSPATH')) exit;

class DataRepository {
    
    /**
     * Get all businesses with optional filters
     */
    public static function getBusinesses(array $args = []): array {
        global $wpdb;
        
        $defaults = [
            'status' => 'approved',
            'orderby' => 'trust_score',
            'order' => 'DESC',
            'limit' => 100,
            'offset' => 0,
            'search' => '',
            'category' => 0,
            'trust_status' => '',
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $where = ["c.status = %s"];
        $values = [$args['status']];
        
        if (!empty($args['search'])) {
            $where[] = "(c.company_name LIKE %s OR c.company_description LIKE %s)";
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = $search;
            $values[] = $search;
        }
        
        if (!empty($args['category'])) {
            $where[] = "c.company_category = %d";
            $values[] = $args['category'];
        }
        
        $where_clause = implode(' AND ', $where);
        
        // Build trust status join if needed
        $join = '';
        if (!empty($args['trust_status'])) {
            $join = "LEFT JOIN {$wpdb->prefix}mp_trust_signals ts ON c.company_id = ts.company_id";
            $where[] = "ts.status = %s";
            $values[] = $args['trust_status'];
        }
        
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        
        $sql = "SELECT c.* 
                FROM {$wpdb->prefix}mp_companies c
                $join
                WHERE $where_clause
                ORDER BY $orderby
                LIMIT %d OFFSET %d";
        
        $values[] = $args['limit'];
        $values[] = $args['offset'];
        
        $businesses = $wpdb->get_results(
            $wpdb->prepare($sql, $values)
        );
        
        return self::formatBusinesses($businesses);
    }
    
    /**
     * Get single business by slug
     */
    public static function getBusinessBySlug(string $slug): ?array {
        global $wpdb;
        
        $business = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT c.*, ts.status as trust_status
                 FROM {$wpdb->prefix}mp_companies c
                 LEFT JOIN {$wpdb->prefix}mp_trust_signals ts ON c.company_id = ts.company_id
                 WHERE c.company_slug = %s AND c.status = 'approved'",
                $slug
            )
        );
        
        if (!$business) return null;
        
        return self::formatBusiness($business);
    }
    
    /**
     * Get business by ID
     */
    public static function getBusinessById(int $id): ?array {
        global $wpdb;
        
        $business = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT c.*, ts.status as trust_status
                 FROM {$wpdb->prefix}mp_companies c
                 LEFT JOIN {$wpdb->prefix}mp_trust_signals ts ON c.company_id = ts.company_id
                 WHERE c.company_id = %d",
                $id
            )
        );
        
        if (!$business) return null;
        
        return self::formatBusiness($business);
    }
    
    /**
     * Get reviews for a business
     */
    public static function getReviewsByBusiness(int $business_id, int $limit = 10): array {
        global $wpdb;
        
        $reviews = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT r.*, u.display_name as reviewer_name, u.user_email as reviewer_email
                 FROM {$wpdb->prefix}mp_reviews r
                 LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                 WHERE r.company_id = %d AND r.is_published = 1 AND r.review_status = 'approved'
                 ORDER BY r.created_at DESC
                 LIMIT %d",
                $business_id,
                $limit
            )
        );
        
        return self::formatReviews($reviews);
    }
    
    /**
     * Get reviews by user
     */
    public static function getReviewsByUser(int $user_id, int $limit = 10): array {
        global $wpdb;
        
        $reviews = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT r.*, c.company_name, c.company_slug
                 FROM {$wpdb->prefix}mp_reviews r
                 LEFT JOIN {$wpdb->prefix}mp_companies c ON r.company_id = c.company_id
                 WHERE r.user_id = %d AND r.is_published = 1
                 ORDER BY r.created_at DESC
                 LIMIT %d",
                $user_id,
                $limit
            )
        );
        
        return self::formatReviews($reviews, true);
    }
    
    /**
     * Get recent reviews across all businesses
     */
    public static function getRecentReviews(int $limit = 10): array {
        global $wpdb;
        
        $reviews = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT r.*, c.company_name, c.company_slug, u.display_name as reviewer_name
                 FROM {$wpdb->prefix}mp_reviews r
                 LEFT JOIN {$wpdb->prefix}mp_companies c ON r.company_id = c.company_id
                 LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                 WHERE r.is_published = 1 AND r.review_status = 'approved'
                 ORDER BY r.published_at DESC
                 LIMIT %d",
                $limit
            )
        );
        
        return self::formatReviews($reviews, true);
    }
    
    /**
     * Get dashboard stats for a user
     */
    public static function getUserStats(int $user_id): array {
        global $wpdb;
        
        // Get review count
        $review_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}mp_reviews WHERE user_id = %d AND is_published = 1",
                $user_id
            )
        );
        
        // Get helpful votes received
        $user_reviews = $wpdb->get_col(
            $wpdb->prepare("SELECT review_id FROM {$wpdb->prefix}mp_reviews WHERE user_id = %d", $user_id)
        );
        
        $helpful_votes = 0;
        if (!empty($user_reviews)) {
            $helpful_votes = $wpdb->get_var(
                "SELECT SUM(helpful_count) FROM {$wpdb->prefix}mp_reviews WHERE review_id IN (" . implode(',', array_map('intval', $user_reviews)) . ")"
            );
        }
        
        // Get trust level
        $trust_level = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT trust_level FROM {$wpdb->prefix}mp_user_trust_levels WHERE user_id = %d",
                $user_id
            )
        );
        
        // Get member since
        $user = get_userdata($user_id);
        $member_since = $user ? date('F Y', strtotime($user->user_registered)) : 'N/A';
        
        return [
            'total_reviews' => (int) $review_count,
            'helpful_votes' => (int) ($helpful_votes ?: 0),
            'trust_score' => $trust_level === 'premium' ? 100 : ($trust_level === 'verified' ? 75 : 50),
            'member_since' => $member_since,
        ];
    }
    
    /**
     * Get categories
     */
    public static function getCategories(): array {
        $categories = get_categories([
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);
        
        $result = [];
        foreach ($categories as $cat) {
            $result[] = [
                'id' => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => $cat->count,
            ];
        }
        
        return $result;
    }
    
    /**
     * Get overall stats
     */
    public static function getStats(): array {
        global $wpdb;
        
        $total_businesses = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}mp_companies WHERE status = 'approved'"
        );
        
        $total_reviews = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}mp_reviews WHERE is_published = 1 AND review_status = 'approved'"
        );
        
        $total_users = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->users}"
        );
        
        return [
            'total_businesses' => (int) $total_businesses,
            'total_reviews' => (int) $total_reviews,
            'total_users' => (int) $total_users,
        ];
    }
    
    /**
     * Format businesses for frontend display
     */
    protected static function formatBusinesses(array $businesses): array {
        return array_map([self::class, 'formatBusiness'], $businesses);
    }
    
    /**
     * Format single business for frontend display
     */
    protected static function formatBusiness($business): array {
        if (!$business) return null;
        
        // Generate logo URL using UI Avatars
        $initials = '';
        $words = explode(' ', $business->company_name);
        foreach ($words as $word) {
            $initials .= substr($word, 0, 1);
            if (strlen($initials) >= 2) break;
        }
        
        $colors = ['0A1F44', '2E7D32', 'D50000', 'FB8C00', '0288D1', '7B1FA2'];
        $color = $colors[$business->company_id % count($colors)];
        
        $logo = 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=' . $color . '&color=fff&size=128';
        
        // Determine trust status
        $trust_status = 'red';
        $trust_score = floatval($business->trust_score ?: 0);
        
        if ($trust_score >= 1.00) {
            $trust_status = 'green';
        } elseif ($trust_score >= 0.67) {
            $trust_status = 'amber';
        }
        
        return [
            'id' => (int) $business->company_id,
            'name' => $business->company_name,
            'slug' => $business->company_slug,
            'description' => $business->company_description ?: '',
            'website' => $business->company_website ?: '',
            'logo' => $logo,
            'location' => self::extractLocation($business->company_address),
            'category' => self::getCategoryName($business->company_category),
            'category_id' => (int) $business->company_category,
            'rating' => floatval($business->avg_rating ?: 0),
            'total_reviews' => (int) $business->total_reviews ?: 0,
            'trust_status' => $trust_status,
            'trust_score' => ($trust_score * 100),
            'claimed' => !empty($business->user_id),
            'insurance_name' => $business->insurance_name ?: '',
            'insurance_url' => $business->insurance_url ?: '',
            'terms_url' => $business->terms_url ?: '',
            'promise_url' => $business->promise_page_url ?: '',
            'promise_title' => $business->promise_page_title ?: '',
            'established' => date('Y', strtotime($business->created_at)),
        ];
    }
    
    /**
     * Format reviews for frontend display
     */
    protected static function formatReviews(array $reviews, bool $include_business = false): array {
        return array_map(function($review) use ($include_business) {
            $formatted = [
                'id' => (int) $review->review_id,
                'business_id' => (int) $review->company_id,
                'title' => $review->review_title,
                'content' => $review->review_content,
                'rating' => (int) $review->review_rating,
                'reviewer' => $review->reviewer_name ?: 'Anonymous',
                'reviewer_avatar' => 'https://ui-avatars.com/api/?name=' . urlencode(substr($review->reviewer_name ?: 'A', 0, 2)),
                'date' => date('Y-m-d', strtotime($review->published_at ?: $review->created_at)),
                'verified' => ($review->trust_level === 'verified' || $review->trust_level === 'premium'),
                'helpful' => (int) ($review->helpful_count ?: 0),
                'images' => [],
            ];
            
            if ($include_business && isset($review->company_name)) {
                $formatted['business_name'] = $review->company_name;
                $formatted['business_slug'] = $review->company_slug;
            }
            
            return $formatted;
        }, $reviews);
    }
    
    /**
     * Extract location string from address
     */
    protected static function extractLocation(?string $address): string {
        if (!$address) return 'Location not specified';
        
        // Try to get city, state from address
        $parts = explode(',', $address);
        if (count($parts) >= 2) {
            return trim(end($parts));
        }
        
        return $address;
    }
    
    /**
     * Get category name by ID
     */
    protected static function getCategoryName(?int $category_id): string {
        if (!$category_id) return 'General';
        
        $category = get_term($category_id, 'category');
        return $category ? $category->name : 'General';
    }
}