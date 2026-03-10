# Table of Contents Usage Guide

The Table of Contents (TOC) feature automatically scans singular post and page content for `<h2>` and `<h3>` headings, injects scrollable IDs, and constructs a stylish sticky sidebar navigation module.

## How to Display the TOC

You can now place it flexibly anywhere on a singular post or page using the following shortcode:

```text
[ntc_toc]
```

### Attributes:
The shortcode supports multiple attributes to customize its behavior per usage:
- `title`: Set a custom title (Default: "On This Page"). Example: `[ntc_toc title="In This Article"]`
- `min`: The minimum number of headings required to display the TOC (Default: "2"). Example: `[ntc_toc min="4"]`
- `headings`: Comma-separated list of heading tags to extract (Default: "h2,h3"). Example: `[ntc_toc headings="h2,h3,h4"]`

You can combine these as needed:
```text
[ntc_toc title="In This Article" min="4" headings="h2,h3,h4"]
```

### Usage Tips:
1. **Gutenberg Editor**: Add a "Shortcode" block to your page and type `[ntc_toc]` into it. 
2. **Elementor / Page Builders**: Add a "Shortcode" widget to either your single post template or directly on a layout wherever you want the navigation sidebar to dynamically stick.
3. **Visibility**: The TOC will only display if the system detects the minimum number of headings within the body content. If it doesn't meet the threshold, the shortcode will return nothing, keeping the interface clean!

## Design Customization
The TOC inherits modern styling directly from the application's compiled asset pipeline (`dist/css/style.css` via Vite). Colors and dimensions automatically conform to the variables established in `_variables.scss` to guarantee visual coherence with the rest of your UI.
