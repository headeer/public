# ✅ Naprawione Hovery - Widget Blog Sorting

_Data: 23.12.2025_

---

## 🎨 PROBLEM

WordPress i Elementor mają domyślne style dla buttonów:
```css
[type=button]:focus, [type=button]:hover,
[type=submit]:focus, [type=submit]:hover,
button:focus, button:hover {
    background-color: #c36; /* czerwony */
    color: #fff;
    text-decoration: none;
}
```

Te style nadpisywały nasze kolory KPG w widgecie.

---

## ✅ ROZWIĄZANIE

### 1. Utworzono Paletę Kolorów KPG

**Nowy plik:** `assets/css/_kpg-colors.css`

**Główne kolory:**
```css
:root {
  /* Primary */
  --kpg-yellow: #F9FF46;        /* Featured, highlights */
  --kpg-green: #0d8e67;         /* Success, accents */
  --kpg-light-green: #C0FFDD;   /* Success bg */
  
  /* Text */
  --kpg-dark: #404848;          /* Primary text (Gray-90) */
  --kpg-text-secondary: #566263; /* Gray-70 */
  --kpg-text-tertiary: #6f7b7c;  /* Gray-60 */
  --kpg-text-inactive: #a3afb0;  /* Gray-50 */
  
  /* Backgrounds */
  --kpg-bg-light: #f7f9f9;      /* Light bg */
  --kpg-bg-gray: #e3ebec;       /* Gray bg (Gray-20) */
  --kpg-bg-dark: #404848;       /* Dark sections */
  --kpg-bg-white: #ffffff;      /* White cards */
  
  /* Borders */
  --kpg-border-dark: #404848;
  --kpg-border-light: #a3afb0;
  --kpg-border-subtle: #e3ebec;
  
  /* States */
  --kpg-hover-bg: #e3ebec;      /* Hover backgrounds */
  --kpg-active-bg: #e3ebec;     /* Active backgrounds */
}
```

---

### 2. Nadpisano Domyślne Style WordPress

**W `blog-sorting.css` dodano:**

```css
/* Reset all default button styles within this widget */
.kpg_sorting-container button,
.kpg_sorting-container button[type="button"],
.kpg_sorting-container button[type="submit"] {
  background-color: transparent !important;
  background-image: none !important;
  border: none !important;
  box-shadow: none !important;
  text-shadow: none !important;
  color: inherit !important;
  /* ... reset wszystkich właściwości */
}

/* Hover states - tylko KPG kolory */
.kpg_sorting-container button:hover,
.kpg_sorting-container button:focus {
  background-color: transparent !important;
  color: inherit !important;
  text-decoration: none !important;
}

.kpg_sorting-container .kpg_sorting-option:hover,
.kpg_sorting-container .kpg_sorting-option:focus {
  background-color: #e3ebec !important; /* --kpg-gray-20 */
  color: #404848 !important; /* --kpg-dark */
  text-decoration: none !important;
}
```

---

### 3. Poprawione Stany Hover/Focus

**Przed:**
```css
.kpg_sorting-option:hover {
  background-color: #f7f9f9; /* za jasny */
}
```

**Po:**
```css
.kpg_sorting-option:hover {
  background-color: #e3ebec; /* --kpg-gray-20 - KPG standard */
  color: #404848; /* --kpg-dark */
}

.kpg_sorting-option.kpg_sorting-active {
  font-weight: 500;
  background-color: #e3ebec; /* --kpg-gray-20 */
  color: #404848; /* --kpg-dark */
}
```

---

### 4. Dodano Focus-Visible dla Keyboard Navigation

```css
/* Focus visible (keyboard navigation) */
.kpg_sorting-button:focus-visible {
  outline: 2px solid #404848 !important; /* --kpg-dark */
  outline-offset: 2px;
  background-color: transparent !important;
}

.kpg_sorting-option:focus-visible {
  outline: 2px solid #404848 !important;
  outline-offset: 2px;
  background-color: #e3ebec !important; /* --kpg-gray-20 */
}
```

---

## 🎨 PALETA KOLORÓW KPG - UŻYCIE

### Kiedy używać którego koloru:

