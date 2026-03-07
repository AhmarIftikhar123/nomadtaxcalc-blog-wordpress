# SASS MODERN BEST PRACTICES — MCP (Master Context Prompt)
> For AI Agents working on WordPress child theme development with Vite + Sass (Dart Sass 1.80+)

---

## ⚠️ CRITICAL: @import is DEPRECATED
`@import` was officially deprecated in **Dart Sass 1.80.0** (October 2024).
It **will be removed entirely in Dart Sass 3.0.0**.

### NEVER write:
```scss
// ❌ DEPRECATED — DO NOT USE
@import 'variables';
@import 'mixins';
@import 'sass/math';
```

### ALWAYS use instead:
```scss
// ✅ MODERN — USE THIS
@use 'variables';
@use 'mixins';
@use 'sass:math';
```

---

## RULE 1: @use — Load a Module

`@use` loads a stylesheet and makes its members (variables, mixins, functions)
available **only in the current file**, under a namespace.

```scss
// ✅ Default namespace = filename
@use 'abstracts/variables';     // access as: variables.$primary-color

// ✅ Custom namespace
@use 'abstracts/variables' as v;  // access as: v.$primary-color

// ✅ Wildcard (use sparingly — only in entrypoint/main.scss)
@use 'abstracts/variables' as *;  // access as: $primary-color (no namespace)
```

### Key Rules for @use:
- Must appear **before any other rules** (except @forward and variable declarations)
- Each module is loaded **exactly once**, no duplicate CSS output
- Members are **not passed along** — each file must @use what it needs
- Variables starting with `_` or `-` are **private** to their file

---

## RULE 2: @forward — Re-export a Module

`@forward` makes a module's members available to files that `@use` your file.
Used in **index files** to create clean entry points.

```scss
// abstracts/_index.scss
@forward 'variables';
@forward 'mixins';
@forward 'functions';

// Prefix all forwarded members (avoids collision)
@forward 'theme' as theme-*;

// Show only specific members
@forward 'variables' show $primary-color, $secondary-color;

// Hide specific members
@forward 'variables' hide $internal-spacing;
```

### Key Rules for @forward:
- Does NOT make members available in the current file — use `@use` for that
- Write `@forward` BEFORE `@use` in the same file
- Used to build a clean public API for your scss folder

---

## RULE 3: Built-in Modules — ALWAYS @use them

Global built-in functions like `lighten()`, `darken()`, `percentage()` are now
**deprecated at the global level**. You must import built-in modules explicitly.

### The 7 Built-in Modules:
```scss
@use 'sass:math';
@use 'sass:color';
@use 'sass:string';
@use 'sass:list';
@use 'sass:map';
@use 'sass:selector';
@use 'sass:meta';
```

### Common Usage Examples:
```scss
@use 'sass:math';
@use 'sass:color';
@use 'sass:map';

.element {
  // ✅ Math
  width: math.percentage(1 / 3);
  font-size: math.div(16px, 1.5);

  // ✅ Color (replaces deprecated lighten/darken)
  background: color.adjust($primary, $lightness: 10%);
  border-color: color.scale($primary, $lightness: -20%);
  opacity-color: color.change($primary, $alpha: 0.5);

  // ✅ Map
  $breakpoints: ('sm': 576px, 'md': 768px, 'lg': 1024px);
  @media (min-width: map.get($breakpoints, 'md')) { ... }
}
```

### ❌ Old Global Functions → ✅ New Module Functions:
| Old (deprecated)              | New (use this)                         |
|-------------------------------|----------------------------------------|
| `lighten($c, 10%)`            | `color.adjust($c, $lightness: 10%)`   |
| `darken($c, 10%)`             | `color.adjust($c, $lightness: -10%)`  |
| `transparentize($c, 0.2)`     | `color.adjust($c, $alpha: -0.2)`      |
| `fade-out($c, 0.2)`           | `color.adjust($c, $alpha: -0.2)`      |
| `opacify($c, 0.2)`            | `color.adjust($c, $alpha: 0.2)`       |
| `saturate($c, 10%)`           | `color.adjust($c, $saturation: 10%)`  |
| `desaturate($c, 10%)`         | `color.adjust($c, $saturation: -10%)` |
| `map-get($map, $key)`         | `map.get($map, $key)`                 |
| `map-merge($a, $b)`           | `map.merge($a, $b)`                   |
| `percentage($n)`              | `math.percentage($n)`                 |
| `round($n)`                   | `math.round($n)`                      |
| `unitless($n)`                | `math.is-unitless($n)`                |
| `comparable($a, $b)`          | `math.compatible($a, $b)`             |
| `adjust-color(...)`           | `color.adjust(...)`                   |
| `scale-color(...)`            | `color.scale(...)`                    |
| `change-color(...)`           | `color.change(...)`                   |

---

## PROJECT STRUCTURE — Modern 7-1 Pattern

```
src/scss/
├── abstracts/
│   ├── _variables.scss
│   ├── _mixins.scss
│   ├── _functions.scss
│   ├── _placeholders.scss
│   └── _index.scss          ← @forward all abstracts
├── base/
│   ├── _reset.scss
│   ├── _typography.scss
│   └── _index.scss
├── components/
│   ├── _buttons.scss
│   ├── _cards.scss
│   └── _index.scss
├── layout/
│   ├── _header.scss
│   ├── _footer.scss
│   ├── _grid.scss
│   └── _index.scss
├── pages/
│   ├── _home.scss
│   └── _index.scss
├── themes/
│   ├── _default.scss
│   └── _index.scss
├── vendors/
│   └── _index.scss
└── main.scss                 ← entrypoint, only @use statements
```

