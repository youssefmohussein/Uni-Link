# TerraFusion Design Guide

## Design System Overview

TerraFusion uses a sophisticated "Masquerade Elegance" theme with a dark, luxurious aesthetic.

## Color Palette

### Primary Colors

| Color | Hex Code | Usage |
|-------|----------|-------|
| Dominant Background | #1A1A1A | Main body/desktop background |
| Primary Accent (Gold) | #C8A252 | Logo, CTAs, navigation highlights, hover effects |
| Neutral Contrast | #F0F0F0 | Primary text, card backgrounds |
| Secondary Text | #999999 | Subheadings, descriptions, dividers |

### Usage Rules

- **Gold (#C8A252):** Use EXCLUSIVELY for:
  - Logo text
  - Primary call-to-action buttons ("Order Now", "Add to Cart", "Checkout")
  - Active navigation links
  - Hover effects on interactive elements
  - Price displays
  - Progress indicators
  
- **Never use gold for:**
  - Body text
  - Backgrounds (except small accents)
  - Error messages (use red)

## Typography

### Font Families

- **Headings:** 'Playfair Display' (Google Fonts)
  - Weight: 600 (Semi-Bold)
  - Usage: All h1-h6, card titles, section headers
  
- **Body Text:** 'Open Sans' (Google Fonts)
  - Weight: 400 (Regular) for body
  - Weight: 700 (Bold) for CTAs and emphasis

### Typography Hierarchy

```
h1: 2.5rem / Playfair Display / 600
h2: 2rem / Playfair Display / 600
h3: 1.75rem / Playfair Display / 600
h4: 1.5rem / Playfair Display / 600
h5: 1.25rem / Playfair Display / 600
h6: 1rem / Playfair Display / 600
Body: 1rem / Open Sans / 400
CTA: 1rem / Open Sans / 700
```

## Component Styles

### Buttons

#### Primary Button (Gold)
```css
.btn-gold {
    background-color: #C8A252;
    color: #1A1A1A;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 700;
}
```

**Hover State:**
- Background: Darken gold by 10%
- Transform: translateY(-2px)
- Box-shadow: 0 5px 15px rgba(200, 162, 82, 0.3)

#### Outline Button
```css
.btn-outline-gold {
    border: 2px solid #C8A252;
    color: #C8A252;
    background: transparent;
}
```

### Cards

- **Background:** #2A2A2A (slightly lighter than primary for depth)
- **Border:** 1px solid rgba(200, 162, 82, 0.2)
- **Border-radius:** 8px
- **Padding:** 1.5rem

#### Menu Cards

- **Hover Effect:**
  - Transform: translateY(-5px)
  - Box-shadow: 0 10px 20px rgba(200, 162, 82, 0.2)

- **Image Overlay:** Dark gradient (rgba(26, 26, 26, 0.7))

### Forms

- **Input Background:** #2A2A2A
- **Border:** 1px solid rgba(200, 162, 82, 0.3)
- **Focus Border:** #C8A252
- **Focus Shadow:** 0 0 0 0.2rem rgba(200, 162, 82, 0.25)

## Animations

### Micro-Interactions

1. **Button Hover:**
   - Duration: 0.3s ease
   - Transform: translateY(-2px)
   - Gold glow effect

2. **Card Hover:**
   - Duration: 0.3s ease
   - Transform: translateY(-5px)
   - Shadow: Gold-tinted shadow

3. **Focus Pulse (Gold Pulse):**
   - Duration: 1.5s
   - Effect: Expanding gold glow ring

### Loading States

- **Spinner:** Gold-colored SVG spinner
- **Animation:** Smooth rotation
- **Usage:** During AJAX calls, form submissions

## Layout Guidelines

### Spacing

- **Container Padding:** 1rem (mobile), 2rem (desktop)
- **Card Spacing:** 2rem gap between cards
- **Section Spacing:** 4rem vertical margin

### Responsive Breakpoints

- **Mobile:** < 576px
  - Single column layout
  - Hamburger menu
  - Stacked cards
  
- **Tablet:** 576px - 992px
  - 2-column menu grid
  - Sidebar navigation
  
- **Desktop:** > 992px
  - 3-column menu grid
  - Full sidebar
  - Optimized spacing

### Grid System

- **Menu Grid:**
  - Mobile: 1 column
  - Tablet: 2 columns
  - Desktop: 3 columns (auto-fill, min 280px)

## Interactive Elements

### Touch Targets

- **Minimum Size:** 48x48px
- **Spacing:** 8px between interactive elements

### Accessibility

- **WCAG AA Compliance:**
  - Contrast ratios meet AA standards
  - ARIA labels on all interactive elements
  - Keyboard navigable (tab index)
  - Focus indicators visible

## Component Examples

### Menu Card

```html
<div class="card menu-card">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body">
        <h5 class="card-title playfair-font">Item Name</h5>
        <p class="card-text text-muted">Description</p>
        <div class="d-flex justify-content-between">
            <span class="text-gold fw-bold">$12.99</span>
            <button class="btn btn-gold btn-sm">Add to Cart</button>
        </div>
    </div>
</div>
```

### Progress Bar

```html
<div class="progress" style="height: 6px;">
    <div class="progress-bar bg-gold" style="width: 33%;"></div>
</div>
```

## Best Practices

1. **Consistency:** Always use the defined color palette
2. **Gold Accent:** Use sparingly for maximum impact
3. **Typography:** Maintain hierarchy - Playfair for headings, Open Sans for body
4. **Spacing:** Follow the 8px grid system
5. **Animations:** Keep them subtle and purposeful (0.3s transitions)
6. **Responsive:** Test on all breakpoints
7. **Accessibility:** Always include ARIA labels and keyboard navigation

## Dark Mode

TerraFusion is designed as a dark theme application. All components should maintain the dark aesthetic with:
- Dark backgrounds (#1A1A1A, #2A2A2A)
- Light text (#F0F0F0, #999999)
- Gold accents for highlights

---

**Last Updated:** December 2024

