<?php
/**
 * Blocksy Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Autoload classes using Composer
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Initialize the Theme
 */
\NomadTaxCalc\Theme\Classes\Theme::getInstance();
