# Reading Progress Bar

A premium reading progress bar for single post pages, designed to match the theme's aesthetic and provide smooth transitions.

## Features
- **Smooth Animation**: Uses `requestAnimationFrame` for high-performance scroll tracking.
- **Theme Integration**: Automatically uses the theme's primary color and typography.
- **Responsive**: Works seamlessly across all device sizes.
- **Lightweight**: Minimal overhead, only active on single post pages.

## Implementation Details
- **PHP**: Managed by `NomadTaxCalc\Theme\Classes\Features\ReadingProgressBar`.
- **SCSS**: Styled in `src/scss/components/components-helpers/_reading-progress.scss`.
- **JS**: Logic handled in `src/js/components/reading-progress.js`.

## Customization
You can customize the bar's appearance by modifying the variables in `_reading-progress.scss`.
- **Height**: Change the `height` property of `.reading-progress-container`.
- **Color**: The bar uses `$primary` by default, but you can override it in the component file.
- **Z-Index**: ensure it stays above other elements by adjusting the `z-index`.
