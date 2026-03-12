<?php

namespace NomadTaxCalc\Theme\Classes\MegaMenu;

use NomadTaxCalc\Theme\Traits\Singleton;

class MegaMenuSetup
{
    use Singleton;

    protected function __construct()
    {
        $this->setup_hooks();
    }

    protected function setup_hooks()
    {
        // Enable description field in menu items
        add_filter('walker_nav_menu_start_el', [$this, 'menu_item_description_placeholder'], 10, 4);

        // Render the nav bar via shortcode and body open hook
        add_action('smh_render_nav', [$this, 'render_nav']);
        add_shortcode('smh_nav', [$this, 'render_nav']);

        // Register menu location
        add_action('init', [$this, 'register_menus']);

        // Disable Blocksy's default header and inject ours
        add_filter('blocksy:header:is-enabled', '__return_false');
        add_action('wp_body_open', [$this, 'render_nav']);
    }

    public function menu_item_description_placeholder($item_output, $item, $depth, $args)
    {
        // Description is already stored in the database, we just hook here to ensure 
        // WordPress shows the field in Appearance -> Menus if it matters for some themes,
        // although usually it's controlled via Screen Options.
        // Returning $item_output unmodified as the Walker handles the actual output natively.
        return $item_output;
    }

    public function render_nav()
    {
?>
        <nav class="smh-nav" id="smh-nav" role="navigation" aria-label="Main Navigation">
            <div class="smh-nav__inner">

                <!-- LOGO -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="smh-nav__logo" aria-label="<?php bloginfo('name'); ?> Home">
                    <?php
                    // Use your site logo if set, else show site name with icon
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <span><?php bloginfo('name'); ?></span>
                    <?php } ?>
                </a>

                <!-- MAIN MENU — uses our Walker -->
                <?php
                // Capture CTA HTML to place inside the UL
                ob_start();
                $cta_url   = get_theme_mod('smh_cta_url', 'https://nomadtaxcalc.com/tax-calculator');
                $cta_label = get_theme_mod('smh_cta_label', 'Calculate Now');
                if ($cta_label) :
                ?>
                    <li class="smh-nav__item smh-nav__item--cta">
                        <a href="<?php echo esc_url($cta_url); ?>" class="smh-nav__cta">
                            <?php echo esc_html($cta_label); ?>
                        </a>
                    </li>
                <?php
                endif;
                $cta_html = ob_get_clean();

                if (has_nav_menu('smh-primary')) {
                    wp_nav_menu([
                        'theme_location'  => 'smh-primary',
                        'menu_id'         => 'smh-menu',
                        'menu_class'      => 'smh-nav__menu',
                        'container'       => false,
                        'walker'          => new MegaMenuWalker(),
                        'fallback_cb'     => false,
                        'items_wrap'      => '<ul id="%1$s" class="%2$s" role="menubar">%3$s' . $cta_html . '</ul>',
                    ]);
                } else {
                    echo '<ul id="smh-menu" class="smh-nav__menu"><li><a href="' . admin_url('nav-menus.php') . '" class="smh-nav__link">Assign a Menu</a></li>' . $cta_html . '</ul>';
                }
                ?>
<!-- Trigger button -->
<button
    id="smh-search-trigger"
    class="ct-toggle-panel"
    aria-label="<?php esc_attr_e( 'Open Search', 'blocksy' ); ?>"
>
    <svg class="ct-icon" aria-hidden="true" width="17" height="17" viewBox="0 0 15 15">
        <path d="M14.8,13.7L12,11c0.9-1.2,1.5-2.6,1.5-4.2c0-3.7-3-6.8-6.8-6.8S0,3,0,6.8s3,6.8,6.8,6.8c1.6,0,3.1-0.6,4.2-1.5l2.8,2.8c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2C15.1,14.5,15.1,14,14.8,13.7z M1.5,6.8c0-2.9,2.4-5.2,5.2-5.2S12,3.9,12,6.8S9.6,12,6.8,12S1.5,9.6,1.5,6.8z"/>
    </svg>
</button>
                <!-- Search Icon -->
                <div class="smh-nav__search">
                    <?php if ( function_exists( 'blocksy_isolated_get_search_form' ) ) : ?>
<div id="search-modal" class="ct-panel" data-behaviour="modal" role="dialog" aria-label="Search modal" inert>
    <div class="ct-panel-actions">
        <button class="ct-toggle-close" data-type="type-1" aria-label="Close search modal">
            <svg class="ct-icon" width="12" height="12" viewBox="0 0 15 15">
                <path d="M1 15a1 1 0 01-.71-.29 1 1 0 010-1.41l5.8-5.8-5.8-5.8A1 1 0 011.7.29l5.8 5.8 5.8-5.8a1 1 0 011.41 1.41l-5.8 5.8 5.8 5.8a1 1 0 01-1.41 1.41l-5.8-5.8-5.8 5.8A1 1 0 011 15z"/>
            </svg>
        </button>
    </div>
    <div class="ct-panel-content">
        <?php blocksy_isolated_get_search_form([
            'enable_search_field_class' => true,
            'search_placeholder'        => 'Search...',
            'search_live_results'       => 'yes',
            'live_results_attr'         => 'thumbs',
            'override_html_atts'        => [],
            'button_type'               => 'icon'
        ]); ?>
    </div>
</div>
<?php endif; ?>
                </div>

                <!-- Mobile hamburger -->
                <button class="smh-nav__burger" id="smh-burger" aria-label="Open menu" aria-expanded="false" aria-controls="smh-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

            </div><!-- /.smh-nav__inner -->
        </nav><!-- /.smh-nav -->

        <!-- Overlay (closes mobile menu on tap outside) -->
        <div class="smh-overlay" id="smh-overlay" aria-hidden="true"></div>
<?php
    }

    public function register_menus()
    {
        register_nav_menus([
            'smh-primary' => __('Smart Home Primary Nav', 'blocksy-child'),
        ]);
    }
}
