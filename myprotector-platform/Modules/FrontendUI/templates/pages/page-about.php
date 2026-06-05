<?php
/**
 * MyProtector Platform - About Page Template
 * 
 * Self-contained template with custom header/footer
 * Loaded via template_include filter - no theme dependencies
 *
 * @package MyProtector\Modules\FrontendUI
 */

if (!defined('ABSPATH')) exit;

// Get plugin URL for assets
$plugin_url = defined('MYPROTECTOR_URL') ? MYPROTECTOR_URL : plugin_dir_url(__FILE__);

// Get FrontendUI module instance
$frontend_ui = MyProtector\Modules\FrontendUI\FrontendUI::getInstance();

$company_url = defined('MYPROTECTOR_COMPANY_URL') ? MYPROTECTOR_COMPANY_URL : home_url();
$founder_name = defined('MYPROTECTOR_FOUNDER_NAME') ? MYPROTECTOR_FOUNDER_NAME : 'Adam Wyrzycki';
$founder_linkedin = defined('MYPROTECTOR_FOUNDER_LINKEDIN') ? MYPROTECTOR_FOUNDER_LINKEDIN : 'https://linkedin.com/in/adamwyrzycki';
$company_email = defined('MYPROTECTOR_COMPANY_EMAIL') ? MYPROTECTOR_COMPANY_EMAIL : 'contact@myprotector.com';
$stats = $frontend_ui->getMockData('stats');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo esc_url($plugin_url . 'Modules/FrontendUI/assets/css/frontend.css'); ?>?ver=<?php echo MYPROTECTOR_VERSION; ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php include $frontend_ui->getPath('templates/components/header.php'); ?>

