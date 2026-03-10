<?php

namespace NomadTaxCalc\Theme\Classes\Toc;

use NomadTaxCalc\Theme\Traits\Singleton;

/**
 * Dynamic Sticky Table of Contents
 *
 * - Auto-reads all H2/H3 headings from post content
 * - Injects IDs into headings so scroll-spy works
 * - Renders a sticky sidebar TOC widget via shortcode
 * - Active item highlights as user scrolls
 * - Fully configurable via constants below
 */
class TableOfContents {

    use Singleton;

    // ── CONFIG ────────────────────────────────────────────
    // Minimum number of headings needed to show the TOC
    const MIN_HEADINGS = 2;

    // Which heading levels to include
    const HEADING_TAGS = [ 'h2', 'h3' ];

    // TOC title
    const TOC_TITLE = 'On This Page';

    // Only show on these post types
    const POST_TYPES = [ 'post', 'page' ];
    // ─────────────────────────────────────────────────────

    protected function __construct() {
        // Inject heading IDs into post content
        add_filter( 'the_content', [ $this, 'inject_heading_ids' ], 20 );

        // Register the shortcode [ntc_toc]
        add_shortcode( 'ntc_toc', [ $this, 'render_shortcode' ] );
    }

    /**
     * Inject unique IDs into headings that don't have one
     */
    public function inject_heading_ids( string $content ): string {
        if ( ! is_singular( self::POST_TYPES ) ) {
            return $content;
        }

        // Inject IDs into all possible headings so the shortcode can pick and choose flexibly
        $tags    = 'h1|h2|h3|h4|h5|h6';
        $slugs   = [];

        $content = preg_replace_callback(
            '/<(' . $tags . ')([\s>][^>]*)>(.*?)<\/\1>/si',
            function ( $matches ) use ( &$slugs ) {
                $tag        = $matches[1];
                $attrs      = $matches[2];
                $inner_html = $matches[3];

                // If heading already has an id, leave it alone
                if ( preg_match( '/\bid=["\'][^"\']+["\']/', $attrs ) ) {
                    return $matches[0];
                }

                // Build slug from plain text of heading
                $text = wp_strip_all_tags( $inner_html );
                $slug = $this->make_unique_slug( $text, $slugs );
                $slugs[] = $slug;

                return '<' . $tag . $attrs . ' id="' . esc_attr( $slug ) . '">' . $inner_html . '</' . $tag . '>';
            },
            $content
        );

        return $content;
    }

    /**
     * Build the TOC HTML from post content headings as a shortcode
     */
    public function render_shortcode( $atts ): string {
        if ( ! is_singular( self::POST_TYPES ) ) {
            return '';
        }

        $atts = shortcode_atts([
            'title'    => self::TOC_TITLE,
            'min'      => self::MIN_HEADINGS,
            'headings' => implode(',', self::HEADING_TAGS),
        ], $atts, 'ntc_toc');

        $target_tags = array_map('trim', explode(',', $atts['headings']));
        // Filter out anything that isn't h1-h6
        $target_tags = array_filter($target_tags, function($t) { return preg_match('/^h[1-6]$/i', $t); });
        if (empty($target_tags)) {
            $target_tags = self::HEADING_TAGS;
        }

        $post    = get_post();
        // We use the raw post_content before filters to avoid infinite loops when extracting tags
        $content = $post->post_content;
        $items   = self::extract_headings( $content, $target_tags );

        if ( count( $items ) < (int) $atts['min'] ) {
            return '';
        }

        ob_start();
        ?>
        <aside class="ntc-toc" id="ntc-toc" aria-label="<?php esc_attr_e( 'Table of Contents', 'nomadtaxcalc' ); ?>">

            <div class="ntc-toc__header">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <span><?php echo esc_html( $atts['title'] ); ?></span>
            </div>

            <nav>
                <ol class="ntc-toc__list">
                    <?php foreach ( $items as $index => $item ) : ?>
                        <li class="ntc-toc__item ntc-toc__item--<?php echo esc_attr( $item['tag'] ); ?>">
                            <a href="#<?php echo esc_attr( $item['id'] ); ?>"
                               class="ntc-toc__link"
                               data-target="<?php echo esc_attr( $item['id'] ); ?>">
                                <span class="ntc-toc__num"><?php echo str_pad( $index + 1, 2, '0', STR_PAD_LEFT ); ?></span>
                                <span class="ntc-toc__text"><?php echo esc_html( $item['text'] ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>

        </aside>
        <?php
        return ob_get_clean();
    }

    /**
     * Extract headings from content — returns array of [id, text, tag]
     */
    private static function extract_headings( string $content, array $target_tags ): array {
        $tags    = implode( '|', $target_tags );
        $items   = [];
        $slugs   = [];

        // Simple extraction for heading logic
        $dom = new \DOMDocument();
        libxml_use_internal_errors( true );
        // Wrap with a div to handle multiple top-level elements or invalid HTML
        $dom->loadHTML( '<?xml encoding="UTF-8"><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        libxml_clear_errors();

        $xpath = new \DOMXPath( $dom );
        $query = '//' . implode( ' | //', $target_tags );
        $nodes = $xpath->query( $query );

        foreach ( $nodes as $node ) {
            $text = trim( $node->textContent );
            if ( empty( $text ) ) continue;

            $id = $node->getAttribute( 'id' );
            if ( empty( $id ) ) {
                $instance = new self();
                $id = $instance->make_unique_slug( $text, $slugs );
                $slugs[] = $id;
            }

            $items[] = [
                'id'   => $id,
                'text' => $text,
                'tag'  => strtolower( $node->nodeName ),
            ];
        }

        return $items;
    }

    /**
     * Convert heading text to a URL-safe unique slug
     */
    private function make_unique_slug( string $text, array $existing ): string {
        $slug = sanitize_title( $text );
        $slug = preg_replace( '/[^a-z0-9\-]/', '', $slug );
        $slug = trim( $slug, '-' );

        if ( empty( $slug ) ) {
            $slug = 'section';
        }

        // Make unique by appending -2, -3 etc if slug already used
        $original = $slug;
        $counter  = 2;
        while ( in_array( $slug, $existing, true ) ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
