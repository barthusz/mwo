# WordPress Theme: Mijn Werk Online (MWO)

## Overzicht
Dit is een moderne WordPress theme speciaal ontwikkeld voor fotografen. De theme is gebouwd voor portfolio- en galerieweergave met professionele lightbox functionaliteit.

**Theme details:**
- Naam: Mijn Werk Online
- Versie: 1.0.0
- Auteur: Bart Sallé
- Text Domain: mwo
- Tags: photography, portfolio, gallery

## Projectstructuur

```
mwo/
├── assets/
│   ├── css/           # Stylesheet bestanden
│   │   ├── all.min.css              # Font Awesome icons
│   │   ├── gallery.css              # Galerie styling
│   │   ├── glightbox.min.css        # Lightbox library
│   │   ├── layout.css               # Basis layout
│   │   ├── lightbox-custom.css      # Custom lightbox styling
│   │   ├── mobile-menu.css          # Mobiele menu styling
│   │   └── sidebar.css              # Sidebar styling
│   └── webfonts/      # Font Awesome webfonts
├── inc/
│   ├── gallery-captions.php         # Galerie bijschriften functionaliteit
│   └── social-media.php             # Social media integratie
├── js/
│   ├── admin.js                     # Admin panel JavaScript
│   ├── glightbox.min.js             # Lightbox library
│   ├── lightbox-init.js             # Lightbox initialisatie
│   ├── masonry-init.js              # Masonry grid initialisatie
│   ├── mobile-menu.js               # Mobiel menu functionaliteit
│   └── sticky-header.js             # Sticky header functionaliteit
├── functions.php      # Hoofd functies bestand
├── header.php         # Header template
├── footer.php         # Footer template
├── index.php          # Hoofd template (blog archief)
├── page.php           # Pagina template
├── single.php         # Enkel bericht template
└── style.css          # Hoofd stylesheet (theme header)
```

## Belangrijke functionaliteit

### 1. Menu Plaatsing Opties
De theme ondersteunt twee menu layouts:
- **Links menu** (sidebar): Traditionele sidebar navigatie aan de linkerkant
- **Boven menu** (top): Horizontale navigatie bovenaan de pagina

Dit wordt beheerd via de theme opties in het WordPress admin panel.

### 2. Sticky Header
- Alleen beschikbaar wanneer "Menu boven" is geselecteerd
- Kan in-/uitgeschakeld worden via theme opties
- JavaScript: [js/sticky-header.js](js/sticky-header.js)
- Body class: `sticky-header` wordt toegevoegd wanneer actief

### 3. Mobiel Menu
- Hamburger menu voor mobiele weergave
- Geanimeerde toggle button met drie lijnen
- JavaScript: [js/mobile-menu.js](js/mobile-menu.js)
- CSS: [assets/css/mobile-menu.css](assets/css/mobile-menu.css)

### 4. Lightbox Functionaliteit
De theme gebruikt GLightbox voor professionele afbeeldingsweergave:
- Library: GLightbox v3.2.0
- Initialisatie: [js/lightbox-init.js](js/lightbox-init.js)
- Custom styling: [assets/css/lightbox-custom.css](assets/css/lightbox-custom.css)
- Optie om bijschriften te tonen/verbergen aan rechterkant
- Zoom functionaliteit is uitgeschakeld voor betere mobiele ervaring

**Recente fixes:**
- Mobiele lightbox weergave geoptimaliseerd (commit: 3b030d5)

### 5. Galerie Weergave
- Gebruikt WordPress Masonry voor grid layout
- imagesLoaded voor correcte afbeeldingsladen
- Initialisatie: [js/masonry-init.js](js/masonry-init.js)
- Styling: [assets/css/gallery.css](assets/css/gallery.css)
- Bijschriften functionaliteit: [inc/gallery-captions.php](inc/gallery-captions.php)

### 6. Social Media Integratie
- Configureerbaar via admin panel
- Ondersteunde platforms gedefinieerd in [inc/social-media.php](inc/social-media.php)
- Icons via Font Awesome
- Weergave locatie afhankelijk van menu plaatsing:
  - Links menu: onder navigatie in sidebar
  - Boven menu: in footer

### 7. Theme Opties (Admin Panel)
Toegankelijk via Dashboard → Mijn Werk Online

