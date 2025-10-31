<?php
/**
 * Admin Settings - Organized in Sections
 *
 * @package Mijn_Werk_Online
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register settings with organized sections
 */
function mwo_register_organized_settings() {
    register_setting( 'mwo_settings', 'mwo_options', 'mwo_sanitize_options' );

    // ============================================
    // SECTION 1: LAYOUT & LOGO
    // ============================================
    add_settings_section(
        'mwo_layout_section',
        '<span class="mwo-section-title">Layout & Logo</span>',
        'mwo_layout_section_callback',
        'mwo-settings'
    );

    add_settings_field( 'mwo_menu_placement', __( 'Menu Plaatsing', 'mwo' ), 'mwo_menu_placement_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_sticky_header', '<span id="mwo-sticky-header-label">' . __( 'Sticky Header', 'mwo' ) . '</span>', 'mwo_sticky_header_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_logo', __( 'Logo', 'mwo' ), 'mwo_logo_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_logo_width', __( 'Logo breedte', 'mwo' ), 'mwo_logo_width_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_show_site_title', __( 'Sitetitel tonen', 'mwo' ), 'mwo_show_site_title_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_show_tagline', __( 'Ondertitel tonen', 'mwo' ), 'mwo_show_tagline_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_content_container_width', __( 'Content Container breedte', 'mwo' ), 'mwo_content_container_width_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_disable_page_titles', __( 'Paginakoppen uitschakelen', 'mwo' ), 'mwo_disable_page_titles_callback', 'mwo-settings', 'mwo_layout_section' );
    add_settings_field( 'mwo_disable_footer_credits', __( 'Footercredits uitschakelen', 'mwo' ), 'mwo_disable_footer_credits_callback', 'mwo-settings', 'mwo_layout_section' );

    // ============================================
    // SECTION 2: TYPOGRAPHY
    // ============================================
    add_settings_section(
        'mwo_typography_section',
        '<span class="mwo-section-title">Typografie</span>',
        'mwo_typography_section_callback',
        'mwo-settings'
    );

    add_settings_field( 'mwo_custom_font', __( 'Custom Font', 'mwo' ), 'mwo_custom_font_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_body_font_size', __( 'Pagina content lettergrootte', 'mwo' ), 'mwo_body_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_heading_font_size', __( 'Pagina koppen lettergrootte', 'mwo' ), 'mwo_heading_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_menu_font_size', __( 'Menu items lettergrootte', 'mwo' ), 'mwo_menu_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_site_title_font_size', __( 'Site titel lettergrootte', 'mwo' ), 'mwo_site_title_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_tagline_font_size', __( 'Ondertitel lettergrootte', 'mwo' ), 'mwo_tagline_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_intro_title_font_size', __( 'Intro titel lettergrootte', 'mwo' ), 'mwo_intro_title_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_intro_tagline_font_size', __( 'Intro ondertitel lettergrootte', 'mwo' ), 'mwo_intro_tagline_font_size_callback', 'mwo-settings', 'mwo_typography_section' );
    add_settings_field( 'mwo_button_font_size', __( 'Knop lettergrootte (introscherm)', 'mwo' ), 'mwo_button_font_size_callback', 'mwo-settings', 'mwo_typography_section' );

    // ============================================
    // SECTION 3: COLORS
    // ============================================
    add_settings_section(
        'mwo_colors_section',
        '<span class="mwo-section-title">Kleuren</span>',
        'mwo_colors_section_callback',
        'mwo-settings'
    );

    add_settings_field( 'mwo_menu_accent_color', __( 'Menu accent kleur', 'mwo' ), 'mwo_menu_accent_color_callback', 'mwo-settings', 'mwo_colors_section' );
    add_settings_field( 'mwo_link_color', __( 'Link kleur', 'mwo' ), 'mwo_link_color_callback', 'mwo-settings', 'mwo_colors_section' );
    add_settings_field( 'mwo_darkmode', __( 'Darkmode', 'mwo' ), 'mwo_darkmode_callback', 'mwo-settings', 'mwo_colors_section' );

    // ============================================
    // SECTION 4: PHOTOGRAPHY & GALLERIES
    // ============================================
    add_settings_section(
        'mwo_photography_section',
        '<span class="mwo-section-title">Fotografie & Galerijen</span>',
        'mwo_photography_section_callback',
        'mwo-settings'
    );

    add_settings_field( 'mwo_enable_masonry', __( 'Masonry layout', 'mwo' ), 'mwo_enable_masonry_callback', 'mwo-settings', 'mwo_photography_section' );
    add_settings_field( 'mwo_lightbox_captions', __( 'Lightbox bijschriften', 'mwo' ), 'mwo_lightbox_captions_callback', 'mwo-settings', 'mwo_photography_section' );
    add_settings_field( 'mwo_content_protection', __( 'Content protectie', 'mwo' ), 'mwo_content_protection_callback', 'mwo-settings', 'mwo_photography_section' );

    // Subsection: Photo Upload Optimization
    add_settings_field( 'mwo_photo_optimization_header', '', 'mwo_photo_optimization_header_callback', 'mwo-settings', 'mwo_photography_section' );
    add_settings_field( 'mwo_auto_resize_images', '<span id="mwo-auto-resize-label">' . __( 'Automatisch foto\'s verkleinen', 'mwo' ) . '</span>', 'mwo_auto_resize_images_callback', 'mwo-settings', 'mwo_photography_section' );
    add_settings_field( 'mwo_max_image_size', '<span id="mwo-max-image-size-label">' . __( 'Maximale foto grootte', 'mwo' ) . '</span>', 'mwo_max_image_size_callback', 'mwo-settings', 'mwo_photography_section' );
    add_settings_field( 'mwo_disable_extra_sizes', __( 'Extra afbeeldingsformaten', 'mwo' ), 'mwo_disable_extra_sizes_callback', 'mwo-settings', 'mwo_photography_section' );
    add_settings_field( 'mwo_optimize_srcset', __( 'Gallery performance', 'mwo' ), 'mwo_optimize_srcset_callback', 'mwo-settings', 'mwo_photography_section' );

    // ============================================
    // SECTION 4: INTRO SCREEN
    // ============================================
    add_settings_section(
        'mwo_intro_section',
        '<span class="mwo-section-title">Intro Scherm <em style="font-weight: normal; color: #666;">(Optioneel)</em></span>',
        'mwo_intro_section_callback',
        'mwo-settings'
    );

    add_settings_field( 'mwo_enable_intro', '<span id="mwo-enable-intro-label">' . __( 'Intro scherm inschakelen', 'mwo' ) . '</span>', 'mwo_enable_intro_callback', 'mwo-settings', 'mwo_intro_section' );
    add_settings_field( 'mwo_intro_images', '<span id="mwo-intro-images-label">' . __( 'Achtergrondafbeeldingen', 'mwo' ) . '</span>', 'mwo_intro_images_callback', 'mwo-settings', 'mwo_intro_section' );
    add_settings_field( 'mwo_intro_button_text', '<span id="mwo-intro-button-text-label">' . __( 'Knoptekst', 'mwo' ) . '</span>', 'mwo_intro_button_text_callback', 'mwo-settings', 'mwo_intro_section' );

    // ============================================
    // SECTION 5: SOCIAL MEDIA
    // ============================================
    add_settings_section(
        'mwo_social_section',
        '<span class="mwo-section-title">Social Media</span>',
        'mwo_social_section_callback',
        'mwo-settings'
    );

    $platforms = mwo_get_social_platforms();
    foreach ( $platforms as $key => $platform ) {
        add_settings_field(
            'mwo_social_' . $key,
            $platform['label'],
            'mwo_social_field_callback',
            'mwo-settings',
            'mwo_social_section',
            array(
                'platform' => $key,
                'data'     => $platform,
            )
        );
    }

    // ============================================
    // SECTION 6: ADVANCED
    // ============================================
    add_settings_section(
        'mwo_advanced_section',
        '<span class="mwo-section-title">Geavanceerd</span>',
        'mwo_advanced_section_callback',
        'mwo-settings'
    );

    add_settings_field( 'mwo_custom_css', __( 'Custom CSS', 'mwo' ), 'mwo_custom_css_callback', 'mwo-settings', 'mwo_advanced_section' );
}
add_action( 'admin_init', 'mwo_register_organized_settings' );