**Żółty (#F9FF46):**
- Featured posts
- Główne highlights
- CTAs (call-to-action)
- Slider backgrounds

**Zielony (#0d8e67):**
- Success states
- Akcenty
- Linki (opcjonalnie)
- Ikony akcji

**Jasny zielony (#C0FFDD):**
- Success backgrounds
- Powiadomienia pozytywne
- Sekcja "Ważne" (opcjonalnie)

**Ciemny (#404848):**
- Główny tekst
- Active states
- Bordery
- Ikony

**Szary 70 (#566263):**
- Przyciski secondary
- Dark elements
- Footer text

**Szary 60 (#6f7b7c):**
- Secondary text
- Labels
- Placeholders

**Szary 50 (#a3afb0):**
- Inactive states
- Disabled elements
- Subtle borders

**Szary 20 (#e3ebec):**
- Backgrounds (cards, sections)
- **HOVER states** ← GŁÓWNE UŻYCIE
- **ACTIVE backgrounds** ← GŁÓWNE UŻYCIE
- Disabled backgrounds

**Jasny (#f7f9f9):**
- Page backgrounds
- Light sections

**Biały (#ffffff):**
- Cards
- Dropdowns
- Modals

---

## 🔧 TEMPLATE DLA WSZYSTKICH WIDGETÓW

**Użyj tego wzorca w każdym widgecie:**

```css
/* Import colors first */
@import '_kpg-colors.css';

/* Widget container */
.kpg_widget-container {
  /* ... */
}

/* Reset WordPress/Elementor button styles */
.kpg_widget-container button,
.kpg_widget-container button[type="button"],
.kpg_widget-container button[type="submit"],
.kpg_widget-container [type="button"],
.kpg_widget-container [type="submit"] {
  background-color: transparent !important;
  background-image: none !important;
  border: none !important;
  box-shadow: none !important;
  text-shadow: none !important;
  color: inherit !important;
  padding: 0 !important;
  margin: 0 !important;
  font-family: inherit !important;
  font-size: inherit !important;
  font-weight: inherit !important;
  line-height: inherit !important;
  text-decoration: none !important;
  text-transform: inherit !important;
  letter-spacing: inherit !important;
}

/* Hover/Focus - tylko KPG kolory */
.kpg_widget-container button:hover,
.kpg_widget-container button:focus,
.kpg_widget-container [type="button"]:hover,
.kpg_widget-container [type="button"]:focus,
.kpg_widget-container [type="submit"]:hover,
.kpg_widget-container [type="submit"]:focus {
  background-color: transparent !important;
  color: inherit !important;
  text-decoration: none !important;
  box-shadow: none !important;
}

/* Widget-specific hover (jeśli potrzebny) */
.kpg_widget-container .specific-element:hover {
  background-color: var(--kpg-hover-bg) !important; /* #e3ebec */
  color: var(--kpg-dark) !important; /* #404848 */
}
```

---

## 📋 CHECKLIST DLA KAŻDEGO NOWEGO WIDGETU

### CSS:
- [ ] Import `_kpg-colors.css` na początku
- [ ] Reset buttonów WordPress/Elementor
- [ ] Hover states z `--kpg-hover-bg` (#e3ebec)
- [ ] Active states z `--kpg-active-bg` (#e3ebec)
- [ ] Focus states z `--kpg-focus-outline` (#404848)
- [ ] Używać CSS variables gdzie możliwe
- [ ] `!important` tylko dla nadpisania WP/Elementor

### Kolory do użycia:
- [ ] Text: `--kpg-dark` (#404848)
- [ ] Labels: `--kpg-text-tertiary` (#6f7b7c)
- [ ] Inactive: `--kpg-text-inactive` (#a3afb0)
- [ ] Backgrounds: `--kpg-bg-gray` (#e3ebec)
- [ ] Hover: `--kpg-hover-bg` (#e3ebec)
- [ ] Borders: `--kpg-border-light` (#a3afb0)

---

## 🎯 CO ZOSTAŁO NAPRAWIONE W WIDGET #1

### Przed:
```css
.kpg_sorting-option:hover {
  background-color: #f7f9f9; /* za jasny, nie KPG */
}

/* Brak nadpisania WordPress defaults */
button:hover {
  background-color: #c36; /* czerwony WP */
  color: #fff;
}
```

### Po:
```css
/* Import palety */
@import '_kpg-colors.css';

/* Reset WP styles */
.kpg_sorting-container button:hover {
  background-color: transparent !important;
  color: inherit !important;
}

/* KPG hover */
.kpg_sorting-option:hover {
  background-color: #e3ebec !important; /* --kpg-gray-20 */
  color: #404848 !important; /* --kpg-dark */
}
```

---

## ✅ REZULTAT

**Teraz w Widget Blog Sorting:**
- ✅ Hover na opcjach: szary (#e3ebec), nie czerwony
- ✅ Focus keyboard: outline czarny (#404848)
- ✅ Active option: szary background (#e3ebec)
- ✅ Brak domyślnych styli WordPress
- ✅ Spójne z paletą KPG
- ✅ Wszystkie stany (hover, focus, active) zgodne z designem

---

## 📦 NOWE PLIKI

```
wp-content/plugins/kpg-elementor-widgets/
└── assets/
    └── css/
        ├── _kpg-colors.css ← NOWY (paleta kolorów)
        └── blog-sorting.css ← ZAKTUALIZOWANY (hovery naprawione)
```

---

## 🚀 TESTUJ TERAZ

1. Odśwież stronę WordPress (Ctrl+Shift+R)
2. Otwórz stronę z widgetem sortowania
3. Najedź na opcje w dropdown
4. Sprawdź czy hover jest **szary** (#e3ebec), nie czerwony
5. Sprawdź focus (Tab + Enter)

---

## 📝 UŻYCIE W PRZYSZŁYCH WIDGETACH

**Każdy nowy widget będzie:**
1. Importował `_kpg-colors.css`
2. Resetował domyślne style WP/Elementor
3. Używał tylko kolorów z palety KPG
4. Miał spójne hover states (#e3ebec)

**Przykład dla następnego widgetu (Breadcrumbs):**
```css
@import '_kpg-colors.css';

.kpg-breadcrumbs-container a:hover {
  color: var(--kpg-green) !important; /* #0d8e67 */
  text-decoration: underline !important;
}
```

---

_Hovery naprawione zgodnie z paletą kolorów KPG_
_Gotowe do testowania!_

