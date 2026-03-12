# Nomad CTA Shortcode Documentation

The `nomad_cta` shortcode allows you to insert clean, minimal Call-to-Action boxes into your posts and pages with built-in button animations.

## Usage

Basic usage (Black & White default with `combo` animation):
```shortcode
[nomad_cta]
```

Using a preset:
```shortcode
[nomad_cta style="blue"]
```

## Attributes

All attributes are optional. Override values provided by presets.

- `style`: Accepts `blue`, `green`, `orange`, `purple`. Defines preset headline, body, button, and URL.
- `color`: Custom hex color for the left border and button (e.g., `#ff0000`).
- `headline`: Custom headline text.
- `body`: Custom body text.
- `button`: Custom button label.
- `url`: Custom button URL.
- `target`: Link target (default: `_blank`).
- `align`: Text alignment (`left`, `center`, `right`).
- `animation`: Button animation effect (default: `combo`).
  - `combo`: Pulse + Shimmer running simultaneously.
  - `pulse`: Ripple ring expands outward and fades.
  - `shimmer`: Diagonal white light sweep passes across button.
  - `breathe`: Button border/shadow fades in and out (automatically switches button to outlined style).
  - `none`: Static button.

## Presets

- **blue**: Blue border/button. Focuses on tax bill calculation.
- **green**: Green border/button. Focuses on scenario comparison.
- **orange**: Orange border/button. Focuses on Spain tax (Beckham Law).
- **purple**: Purple border/button. Focuses on 2026 travel planning.

## Examples

Using the **pulse** animation with the blue preset:
```shortcode
[nomad_cta style="blue" animation="pulse"]
```

Using the **breathe** animation (outlined button):
```shortcode
[nomad_cta style="purple" animation="breathe"]
```

Full manual override with no animation:
```shortcode
[nomad_cta color="#e11d48" headline="Urgent Update" animation="none" button="Read More" url="/news"]
```
