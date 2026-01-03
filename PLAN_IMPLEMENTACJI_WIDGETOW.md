# 🎯 Plan Implementacji 12 Widgetów Elementor - KPG

_Szczegółowa analiza promptów i plan implementacji_
_Data: 23.12.2025_

---

## 📋 EXECUTIVE SUMMARY

**Status analizy dokumentacji:**
- ✅ Przeanalizowano 122,173 linie
- ✅ Znaleziono 116 promptów
- ✅ Wyekstrahowano 72 kluczowe prompty
- ⚠️ Niektóre widgety nie mają jeszcze promptów w dokumentacji

**Widgety z pełną dokumentacją w promptach:**
1. ✅ Sortowanie bloga (prompty: #4, #13, #28)
2. ✅ Breadcrumbs (prompty: #51, #52)
3. ✅ Pojedynczy post na liście (prompty: #1-12)
4. ✅ Blog mobile (prompty: #1-12)
5. ✅ Paginacja (prompty: #18-27)
6. ✅ Team slider (prompty: #28-48)

**Widgety wymagające designu:**
7. ⚠️ Spis treści - BRAK promptów (trzeba stworzyć design)
8. ⚠️ Komentarze - BRAK promptów (trzeba stworzyć design)
9. ⚠️ Sekcja "Ważne" - BRAK promptów (trzeba stworzyć design)
10. ✅ O autorze (dół) - częściowo w promptach (widget "Articles From")
11. ✅ Meta autora (góra) - częściowo w promptach (post-meta-bar)
12. ⚠️ Treść bloga - BRAK promptów o generowaniu

---

## 🗂️ ANALIZA SZCZEGÓŁOWA KAŻDEGO WIDGETU

### WIDGET 1: Sortowanie Bloga (Dropdown "od najstarszego/najnowszego")

**Status:** ✅ Pełna dokumentacja w promptach

**Związane prompty:**
```
Prompt #4 (line 3983):
kpg-blog-sorting
tu dodaj jakis margin bottom 32px np

Prompt #13 (line 6278):
<div class="kpg_sorting-container">
  <div class="kpg_sorting-container-inner">
    <div class="kpg_sorting-label-wrapper">
      <span class="kpg_sorting-label">SORTOWANIE:</span>
    </div>
    <div class="kpg_sorting-dropdown" aria-expanded="false">
      <button class="kpg_sorting-button" type="button">
        <span class="kpg_sorting-selected">OD NAJNOWSZYCH</span>
        <svg class="kpg_sorting-icon" width="14" height="8">
          <path d="M0.63623 0.636719L6.63623 6.63672L12.6362 0.636719" stroke="#404848" stroke-width="1.8"/>
        </svg>
      </button>
      <ul class="kpg_sorting-menu" role="menu">
        <li><button data-sort="newest">OD NAJNOWSZYCH</button></li>
        <li><button data-sort="oldest">OD NAJSTARSZYCH</button></li>
      </ul>
    </div>
  </div>
</div>
```

**Wymagania techniczne:**
- Dropdown z opcjami "OD NAJNOWSZYCH" / "OD NAJSTARSZYCH"
- SVG arrow (14x8px) - `<path d="M0.63623 0.636719L6.63623 6.63672L12.6362 0.636719"`
- Margin bottom: 32px na mobile
- Label: "SORTOWANIE:" (font: DM Mono, uppercase)
- JavaScript obsługujący sortowanie (zmiana URL param `?sort=`)
- Accessibility: `role="menu"`, `aria-expanded`

**Kontrolki Elementor:**
- Enable/Disable sorting
- Default sort order (newest/oldest)
- Label text (edytowalny)
- Colors: label, button, arrow
- Typography: label, button text

**Design tokens:**
```css
/* Mobile */
label-color: #6f7b7c;
button-color: #404848;
font-family: "DM Mono";
font-size: 16px;
font-weight: 300/500;
margin-bottom: 8.5333vw; /* 32px */

/* Desktop */
[same but with desktop vw units]
```

**Pliki do utworzenia:**
- `widgets/blog-sorting.php`
- `assets/css/blog-sorting.css`
- `assets/js/blog-sorting.js`

---

### WIDGET 2: Breadcrumbs

**Status:** ✅ Pełna dokumentacja w promptach

**Związane prompty:**
```
Prompt #51 (line 13678):
http://kontrola-dotacji-oswiatowych.local/blog-test/ ten breadcrumbs zle dziala jestem tutaj powinienem byc na home / blog-test 

a jest:
<nav class="kpg-breadcrumbs" aria-label="breadcrumbs">
  <div class="kpg-breadcrumbs-inner">
    <span class="kpg-breadcrumbs-item kpg-breadcrumbs-item--current">home</span>
  </div>
</nav>

POWINNO BYĆ:
<nav class="kpg-breadcrumbs">
  <div class="kpg-breadcrumbs-inner">
    <a href="/" class="kpg-breadcrumbs-item kpg-breadcrumbs-item--type-home">home</a>
    <span class="kpg-breadcrumbs-separator">/</span>
    <span class="kpg-breadcrumbs-item kpg-breadcrumbs-item--current">blog-test</span>
  </div>
</nav>

Prompt #52 (line 14997):
http://kontrola-dotacji-oswiatowych.local/author/m.peczkowski/ 
breadcrumbs pokazuje tytuł posta zamiast imienia autora

POWINNO BYĆ:
home / Mateusz Pęczkowski
```

**Wymagania techniczne:**
- Działający na wszystkich typach stron:
  - Single post: home / post-title
  - Blog archive: home / blog-title
  - Author archive: home / author-full-name
  - Category: home / category-name
  - Page: home / page-title
- Separator: "/" między elementami
- Home zawsze jako link
- Ostatni element (current) bez linka
- Responsive (mobile + desktop)

**Istniejący widget do poprawy:**
✅ `widgets/breadcrumbs.php` - ISTNIEJE, wymaga poprawek

**Poprawki wymagane:**
1. Logika dla `is_home()` - pobranie tytułu strony ustawionej jako posts page
2. Logika dla `is_author()` - first_name + last_name zamiast display_name
3. Logika dla pojedynczych postów
4. Sprawdzenie specyficzności selektorów CSS

**Kontrolki Elementor:**
- Show/Hide home link
- Home text (default: "home")
- Separator (default: "/")
- Colors: links, current, separator
- Typography: font family, size, weight

**Design tokens:**
```css
/* Existing in breadcrumbs.php/css */
.kpg-breadcrumbs-item {
  text-transform: lowercase;
  font-family: "DM Mono";
}
.kpg-breadcrumbs-item--current {
  /* current page style */
}
```

**Status:** ✅ Widget istnieje - wymaga POPRAWEK zgodnie z promptami

---

### WIDGET 3: Pojedynczy Post na Liście Blogów (Desktop)

**Status:** ✅ Pełna dokumentacja w promptach

**Związane prompty:**
```
Prompt #1 (line 6):
Featured post structure z BLOG label, image, title, excerpt, author

Prompt #7 (line 5684):
import React from "react";
export default function Main() {
  return (
    <div className="main-container">
      <div className="frame">
        <span className="blog">BLOG</span>
      </div>
      <div className="image" />
      <span className="poufnosc-danych-sygnalistow">Poufność danych...</span>
      <div className="frame-1">
        <span>Od września 2024 roku każda szkoła...</span>
      </div>
      <div className="frame-2">
        <div className="ellipse" />
        <span className="mateusz-pieczkowski">Mateusz Pęczkowski • 07 lipiec 2025</span>
      </div>
      <div className="rectangle" />
    </div>
  );
}
first featured post of blog should look like this

Design CSS (lines 40-300):
.rectangle {
  width: 359px;
  height: 591px;
  background: url(...);
  overflow: visible auto;
}
.frame-3 {
  width: 343px;
  padding: 0 0 8px 0;
  border-top: 1px solid #404848;
}
.blog-4 {
  width: 54px;
  height: 14px;
  color: #484440;
  font-family: Nohemi;
  font-size: 20px;
  font-weight: 300;
  line-height: 14px;
  letter-spacing: 0.2px;
}
[pełny CSS z liniami 163-300]
```

**Wymagania techniczne:**

**FEATURED POST (żółty):**
- Background: `#F9FF46`
- Border-radius: `8px` (mobile: 2.1333vw)
- Struktura:
  1. Label "BLOG" (border-top: 1px, padding-bottom: 8px)
  2. Featured image (aspect ratio zachowany)
  3. Title (Nohemi, 26px, font-weight: 300)
  4. Excerpt (border-left: 4px, padding-left: 16px)
  5. Meta (avatar 32px + author • date)
  6. Separator line na dole (48px margin-top)

**REGULAR POST (biały/szary):**
- Background: `#ffffff` lub `#e3ebec`
- Thumbnail (74x56px na mobile)
- Title + excerpt (skrócony)
- Author meta

**LARGE POST (co 3. post):**
- Background: `#e3ebec`
- Większy obrazek
- Pełny excerpt
- Border-left na excerpt

**Kontrolki Elementor:**
- Post query (category, tags, exclude)
- Show featured (yes/no)
- Featured background color
- Show excerpt (yes/no)
- Excerpt length
- Show author meta (yes/no)
- Large post interval (default: 3)

**Pliki do utworzenia/poprawy:**
- ✅ `widgets/blog-archive.php` - ISTNIEJE, wymaga dopracowania
- ✅ `assets/css/blog-framework.css` - ISTNIEJE
- Potrzebne: separate widget dla SINGLE post item

---

### WIDGET 4: Cały Wygląd Strony /blogs na Mobile

**Status:** ✅ Pełna dokumentacja w promptach

**To jest MEGA-WIDGET łączący:**
1. Featured post (żółty)
2. Search bar
3. Sorting dropdown
4. Post list
5. Pagination

**Związane prompty:**
```
Wszystkie prompty #1-27 (blog archive)

Główna struktura (line 3485):
<div class="kpg-blog-archive-container">
  <!-- Featured Post (żółty #F9FF46) -->
  <div class="kpg-featured-post">
    <div class="kpg-featured-post-label">
      <span>BLOG</span>
    </div>
    <div class="kpg-featured-post-image">...</div>
    <h2 class="kpg-featured-post-title">...</h2>
    <div class="kpg-featured-post-excerpt">...</div>
    <div class="kpg-featured-post-meta">
      <div class="kpg-featured-post-avatar">...</div>
      <span>author • date</span>
    </div>
    <div class="kpg-featured-post-separator"></div>
  </div>
  
  <!-- Search Bar -->
  <div class="kpg-blog-search">
    <input type="search" placeholder="Szukaj">
    <div class="kpg-blog-search-results"></div>
  </div>
  
  <!-- Sorting -->
  <div class="kpg-blog-sorting">
    <span class="kpg-blog-sorting-label">SORTOWANIE:</span>
    <div class="kpg-blog-sorting-select">...</div>
  </div>
  
  <!-- Post List -->
  <div class="kpg-post-list">
    <!-- 2 regular posts -->
    <!-- 1 large post (3rd) -->
    <!-- 2 more regular posts -->
    <!-- repeat... -->
  </div>
  
  <!-- Pagination -->
  <div class="kpg-blog-pagination">...</div>
</div>
```

**Wymiary mobile (base: 375px):**
```css
.kpg-blog-archive-container {
  width: 100%;
  max-width: 100vw;
  background: #f7f9f9;
}

.kpg-featured-post {
  width: 100%;
  max-width: 100vw;
  padding: 4.2667vw 2.1333vw; /* 16px 8px */
  background: #F9FF46;
  border-radius: 2.1333vw; /* 8px */
}

.kpg-featured-post-label {
  border-top: 0.2667vw solid #404848; /* 1px */
  padding-bottom: 2.1333vw; /* 8px */
}

.kpg-featured-post-label-text {
  font-family: Nohemi;
  font-size: 5.3333vw; /* 20px */
  font-weight: 300;
  line-height: 3.7333vw; /* 14px */
  color: #484440;
  letter-spacing: 0.0533vw; /* 0.2px */
}

.kpg-featured-post-image {
  width: 100%;
  height: 68.5333vw; /* 257px */
  margin: 2.1333vw 0 0 0; /* 8px top */
  border-radius: 1.8267vw; /* 6.853px */
}

.kpg-featured-post-title {
  font-family: Nohemi;
  font-size: 6.9333vw; /* 26px */
  font-weight: 300;
  line-height: 8.32vw; /* 31.2px */
  color: #404848;
  margin: 8.5333vw 0 0 0; /* 32px top */
  padding: 0 2.1333vw; /* 0 8px */
}

.kpg-featured-post-excerpt {
  margin: 8.5333vw 0 0 0; /* 32px top */
  padding: 0 0 0 4.2667vw; /* 0 0 0 16px */
  border-left: 1.0667vw solid #404848; /* 4px */
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px */
  line-height: 6.4vw; /* 24px */
}

.kpg-featured-post-meta {
  display: flex;
  gap: 3.2vw; /* 12px */
  margin: 12.8vw 0 0 0; /* 48px top */
  padding: 0 2.1333vw; /* 0 8px */
}

.kpg-featured-post-avatar {
  width: 8.5333vw; /* 32px */
  height: 8.5333vw; /* 32px */
  border-radius: 50%;
}

.kpg-featured-post-author-date {
  font-family: "DM Mono";
  font-size: 3.2vw; /* 12px */
  font-weight: 300;
  line-height: 2.1333vw; /* 8px */
  letter-spacing: 0.16vw; /* 0.6px */
  text-transform: uppercase;
}

.kpg-featured-post-separator {
  width: 100%;
  height: 0.1333vw; /* 0.5px */
  background: #404848;
  margin: 12.8vw 0 0 0; /* 48px top */
}

/* Search bar */
.kpg-blog-search {
  width: 91.4667vw; /* 343px */
  height: 11.4667vw; /* 43px */
  margin: 8.5333vw 0 0 4.2667vw; /* 32px 0 0 16px */
  padding: 4.2667vw; /* 16px */
  background: #ffffff;
  border: 1px solid #e3ebec;
  border-radius: 2.1333vw; /* 8px */
}

/* Post list */
.kpg-post-list {
  width: 100%;
  max-width: 100vw;
  padding: 0 4.2667vw; /* 0 16px */
  gap: 4.2667vw; /* 16px */
}

.kpg-post-list-item {
  display: flex;
  gap: 4.2667vw; /* 16px */
  width: 87.2vw; /* 327px */
}

.kpg-post-list-item-image {
  width: 19.7333vw; /* 74px */
  height: 14.9333vw; /* 56px */
  border-radius: 0.5333vw; /* 2px */
}

.kpg-post-list-item-title {
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px */
  font-weight: 300;
  line-height: 6.4vw; /* 24px */
  color: #404848;
}

.kpg-post-large {
  width: 95.7333vw; /* 359px */
  min-height: 131.7333vw; /* 494px */
  background: #e3ebec;
  border-radius: 2.1333vw; /* 8px */
}
```

**Kontrolki Elementor:**
- Posts per page
- Show featured (yes/no)
- Featured post ID (auto = latest, or select)
- Show search (yes/no)
- Show sorting (yes/no)
- Show pagination (yes/no)
- Large post interval (every Nth post)
- Category filter
- Tag filter
- Author filter
- Exclude post IDs

**Istniejący widget:**
✅ `widgets/blog-archive.php` - ISTNIEJE

**Wymagane poprawki:**
1. Zastosować DOKŁADNIE wszystkie style z promptu #1 i #7
2. Featured post na górze z żółtym tłem
3. Separator po featured post
4. Co 3. post jako `.kpg-post-large`
5. 100% width, max-width 100vw na wszystkim
6. Placeholder dla postów bez obrazka

---

### WIDGET 5: Paginacja

**Status:** ✅ Pełna dokumentacja w promptach

**Związane prompty:**
```
Prompt #11 (line 6175):
<svg width="32" height="32">
  <path d="M12 24L20 16L12 8.00001" stroke="#6F7B7C"/>
</svg>
add these 2x instead of arrow right on pagination 
width: 48px; height: 32px; border-radius: 8px;
background: var(--Gray-20, #E3EBEC);

Prompt #12 (line 6266):
podwojna ma byc 2 oboj siebie te same svg

Prompt #13 (line 8801):
dobra uzztyj po prpstu tego svg zamiast 2 
<svg width="40" height="32" viewBox="0 0 40 32">
  <path d="M20 24L28 16L20 8" stroke="#252B2B"/>
  <path d="M12 24L20 16L12 8" stroke="#252B2B"/>
</svg>

Prompt #22 (line 8351):
active: color: var(--Gray-90, #404848);
font-family: "DM Mono"; font-size: 16px;
font-weight: 300; line-height: 160%;
text-transform: uppercase;

non active: color: var(--Gray-50, #A3AFB0);
[same typography]

Prompt #24 (line 8626):
is this correct way to do it? what if we are on 4 page will it work? is it dynamic

Odpowiedź: Musi być DYNAMICZNE, zawsze pokazywać current page
Format: 01 02 03 ... current ... max
```

**Design spec - DOKŁADNY:**

**Mobile:**
```css
.kpg-blog-pagination {
  display: flex;
  gap: 11.2vw; /* 42px / 375px */
  width: 67.4667vw; /* 253px */
  margin: 17.0667vw 0 0 28.2667vw; /* 64px 0 0 106px */
}

.kpg-blog-pagination-numbers {
  display: flex;
  gap: 4.2667vw; /* 16px */
}

.kpg-blog-pagination-item {
  font-family: "DM Mono";
  font-size: 3.2vw; /* 12px */
  font-weight: 300;
  line-height: 2.1333vw; /* 8px */
  letter-spacing: 0.16vw; /* 0.6px */
  color: #a3afb0; /* inactive */
}

.kpg-blog-pagination-item.active {
  color: #404848;
}

.kpg-blog-pagination-separator {
  width: 10.4vw; /* 39px */
  height: 0.2667vw; /* 1px */
  background: #e3ebec;
}

.kpg-blog-pagination-arrow {
  width: 16vw; /* 60px */
  height: 8.5333vw; /* 32px */
  border-radius: 2.1333vw; /* 8px */
  background: #E3EBEC;
  display: flex;
  align-items: center;
  justify-content: center;
}

.kpg-blog-pagination-arrow svg {
  width: 10.6667vw; /* 40px */
  height: 8.5333vw; /* 32px */
}
```

**Desktop (base: 1696px):**
```css
.kpg-blog-pagination {
  gap: 2.4764vw; /* 42px */
  width: 14.9175vw; /* 253px */
  margin: 3.7736vw auto 0; /* 64px top, centered */
}

.kpg-blog-pagination-item {
  font-size: 0.9434vw; /* 16px */
  line-height: 160%; /* 25.6px */
  text-transform: uppercase;
}

.kpg-blog-pagination-arrow {
  width: 3.5377vw; /* 60px */
  height: 1.8868vw; /* 32px */
  border-radius: 0.4717vw; /* 8px */
}
```

**Separator przed paginacją (desktop only):**
```css
.kpg-blog-separator {
  width: 73.1132vw; /* 1240px / 1696px */
  height: 0.0295vw; /* 0.5px */
  background: #A3AFB0;
  margin: 0 auto;
}
```

**Logika paginacji:**
```
Strona 1 z 10: 01 02 03 ... 10
Strona 2 z 10: 01 02 03 ... 10
Strona 3 z 10: 01 02 03 ... 10
Strona 4 z 10: 01 02 03 ... 04 ... 10
Strona 5 z 10: 01 02 03 ... 05 ... 10
Strona 10 z 10: 01 02 03 ... 10
```

**Kontrolki Elementor:**
- Show pagination (yes/no)
- Items per page
- Show page numbers (yes/no)
- Show arrows (yes/no)
- Arrow color
- Active color
- Inactive color

**Pliki:**
- Part of `blog-archive.php` widget
- Może być wyodrębniony jako separate widget

---

### WIDGET 6: Team Slider (Desktop + Mobile)

**Status:** ✅ BARDZO szczegółowa dokumentacja (24 prompty!)

**Związane prompty:**
```
Prompt #28-48 (lines 9578-12546) - BARDZO DUŻO!

Kluczowe:
1. Border pod tytułem (8px mobile, 16px desktop)
2. Nawigacja NAD tytułem na mobile (CSS order)
3. Object-position tylko desktop (data-attribute + JS)
4. Mobile: width: 343px, height: 457px, aspect-ratio: 343/457
5. Min-height: 427px dla aktywnego slajdu
6. SpaceBetween: 30px między slajdami
7. Wszystkie teksty (oprócz tytułu i stanowiska):
   font-family: Nohemi; font-size: 16px; line-height: 24px;
8. Text-right ukryty domyślnie, pokazuje się po kliknięciu
9. Max-height dynamiczny: scrollHeight + 20px buffer
10. Animacja: 0.8s cubic-bezier(0.4, 0, 0.2, 1) + opacity 0.6s
```

**Struktura HTML (DOKŁADNA):**
```html
<div class="kpg-team-slider-container">
  <div class="kpg-team-slider-wrapper">
    <!-- Header -->
    <div class="kpg-team-slider-header">
      <h2 class="kpg-team-slider-title">TEAM</h2>
      <hr>
    </div>
    
    <!-- Main Slider -->
    <div class="kpg-team-slider-main">
      <!-- Swiper -->
      <div class="kpg-team-slider-swiper swiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="kpg-team-slider-main-image-wrapper">
              <div class="kpg-team-slider-main-image">
                <img src="..." data-object-position="center calc(50% + 50px)">
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Thumbnails (desktop only) -->
      <div class="kpg-team-slider-thumbnails">
        <div class="kpg-team-slider-thumb">...</div>
      </div>
    </div>
    
    <!-- Content -->
    <div class="kpg-team-slider-content">
      <div class="kpg-team-slider-slide-content active">
        <!-- Top Section -->
        <div class="kpg-team-slider-top-section">
          <!-- Navigation (mobile: order: 1) -->
          <div class="kpg-team-slider-navigation">
            <button class="kpg-team-slider-arrow-prev">
              <svg width="48" height="32">...</svg>
            </button>
            <button class="kpg-team-slider-arrow-next">
              <svg width="48" height="32">...</svg>
            </button>
          </div>
          
          <!-- Name Section (mobile: order: 2) -->
          <div class="kpg-team-slider-name-section">
            <h3 class="kpg-team-slider-name">Imię Nazwisko</h3>
            <span class="kpg-team-slider-job-title">STANOWISKO</span>
          </div>
          
          <!-- Intro Text (mobile: order: 3) -->
          <div class="kpg-team-slider-intro-text">
            Tekst wprowadzający z text-indent...
          </div>
        </div>
        
        <!-- Bottom Section -->
        <div class="kpg-team-slider-bottom-section">
          <!-- Text Section Desktop -->
          <div class="kpg-team-slider-text-section">
            <div class="kpg-team-slider-text kpg-team-slider-text-left">
              Tekst lewej kolumny...
            </div>
            <div class="kpg-team-slider-text kpg-team-slider-text-right">
              Tekst prawej kolumny (desktop only)...
            </div>
          </div>
          
          <!-- Text Section Mobile -->
          <div class="kpg-team-slider-text-section-mobile">
            <div class="kpg-team-slider-text kpg-team-slider-text-left">
              Collapsed text (max 20 lines)...
            </div>
            <div class="kpg-team-slider-text kpg-team-slider-text-right">
              Hidden by default...
            </div>
          </div>
          
          <!-- Expand Button Mobile -->
          <div class="kpg-team-slider-expand-mobile">
            <button class="kpg-team-slider-expand-btn">
              <span class="kpg-team-slider-btn-text">zobacz więcej</span>
              <span class="kpg-team-slider-btn-icon">+</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

**Istniejący widget:**
✅ `widgets/team-slider.php` - ISTNIEJE, kompletny
✅ `assets/css/team-slider.css` - ISTNIEJE
✅ `assets/js/team-slider.js` - ISTNIEJE

**Status:** ✅ GOTOWY - tylko małe poprawki (są już w plikach)

---

### WIDGET 7: Spis Treści (TOC) na Stronie /blogs/id

**Status:** ⚠️ BRAK promptów w dokumentacji

**Co trzeba stworzyć:**

**Struktura HTML (standard):**
```html
<div class="kpg-toc-container">
  <div class="kpg-toc-header">
    <h3 class="kpg-toc-title">SPIS TREŚCI</h3>
  </div>
  <nav class="kpg-toc-nav">
    <ul class="kpg-toc-list">
      <li class="kpg-toc-item">
        <a href="#heading-1" class="kpg-toc-link">
          <span class="kpg-toc-number">01</span>
          <span class="kpg-toc-text">Pierwsza sekcja</span>
        </a>
      </li>
      <li class="kpg-toc-item kpg-toc-item--h3">
        <a href="#heading-2" class="kpg-toc-link">
          <span class="kpg-toc-number">02</span>
          <span class="kpg-toc-text">Pod-sekcja</span>
        </a>
      </li>
    </ul>
  </nav>
</div>
```

**Style (dopasowane do designu KPG):**
```css
/* Mobile */
.kpg-toc-container {
  width: 100%;
  padding: 4.2667vw; /* 16px */
  background: #E3EBEC;
  border-radius: 2.1333vw; /* 8px */
}

.kpg-toc-title {
  font-family: Nohemi;
  font-size: 5.3333vw; /* 20px */
  font-weight: 300;
  line-height: 3.7333vw; /* 14px */
  color: #484440;
  border-top: 0.2667vw solid #404848;
  padding: 0 0 2.1333vw 0; /* 0 0 8px 0 */
}

.kpg-toc-item {
  padding: 2.1333vw 0; /* 8px 0 */
  border-bottom: 0.1333vw solid #a3afb0; /* 0.5px */
}

.kpg-toc-link {
  display: flex;
  gap: 2.1333vw; /* 8px */
  color: #404848;
  text-decoration: none;
}

.kpg-toc-number {
  font-family: "DM Mono";
  font-size: 3.2vw; /* 12px */
  font-weight: 300;
  min-width: 5.3333vw; /* 20px */
}

.kpg-toc-text {
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px */
  font-weight: 300;
  line-height: 6.4vw; /* 24px */
}

/* Desktop (base: 1696px) */
.kpg-toc-container {
  width: 23.1132vw; /* ~392px */
  padding: 0.9434vw; /* 16px */
  position: sticky;
  top: 2.3585vw; /* 40px */
}
```

**Funkcjonalność:**
- Auto-generowanie z H2, H3 w treści posta
- Smooth scroll do sekcji
- Active state dla aktualnej sekcji (scroll spy)
- Hierarchia (H2 jako główne, H3 jako podpunkty)
- Numery 01, 02, 03...

**Kontrolki Elementor:**
- Title text (default: "SPIS TREŚCI")
- Heading levels to include (H2, H3, H4)
- Show numbers (yes/no)
- Sticky on desktop (yes/no)
- Sticky offset
- Smooth scroll (yes/no)

**Pliki do utworzenia:**
- `widgets/table-of-contents.php` - NOWY
- `assets/css/table-of-contents.css` - NOWY
- `assets/js/table-of-contents.js` - NOWY

---

### WIDGET 8: System Komentarzy z Odpowiedziami

**Status:** ⚠️ BRAK promptów w dokumentacji

**Co trzeba stworzyć:**

**Struktura HTML (standard WordPress + custom design):**
```html
<div class="kpg-comments-container">
  <!-- Header -->
  <div class="kpg-comments-header">
    <h3 class="kpg-comments-title">KOMENTARZE</h3>
    <span class="kpg-comments-count">(12)</span>
  </div>
  
  <!-- Comment List -->
  <div class="kpg-comments-list">
    <!-- Single Comment -->
    <div class="kpg-comment">
      <div class="kpg-comment-avatar">
        <img src="..." alt="...">
      </div>
      <div class="kpg-comment-body">
        <div class="kpg-comment-meta">
          <span class="kpg-comment-author">Jan Kowalski</span>
          <span class="kpg-comment-date">23.12.2025</span>
        </div>
        <div class="kpg-comment-text">
          Treść komentarza...
        </div>
        <button class="kpg-comment-reply-btn">Odpowiedz</button>
      </div>
    </div>
    
    <!-- Nested Reply -->
    <div class="kpg-comment kpg-comment--reply">
      [same structure, indented]
    </div>
  </div>
  
  <!-- Comment Form -->
  <div class="kpg-comment-form-container">
    <h4 class="kpg-comment-form-title">DODAJ KOMENTARZ</h4>
    <form class="kpg-comment-form">
      <div class="kpg-comment-form-row">
        <input type="text" name="author" placeholder="IMIĘ" required>
        <input type="email" name="email" placeholder="EMAIL" required>
      </div>
      <textarea name="comment" placeholder="WIADOMOŚĆ" required></textarea>
      <div class="kpg-comment-form-privacy">
        <input type="checkbox" id="privacy" required>
        <label for="privacy">Akceptuję politykę prywatności</label>
      </div>
      <button type="submit" class="kpg-comment-submit">WYŚLIJ</button>
    </form>
  </div>
</div>
```

**Style (dopasowane do designu KPG):**
```css
/* Zgodne z formularze contact (cursor_padding lines 6000-6700) */
.kpg-comments-container {
  width: 100%;
  background: #404848; /* dark like contact form */
  padding: 4.2667vw; /* 16px mobile */
  border-radius: 2.1333vw; /* 8px */
}

.kpg-comments-title {
  font-family: Nohemi;
  font-size: 5.3333vw; /* 20px mobile */
  font-weight: 300;
  color: #f7f9f9; /* light text on dark bg */
  border-top: 0.2667vw solid #f7f9f9;
  padding-bottom: 2.1333vw;
}

.kpg-comment {
  display: flex;
  gap: 3.2vw; /* 12px */
  padding: 4.2667vw 0; /* 16px 0 */
  border-bottom: 0.1333vw solid #6f7b7c;
}

.kpg-comment--reply {
  margin-left: 8.5333vw; /* 32px indent */
}

.kpg-comment-avatar {
  width: 8.5333vw; /* 32px */
  height: 8.5333vw;
  border-radius: 50%;
}

.kpg-comment-author {
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px */
  font-weight: 300;
  color: #f7f9f9;
}

.kpg-comment-date {
  font-family: "DM Mono";
  font-size: 3.2vw; /* 12px */
  color: #a3afb0;
}

.kpg-comment-text {
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px */
  line-height: 6.4vw; /* 24px */
  color: #f7f9f9;
}

.kpg-comment-reply-btn {
  font-family: "DM Mono";
  font-size: 3.2vw; /* 12px */
  font-weight: 300;
  color: #a3afb0;
  text-transform: uppercase;
  background: transparent;
  border: none;
  cursor: pointer;
}

/* Comment Form (similar to contact form lines 6000+) */
.kpg-comment-form input,
.kpg-comment-form textarea {
  background: transparent;
  border: none;
  border-top: 0.1333vw solid #a3afb0;
  padding: 4.2667vw 0 2.1333vw; /* 16px 0 8px */
  color: #f7f9f9;
  font-family: Nohemi;
  font-size: 3.2vw; /* 12px */
}

.kpg-comment-submit {
  width: 100%;
  padding: 3.2vw; /* 12px */
  background: #566263;
  border-radius: 2.1333vw; /* 8px */
  color: #f7f9f9;
  font-family: "DM Mono";
  font-size: 3.2vw; /* 12px */
  text-transform: uppercase;
}
```

**Funkcjonalność:**
- Wyświetlanie komentarzy z WordPress (wp_list_comments)
- Nested replies (odpowiedzi na komentarze)
- AJAX submit (bez przeładowania strony)
- Walidacja (required fields)
- Privacy checkbox
- Gravatar / auutor.png dla avatarów
- Sortowanie (najnowsze na górze)

**Kontrolki Elementor:**
- Show comments (yes/no)
- Comments per page
- Show comment form (yes/no)
- Allow replies (yes/no)
- Max nesting level
- Require approval (yes/no)
- Show avatars (yes/no)
- Default avatar (select from media)
- Form background color
- Text color
- Submit button color

**Pliki do utworzenia:**
- `widgets/comments.php` - NOWY
- `assets/css/comments.css` - NOWY
- `assets/js/comments.js` - NOWY

**Integracja z WordPress:**
```php
// Use native WordPress functions
comments_template();
wp_list_comments();
comment_form();

// Custom callback for comment display
wp_list_comments(array(
  'callback' => array($this, 'custom_comment_html'),
  'style' => 'div',
  'avatar_size' => 32,
));
```

---

### WIDGET 9: Sekcja "Ważne" na Stronie Bloga

**Status:** ⚠️ BRAK promptów w dokumentacji

**Co trzeba stworzyć (na podstawie stylu projektu):**

**Struktura HTML:**
```html
<div class="kpg-important-section">
  <div class="kpg-important-header">
    <span class="kpg-important-icon">!</span>
    <h3 class="kpg-important-title">WAŻNE</h3>
  </div>
  <div class="kpg-important-content">
    <p>Treść sekcji ważne - najważniejsze informacje z artykułu...</p>
  </div>
</div>
```

**Style (dopasowane do designu KPG):**
```css
/* Mobile */
.kpg-important-section {
  width: 91.4667vw; /* 343px */
  padding: 4.2667vw; /* 16px */
  margin: 8.5333vw 4.2667vw; /* 32px 16px */
  background: #C0FFDD; /* light green like in contact success */
  border-radius: 2.1333vw; /* 8px */
  border-left: 1.0667vw solid #0d8e67; /* 4px - accent */
}

.kpg-important-header {
  display: flex;
  align-items: center;
  gap: 2.1333vw; /* 8px */
  margin-bottom: 3.2vw; /* 12px */
}

.kpg-important-icon {
  width: 8.5333vw; /* 32px */
  height: 8.5333vw;
  border-radius: 50%;
  background: #0d8e67;
  color: #ffffff;
  font-family: Nohemi;
  font-size: 5.3333vw; /* 20px */
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.kpg-important-title {
  font-family: Nohemi;
  font-size: 5.3333vw; /* 20px */
  font-weight: 300;
  color: #0d8e67;
  text-transform: uppercase;
}

.kpg-important-content {
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px */
  line-height: 6.4vw; /* 24px */
  color: #404848;
}

/* Desktop (base: 1696px) */
.kpg-important-section {
  width: 73.1132vw; /* 1240px - max content width */
  padding: 1.8868vw; /* 32px */
  margin: 3.7736vw auto; /* 64px auto */
}
```

**Funkcjonalność:**
- Edytowalna treść w Elementor
- Optional ikona (! lub custom)
- Sticky na desktop (optional)

**Kontrolki Elementor:**
- Content (WYSIWYG editor)
- Show icon (yes/no)
- Icon type (exclamation / custom image)
- Background color
- Border color
- Text color
- Title text (default: "WAŻNE")

**Pliki do utworzenia:**
- `widgets/important-section.php` - NOWY
- `assets/css/important-section.css` - NOWY

---

### WIDGET 10: O Autorze (Dół Strony Bloga)

**Status:** ✅ Częściowo w promptach (widget "Articles From")

**Związane prompty:**
```
Prompt #59 (line 13900):
widget zrob musimy do artykuly od dac 
[React component + CSS]
display: flex;
width: 385px;
padding: 16px 16px 32px 16px;
align-items: flex-start;
gap: 32px;

Structure:
<div className="main-container">
  <div className="frame-1">
    <div className="frame-2">
      <span className="articles-from">Artykuły od:</span>
    </div>
    <div className="image" /> {/* author photo */}
    <div className="frame-3">
      <div className="frame-4">
        <div className="frame-5">
          <div className="frame-6">
            <span className="mateusz-peczkowski">Mateusz Pęczkowski</span>
            <span className="radca-prawny">RADCA PRAWNY</span>
          </div>
        </div>
        <div className="frame-7">
          <span className="radca-prawny-krakowska-akademia">
            Radca Prawny, absolwent Krakowskiej Akademii im. Andrzeja Frycza Modrzewskiego...
          </span>
        </div>
      </div>
      <div className="frame-8">
        <span className="linkedin">LINKEDIN</span>
        <span className="facebook">FACEBOOK </span>
      </div>
    </div>
  </div>
</div>

dynamiczne z profilu wszystko 
```

**Istniejący widget:**
✅ `widgets/articles-from.php` - UTWORZONY W SESJI

**Status:** ✅ GOTOWY - sprawdzić zgodność 1:1 z designem

---

### WIDGET 11: Meta Autora (Góra - Data i Udostępnienie)

**Status:** ✅ Częściowo w promptach

**Związane prompty:**
```
Prompt #49 (line 13632):
@post-meta-bar.php (261) zamiast tego wyswietl imie i nazwisko w kazdym miesjcu tak ma byc

Rozwiązanie: first_name + last_name
```

**Istniejący widget:**
✅ `widgets/post-meta-bar.php` - ISTNIEJE

**Struktura (z dokumentacji):**
```html
<div class="kpg-post-meta-bar">
  <div class="kpg-post-meta-avatar">
    <img src="auutor.png" alt="Author">
  </div>
  <div class="kpg-post-meta-info">
    <span class="kpg-post-meta-author">Mateusz Pęczkowski</span>
    <span class="kpg-post-meta-date">23.12.2025</span>
  </div>
  <div class="kpg-post-meta-share">
    <button class="kpg-share-btn" data-platform="facebook">
      <svg>...</svg>
    </button>
    <button class="kpg-share-btn" data-platform="twitter">
      <svg>...</svg>
    </button>
    <button class="kpg-share-btn" data-platform="linkedin">
      <svg>...</svg>
    </button>
  </div>
</div>
```

**Wymagane poprawki:**
1. ✅ Full name (first + last) - ZROBIONE
2. Dodać przyciski share (Facebook, Twitter, LinkedIn)
3. Share functionality (native share API + fallback)

**Status:** ⚠️ Wymaga ROZBUDOWY (dodanie share buttons)

---

### WIDGET 12: Treść Bloga + Prompt do Generowania

**Status:** ⚠️ BRAK promptów w dokumentacji

**Co to ma robić:**
- Wyświetlać `the_content()` WordPress
- Stylować headingi, paragrafy, listy, cytaty zgodnie z designem KPG
- Opcjonalnie: Template/prompt do generowania treści przez AI

**Struktura:**
```html
<div class="kpg-blog-content">
  <div class="kpg-blog-content-inner">
    <?php the_content(); ?>
  </div>
</div>
```

**Style dla content:**
```css
.kpg-blog-content {
  width: 100%;
  max-width: 73.1132vw; /* 1240px desktop */
  margin: 0 auto;
}

.kpg-blog-content-inner {
  font-family: Nohemi;
  font-size: 4.2667vw; /* 16px mobile */
  line-height: 6.4vw; /* 24px */
  color: #404848;
}

.kpg-blog-content h2 {
  font-family: Nohemi;
  font-size: 6.9333vw; /* 26px mobile */
  font-weight: 300;
  margin: 8.5333vw 0 4.2667vw; /* 32px 0 16px */
  color: #404848;
}

.kpg-blog-content h3 {
  font-family: Nohemi;
  font-size: 5.3333vw; /* 20px mobile */
  font-weight: 300;
  margin: 6.4vw 0 3.2vw; /* 24px 0 12px */
}

.kpg-blog-content p {
  margin-bottom: 4.2667vw; /* 16px */
}

.kpg-blog-content ul,
.kpg-blog-content ol {
  margin: 4.2667vw 0;
  padding-left: 6.4vw; /* 24px */
}

.kpg-blog-content li {
  margin-bottom: 2.1333vw; /* 8px */
}

.kpg-blog-content blockquote {
  border-left: 1.0667vw solid #404848; /* 4px */
  padding-left: 4.2667vw; /* 16px */
  margin: 8.5333vw 0; /* 32px 0 */
  font-style: italic;
  color: #566263;
}

.kpg-blog-content a {
  color: #0d8e67;
  text-decoration: underline;
}

.kpg-blog-content img {
  max-width: 100%;
  height: auto;
  border-radius: 2.1333vw; /* 8px */
  margin: 4.2667vw 0; /* 16px 0 */
}
```

**Kontrolki Elementor:**
- (To może być tylko CSS styling, nie widget)
- Lub: WYSIWYG editor dla custom content

**Pliki do utworzenia:**
- `assets/css/blog-content.css` - NOWY (global styling)
- Możliwe: `widgets/blog-content.php` - jeśli ma być edytowalny content

---

## 📊 PODSUMOWANIE ANALIZY

### Widgety z pełną dokumentacją (gotowe do implementacji):
1. ✅ Sortowanie bloga - 100% spec z promptów
2. ✅ Breadcrumbs - 100% spec + istniejący widget do poprawy
3. ✅ Pojedynczy post - 100% spec (featured + regular + large)
4. ✅ Blog mobile - 100% spec (kompletny layout)
5. ✅ Paginacja - 100% spec (dokładne wymiary, SVG, logika)
6. ✅ Team slider - 100% spec (24 prompty!)

### Widgety istniejące do poprawy:
- ✅ `blog-archive.php` - dostosować do promptów 1:1
- ✅ `breadcrumbs.php` - poprawić logikę
- ✅ `articles-from.php` - sprawdzić zgodność z promptem #59
- ✅ `post-meta-bar.php` - dodać share buttons

### Widgety do utworzenia od zera:
- ⚠️ `table-of-contents.php` - NOWY (brak promptów, design do stworzenia)
- ⚠️ `comments.php` - NOWY (brak promptów, design do stworzenia)
- ⚠️ `important-section.php` - NOWY (brak promptów, design do stworzenia)

### CSS do utworzenia:
- ⚠️ `blog-content.css` - global styling dla treści postów

---

## 🚀 PLAN IMPLEMENTACJI - KOLEJNOŚĆ

### FAZA 1: Poprawki istniejących widgetów (najpierw)
**Czas: ~2-3 godziny**

1. **Breadcrumbs** (30 min)
   - Poprawić logikę dla is_home(), is_author()
   - Full name dla autorów
   - Testy na różnych typach stron

2. **Blog Archive** (60 min)
   - Featured post 1:1 z promptem #7
   - Żółte tło #F9FF46
   - Label "BLOG" z border-top
   - Separator na dole featured
   - Co 3. post jako large
   - 100vw compliance
   - Placeholder dla postów bez obrazka

3. **Post Meta Bar** (30 min)
   - Dodać share buttons
   - Share functionality
   - Zachować full name

4. **Articles From** (30 min)
   - Sprawdzić zgodność 1:1 z promptem #59
   - Wszystkie wymiary zgodne z designem

### FAZA 2: Nowe widgety z pełną specyfikacją
**Czas: ~3-4 godziny**

5. **Blog Sorting Widget** (45 min)
   - Standalone widget (teraz część blog-archive)
   - HTML + CSS + JS z promptu #13
   - Kontrolki Elementor
   - Testy

6. **Pagination Widget** (60 min)
   - Standalone widget (teraz część blog-archive)
   - SVG z dwoma pathami
   - Dynamiczna logika
   - Desktop separator
   - Testy dla różnych liczb stron

7. **Team Slider** (60 min)
   - Sprawdzić zgodność z 24 promptami
   - Wszystkie poprawki z sesji
   - Testy animacji
   - Testy mobile/desktop

### FAZA 3: Nowe widgety bez specyfikacji (design needed)
**Czas: ~4-5 godzin**

8. **Table of Contents** (90 min)
   - Design based on KPG style
   - Auto-generation from headings
   - Scroll spy
   - Smooth scroll
   - Sticky positioning

9. **Comments System** (120 min)
   - WordPress integration
   - Nested replies
   - AJAX submission
   - Custom styling matching KPG
   - Avatars (auutor.png fallback)

10. **Important Section** (45 min)
    - Simple highlight box
    - Editable content
    - Icon + title + content
    - Accent color

### FAZA 4: Content styling
**Czas: ~1 godzina**

11. **Blog Content CSS** (60 min)
    - Global styling dla the_content()
    - Headings, paragraphs, lists, blockquotes
    - Images, links
    - Responsive

---

## 📦 SYSTEM ŁATWY DO PRZENIESIENIA NA LIVE

### Struktura plików (AKTUALNA):
```
wp-content/
├── plugins/
│   └── kpg-elementor-widgets/
│       ├── kpg-elementor-widgets.php (main file)
│       ├── widgets/
│       │   ├── blog-archive.php ✅
│       │   ├── breadcrumbs.php ✅
│       │   ├── team-slider.php ✅
│       │   ├── onas.php ✅
│       │   ├── articles-from.php ✅
│       │   ├── post-meta-bar.php ✅
│       │   ├── author-section.php ✅
│       │   ├── blog-sorting.php ⚠️ DO UTWORZENIA
│       │   ├── pagination.php ⚠️ DO UTWORZENIA
│       │   ├── table-of-contents.php ⚠️ DO UTWORZENIA
│       │   ├── comments.php ⚠️ DO UTWORZENIA
│       │   └── important-section.php ⚠️ DO UTWORZENIA
│       ├── assets/
│       │   ├── css/
│       │   │   ├── blog-framework.css ✅
│       │   │   ├── team-slider.css ✅
│       │   │   ├── onas.css ✅
│       │   │   ├── articles-from.css ✅
│       │   │   ├── blog-sorting.css ⚠️
│       │   │   ├── pagination.css ⚠️
│       │   │   ├── table-of-contents.css ⚠️
│       │   │   ├── comments.css ⚠️
│       │   │   ├── important-section.css ⚠️
│       │   │   └── blog-content.css ⚠️
│       │   └── js/
│       │       ├── team-slider.js ✅
│       │       ├── blog-sorting.js ⚠️
│       │       ├── pagination.js ⚠️
│       │       ├── table-of-contents.js ⚠️
│       │       └── comments.js ⚠️
│       └── README.md
└── mu-plugins/
    ├── kpg-blog-framework.php ✅
    └── kpg-loop-post-footer-styles.php ✅
```

### Przeniesienie na live (PROCEDURA):

**Krok 1: Backup**
```bash
# Na live
wp plugin list
wp db export backup-$(date +%Y%m%d).sql
tar -czf wp-content-backup-$(date +%Y%m%d).tar.gz wp-content/
```

**Krok 2: Upload plików**
```bash
# Upload całego folderu plugins/kpg-elementor-widgets/
rsync -avz --progress \
  wp-content/plugins/kpg-elementor-widgets/ \
  user@live:/path/to/wp-content/plugins/kpg-elementor-widgets/

# Upload mu-plugins
rsync -avz --progress \
  wp-content/mu-plugins/ \
  user@live:/path/to/wp-content/mu-plugins/
```

**Krok 3: Aktywacja**
```bash
# Na live
wp plugin activate kpg-elementor-widgets
wp cache flush
```

**Krok 4: Elementor sync**
```
W Elementor → Tools → Regenerate CSS
W Elementor → Tools → Sync Library
```

**Krok 5: Testy**
- Sprawdź każdy widget w edytorze
- Przetestuj na stronie testowej
- Sprawdź responsive (mobile/desktop)
- Sprawdź performance

---

## 🎬 ROZPOCZĘCIE IMPLEMENTACJI

### Widget #1 - Blog Sorting (PIERWSZY DO ZROBIENIA)

**Dlaczego pierwszy:**
- Mały, standalone
- Pełna spec w promptach
- Test systemu tworzenia widgetów

**Implementacja:**
1. Utworzyć `widgets/blog-sorting.php`
2. Utworzyć `assets/css/blog-sorting.css`
3. Utworzyć `assets/js/blog-sorting.js`
4. Zarejestrować w `kpg-elementor-widgets.php`
5. Przetestować

**Następne:**
Widget #2 - Breadcrumbs (poprawki)
Widget #5 - Pagination
Widget #3 - Blog Archive (poprawki)
[itd...]

---

## ✅ CHECKLIST DLA KAŻDEGO WIDGETU

### Przed implementacją:
- [ ] Przeczytać wszystkie związane prompty
- [ ] Wypisać dokładne wymiary (px → vw)
- [ ] Przygotować design tokens
- [ ] Sprawdzić czy widget już istnieje

### Podczas implementacji:
- [ ] PHP: Utworzyć klasę widgetu
- [ ] PHP: Dodać kontrolki Elementor
- [ ] PHP: Render HTML
- [ ] PHP: Content template (editor preview)
- [ ] CSS: Mobile styles (383px base)
- [ ] CSS: Desktop styles (1696px base)
- [ ] CSS: Wszystkie stany (hover, active, disabled)
- [ ] JS: Funkcjonalność (jeśli potrzebna)
- [ ] JS: Elementor compatibility
- [ ] Rejestracja: Widget + styles + scripts

### Po implementacji:
- [ ] Test w edytorze Elementor
- [ ] Test na froncie (mobile)
- [ ] Test na froncie (desktop)
- [ ] Test wszystkich kontrolek
- [ ] Test edge cases
- [ ] Sprawdzić 100vw compliance
- [ ] Sprawdzić performance
- [ ] Kod review

---

## 🎯 OBECNY STATUS

**Gotowe do użycia:**
- ✅ Team Slider (kompletny)
- ✅ O Nas (kompletny)
- ✅ Articles From (kompletny)

**Wymaga małych poprawek:**
- ⚠️ Breadcrumbs (logic fixes)
- ⚠️ Post Meta Bar (add share)
- ⚠️ Blog Archive (1:1 with prompts)

**Do utworzenia:**
- ❌ Blog Sorting (separate widget)
- ❌ Pagination (separate widget)
- ❌ Table of Contents (new)
- ❌ Comments System (new)
- ❌ Important Section (new)
- ❌ Blog Content CSS (global)

---

## 📅 HARMONOGRAM

**Dzień 1 (4 godziny):**
- Widget 1: Blog Sorting
- Widget 2: Breadcrumbs (poprawki)
- Widget 5: Pagination

**Dzień 2 (4 godziny):**
- Widget 3/4: Blog Archive (1:1 z promptami)
- Widget 11: Post Meta Bar (share buttons)

**Dzień 3 (4 godziny):**
- Widget 6: Team Slider (final checks)
- Widget 10: Articles From (1:1 check)

**Dzień 4 (4 godziny):**
- Widget 7: Table of Contents (design + implement)
- Widget 9: Important Section (design + implement)

**Dzień 5 (4 godziny):**
- Widget 8: Comments System (design + implement)
- Widget 12: Blog Content CSS

**Dzień 6 (2 godziny):**
- Final testing
- Documentation
- Przygotowanie do live

**TOTAL: ~22 godziny pracy**

---

## 🔥 START - WIDGET #1: BLOG SORTING

Zaczynam implementację od najmniejszego, standalone widgetu z pełną specyfikacją w promptach.

**Ready to implement?** → TAK

---

_Dokument przygotowujący do implementacji 12 widgetów Elementor dla projektu KPG_
_Następny krok: Implementacja Widget #1 - Blog Sorting_



