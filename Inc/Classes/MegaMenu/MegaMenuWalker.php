<?php

namespace NomadTaxCalc\Theme\Classes\MegaMenu;

use Walker_Nav_Menu;

/**
 * Custom Walker Class for rendering the Mega Menu
 *
 * HOW TO USE IN Appearance → Menus (child items):
 * ─────────────────────────────────────────────────
 * URL              → https://yoursite.com/page         (card clickable link)
 * Title Attribute  → img:https://yoursite.com/icon.png (icon image)
 * Navigation Label → Feature Name                      (bold title)
 * Description      → Short subtitle text               (small text)
 *
 * Enable "Title Attribute" via Screen Options (top right of Menus page).
 */
class MegaMenuWalker extends Walker_Nav_Menu {

    /**
     * Start the list — wraps the <ul>
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '<div class="smh-mega" role="region">';
            $output .= '<div class="smh-mega__grid">';
        }
        // Deeper levels (grandchildren) are ignored / not rendered
    }

    /**
     * End the list
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</div>'; // .smh-mega__grid
            $output .= '</div>'; // .smh-mega
        }
    }

    /**
     * Each menu item
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes      = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes );
        $is_current   = in_array( 'current-menu-item', $classes )
                     || in_array( 'current-menu-ancestor', $classes );

        // ── TOP-LEVEL ITEM ────────────────────────────────────────
        if ( $depth === 0 ) {

            $li_class = implode( ' ', array_filter([
                'smh-nav__item',
                $has_children ? 'has-mega'   : '',
                $is_current   ? 'is-current' : '',
            ]));

            $output .= '<li class="' . esc_attr( $li_class ) . '">';

            $href = ( $has_children && ( empty( $item->url ) || '#' === $item->url ) )
                    ? '#'
                    : esc_url( $item->url );

            $output .= '<a href="' . $href . '" class="smh-nav__link"';
            if ( $has_children ) {
                $output .= ' aria-haspopup="true" aria-expanded="false"';
            }
            $output .= '>';
            $output .= '<span>' . esc_html( $item->title ) . '</span>';
            if ( $has_children ) {
                $output .= '<svg class="smh-nav__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
            }
            $output .= '</a>';

        // ── CHILD ITEM (mega grid card) ───────────────────────────
        } elseif ( $depth === 1 ) {

            // URL field → card's clickable link (standard usage)
            $href = ( ! empty( $item->url ) && '#' !== $item->url )
                    ? esc_url( $item->url )
                    : '#';

            // Title Attribute field → icon image
            // Enter it as:  img:https://yoursite.com/wp-content/uploads/icon.png
            // Leave blank   → fallback SVG icon is shown instead
            $icon_url   = '';
            $attr_title = ! empty( $item->attr_title ) ? $item->attr_title : '';

            if ( strpos( $attr_title, 'img:' ) === 0 ) {
                $icon_url = esc_url( trim( substr( $attr_title, 4 ) ) );
            }

            // Description field → subtitle text under the title
            $description = ! empty( $item->description ) ? $item->description : '';

            // Fallback SVG shown when no icon image is set in Title Attribute
            $fallback_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>';

            $output .= '<a href="' . $href . '" class="smh-mega__card">';

            // Icon
            $output .= '<div class="smh-mega__icon">';
            if ( $icon_url ) {
                $output .= '<img src="' . $icon_url . '" alt="' . esc_attr( $item->title ) . '" loading="lazy">';
            } else {
                $output .= $fallback_svg;
            }
            $output .= '</div>';

            // Text
            $output .= '<div class="smh-mega__text">';
            $output .= '<strong class="smh-mega__title">' . esc_html( $item->title ) . '</strong>';
            if ( $description ) {
                $output .= '<span class="smh-mega__desc">' . esc_html( $description ) . '</span>';
            }
            $output .= '</div>';

            $output .= '</a>';
        }
        // Grandchildren (depth 2+) are intentionally ignored
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</li>';
        }
        // Child items are <a> tags (self-contained), no closing needed
    }
}