**Beschikbare opties:**
- Menu plaatsing (links/boven)
- Sticky header (alleen bij boven menu)
- Logo upload en breedte instelling
- Sitetitel en ondertitel weergave toggles
- Paginakoppen uitschakelen
- Footer credits uitschakelen
- Lightbox bijschriften toggle
- Social media links configuratie

**Settings locatie:** `mwo_options` in wp_options tabel

## Belangrijke code locaties

### Theme Setup (functions.php)
- **Regel 21-41**: `mwo_setup()` - Theme support en menu registratie
- **Regel 46-96**: `mwo_enqueue_assets()` - Scripts en styles laden
- **Regel 117-128**: `mwo_widgets_init()` - Sidebar registratie
- **Regel 165-246**: `mwo_register_settings()` - Admin opties registratie
- **Regel 426-464**: `mwo_sanitize_options()` - Input validatie voor opties

### Header (header.php)
- **Regel 8-22**: Theme opties ophalen en body classes instellen
- **Regel 34-60**: Mobiele header met hamburger menu
- **Regel 62-95**: Desktop branding (logo, titel, ondertitel)
- **Regel 97-105**: Primaire navigatie
- **Regel 107-131**: Social media icons (alleen bij links menu)

### Footer (footer.php)
- **Regel 15-53**: Footer content (alleen bij boven menu)
- **Regel 19-40**: Social media icons
- **Regel 42**: Copyright en credits
- **Regel 44-50**: Footer menu

## CSS Architectuur

De theme gebruikt modulaire CSS bestanden:
1. **layout.css** - Basis layout en grid systeem
2. **sidebar.css** - Sidebar/links menu styling
3. **gallery.css** - Galerie grid en afbeeldingen
4. **mobile-menu.css** - Mobiele navigatie
5. **lightbox-custom.css** - Custom lightbox overrides
6. **glightbox.min.css** - GLightbox library styles
7. **all.min.css** - Font Awesome icons

## JavaScript Dependencies

**WordPress bundled:**
- jQuery
- Masonry
- imagesLoaded

**Externe libraries (lokaal gehost):**
- GLightbox 3.2.0
- Font Awesome 6.5.1

## Git Repository

**Huidige branch:** main
**Recente commits:**
- 3b030d5: Mobiele lightbox fix
- 3e9f9e2: Mobiele weergave fix voor menu links
- 8405c84: Responsive menu
- 68d86a4: Zoomable uitgezet
- 955a5a6: Sticky header gefikst

## Development Notes

### Bij wijzigingen aan CSS:
- Modulaire bestanden in [assets/css/](assets/css/) aanpassen
- Versienummers updaten in [functions.php](functions.php) voor cache busting

### Bij wijzigingen aan JavaScript:
- Bestanden in [js/](js/) directory
- Versienummers updaten in [functions.php](functions.php)
- Let op dependencies bij wp_enqueue_script

### Bij toevoegen van nieuwe opties:
1. Nieuwe field toevoegen in `mwo_register_settings()` (functions.php:165)
2. Callback functie maken voor field rendering
3. Sanitization toevoegen in `mwo_sanitize_options()` (functions.php:426)

### Testing checklist:
- [ ] Desktop weergave (links menu)
- [ ] Desktop weergave (boven menu)
- [ ] Desktop weergave (boven menu + sticky header)
- [ ] Mobiele weergave (hamburger menu)
- [ ] Lightbox functionaliteit (desktop en mobiel)
- [ ] Galerie Masonry layout
- [ ] Social media links
- [ ] Logo upload en weergave

## Taal en Localisatie
- Text domain: 'mwo'
- Alle strings zijn vertaalbaar via __() en esc_html_e()
- Nederlandse vertalingen hardcoded in de code

## Browser Support
- Moderne browsers (Chrome, Firefox, Safari, Edge)
- Responsive design voor mobiele apparaten
- Touch-friendly navigatie en lightbox

## Performance Overwegingen
- Font Awesome en GLightbox lokaal gehost (niet via CDN)
- Retina-ready logo support
- Optimized image loading met imagesLoaded
- Lazy loading voor galleries via Masonry

## Wordpress Requirements
- Minimum versie: Niet gespecificeerd (maar gebruikt moderne WordPress features)
- PHP versie: 7.0+ (vanwege syntax)
- Theme ondersteunt: title-tag, post-thumbnails, html5, custom-logo, responsive-embeds

## Support en Contact
- Website: https://mijnwerkonline.nl
- Auteur: Bart Sallé

---

**Laatste update:** 14 oktober 2025
**Status:** Actief in ontwikkeling