---

## INDEX FILES — The @forward Hub Pattern

Each folder must have `_index.scss` that forwards its partials:

```scss
// abstracts/_index.scss
@forward 'variables';
@forward 'mixins';
@forward 'functions';
```

```scss
// components/_index.scss
@forward 'buttons';
@forward 'cards';
@forward 'forms';
```

---

## MAIN ENTRYPOINT (main.scss)

```scss
// main.scss — entrypoint, no actual styles here
@use 'abstracts' as *;   // wildcard: access $vars and mixins without namespace
@use 'base';
@use 'layout';
@use 'components';
@use 'pages';
@use 'themes';
@use 'vendors';
```

---

## COMPONENT FILE TEMPLATE

Every component file should be self-contained:

```scss
// components/_buttons.scss
@use 'sass:color';
@use '../abstracts' as *;   // gets variables and mixins

.btn {
  background-color: $primary-color;
  padding: spacing(2) spacing(4);

  &:hover {
    background-color: color.adjust($primary-color, $lightness: -10%);
  }

  &--secondary {
    background-color: $secondary-color;
  }
}
```

---

## VARIABLES WITH !default (for theming)

```scss
// abstracts/_variables.scss
$primary-color: #3b82f6 !default;
$secondary-color: #10b981 !default;
$font-family: 'Clash Grotesk', sans-serif !default;
$base-font-size: 16px !default;
```

Configuring a module with custom values:
```scss
// Override defaults at @use time
@use 'abstracts/variables' with (
  $primary-color: #ff5733,
  $font-family: 'Inter', sans-serif
);
```

---

## PRIVATE MEMBERS

Prefix with `_` or `-` to make a member private (only accessible in its own file):

```scss
// ✅ Private — not accessible outside this file
$_internal-gutter: 8px;
$-base-unit: 4px;

// ✅ Public — accessible via @use
$spacing-unit: $_internal-gutter * 2;
```

---

## MIXIN BEST PRACTICES

```scss
// abstracts/_mixins.scss
@use 'sass:math';

// ✅ Responsive breakpoint mixin
$breakpoints: (
  'sm': 576px,
  'md': 768px,
  'lg': 1024px,
  'xl': 1280px,
) !default;

@mixin respond-to($breakpoint) {
  @if map.has-key($breakpoints, $breakpoint) {
    @media (min-width: map.get($breakpoints, $breakpoint)) {
      @content;
    }
  }
}

// ✅ Fluid typography mixin
@mixin fluid-type($min-size, $max-size, $min-width: 320px, $max-width: 1280px) {
  font-size: clamp(
    #{$min-size},
    #{math.div($max-size - $min-size, $max-width - $min-width)} * 100vw,
    #{$max-size}
  );
}
```

---

## VITE CONFIG — Correct Setup for Modern Sass

```javascript
// vite.config.js
import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  css: {
    preprocessorOptions: {
      scss: {
        // ✅ Use @use, not @import for global abstracts
        // Inject abstracts into every file automatically
        additionalData: `@use "@/scss/abstracts" as *;`,
        // api: 'modern-compiler' // optional: use new Sass API (faster)
      }
    }
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    }
  },
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/scss/main.scss'),
        app: resolve(__dirname, 'src/js/main.js'),
      },
      output: {
        assetFileNames: 'css/[name][extname]',
        entryFileNames: 'js/[name].js',
      }
    }
  }
})
```

---

## MIGRATION: Automated Tool

If migrating an existing project with `@import`:

```bash
# Install the official Sass migrator
npm install -g sass-migrator

# Migrate a single entrypoint (and all its dependencies)
sass-migrator module --migrate-deps src/scss/main.scss

# Migrate + remove old vendor prefixes
sass-migrator module --migrate-deps --forward=all src/scss/main.scss

# Migrate built-in functions only (keep @import for now)
sass-migrator module --built-in-only src/scss/main.scss
```

---

## NESTING — Keep it Shallow

```scss
// ✅ Max 3 levels deep
.card {
  padding: 1rem;

  &__title {
    font-size: 1.25rem;
  }

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
}

// ❌ Too deep — avoid this
.nav ul li a span strong { ... }
```

---

## SUMMARY CHEATSHEET

| Feature             | Old Way ❌             | New Way ✅                           |
|---------------------|------------------------|--------------------------------------|
| Load a file         | `@import 'file'`       | `@use 'file'`                        |
| Share with others   | `@import 'file'`       | `@forward 'file'`                    |
| Math functions      | `percentage(0.5)`      | `math.percentage(0.5)`               |
| Color functions     | `lighten($c, 10%)`     | `color.adjust($c, $lightness: 10%)`  |
| Map functions       | `map-get($m, $k)`      | `map.get($m, $k)`                    |
| Private variable    | `$_var` (convention)   | `$_var` (enforced by module system)  |
| Load all in folder  | Multiple `@import`s    | `_index.scss` with `@forward`s       |
| Configure defaults  | Override before import | `@use 'module' with ($var: val)`     |

---

## REFERENCES
- https://sass-lang.com/documentation/breaking-changes/import/
- https://sass-lang.com/documentation/at-rules/use/
- https://sass-lang.com/documentation/at-rules/forward/
- https://sass-lang.com/documentation/modules/
- https://sass-lang.com/blog/import-is-deprecated/