/**
 * Section Callbacks
 */
function mwo_layout_section_callback() {
    echo '<p>' . esc_html__( 'Configureer de algemene layout en het logo van je website.', 'mwo' ) . '</p>';
}

function mwo_colors_section_callback() {
    echo '<p>' . esc_html__( 'Pas de kleuren van het menu, links en andere elementen aan.', 'mwo' ) . '</p>';
}

function mwo_photography_section_callback() {
    echo '<p>' . esc_html__( 'Instellingen voor foto\'s, galerijen en upload optimalisatie.', 'mwo' ) . '</p>';
}

function mwo_intro_section_callback() {
    echo '<p>' . esc_html__( 'Toon een introscherm dat bezoekers zien voordat ze je site betreden. Let op, hiervoor dien je een pagina aan te maken met de naam "Intro" en de template van deze pagina aan te passen naar "Intro Screen".', 'mwo' ) . '</p>';
}

function mwo_typography_section_callback() {
    echo '<p>' . esc_html__( 'Configureer het lettertype en de lettergrootte van verschillende elementen.', 'mwo' ) . '</p>';
}

function mwo_social_section_callback() {
    echo '<p>' . esc_html__( 'Voeg links toe naar je social media profielen.', 'mwo' ) . '</p>';
}

function mwo_advanced_section_callback() {
    echo '<p>' . esc_html__( 'Geavanceerde instellingen.', 'mwo' ) . '</p>';
}

/**
 * Subsection header (no actual field)
 */
