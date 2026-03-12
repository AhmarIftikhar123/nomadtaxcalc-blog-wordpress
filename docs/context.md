# Project Context
This is a child theme setup for `nomadtaxcalc.com`, functioning as a blog on a subdomain (`blog.nomadtaxcalc`). It uses Vite + SCSS (Dart SASS modern @use structure) and PSR-4 Composer autoloading for its PHP structure.

we're in blocksy-child theme.
using sass we have sass-mcp for bestpractices dir str is : 
gen this SCSS structure using modern @use/@forward with aliases (e.g., abstracts as abs)

Directory: src/scss/

src/
└── scss/
    ├── abstracts/
    │   ├── _abstracts.scss    (Main abstracts file)
    │   ├── abstracts-helpers/
    │   │   ├── _variables.scss    (Variables for colors, spacing, etc.)
    │   │   ├── _functions.scss    (SCSS functions)
    │   │   ├── _mixins.scss       (SCSS mixins)
    │   │   └── _placeholders.scss (SCSS placeholders/extends)
    │
    ├── base/
    │   ├── _base.scss        (Main base file)
    │   ├── base-helpers/
    │   │   ├── _reset.scss       (CSS reset/normalize)
    │   │   ├── _typography.scss  (Typography rules)
    │   │   └── _utilities.scss   (Utility classes)
    │
    ├── components/
    │   ├── _components.scss   (Main components file)
    │   ├── components-helpers/
    │   │   ├── _buttons.scss     (Button styles)
    │   │   ├── _dropdown.scss    (Dropdown styles)
    │   │   ├── _forms.scss       (Form elements)
    │   │   └── _cards.scss       (Card components)
    │
    ├── layout/
    │   ├── _layout.scss      (Main layout file)
    │   ├── layout-helpers/
    │   │   ├── _header.scss      (Header styles)
    │   │   ├── _navigation.scss  (Navigation styles)
    │   │   ├── _sidebar.scss     (Sidebar styles)
    │   │   ├── _footer.scss      (Footer styles)
    │   │   └── _grid.scss        (Grid system)
    │
    ├── pages/
    │   ├── _pages.scss       (Main pages file)
    │   ├── pages-helpers/
    │   │   ├── _home.scss        (Home page specific styles)
    │   │   ├── _videos.scss      (Videos page styles)
    │   │   └── _profile.scss     (Profile page styles)
    │
    ├── themes/
    │   ├── _themes.scss      (Main themes file)
    │   ├── themes-helpers/
    │   │   ├── _dark.scss        (Dark theme)
    │   │   └── _light.scss       (Light theme)
    │
    ├── vendors/
    │   ├── _vendors.scss     (Main vendors file)
    │   ├── vendors-helpers/
    │   │   ├── _bootstrap.scss   (Third-party CSS)
    │   │   └── _fontawesome.scss (Icon library styles)
    │
    └── main.scss             (Main SCSS file that imports all others)

Using Vite for build process.
Composer for auto loading.

Everytime U add a feature add the respective files always as question needs to add the respective file in /docs dir how to use this feature when planing the feature.