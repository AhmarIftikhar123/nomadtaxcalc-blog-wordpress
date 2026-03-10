<?php

namespace NomadTaxCalc\Theme\Classes\Media;

use NomadTaxCalc\Theme\Traits\Singleton;
use DOMDocument;

/**
 * Enable SVG uploads in WordPress Media Library
 *
 * TWO LAYERS OF SECURITY:
 * 1. Only administrators can upload SVGs (editors/authors cannot)
 * 2. SVG contents are sanitized on upload to strip malicious code
 */
class SvgSupport {

    use Singleton;

    protected function __construct() {
        $this->setup_hooks();
    }

    protected function setup_hooks() {
        // 1. ALLOW SVG MIME TYPE — but only for administrators
        add_filter( 'upload_mimes', [ $this, 'allow_svg_mimes' ] );

        // 2. FIX WORDPRESS SVG MIME CHECK (WP 5.1+)
        add_filter( 'wp_check_filetype_and_ext', [ $this, 'check_filetype_and_ext' ], 10, 4 );

        // 3. SANITIZE SVG ON UPLOAD
        add_filter( 'wp_handle_upload', [ $this, 'sanitize_svg_on_upload' ] );

        // 4. SHOW SVG PREVIEW IN MEDIA LIBRARY
        add_filter( 'wp_prepare_attachment_for_js', [ $this, 'prepare_attachment_for_js' ] );
    }

    public function allow_svg_mimes( $mimes ) {
        if ( current_user_can( 'administrator' ) ) {
            $mimes['svg']  = 'image/svg+xml';
            $mimes['svgz'] = 'image/svg+xml';
        }
        return $mimes;
    }

    public function check_filetype_and_ext( $data, $file, $filename, $mimes ) {
        if ( ! current_user_can( 'administrator' ) ) {
            return $data;
        }

        $filetype = wp_check_filetype( $filename, $mimes );

        if ( 'svg' === $filetype['ext'] || 'svgz' === $filetype['ext'] ) {
            $data['ext']             = $filetype['ext'];
            $data['type']            = $filetype['type'];
            $data['proper_filename'] = $filename;
        }

        return $data;
    }

    public function sanitize_svg_on_upload( $upload ) {
        if ( ! isset( $upload['type'] ) || 'image/svg+xml' !== $upload['type'] ) {
            return $upload;
        }

        $file_path = $upload['file'];

        if ( ! file_exists( $file_path ) ) {
            return $upload;
        }

        $svg_content = file_get_contents( $file_path );

        if ( false === $svg_content ) {
            return $upload;
        }

        $sanitized = $this->sanitize_svg( $svg_content );

        file_put_contents( $file_path, $sanitized );

        return $upload;
    }

    /**
     * SANITIZE SVG
     * Removes known dangerous elements and attributes.
     * Does NOT require any external library.
     */
    public function sanitize_svg( $svg ) {
        // ── Dangerous elements to strip entirely (including contents) ──
        $strip_elements = [
            'script',       // JavaScript
            'use',          // Can reference external content
            'foreignObject',// Can embed HTML
            'animate',      // Can be abused for timing attacks
            'set',
            'animateMotion',
            'animateTransform',
            'discard',
        ];

        // ── Dangerous attributes to remove from any element ──
        $strip_attributes = [
            // JavaScript event handlers
            'onload', 'onclick', 'onmouseover', 'onmouseout', 'onmousedown',
            'onmouseup', 'onmousemove', 'onfocus', 'onblur', 'onchange',
            'onsubmit', 'onreset', 'onselect', 'onabort', 'onerror',
            'onkeydown', 'onkeypress', 'onkeyup',
            // External resource loading
            'href',         // Can load external resources — remove from non-<a> tags
            'xlink:href',   // Legacy SVG external reference
            'src',          // Should not appear in SVG
            // Stylesheet injection
            'style',        // Inline styles can embed JS via expression()
        ];

        $dom = new DOMDocument();

        // Suppress errors from malformed SVG
        libxml_use_internal_errors( true );
        $dom->loadXML( $svg, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( false );

        // Remove dangerous elements
        foreach ( $strip_elements as $tag ) {
            $nodes = $dom->getElementsByTagName( $tag );
            // Iterate in reverse — removing nodes shifts the live NodeList
            for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
                $node = $nodes->item( $i );
                if ( $node && $node->parentNode ) {
                    $node->parentNode->removeChild( $node );
                }
            }
        }

        // Remove dangerous attributes from all remaining elements
        $all_elements = $dom->getElementsByTagName( '*' );
        foreach ( $all_elements as $element ) {
            foreach ( $strip_attributes as $attr ) {
                if ( $element->hasAttribute( $attr ) ) {
                    $element->removeAttribute( $attr );
                }
            }

            // Remove any attribute that starts with "on" (catches all event handlers)
            $attrs_to_remove = [];
            foreach ( $element->attributes as $attribute ) {
                if ( strpos( strtolower( $attribute->name ), 'on' ) === 0 ) {
                    $attrs_to_remove[] = $attribute->name;
                }
                // Remove javascript: URIs from any attribute value
                if ( strpos( strtolower( $attribute->value ), 'javascript:' ) !== false ) {
                    $attrs_to_remove[] = $attribute->name;
                }
            }
            foreach ( $attrs_to_remove as $attr ) {
                $element->removeAttribute( $attr );
            }
        }

        return $dom->saveXML( $dom->documentElement );
    }

    public function prepare_attachment_for_js( $response ) {
        if ( 'image/svg+xml' === $response['mime'] ) {
            // Use the file URL directly as the thumbnail since SVGs are scalable
            $response['sizes'] = [
                'full' => [
                    'url'         => $response['url'],
                    'width'       => 100,
                    'height'      => 100,
                    'orientation' => 'landscape',
                ],
            ];
        }

        return $response;
    }
}