function mwo_photo_optimization_header_callback() {
    echo '<h4 style="margin: 20px 0 10px 0; padding: 10px 0; border-top: 1px solid #ddd; color: #666; font-weight: 600;">Foto Upload Optimalisatie</h4>';
    echo '<p class="description" style="margin-top: -5px;">' . esc_html__( 'Automatische optimalisatie bij het uploaden van foto\'s.', 'mwo' ) . '</p>';
}

/**
 * Add custom CSS and JS for tabbed settings page
 */
function mwo_admin_settings_css() {
    $screen = get_current_screen();
    if ( $screen->id !== 'dashboard_page_mwo-settings' ) {
        return;
    }
    ?>
    <style>
        /* Header with submit button */
        .mwo-settings-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .mwo-settings-header h1 {
            margin: 0;
        }

        /* Submit button styling */
        .mwo-submit-top {
            margin: 0;
            padding: 0;
            border: none !important;
        }

        .mwo-submit-top .button-primary {
            height: 36px;
            line-height: 34px;
            padding: 0 20px;
        }

        /* Tab Navigation */
        .mwo-tab-nav {
            margin: 0 0 0 0;
            padding: 0;
            border-bottom: 1px solid #ccd0d4;
            display: flex;
            gap: 0;
            list-style: none;
        }

        .mwo-tab-nav li {
            margin: 0 0 -1px 0;
        }

        .mwo-tab-nav button {
            background: #f0f0f1;
            border: 1px solid #ccd0d4;
            border-bottom: none;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 14px;
            color: #2c3338;
            border-radius: 4px 4px 0 0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mwo-tab-nav button:hover {
            background: #fff;
        }

        .mwo-tab-nav button.active {
            background: #fff;
            border-bottom: 1px solid #fff;
            font-weight: 600;
            color: #000;
        }

        .mwo-tab-nav button .tab-icon {
            font-size: 16px;
        }

        /* Tab Content */
        .mwo-tab-content {
            display: none;
        }

        .mwo-tab-content.active {
            display: block;
        }

        /* Hide section headers inside tabs */
        .mwo-tab-content > h2 {
            display: none;
        }

        /* Form table styling */
        .form-table {
            margin-top: 20px;
        }

        /* Subsection header */
        .form-table tr:has(h4) th,
        .form-table tr:has(h4) td {
            padding: 0;
        }

        /* Better spacing for description */
        .form-table th {
            width: 200px;
        }

        /* Submit button */
        .submit {
            padding-top: 10px;
            border-top: 1px solid #ccd0d4;
            margin-top: 20px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var $wrap = $('.wrap');
        var $h1 = $wrap.find('h1').first();
        var $form = $('form[action="options.php"]');

        // Group sections with their tables
        var tabs = [];
        var currentTab = null;

        $form.children().each(function() {
            if ($(this).is('h2')) {
                if (currentTab) {
                    tabs.push(currentTab);
                }
                currentTab = {
                    title: $(this).html(),
                    id: $(this).attr('id'),
                    elements: [$(this)]
                };
            } else if (currentTab) {
                // Add all elements between h2 tags (including p tags from callbacks and tables)
                if ($(this).is('table.form-table') || $(this).is('p')) {
                    currentTab.elements.push($(this));
                }
            }
        });
        if (currentTab) {
            tabs.push(currentTab);
        }

        // Only create tabs if we have sections
        if (tabs.length > 1) {
            // Clone submit button for header, keep original in form hidden
            var $originalSubmit = $form.find('p.submit');
            var $submitClone = $originalSubmit.clone().addClass('mwo-submit-top');

            // Hide original submit button
            $originalSubmit.hide();

            // Create header wrapper with h1 and cloned submit button
            var $headerWrapper = $('<div class="mwo-settings-header"></div>');
            $headerWrapper.append($h1.clone()).append($submitClone);
            $h1.replaceWith($headerWrapper);

            // Make cloned button trigger the real submit button
            $submitClone.find('input[type="submit"]').on('click', function(e) {
                e.preventDefault();
                $originalSubmit.find('input[type="submit"]').click();
            });

            // Create tab navigation
            var $tabNav = $('<ul class="mwo-tab-nav"></ul>');

            tabs.forEach(function(tab, index) {
                var tabId = 'mwo-tab-' + index;
                var $button = $('<button type="button" data-tab="' + tabId + '">' + tab.title + '</button>');

                if (index === 0) {
                    $button.addClass('active');
                }

                $tabNav.append($('<li></li>').append($button));

                // Wrap tab content
                var $wrapper = $('<div class="mwo-tab-content" id="' + tabId + '"></div>');
                if (index === 0) {
                    $wrapper.addClass('active');
                }

                tab.elements.forEach(function($el) {
                    $wrapper.append($el);
                });

                $form.append($wrapper);
            });

            // Insert tab navigation before form
            $form.before($tabNav);

            // Tab switching
            $tabNav.on('click', 'button', function() {
                var $btn = $(this);
                var tabId = $btn.data('tab');

                $tabNav.find('button').removeClass('active');
                $btn.addClass('active');

                $('.mwo-tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
            });
        }
    });
    </script>
    <?php
}
add_action( 'admin_head', 'mwo_admin_settings_css' );
