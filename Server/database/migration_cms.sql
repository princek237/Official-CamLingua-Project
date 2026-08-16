-- ============================================================================
-- CamLingua CMS Content Migration
-- Adds content-management keys to the settings table so the admin can edit
-- website content from the dashboard and changes reflect on the frontend.
-- Run this against the existing `camlingua` database (does NOT drop tables).
-- ============================================================================

USE `camlingua`;

-- ── Site / Global ─────────────────────────────────────────────────────────────
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_tagline',      'Translate. Connect. Preserve Cameroon\'s Languages.', 'Short tagline shown in the footer and meta'),
('contact_phone',     '+237 6 12 34 56 78',                                  'Phone number shown on the contact page'),
('contact_location',  'Buea, Cameroon',                                       'Physical location shown on the contact page'),
('social_github',     '',                                                      'GitHub profile URL'),
('social_twitter',    '',                                                      'Twitter/X profile URL'),
('social_linkedin',   '',                                                      'LinkedIn profile URL');

-- ── Homepage Hero ─────────────────────────────────────────────────────────────
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('home_hero_badge',   'Cameroonian Language Translation System',              'Small badge text above the hero headline'),
('home_hero_title',   'Translate. Connect.\nPreserve Cameroon\'s\nLanguages.','Main hero headline (use \n for line breaks)'),
('home_hero_desc',    'CamLingua helps you translate between Cameroonian languages and the world. Fast, accurate and easy to use.', 'Hero subtitle paragraph'),
('home_hero_btn1',    'Start Translating',                                    'Primary CTA button label'),
('home_hero_btn2',    'Explore Languages',                                    'Secondary CTA button label'),
('home_feat1_title',  'AI-Powered',                                           'Feature card 1 title'),
('home_feat1_desc',   'Accurate translations powered by advanced AI models.', 'Feature card 1 description'),
('home_feat2_title',  'Local Languages',                                      'Feature card 2 title'),
('home_feat2_desc',   'Support for major Cameroonian languages and dialects.','Feature card 2 description'),
('home_feat3_title',  'Secure & Private',                                     'Feature card 3 title'),
('home_feat3_desc',   'Your data is encrypted and your privacy is protected.','Feature card 3 description'),
('home_feat4_title',  'History & Sync',                                       'Feature card 4 title'),
('home_feat4_desc',   'Access your past translations anytime, anywhere.',     'Feature card 4 description');

-- ── About Page ────────────────────────────────────────────────────────────────
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('about_hero_title',    'Building bridges through language.',                  'About page hero headline'),
('about_hero_desc',     'CamLingua is a Cameroonian language translation system built to break language barriers and promote access to linguistic heritage for our cultures.', 'About page hero subtitle'),
('about_story_p1',      'CamLingua was born from a simple observation: millions of Cameroonians speak rich, vibrant languages — Ewondo, Bassa, Duala, Bamileke, Fulfulde — yet digital tools barely recognize their existence.', 'About story paragraph 1'),
('about_story_p2',      'We started as a small academic project determined to change that. By combining the power of Meta\'s NLLB-200 AI model with a community-driven dictionary, we created a system that not only translates words but carries the culture within them.', 'About story paragraph 2'),
('about_story_p3',      'Today, CamLingua supports major Cameroonian languages and dialects. We believe language is identity — and every word preserved is a story kept alive.', 'About story paragraph 3'),
('about_mission_title', 'Our Mission',                                         'Mission section title'),
('about_mission_text',  'To break language barriers and make Cameroonian languages accessible to everyone through technology.',  'Mission text'),
('about_vision_title',  'Our Vision',                                          'Vision section title'),
('about_vision_text',   'A world where every Cameroonian language is preserved, accessible, and celebrated in the digital age.', 'Vision text'),
('about_stats_langs',   '20+',                                                 'Languages supported stat'),
('about_stats_trans',   '50K+',                                                'Translations completed stat'),
('about_stats_users',   '10K+',                                                'Happy users stat'),
('about_cta_title',     'Ready to bridge the gap?',                            'About page CTA heading'),
('about_cta_desc',      'Start translating today or contribute to our community dictionary. Every word you add helps preserve a language.', 'About page CTA sub-text');

-- ── Pricing / Subscription Page ───────────────────────────────────────────────
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('pricing_hero_badge',  'Simple, Transparent Pricing',                         'Pricing page badge text'),
('pricing_hero_title',  'Translate without limits',                            'Pricing page hero headline'),
('pricing_hero_desc',   'Choose the plan that fits your needs. Upgrade or downgrade anytime — no hidden fees.', 'Pricing page hero subtitle'),
('pricing_cta_title',   'Start preserving Cameroon\'s languages today',        'Pricing page bottom CTA heading'),
('pricing_cta_desc',    'Join over 50,000 users translating across 20+ Cameroonian languages.', 'Pricing page CTA subtext'),
('pricing_faq_1_q',     'Can I change my plan at any time?',                   'FAQ item 1 question'),
('pricing_faq_1_a',     'Yes! You can upgrade or downgrade at any time. Changes take effect immediately for upgrades, and at the end of your billing cycle for downgrades.', 'FAQ item 1 answer'),
('pricing_faq_2_q',     'Is there a free trial?',                              'FAQ item 2 question'),
('pricing_faq_2_a',     'We offer a 7-day free trial for both Pro and Premium plans. No credit card required.', 'FAQ item 2 answer'),
('pricing_faq_3_q',     'Can I use Mobile Money to pay?',                      'FAQ item 3 question'),
('pricing_faq_3_a',     'Absolutely. CamLingua fully supports MTN Mobile Money and Orange Money.', 'FAQ item 3 answer');

-- ── Contact Page ──────────────────────────────────────────────────────────────
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('contact_response_time', 'We typically reply within 24 hours', 'Response time message shown on contact page'),
('contact_hero_title',    'Get in touch',                        'Contact page hero heading'),
('contact_hero_desc',     'Have a question, suggestion, or just want to say hello? We\'d love to hear from you.', 'Contact page hero subtitle');

-- ============================================================================
-- END OF CMS MIGRATION
-- ============================================================================