<main class="mp-frontend-ui">
    <!-- Page Header -->
    <section class="mp-hero" style="padding: var(--mp-spacing-3xl) 0;">
        <div class="mp-container mp-hero-content">
            <h1>About MyProtector</h1>
            <p class="mp-hero-subtitle">
                Building trust between businesses and customers through transparent 
                verification and authentic reviews. Our mission is to make the 
                marketplace safer for everyone.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-grid" style="max-width: 1000px; margin: 0 auto; align-items: center;">
                <div style="flex: 1;">
                    <h2 class="mp-section-title">Our Mission</h2>
                    <p style="font-size: var(--mp-font-size-lg); color: var(--mp-gray-600); margin-bottom: var(--mp-spacing-lg);">
                        At MyProtector, we believe everyone deserves to make informed decisions 
                        when dealing with businesses. The internet should be a safe place where 
                        consumers can trust the companies they interact with.
                    </p>
                    <p style="color: var(--mp-gray-600);">
                        That's why we created the Traffic Light Trust System – a simple, visual way 
                        to understand how transparent and trustworthy a business is. We verify 
                        multiple criteria so you don't have to spend hours researching.
                    </p>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 80px; margin-bottom: var(--mp-spacing-md);">🎯</div>
                    <h3 style="color: var(--mp-primary);">Trust Through Transparency</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="mp-section mp-section-light">
        <div class="mp-container">
            <div class="mp-stats">
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['total_businesses'] ?? 1250); ?>+</div>
                    <div class="mp-stat-label">Verified Businesses</div>
                </div>
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['total_reviews'] ?? 28400); ?>+</div>
                    <div class="mp-stat-label">Customer Reviews</div>
                </div>
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['total_users'] ?? 18200); ?>+</div>
                    <div class="mp-stat-label">Active Members</div>
                </div>
                <div class="mp-stat-card">
                    <div class="mp-stat-value"><?php echo number_format($stats['trust_score'] ?? 96); ?>%</div>
                    <div class="mp-stat-label">Trust Score</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust System Explained -->
    <section class="mp-section">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">How the Trust System Works</h2>
                <p class="mp-section-subtitle">
                    Our Traffic Light system evaluates businesses across 5 key criteria.
                </p>
            </div>
            
            <div class="mp-grid mp-grid-3" style="max-width: 1000px; margin: 0 auto;">
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">📋</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Insurance Verification</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        We verify that businesses carry appropriate insurance coverage to protect 
                        their customers in case of accidents or damages.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">📜</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Terms & Conditions</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Businesses must have clear, accessible terms of service that outline 
                        their policies, refund procedures, and customer rights.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">🤝</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Promise Pledges</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Verified businesses can pledge specific promises to their customers, 
                        demonstrating commitment to quality service.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">⭐</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Customer Reviews</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Real reviews from verified customers help you understand the actual 
                        experience of dealing with each business.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">✅</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Identity Verification</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        We verify business identity through multiple channels to ensure 
                        they're legitimate and can be held accountable.
                    </p>
                </div>
                
                <div class="mp-card">
                    <div style="width: 60px; height: 60px; background: var(--mp-primary-bg); border-radius: var(--mp-radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--mp-spacing-lg);">
                        <span style="font-size: 28px;">🛡️</span>
                    </div>
                    <h3 style="margin-bottom: var(--mp-spacing-md);">Trust Score</h3>
                    <p style="color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                        Our algorithm calculates a comprehensive trust score based on 
                        all verified criteria, updated in real-time.
                    </p>
                </div>
            </div>
            
            <!-- Traffic Light Trust System Visual -->
            <div style="margin-top: var(--mp-spacing-3xl);">
                <h3 class="mp-section-title" style="margin-bottom: var(--mp-spacing-xl);">Trust Status Levels</h3>
                <div class="mp-grid mp-grid-3" style="max-width: 900px; margin: 0 auto;">
                    <!-- Green - Shopping Safe -->
                    <div class="mp-card" style="border-top: 4px solid var(--mp-green);">
                        <div style="width: 60px; height: 60px; margin: 0 auto var(--mp-spacing-lg);">
                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="32" cy="12" r="7" fill="#059669"/>
                                <path d="M25 11C25 7 28 4 32 4C36 4 39 7 39 11" fill="#059669"/>
                                <path d="M23 13C23 9 27 6 32 6C37 6 41 9 41 13C41 15 40 17 38 18L26 18C24 17 23 15 23 13Z" fill="#059669"/>
                                <path d="M26 19L24 35L22 40H42L40 35L38 19" fill="#059669"/>
                                <path d="M26 21L18 30L16 28" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M38 21L46 30L48 28" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/>
                                <rect x="42" y="26" width="14" height="16" rx="2" fill="#059669"/>
                                <path d="M45 26V22C45 20 47 18 49 18C51 18 53 20 53 22V26" stroke="#059669" stroke-width="2"/>
                                <path d="M28 40L26 56" stroke="#059669" stroke-width="3" stroke-linecap="round"/>
                                <path d="M36 40L38 56" stroke="#059669" stroke-width="3" stroke-linecap="round"/>
                                <ellipse cx="25" cy="58" rx="4" ry="2" fill="#059669"/>
                                <ellipse cx="39" cy="58" rx="4" ry="2" fill="#059669"/>
                            </svg>
                        </div>
                        <h3 style="text-align: center; color: var(--mp-green);">Shopping Safe</h3>
                        <p style="text-align: center; color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                            4-5 criteria met. High transparency.
                        </p>
                    </div>
                    
                    <!-- Amber - Walking Safe -->
                    <div class="mp-card" style="border-top: 4px solid var(--mp-amber);">
                        <div style="width: 60px; height: 60px; margin: 0 auto var(--mp-spacing-lg);">
                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="32" cy="12" r="7" fill="#d97706"/>
                                <path d="M25 10C25 6 28 4 32 4C36 4 39 6 39 10L40 12H24L25 10Z" fill="#d97706"/>
                                <path d="M26 19L24 35L22 40H42L40 35L38 19" fill="#d97706"/>
                                <path d="M26 21L18 30L16 28" stroke="#d97706" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M38 21L46 30L48 28" stroke="#d97706" stroke-width="2.5" stroke-linecap="round"/>
                                <rect x="42" y="26" width="14" height="16" rx="2" fill="#d97706"/>
                                <path d="M45 26V22C45 20 47 18 49 18C51 18 53 20 53 22V26" stroke="#d97706" stroke-width="2"/>
                                <path d="M28 40L26 56" stroke="#d97706" stroke-width="3" stroke-linecap="round"/>
                                <path d="M36 40L38 56" stroke="#d97706" stroke-width="3" stroke-linecap="round"/>
                                <ellipse cx="25" cy="58" rx="4" ry="2" fill="#d97706"/>
                                <ellipse cx="39" cy="58" rx="4" ry="2" fill="#d97706"/>
                            </svg>
                        </div>
                        <h3 style="text-align: center; color: var(--mp-amber);">Walking Safe</h3>
                        <p style="text-align: center; color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                            2-3 criteria met. Normal caution.
                        </p>
                    </div>
                    
                    <!-- Red - Caution -->
                    <div class="mp-card" style="border-top: 4px solid var(--mp-red);">
                        <div style="width: 60px; height: 60px; margin: 0 auto var(--mp-spacing-lg);">
                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="32" cy="12" r="7" fill="#dc2626"/>
                                <path d="M25 10C25 6 28 4 32 4C36 4 39 6 39 10L40 12H24L25 10Z" fill="#dc2626"/>
                                <path d="M26 19L24 35L22 40H42L40 35L38 19" fill="#dc2626"/>
                                <path d="M26 21L18 26" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M38 21L46 26" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M28 40L26 56" stroke="#dc2626" stroke-width="3" stroke-linecap="round"/>
                                <path d="M36 40L38 56" stroke="#dc2626" stroke-width="3" stroke-linecap="round"/>
                                <ellipse cx="25" cy="58" rx="4" ry="2" fill="#dc2626"/>
                                <ellipse cx="39" cy="58" rx="4" ry="2" fill="#dc2626"/>
                            </svg>
                        </div>
                        <h3 style="text-align: center; color: var(--mp-red);">Caution</h3>
                        <p style="text-align: center; color: var(--mp-gray-600); font-size: var(--mp-font-size-sm);">
                            0-1 criteria met. Do research.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="mp-section mp-section-light">
        <div class="mp-container">
            <div class="mp-section-header">
                <h2 class="mp-section-title">Meet Our Founder</h2>
                <p class="mp-section-subtitle">
                    The vision and passion behind MyProtector's mission.
                </p>
            </div>
            
            <div class="mp-grid" style="max-width: 700px; margin: 0 auto;">
                <div class="mp-founder-card" style="box-shadow: var(--mp-shadow-lg);">
                    <div class="mp-founder-photo">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($founder_name); ?>&background=0A1F44&color=fff&size=200" 
                             alt="<?php echo esc_attr($founder_name); ?>">
                    </div>
                    <h3 class="mp-founder-name"><?php echo esc_html($founder_name); ?></h3>
                    <p class="mp-founder-title">Co-Founder</p>
                    <p class="mp-founder-bio">
                        With a passion for consumer protection and business transparency, 
                        <?php echo esc_html($founder_name); ?> founded MyProtector to help people make 
                        informed decisions when dealing with businesses. After seeing countless 
                        cases where consumers were scammed or misled, the Traffic Light Trust 
                        System was created to bring clarity and confidence to online transactions.
                    </p>
                    <p class="mp-founder-bio">
                        "I believe that by making business transparency visible, we can build 
                        a marketplace where trust is the norm, not the exception. Every consumer 
                        deserves to know exactly who they're dealing with."
                    </p>
                    <div class="mp-founder-social">
                        <a href="<?php echo esc_url($founder_linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="mp-cta">
        <div class="mp-container">
            <h2 class="mp-cta-title">Join the Trust Revolution</h2>
            <p class="mp-cta-description">
                Whether you're a consumer looking for trustworthy businesses or a business 
                wanting to demonstrate your commitment to transparency, MyProtector is here for you.
            </p>
            <div class="mp-cta-actions">
                <a href="<?php echo esc_url($company_url); ?>/register" class="mp-btn mp-btn-primary mp-btn-lg">
                    Get Started Free
                </a>
                <a href="mailto:<?php echo esc_attr($company_email); ?>" class="mp-btn mp-btn-secondary mp-btn-lg" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</main>

<?php include $frontend_ui->getPath('templates/components/footer.php'); ?>
<?php wp_footer(); ?>
</body>
</html>