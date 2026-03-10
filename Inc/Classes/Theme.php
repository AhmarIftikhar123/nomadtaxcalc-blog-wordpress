<?php

namespace NomadTaxCalc\Theme\Classes;

use NomadTaxCalc\Theme\Traits\Singleton;
use NomadTaxCalc\Theme\Classes\MegaMenu\MegaMenuSetup;
use NomadTaxCalc\Theme\Classes\Media\SvgSupport;
use NomadTaxCalc\Theme\Classes\Toc\TableOfContents;

class Theme
{
    use Singleton;

    protected function __construct()
    {
        $this->setup_hooks();
        MegaMenuSetup::getInstance();
        SvgSupport::getInstance();
        TableOfContents::getInstance();
    }

    protected function setup_hooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets()
    {
        // 1. Load parent theme (Blocksy) styles
        wp_enqueue_style(
            'blocksy-style',
            get_template_directory_uri() . '/style.css',
            [],
            wp_get_theme('blocksy')->get('Version')
        );

        // Enqueue Clash Grotesk Font from Fontshare
        wp_enqueue_style(
            'clash-grotesk',
            'https://api.fontshare.com/v2/css?f[]=clash-grotesk@400,500,600,700&display=swap',
            array(),
            null
        );

        wp_enqueue_script('jquery');
        // Enqueue Vite Compiled Main CSS
        $css_path = get_stylesheet_directory() . '/dist/css/style.css';
        if (file_exists($css_path)) {
            wp_enqueue_style(
                'child-style',
                get_stylesheet_directory_uri() . '/dist/css/style.css',
                array('blocksy-style'), // Depends on parent theme style
                filemtime($css_path)
            );
        }

        // Enqueue Vite Compiled Main JS
        $js_path = get_stylesheet_directory() . '/dist/js/main.js';
        if (file_exists($js_path)) {
            wp_enqueue_script(
                'child-script',
                get_stylesheet_directory_uri() . '/dist/js/main.js',
                array('jquery'),
                filemtime($js_path),
                true
            );
        }
    }
}
