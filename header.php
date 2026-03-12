<?php
/**
 * Child Theme — header.php
 *
 * Based on Blocksy's header.php.
 * ONLY CHANGE: replaced Blocksy's header output with our custom mega menu.
 * Everything else is kept exactly as the parent theme requires.
 */
?><!doctype html>
<html <?php language_attributes(); ?><?php echo blocksy_html_attr() ?>>
<head>
    <?php do_action('blocksy:head:start') ?>

    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
    <?php do_action('blocksy:head:end') ?>
</head>

<body <?php body_class(); ?> <?php echo blocksy_body_attr() ?>>

<?php
    if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
    }
?>

<div id="main-container">
    <?php
        do_action('blocksy:header:before');

        do_action('blocksy:header:after');
        do_action('blocksy:content:before');
    ?>

    <main <?php echo blocksy_main_attr() ?>>

        <?php
            do_action('blocksy:content:top');
            blocksy_before_current_template();
        ?>
        