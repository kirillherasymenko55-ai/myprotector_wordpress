<?php
/**
 * MyProtector Platform - Data Seeder
 * 
 * Seeds the database with sample data for testing
 * 
 * @package MyProtector\Core
 */

namespace MyProtector\Core;

if (!defined('ABSPATH')) exit;

class DataSeeder {
    
    /**
     * Run all seeders
     */
    public static function seed(): void {
        $instance = new self();
        $instance->seedUsers();
        $instance->seedBusinesses();
        $instance->seedReviews();
        $instance->seedCategories();
        $instance->seedTrustSignals();
        
        // Update stats
        $instance->updateCompanyStats();
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[MyProtector] DataSeeder: Seed completed successfully');
        }
    }
    
    /**
     * Seed 3 test users
     */
    protected function seedUsers(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'mp_user_trust_levels';
        
        $users = [
            [
                'email' => 'michael.chen@example.com',
                'username' => 'michaelchen',
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'trust_level' => 'premium'
            ],
            [
                'email' => 'sarah.johnson@example.com',
                'username' => 'sarahjohnson',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'trust_level' => 'verified'
            ],
            [
                'email' => 'david.park@example.com',
                'username' => 'davidpark',
                'first_name' => 'David',
                'last_name' => 'Park',
                'trust_level' => 'verified'
            ],
        ];
        
        foreach ($users as $user_data) {
            // Check if user already exists
            $existing = get_user_by('email', $user_data['email']);
            
            if (!$existing) {
                $user_id = wp_create_user(
                    $user_data['username'],
                    'demo123456',
                    $user_data['email']
                );
                
                if (!is_wp_error($user_id)) {
                    wp_update_user([
                        'ID' => $user_id,
                        'first_name' => $user_data['first_name'],
                        'last_name' => $user_data['last_name'],
                        'display_name' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                        'role' => 'subscriber'
                    ]);
                    
                    // Add trust level
                    $wpdb->insert($table, [
                        'user_id' => $user_id,
                        'trust_level' => $user_data['trust_level'],
                        'verified_at' => current_time('mysql'),
                        'verification_method' => 'email_verified'
                    ]);
                }
            } else {
                // Ensure trust level exists for existing user
                $existing_level = $wpdb->get_var(
                    $wpdb->prepare("SELECT trust_id FROM $table WHERE user_id = %d", $existing->ID)
                );
                
                if (!$existing_level) {
                    $wpdb->insert($table, [
                        'user_id' => $existing->ID,
                        'trust_level' => $user_data['trust_level'],
                        'verified_at' => current_time('mysql'),
                        'verification_method' => 'email_verified'
                    ]);
                }
            }
        }
    }
    
    /**
     * Seed 10 businesses
     */
    protected function seedBusinesses(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'mp_companies';
        
        $businesses = [
            [
                'name' => 'TechVentures Solutions',
                'slug' => 'techventures-solutions',
                'description' => 'Enterprise software development and cloud infrastructure solutions for modern businesses.',
                'website' => 'https://techventures.example.com',
                'address' => '123 Innovation Drive, San Francisco, CA 94102',
                'phone' => '+1 (415) 555-0100',
                'email' => 'contact@techventures.example.com',
                'category' => 1,
                'insurance_name' => 'TechShield Insurance',
                'insurance_url' => 'https://techventures.example.com/insurance',
                'terms_url' => 'https://techventures.example.com/terms',
                'promise_page_url' => 'https://techventures.example.com/promise',
                'promise_page_title' => 'Our Customer Promise',
                'status' => 'approved',
                'trust_score' => 1.00,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'GreenLeaf Landscaping',
                'slug' => 'greenleaf-landscaping',
                'description' => 'Professional landscaping and garden design services for residential and commercial properties.',
                'website' => 'https://greenleaf.example.com',
                'address' => '456 Garden Lane, Portland, OR 97201',
                'phone' => '+1 (503) 555-0200',
                'email' => 'hello@greenleaf.example.com',
                'category' => 2,
                'insurance_name' => 'GreenGuard Insurance',
                'insurance_url' => 'https://greenleaf.example.com/insurance',
                'terms_url' => 'https://greenleaf.example.com/terms',
                'promise_page_url' => '',
                'promise_page_title' => '',
                'status' => 'approved',
                'trust_score' => 0.67,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'Metro Auto Repair',
                'slug' => 'metro-auto-repair',
                'description' => 'Full-service auto repair shop providing quality mechanical services with certified technicians.',
                'website' => 'https://metroauto.example.com',
                'address' => '789 Motor Way, Chicago, IL 60601',
                'phone' => '+1 (312) 555-0300',
                'email' => 'service@metroauto.example.com',
                'category' => 3,
                'insurance_name' => '',
                'insurance_url' => '',
                'terms_url' => '',
                'promise_page_url' => '',
                'promise_page_title' => '',
                'status' => 'approved',
                'trust_score' => 0.33,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'SafeHome Security',
                'slug' => 'safehome-security',
                'description' => 'Advanced home security systems and monitoring services for peace of mind.',
                'website' => 'https://safehome.example.com',
                'address' => '321 Safety Boulevard, Austin, TX 78701',
                'phone' => '+1 (512) 555-0400',
                'email' => 'info@safehome.example.com',
                'category' => 4,
                'insurance_name' => 'SafeGuard Insurance',
                'insurance_url' => 'https://safehome.example.com/insurance',
                'terms_url' => 'https://safehome.example.com/terms',
                'promise_page_url' => 'https://safehome.example.com/promise',
                'promise_page_title' => 'Our Service Guarantee',
                'status' => 'approved',
                'trust_score' => 1.00,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'QuickFix Plumbing',
                'slug' => 'quickfix-plumbing',
                'description' => '24/7 emergency plumbing services with licensed professionals for residential and commercial.',
                'website' => 'https://quickfix.example.com',
                'address' => '654 Pipe Lane, Seattle, WA 98101',
                'phone' => '+1 (206) 555-0500',
                'email' => 'help@quickfix.example.com',
                'category' => 5,
                'insurance_name' => 'PlumberShield Insurance',
                'insurance_url' => 'https://quickfix.example.com/insurance',
                'terms_url' => 'https://quickfix.example.com/terms',
                'promise_page_url' => '',
                'promise_page_title' => '',
                'status' => 'approved',
                'trust_score' => 0.67,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'EduLearn Academy',
                'slug' => 'edulearn-academy',
                'description' => 'Online learning platform offering courses in technology, business, and creative skills.',
                'website' => 'https://edulearn.example.com',
                'address' => '987 Knowledge Street, Boston, MA 02101',
                'phone' => '+1 (617) 555-0600',
                'email' => 'admissions@edulearn.example.com',
                'category' => 6,
                'insurance_name' => 'EduSecure Insurance',
                'insurance_url' => 'https://edulearn.example.com/insurance',
                'terms_url' => 'https://edulearn.example.com/terms',
                'promise_page_url' => 'https://edulearn.example.com/promise',
                'promise_page_title' => 'Our Learning Promise',
                'status' => 'approved',
                'trust_score' => 1.00,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'FitLife Gym',
                'slug' => 'fitlife-gym',
                'description' => 'State-of-the-art fitness center with personal training and group classes.',
                'website' => 'https://fitlife.example.com',
                'address' => '159 Wellness Way, Denver, CO 80201',
                'phone' => '+1 (303) 555-0700',
                'email' => 'memberships@fitlife.example.com',
                'category' => 7,
                'insurance_name' => '',
                'insurance_url' => '',
                'terms_url' => 'https://fitlife.example.com/terms',
                'promise_page_url' => '',
                'promise_page_title' => '',
                'status' => 'approved',
                'trust_score' => 0.67,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'PetCare Plus',
                'slug' => 'petcare-plus',
                'description' => 'Comprehensive veterinary services and pet care products for all your furry friends.',
                'website' => 'https://petcare.example.com',
                'address' => '753 Animal Avenue, Miami, FL 33101',
                'phone' => '+1 (305) 555-0800',
                'email' => 'care@petcare.example.com',
                'category' => 8,
                'insurance_name' => 'PetShield Insurance',
                'insurance_url' => 'https://petcare.example.com/insurance',
                'terms_url' => 'https://petcare.example.com/terms',
                'promise_page_url' => 'https://petcare.example.com/promise',
                'promise_page_title' => 'Our Pet Promise',
                'status' => 'approved',
                'trust_score' => 1.00,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'ChefMaster Catering',
                'slug' => 'chefmaster-catering',
                'description' => 'Professional catering services for events, weddings, and corporate gatherings.',
                'website' => 'https://chefmaster.example.com',
                'address' => '246 Culinary Court, Los Angeles, CA 90001',
                'phone' => '+1 (213) 555-0900',
                'email' => 'events@chefmaster.example.com',
                'category' => 9,
                'insurance_name' => 'CaterSafe Insurance',
                'insurance_url' => 'https://chefmaster.example.com/insurance',
                'terms_url' => 'https://chefmaster.example.com/terms',
                'promise_page_url' => '',
                'promise_page_title' => '',
                'status' => 'approved',
                'trust_score' => 0.67,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
            [
                'name' => 'TechFix Electronics',
                'slug' => 'techfix-electronics',
                'description' => 'Expert electronics repair and tech support for computers, phones, and gadgets.',
                'website' => 'https://techfix.example.com',
                'address' => '864 Circuit Road, Phoenix, AZ 85001',
                'phone' => '+1 (602) 555-1000',
                'email' => 'support@techfix.example.com',
                'category' => 1,
                'insurance_name' => '',
                'insurance_url' => '',
                'terms_url' => '',
                'promise_page_url' => '',
                'promise_page_title' => '',
                'status' => 'approved',
                'trust_score' => 0.00,
                'total_reviews' => 0,
                'avg_rating' => 0.0
            ],
        ];
        
        // Get admin user for ownership
        $admin = get_user_by('email', get_option('admin_email'));
        $user_id = $admin ? $admin->ID : 1;
        
        foreach ($businesses as $business) {
            // Check if business already exists
            $existing = $wpdb->get_var(
                $wpdb->prepare("SELECT company_id FROM $table WHERE company_slug = %s", $business['slug'])
            );
            
            if (!$existing) {
                $wpdb->insert($table, [
                    'user_id' => $user_id,
                    'company_name' => $business['name'],
                    'company_slug' => $business['slug'],
                    'company_description' => $business['description'],
                    'company_website' => $business['website'],
                    'company_address' => $business['address'],
                    'company_phone' => $business['phone'],
                    'company_email' => $business['email'],
                    'company_category' => $business['category'],
                    'insurance_name' => $business['insurance_name'],
                    'insurance_url' => $business['insurance_url'],
                    'terms_url' => $business['terms_url'],
                    'promise_page_url' => $business['promise_page_url'],
                    'promise_page_title' => $business['promise_page_title'],
                    'status' => $business['status'],
                    'trust_score' => $business['trust_score'],
                    'total_reviews' => $business['total_reviews'],
                    'avg_rating' => $business['avg_rating'],
                    'approved_by' => $user_id,
                    'approved_at' => current_time('mysql'),
                    'created_at' => current_time('mysql'),
                ]);
            }
        }
    }
    
    /**
     * Seed 15 reviews (5 per user, distributed across businesses)
     */
    protected function seedReviews(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'mp_reviews';
        
        // Get users
        $users = [
            get_user_by('email', 'michael.chen@example.com'),
            get_user_by('email', 'sarah.johnson@example.com'),
            get_user_by('email', 'david.park@example.com'),
        ];
        
        // Get businesses
        $businesses = $wpdb->get_results("SELECT company_id, company_name FROM {$wpdb->prefix}mp_companies");
        
        if (empty($businesses)) return;
        
        $reviews_templates = [
            [
                'title' => 'Exceptional service and professional team',
                'content' => 'I was thoroughly impressed with the quality of service provided. The team was professional, punctual, and went above and beyond my expectations. Highly recommended for anyone looking for reliable service.',
                'rating' => 5
            ],
            [
                'title' => 'Great experience overall',
                'content' => 'Had a great experience working with this business. They were responsive to my needs and delivered on their promises. Would definitely use their services again.',
                'rating' => 4
            ],
            [
                'title' => 'Good quality work',
                'content' => 'The work was completed to a high standard. There were a few minor issues along the way, but the team was quick to address them. Overall satisfied with the results.',
                'rating' => 4
            ],
            [
                'title' => 'Exceeded expectations',
                'content' => 'From start to finish, this was an excellent experience. The attention to detail and customer service was outstanding. They truly care about their clients.',
                'rating' => 5
            ],
            [
                'title' => 'Reliable and trustworthy',
                'content' => 'I felt confident throughout the entire process knowing I was in good hands. They were transparent about costs and timelines. Will definitely recommend to friends and family.',
                'rating' => 5
            ],
        ];
        
        $review_index = 0;
        foreach ($users as $user) {
            if (!$user) continue;
            
            // Create 5 reviews for each user
            for ($i = 0; $i < 5; $i++) {
                $business = $businesses[$review_index % count($businesses)];
                $template = $reviews_templates[$i];
                
                // Check if review already exists
                $existing = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT review_id FROM $table WHERE company_id = %d AND user_id = %d AND review_title = %s",
                        $business->company_id,
                        $user->ID,
                        $template['title']
                    )
                );
                
                if (!$existing) {
                    $dates = [
                        date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                        date('Y-m-d H:i:s', strtotime('-' . rand(31, 60) . ' days')),
                        date('Y-m-d H:i:s', strtotime('-' . rand(61, 90) . ' days')),
                    ];
                    
                    $wpdb->insert($table, [
                        'company_id' => $business->company_id,
                        'user_id' => $user->ID,
                        'review_title' => $template['title'],
                        'review_content' => $template['content'],
                        'review_rating' => $template['rating'],
                        'review_status' => 'approved',
                        'trust_level' => 'verified',
                        'is_published' => 1,
                        'published_at' => $dates[array_rand($dates)],
                        'helpful_count' => rand(0, 50),
                        'created_at' => current_time('mysql'),
                    ]);
                }
                
                $review_index++;
            }
        }
    }
    
    /**
     * Seed categories
     */
    protected function seedCategories(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'terms';
        $tax_table = $wpdb->prefix . 'term_taxonomy';
        
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Software, IT, and tech services'],
            ['name' => 'Home Services', 'slug' => 'home-services', 'description' => 'Home improvement and maintenance'],
            ['name' => 'Automotive', 'slug' => 'automotive', 'description' => 'Auto repair and vehicle services'],
            ['name' => 'Security', 'slug' => 'security', 'description' => 'Security systems and monitoring'],
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'description' => 'Plumbing and water services'],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Schools and training'],
            ['name' => 'Fitness', 'slug' => 'fitness', 'description' => 'Gyms and fitness centers'],
            ['name' => 'Pets', 'slug' => 'pets', 'description' => 'Pet care and veterinary'],
            ['name' => 'Food & Catering', 'slug' => 'food-catering', 'description' => 'Restaurants and catering'],
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronics repair and sales'],
        ];
        
        // Use WordPress categories taxonomy
        foreach ($categories as $cat) {
            $existing = get_term_by('slug', $cat['slug'], 'category');
            
            if (!$existing) {
                wp_insert_term($cat['name'], 'category', [
                    'slug' => $cat['slug'],
                    'description' => $cat['description'],
                ]);
            }
        }
    }
    
    /**
     * Seed trust signals for all businesses
     */
    protected function seedTrustSignals(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'mp_trust_signals';
        
        $businesses = $wpdb->get_results("SELECT company_id, trust_score FROM {$wpdb->prefix}mp_companies");
        
        foreach ($businesses as $business) {
            // Determine status based on trust score
            $status = 'red';
            if ($business->trust_score >= 0.67) {
                $status = 'amber';
            }
            if ($business->trust_score >= 1.00) {
                $status = 'green';
            }
            
            // Check if trust signal exists
            $existing = $wpdb->get_var(
                $wpdb->prepare("SELECT signal_id FROM $table WHERE company_id = %d", $business->company_id)
            );
            
            if (!$existing) {
                $wpdb->insert($table, [
                    'company_id' => $business->company_id,
                    'status' => $status,
                    'calculated_status' => $status,
                    'requirements' => json_encode([
                        'has_insurance' => $business->trust_score >= 0.67,
                        'has_terms' => $business->trust_score >= 0.67,
                        'has_promise' => $business->trust_score >= 1.00,
                    ]),
                    'created_at' => current_time('mysql'),
                ]);
            }
        }
    }
    
    /**
     * Update company stats (total_reviews, avg_rating)
     */
    protected function updateCompanyStats(): void {
        global $wpdb;
        $companies = $wpdb->get_results("SELECT company_id FROM {$wpdb->prefix}mp_companies");
        
        foreach ($companies as $company) {
            // Get review stats
            $stats = $wpdb->get_row($wpdb->prepare(
                "SELECT COUNT(*) as total, AVG(review_rating) as avg_rating 
                 FROM {$wpdb->prefix}mp_reviews 
                 WHERE company_id = %d AND is_published = 1",
                $company->company_id
            ));
            
            // Update company
            $wpdb->update(
                $wpdb->prefix . 'mp_companies',
                [
                    'total_reviews' => $stats->total ?: 0,
                    'avg_rating' => $stats->avg_rating ?: 0.0,
                ],
                ['company_id' => $company->company_id]
            );
        }
    }
}

// Run seeder if called directly
if (isset($_GET['mp_seed_data']) || (defined('DOING_AJAX') && DOING_AJAX)) {
    // Only allow in admin or with nonce
    if (is_admin() || wp_verify_nonce($_GET['_wpnonce'] ?? '', 'mp_seed_data')) {
        DataSeeder::seed();
    }
}