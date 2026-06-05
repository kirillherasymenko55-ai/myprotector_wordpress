<?php
/**
 * MyProtector Platform - Business Profile Template
 * 
 * Self-contained template with custom header/footer
 * Shows individual business details and reviews
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Get plugin URL for assets
$plugin_url = defined('MYPROTECTOR_URL') ? MYPROTECTOR_URL : plugin_dir_url(__FILE__);

// Get FrontendUI module instance
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();
$businesses = $frontend_ui->getMockData('businesses');
$reviews = $frontend_ui->getMockData('reviews');
$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();

// Get business slug from query var
$business_slug = get_query_var('mp_slug');

// Find business by slug
$business = null;
foreach ($businesses as $b) {
    if ($b['slug'] === $business_slug) {
        $business = $b;
        break;
    }
}

// If business not found, show 404
if (!$business) {
    wp_die('Business not found. <a href="' . esc_url($company_url) . '/businesses">Browse all businesses</a>');
}

// Get reviews for this business
$business_reviews = [];
foreach ($reviews as $review) {
    if ($review['business_id'] == $business['id']) {
        $business_reviews[] = $review;
    }
}

// Trust status colors
$trust_colors = [
    'green' => 'var(--mp-green)',
    'amber' => 'var(--mp-amber)',
    'red' => 'var(--mp-red)',
];
$trust_color = $trust_colors[$business['trust_status']] ?? 'var(--mp-gray-500)';

$trust_labels = [
    'green' => 'Shopping Safe',
    'amber' => 'Walking Safe',
    'red' => 'Caution',
];
$trust_label = $trust_labels[$business['trust_status']] ?? 'Unknown';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($business['name']); ?> - <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo esc_url($plugin_url . 'Modules/FrontendUI/assets/css/frontend.css'); ?>?ver=<?php echo MYPROTECTOR_VERSION; ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php include $frontend_ui->getPath('templates/components/header.php'); ?>

<main class="mp-frontend-ui">
    <!-- Business Profile Header -->
    <section class="mp-business-profile-header" style="background: linear-gradient(135deg, var(--mp-primary) 0%, var(--mp-primary-light) 100%); padding: var(--mp-spacing-3xl) 0; color: white;">
        <div class="mp-container">
            <div style="display: flex; align-items: center; gap: var(--mp-spacing-xl); flex-wrap: wrap;">
                <!-- Business Logo -->
                <div class="mp-business-profile-logo" style="width: 100px; height: 100px; border-radius: var(--mp-radius-xl); background: white; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if (!empty($business['logo'])): ?>
                        <img src="<?php echo esc_url($business['logo']); ?>" alt="<?php echo esc_attr($business['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span style="font-size: 32px; font-weight: 700; color: var(--mp-primary);"><?php echo esc_html(substr($business['name'], 0, 2)); ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Business Info -->
                <div style="flex: 1; min-width: 250px;">
                    <h1 style="font-size: var(--mp-font-size-2xl); font-weight: 700; margin: 0 0 var(--mp-spacing-sm); color: white;"><?php echo esc_html($business['name']); ?></h1>
                    <p style="color: rgba(255,255,255,0.8); margin: 0 0 var(--mp-spacing-md);">
                        <span style="background: var(--mp-gray-200); color: var(--mp-gray-700); padding: 2px 8px; border-radius: 4px; font-size: 13px;"><?php echo esc_html($business['category']); ?></span>
                        <span style="margin-left: 8px;">📍 <?php echo esc_html($business['location']); ?></span>
                    </p>
                    
                    <!-- Rating & Trust Status -->
                    <div style="display: flex; align-items: center; gap: var(--mp-spacing-lg); flex-wrap: wrap;">
                        <div class="mp-rating-display" style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 24px; font-weight: 700; color: #fbbf24;"><?php echo number_format($business['rating'], 1); ?></span>
                            <div class="mp-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="mp-star <?php echo $i <= round($business['rating']) ? 'mp-star-filled' : 'mp-star-empty'; ?>" style="color: #fbbf24;">★</span>
                                <?php endfor; ?>
                            </div>
                            <span style="color: rgba(255,255,255,0.8); font-size: 14px;">(<?php echo number_format($business['total_reviews']); ?> reviews)</span>
                        </div>
                        
                        <div style="background: <?php echo esc_attr($trust_color); ?>; padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                            <?php echo esc_html($trust_label); ?>
                        </div>
                        
                        <div style="color: rgba(255,255,255,0.8); font-size: 14px;">
                            Trust Score: <strong style="color: white;"><?php echo number_format($business['trust_score']); ?>%</strong>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div style="display: flex; gap: var(--mp-spacing-sm);">
                    <?php if (!empty($business['website'])): ?>
                        <a href="<?php echo esc_url($business['website']); ?>" target="_blank" class="mp-btn mp-btn-outline" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                            Visit Website
                        </a>
                    <?php endif; ?>
                    <?php if (!$business['claimed']): ?>
                        <a href="<?php echo esc_url($company_url); ?>/register?type=business" class="mp-btn mp-btn-primary">
                            Claim This Business
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Business Content -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-grid" style="grid-template-columns: 2fr 1fr; gap: var(--mp-spacing-2xl);">
                <!-- Main Content -->
                <div>
                    <!-- Description -->
                    <div class="mp-card" style="margin-bottom: var(--mp-spacing-xl);">
                        <h3 style="margin-bottom: var(--mp-spacing-md);">About</h3>
                        <p style="color: var(--mp-gray-600); line-height: 1.7;"><?php echo esc_html($business['description']); ?></p>
                    </div>
                    
                    <!-- Reviews Section -->
                    <div class="mp-section-header" style="margin-bottom: var(--mp-spacing-lg);">
                        <h3 style="font-size: var(--mp-font-size-xl); font-weight: 600;">Reviews (<?php echo count($business_reviews); ?>)</h3>
                    </div>
                    
                    <?php if (empty($business_reviews)): ?>
                        <div class="mp-card" style="text-align: center; padding: var(--mp-spacing-2xl);">
                            <p style="color: var(--mp-gray-500);">No reviews yet. Be the first to review this business!</p>
                            <a href="#" class="mp-btn mp-btn-primary" style="margin-top: var(--mp-spacing-md);">Write a Review</a>
                        </div>
                    <?php else: ?>
                        <div class="mp-reviews-list">
                            <?php foreach ($business_reviews as $review): ?>
                                <div class="mp-review-card" style="margin-bottom: var(--mp-spacing-md);">
                                    <div class="mp-review-header">
                                        <div class="mp-review-avatar"><?php echo esc_html(substr($review['reviewer'], 0, 1)); ?></div>
                                        <div class="mp-review-meta">
                                            <h4 class="mp-review-author"><?php echo esc_html($review['reviewer']); ?></h4>
                                            <span class="mp-review-date"><?php echo esc_html($review['date']); ?></span>
                                        </div>
                                        <div class="mp-review-rating">
                                            <div class="mp-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="mp-star <?php echo $i <= $review['rating'] ? 'mp-star-filled' : 'mp-star-empty'; ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="mp-review-title"><?php echo esc_html($review['title']); ?></h4>
                                    <p class="mp-review-content"><?php echo esc_html($review['content']); ?></p>
                                    <?php if ($review['verified']): ?>
                                        <span class="mp-review-verified">✓ Verified Customer</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div>
                    <!-- Trust Details -->
                    <div class="mp-card" style="margin-bottom: var(--mp-spacing-lg);">
                        <h4 style="margin-bottom: var(--mp-spacing-md);">Trust Details</h4>
                        
                        <div style="margin-bottom: var(--mp-spacing-md);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: var(--mp-gray-600);">Trust Score</span>
                                <span style="font-weight: 600; color: <?php echo esc_attr($trust_color); ?>;"><?php echo number_format($business['trust_score']); ?>%</span>
                            </div>
                            <div style="background: var(--mp-gray-100); height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="background: <?php echo esc_attr($trust_color); ?>; height: 100%; width: <?php echo esc_attr($business['trust_score']); ?>%;"></div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: var(--mp-spacing-md);">
                            <span style="color: var(--mp-gray-500); font-size: 13px;">Business Status</span>
                            <div style="margin-top: 4px;">
                                <?php if ($business['claimed']): ?>
                                    <span style="color: var(--mp-green); font-size: 14px;">✓ Claimed Business</span>
                                <?php else: ?>
                                    <span style="color: var(--mp-gray-500); font-size: 14px;">Unclaimed</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($business['established'])): ?>
                            <div style="margin-bottom: var(--mp-spacing-md);">
                                <span style="color: var(--mp-gray-500); font-size: 13px;">Established</span>
                                <div style="margin-top: 4px; font-weight: 500;"><?php echo esc_html($business['established']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Verification Status -->
                    <div class="mp-card" style="margin-bottom: var(--mp-spacing-lg);">
                        <h4 style="margin-bottom: var(--mp-spacing-md);">Verification</h4>
                        
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid var(--mp-gray-100);">
                                <?php if (!empty($business['insurance_name'])): ?>
                                    <span style="color: var(--mp-green);">✓</span>
                                    <span style="font-size: 14px;">Insurance Verified</span>
                                <?php else: ?>
                                    <span style="color: var(--mp-red);">✗</span>
                                    <span style="font-size: 14px; color: var(--mp-gray-500);">Insurance Not Verified</span>
                                <?php endif; ?>
                            </li>
                            <li style="display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid var(--mp-gray-100);">
                                <?php if (!empty($business['terms_url'])): ?>
                                    <span style="color: var(--mp-green);">✓</span>
                                    <span style="font-size: 14px;">Terms Posted</span>
                                <?php else: ?>
                                    <span style="color: var(--mp-red);">✗</span>
                                    <span style="font-size: 14px; color: var(--mp-gray-500);">No Terms Posted</span>
                                <?php endif; ?>
                            </li>
                            <li style="display: flex; align-items: center; gap: 8px; padding: 8px 0;">
                                <?php if (!empty($business['promise_url'])): ?>
                                    <span style="color: var(--mp-green);">✓</span>
                                    <span style="font-size: 14px;">Promise Pledge</span>
                                <?php else: ?>
                                    <span style="color: var(--mp-amber);">⚠</span>
                                    <span style="font-size: 14px; color: var(--mp-gray-500);">No Promise Pledge</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Back Link -->
                    <a href="<?php echo esc_url($company_url); ?>/businesses" class="mp-btn mp-btn-outline" style="width: 100%; text-align: center;">
                        ← Back to Businesses
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include $frontend_ui->getPath('templates/components/footer.php'); ?>
<?php wp_footer(); ?>
</body>
</html>