# 🎯 Kluczowe Prompty z Sesji Rozwoju Projektu KPG

_Wyekstrahowane i przeanalizowane z 122,173 linii dokumentacji (2 pliki)_
_Data: 23 grudnia 2025_

---

## 📋 SPIS TREŚCI
1. [Git & Setup](#1-git--setup)
2. [Blog Archive - Featured Post](#2-blog-archive---featured-post)
3. [Blog Archive - Layout i Posty](#3-blog-archive---layout-i-posty)
4. [Paginacja](#4-paginacja)
5. [Team Slider - Layout](#5-team-slider---layout)
6. [Team Slider - Obrazy](#6-team-slider---obrazy)
7. [Team Slider - Teksty](#7-team-slider---teksty)
8. [Team Slider - Animacje](#8-team-slider---animacje)
9. [Widget "O Nas"](#9-widget-o-nas)
10. [Widget "Artykuły Od"](#10-widget-artykuły-od)
11. [Avatary i Metadane Użytkownika](#11-avatary-i-metadane-użytkownika)
12. [Breadcrumbs](#12-breadcrumbs)
13. [Strona Wyszukiwania](#13-strona-wyszukiwania)
14. [Best Practices & Zasady](#14-best-practices--zasady)

---

## 1. Git & Setup

### 1.1 Dodanie .gitignore dla WordPress
```
add git ignore for wordpress
```
**Rezultat:** Utworzony `.gitignore` z ignorowaniem core WordPress, pluginów, theme'ów, uploads, cache, itp.

### 1.2 Utworzenie skryptu PHP do tworzenia użytkownika
```
create new user please with php script
```
**Rezultat:** Plik `create-user.php` z funkcjonalnością tworzenia użytkowników WordPress

### 1.3 Problem z 10,000 zmian w git
```
dalej mam 10000 zmian git ignore jest zly
```
**Rezultat:** Poprawiony `.gitignore` z ignorowaniem `wp-admin/`, `wp-includes/`, `wp-content/plugins/`, `wp-content/themes/`

---

## 2. Blog Archive - Featured Post

### 2.1 Height auto + featured post na górze
```
.kpg-blog-archive-container .kpg-featured-post
height auto prosze a na gorze ma byc feature post
ten taki zolty
```

### 2.2 Design featured post z React
```html
<div className="rectangle">
  <div className="frame-3">
    <span className="blog-4">BLOG</span>
  </div>
  <div className="image" />
  <span className="poufnosc-danych-sygnalistow-w-szkole">
    Poufność danych sygnalistów w szkole
  </span>
  <div className="frame-5">
    <span>Od września 2024 roku każda szkoła...</span>
  </div>
  <div className="frame-6">
    <div className="ellipse" />
    <span className="mateusz-peczkowski-lipiec">
      Mateusz Pęczkowski • 07 lipiec 2025
    </span>
  </div>
</div>
```
**Kontekst:** Przykład struktury HTML z Figma/React

### 2.3 Żółte tło dla featured post
```
bg of first featured #F9FF46
```

### 2.4 Featured post jako pierwszy - pełna instrukcja
```
first featured post of blog should look like this
[+ pełny React component code]
```

---

## 3. Blog Archive - Layout i Posty

### 3.1 Usunięcie marginsów i paddingu
```
kpg-post-list tu nie dodawaj marginsue 
kpg-post-large
tu tez i paddingu
```

### 3.2 Problem z overflow - layout nie mieści się w 100vw
```
caly layoutr nie miesci sie w 100vw wywala poza nie wiem czemu 
np te sie nie mieszcza i ucina 
kpg-featured-post-title
kpg-post-list
```
**Rozwiązanie:** `width: 100% !important; max-width: 100vw !important; box-sizing: border-box !important;`

### 3.3 Posty się powtarzają
```
popatrz posty soe potwarzaja to zle maja sie po kolei wysiwetlac
[+ przykład HTML gdzie ten sam post jest wielokrotnie]
```

### 3.4 Pokazanie przykładu prawidłowego loopa
```
tak jak tu 
<div class="elementor-loop-container elementor-grid" role="list">
  [różne posty po kolei]
</div>
```

### 3.5 Margin bottom dla sortowania
```
kpg-blog-sorting
tu dodaj jakis margin bottom 32px np
```

### 3.6 Trzeci post jako duży w liście
```
ale mamy miec liste postow i w srodku np 3 ma byc duzym postem mozemy tak zerobic? zeby bylo troche sie dzialo
```

### 3.7 Placeholder dla postów bez obrazka
```
jak nie ma post obrazka to dodaj tam pusty kwadrat
```

### 3.8 Layout desktop 25% / 75% z gap 20px
```
na blogu 
<div class="elementor-element ... id="blog-desktop-v2">
  [search + sortowanie + kontakt - 25%]
  [loop-grid z postami - 75%]
</div>
to jest overflow ta sekcja z blogami zadbaj zeby to bylo 25% 75% z gap 20px prosze na desktopie
```

### 3.9 Nadal overflow na prawo
```
dalej overflowuje na prawo za duza sekcja z blogami
```

### 3.10 Max-width w vw zamiast px
```
.e-loop-item .e-con:has(.elementor-widget-author-box):has(.elementor-widget-post-info)
tu ustal max width w vw a nie px z importantem
```

### 3.11 Zmiana konkretnego pliku
```
@kpg-loop-post-footer-styles.php (73-74) tu zmien
```
```
zmien na vw
```

---

## 4. Paginacja

### 4.1 Separator i paginacja na desktop
```
background: #A3AFB0;
width: 1240px;
height: 0.5px;
on desktop in blog we need to add this below all blogs list in grid + same pagination as on mobile with 01 02 03 - xx and arrow right
```

### 4.2 SVG strzałki - pojedyncza (wersja 1)
```xml
<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
<path d="M12 24L20 16L12 8.00001" stroke="#6F7B7C" stroke-width="1.33333" stroke-linecap="square"/>
</svg>
1x

add these 2x instead of arrow right on pagination 
width: 48px;
height: 32px;
border-radius: 8px;
background: var(--Gray-20, #E3EBEC);
```

### 4.3 Podwójna strzałka
```
podwojna ma byc 2 oboj siebie te same svg
```

### 4.4 SVG strzałki - finalna wersja z dwoma pathami
```xml
dobra uzztyj po prpstu tego svg zamiast 2 
<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
</svg>
```

### 4.5 Style kontenera strzałki
```
kpg-blog-pagination-arrow
to powinno wygladac tak 
width: 60px;
height: 32px;
border-radius: 8px;
background: var(--Gray-20, #E3EBEC);
```

### 4.6 Kolory i typografia paginacji
```
change color of these 
active: 
page-numbers color: var(--Gray-90, #404848);
leading-trim: both;
text-edge: cap;
font-family: "DM Mono";
font-size: 16px;
font-style: normal;
font-weight: 300;
line-height: 160%; /* 25.6px */
text-transform: uppercase;

non active color: var(--Gray-50, #A3AFB0);
[same typography]
```

### 4.7 Strzałka 1:1 i absolutne pozycjonowanie
```
kpg-pagination-arrow
zmienmy na ikonke 1:1 jak na mobile 
dodatkowo ta ikonka 
kpg-blog-pagination-arrow
ma miec jedno svg absolutne 6px obok tego pierwszego
```

### 4.8 Ta sama strzałka na desktop
```
na desktop tak samo kpg-pagination-arrow
```

### 4.9 Paginacja nie działa na desktopie
```
testuje n blog i test blog dalej nie ma na desktopie paginacji
```

### 4.10 Elementor pagination nie wygląda dobrze
```html
<nav class="elementor-pagination" aria-label="Paginacja">
  <span aria-current="page" class="page-numbers current">
    <span class="elementor-screen-only">Strona</span>1
  </span>
  <a class="page-numbers" href="...">
    <span class="elementor-screen-only">Strona</span>2
  </a>
</nav>
dodalem ale nie wyglada tak jak tamto
```

### 4.11 Nadal te same style
```
http://kontrola-dotacji-oswiatowych.local/blog-test/ 
tu patrze i dalej takie same style maja
[+ kod HTML]
```

### 4.12 Pytanie o dynamiczność paginacji
```
@blog-archive.php (543-547) is this correct way to do it? what if we are on 4 page will it work? is everytime 10 pages? is it dynamic
```
**Kontekst:** Sprawdzenie, czy logika paginacji działa dla wszystkich stron

---

## 5. Team Slider - Layout

### 5.1 Border pod tytułem
```
albo border bottom 8px pod tytulem
```

### 5.2 16px odstępu na desktop
```
na desktopie 16px pod tym
```

### 5.3 Nawigacja nad tytułem na mobile
```
dalej na mobile kpg-team-slider-navigation to ma byc nad tym kpg-team-slider-top-section
```
**Rozwiązanie:** Użycie CSS `order` property

### 5.4 Padding i min-height kontenera
```
kpg-team-slider-container tu daj min-height auto na moible i padding bottom 32px
```

### 5.5 Brak przerwy między slajdami
```
czemu nie ma na mobile przerwy miedzy slidami
```
**Rozwiązanie:** Dodanie `spaceBetween` w konfiguracji Swiper

---

## 6. Team Slider - Obrazy

### 6.1 Problem z object-position
```
w team sliderze 
kpg-team-slider-main-image-wrapper
na obrazy nie powinny dzialc te style object-position
dodatkowo obrazki na mobile bardzo zle sa dopasowane do miejsca w ktoyrym sa porpaw je
```

### 6.2 Proporcje tragiczne - aspect-ratio
```
.kpg-team-slider-main-image {
    width: 89.5561vw;
przez to proporcje sa tragiczne musimy to poprawic zeby bylo normalne tam zdj duze
wg designu width: 343px;
height: 457px;
aspect-ratio: 343/457;
```

### 6.3 Inline styles nadpisują CSS
```html
<img decoding="async" src="..." alt="Slide 1" class="kpg-team-slider-main-image" style="object-position: center calc(50% + 50px);">
dalej te obrazki fatalnie na mobile sie wyswietlaja
```
**Problem:** Inline `style` nadpisuje CSS

### 6.4 Zdjęcie ok ale nie widać całej wysokości
```
teraz zdj jest ok ale nie widac calego height powinno by
```

### 6.5 Min-height dla aktywnego slajdu
```
swiper-slide swiper-slide-active
min-height ma byc 427px na 370px
```
**Przeliczenie:** `niee na max 370 na mobile po prostu tylko ci dalem zebys na vw przeliczyl`
**Rezultat:** `111.4888vw` (427px / 383px * 100vw)

### 6.6 Wszystkie elementy mają podążać za wysokością
```
kpg-team-slider-main-image-wrapper
to i kpg-team-slider-main-image
i img inside should follow this height
```

---

## 7. Team Slider - Teksty

### 7.1 Specyfikacja tekstu na mobile
```
.kpg-team-slider-text.kpg-team-slider-text-left ten tekst na mobile ma miec takie wymiary 
overflow: hidden;
color: #404848;
leading-trim: both;
text-edge: cap;
text-overflow: ellipsis;
/* Body Text/Lg */
font-family: Nohemi;
font-size: 16px;
font-style: normal;
font-weight: 300;
line-height: 24px; /* 150% */
letter-spacing: 0.16px;
```

### 7.2 Ostylowanie text-right
```
kpg-team-slider-text kpg-team-slider-text-right ten tekst tez ostyluj tak jak 
te .kpg-team-slider-text-item na mobile
```

### 7.3 Ujednolicenie WSZYSTKICH tekstów
```
kpg-team-slider-text kpg-team-slider-text-right
to tez ostyluj kurwa wszystkie teksty w sliderze mialaj oprocz tytulu i stanowiska taki sam font size etc
```
**Emocja:** Frustracja, że trzeba powtarzać

### 7.4 Tekst za mały
```
.kpg-team-slider-text.kpg-team-slider-text-right
dalej jest malutkie na mobile
```

### 7.5 Text-right nie powinien być widoczny od razu
```
kpg-team-slider-text kpg-team-slider-text-right a to nie mialo sie pokazywac pozniej? bo teraz calyczas jest widoczny
```

### 7.6 Nie jest ukryty mimo CSS
```
nie jest ukryty
```

### 7.7 Działało wcześniej, teraz nie
```
wczesniej dzialalo teraz nie dziala dalej widze za duzo
```

### 7.8 Pytanie o klasę active
```
kpg-team-slider-text-section kpg-team-slider-text-section-mobile active to jest aktive nie wiem czemu active chyba ma byc dopeiro po tym jak klikne zobacz wiecej tak?
```

### 7.9 Sekcja i przycisk powinny być widoczne
```
.kpg-team-slider-text-section-mobile
to powinno byc widoczne jak i to class="kpg-team-slider-expand-mobile"
```

### 7.10 Zawsze widoczne
```
zawsze
```
**Kontekst:** Potwierdzenie, że elementy mają być widoczne domyślnie

---

## 8. Team Slider - Animacje

### 8.1 Max-height źle ustalany
```
kpg-team-slider-text kpg-team-slider-text-left expanded
tu max-height jest zle ustalany czasem i teksty nachodza na siebie
```

### 8.2 Inline styles na expanded
```
display: flex !important;
max-height: 675px !important;
overflow: visible !important;
-webkit-line-clamp: unset !important;
-webkit-box-orient: unset !important;
dalej takie style inline sa tu 
kpg-team-slider-text kpg-team-slider-text-left expanded
```

### 8.3 Animacja tragiczna
```
ta animacja pojawiania sie tekstu jest tragiczna po tym jak sie pojawia mozemy ja jakos pokazac aldniej
```
**Rozwiązanie:** Zmiana z `ease` na `cubic-bezier(0.4, 0, 0.2, 1)`, dodanie `opacity` transition

---

## 9. Widget "O Nas"

### 9.1 Utworzenie widgetu
```
stworz teraz nowy wideget o nas @onas_desktop/ 
@onasmobile/ 
tu masz mobile i desktop 
@SCSS_CSS_BEST_PRACTICES.md (1-875) 
edycja tekstu i zdjec ma byc
```

### 9.2 Avatar z odstępem
```
10 px po lewej od kpg-onas-author-info powinno byc
@wp-content/uploads-webpc/uploads/avatar.svg
width: 32px;
height: 32px;
```

### 9.3 Zmiana avatara
```html
<div class="kpg-author-section-avatar">
  <img src="http://kontrola-dotacji-oswiatowych.local/wp-content/uploads/Ellipse-3.png" alt="test">
</div>
zmien ten obrazek na ten
```

### 9.4 Desktop nie wygląda poprawnie
```
o nas w ogole nie wyglada na desktop poprawnie ma wygladac tak
[+ pełny React component i CSS z designu]
```

### 9.5 Design dla "O Nas"
```jsx
export default function Main() {
  return (
    <div className="main-container">
      <div className="frame-1">
        <div className="frame-2">
          <span className="o-nas">O NAS</span>
        </div>
        <div className="frame-3">
          <span className="mission-statement">
            Naszą misją jest tworzenie przestrzeni...
          </span>
          <span className="dot">1.0</span>
        </div>
      </div>
      <div className="frame-4">
        <div className="frame-5">
          <span className="education-support">
            Dążymy do usprawnienia systemu edukacji...
          </span>
        </div>
      </div>
      <div className="frame-6">
        <div className="quote" />
        <div className="frame-7">...</div>
      </div>
    </div>
  );
}
```

---

## 10. Widget "Artykuły Od"

### 10.1 Utworzenie widgetu
```
widget zrob musimy do artykuly od dac 
[+ React component + CSS]
dynamiczne z profilu wszystko 
ale na razie dla demo daj te co sa tu
mniej wiecje taki 
display: flex;
width: 385px;
padding: 16px 16px 32px 16px;
align-items: flex-start;
gap: 32px;
```

### 10.2 Design dla widgetu
```css
.main-container {
  display: flex;
  align-items: flex-start;
  flex-wrap: nowrap;
  gap: 32px;
  position: relative;
  width: 385px;
  margin: 0 auto;
  padding: 16px 16px 32px 16px;
  background: #e3ebec;
  border-radius: 8px;
}
.articles-from {
  color: #484440;
  font-family: Nohemi;
  font-size: 42px;
  font-weight: 300;
  line-height: 30px;
  text-transform: uppercase;
}
```

---

## 11. Avatary i Metadane Użytkownika

### 11.1 Wyświetlanie pełnego imienia
```
@post-meta-bar.php (261) zamiast tego wyswietl imie i nazwisko w kazdym miesjcu tak ma byc
```
**Rozwiązanie:** Łączenie `first_name` + `last_name` zamiast tylko `display_name`

### 11.2 Avatar pusty po zmianie
```
teraz jestpuste..
```
**Kontekst:** Po zmianie z `Ellipse-3.png` na `auutor.png`, avatary przestały się wyświetlać

### 11.3 Pokazanie HTML z Gravatar
```html
<div class="kpg-author-section-avatar">
  <img src="https://secure.gravatar.com/avatar/43b98dc3db0d33c9451fd2e66a09da4dbe4e67ab4067d5e976093d89a790856e?s=96&amp;d=mm&amp;r=g" alt="test">
</div>
```
**Kontekst:** System nadal używa Gravatar zamiast `auutor.png`

---

## 12. Breadcrumbs

### 12.1 Breadcrumbs źle na stronie bloga
```
http://kontrola-dotacji-oswiatowych.local/blog-test/ ten breadcrumbs zle dziala jestem tutaj powinienem byc na home / blog-test 

a jest:
<nav class="kpg-breadcrumbs" aria-label="breadcrumbs">
  <div class="kpg-breadcrumbs-inner">
    <span class="kpg-breadcrumbs-item kpg-breadcrumbs-item--current">home</span>
  </div>
</nav>
```
**Problem:** Brakuje aktualnej strony w breadcrumbs

### 12.2 Breadcrumbs na stronie autora
```
http://kontrola-dotacji-oswiatowych.local/author/m.peczkowski/ 
jestem tu. abreadcrumbs nasz mowi ze 
cos jest nie tak 

[pokazuje]:
<nav class="kpg-breadcrumbs">
  <a href="/" class="kpg-breadcrumbs-item--type-home">home</a>
  <span class="kpg-breadcrumbs-separator">/</span>
  <span class="kpg-breadcrumbs-item--current">
    Uznanie dyplomu zagranicznego za równoważny polskiemu
  </span>
</nav>
```
**Problem:** Pokazuje tytuł posta zamiast imienia autora

---

## 13. Strona Wyszukiwania

### 13.1 Strona wyszukiwania wygląda źle
```
STRONA SZUkania wyglada w ogole jak z innej bajki dopasuj podstawowo ja chociaz 
<div class="page-content">
  <article class="post">
    <h2 class="entry-title"><a href="...">Strona główna – TEST</a></h2>
    <p>TEAM Daria Bezwińska Adwokat...</p>
  </article>
  <article class="post">...</article>
</div>
zeby przypominla te artykuly co na blogu
```

---

## 14. Best Practices & Zasady

### 14.1 Nie ustawiać height na tekstach
```
nie ustalaj height prosze span a, h.. itd tekstowym elementom chyba ze to bardzo potrezebne do aniamcji np teraz te elementy sa za duzo w onas
```
**Zasada:** Height tylko dla kontenerów i animacji, nie dla tekstów

### 14.2 Używanie vw zamiast px
```
tu ustal max width w vw a nie px z importantem
```
```
zmien na vw
```

### 14.3 Separator SVG
```xml
<svg xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none">
<path d="M0.63623 0.636719L6.63623 6.63672L12.6362 0.636719" stroke="#404848" stroke-width="1.8"/>
</svg>
1x
```

---

## 15. Eksport i Meta

### 15.1 Request eksportu
```
can you export for me all messages from this chat?
```

### 15.2 Finalna analiza
```
@cursor_padding_po_rozwini_ciu.md (1-15430) 
@cursor_project_documentation_review.md (1-106743) 

zapoznaj sie prosze z tymi plikami, przestuduj je porzadnie i musimy wyluskac wszydstkie moje prompty, wziac te najwazniejsze i dac mi je jeden po drugim do pliku
```

---

## 📊 STATYSTYKI PROMPTÓW

### Rozkład kategorii:
- **Git & Setup:** 3 prompty
- **Blog Archive - Featured:** 4 prompty
- **Blog Archive - Layout:** 11 promptów
- **Paginacja:** 12 promptów
- **Team Slider - Layout:** 5 promptów
- **Team Slider - Obrazy:** 6 promptów
- **Team Slider - Teksty:** 10 promptów
- **Team Slider - Animacje:** 3 prompty
- **Widget "O Nas":** 5 promptów
- **Widget "Artykuły Od":** 2 prompty
- **Avatary:** 3 prompty
- **Breadcrumbs:** 2 prompty
- **Search:** 1 prompt
- **Best Practices:** 3 prompty
- **Meta:** 2 prompty

**Łącznie: 72 kluczowe prompty**

---

## 🎓 WZORCE SKUTECZNYCH PROMPTÓW

### 1. Precyzyjny selektor
```
.kpg-team-slider-text.kpg-team-slider-text-left
```

### 2. Pokazanie problemu z HTML
```html
<div class="kpg-post-list">
  [przykład problemu]
</div>
```

### 3. Pokazanie oczekiwanego stanu
```
tak jak tu
[przykład prawidłowy]
```

### 4. Konkretne wymiary ze źródła
```
wg designu width: 343px;
height: 457px;
aspect-ratio: 343/457;
```

### 5. Wskazanie konkretnego pliku i linii
```
@kpg-loop-post-footer-styles.php (73-74) tu zmien
```

### 6. Pytanie o logikę/dynamiczność
```
is this correct way to do it? what if we are on 4 page will it work? is it dynamic
```

### 7. Pokazanie inline styles jako problem
```html
<img style="object-position: center calc(50% + 50px);">
dalej te obrazki fatalnie na mobile sie wyswietlaja
```

---

## 🔧 NAJWAŻNIEJSZE ZASADY TECHNICZNE

### CSS:
1. **Jednostki vw** (desktop: 1696px base, mobile: 383px base)
2. **Nie ustawiać height na tekstach** (span, h, a, p) - tylko dla kontenerów
3. **Zapobieganie overflow:**
   ```css
   width: 100% !important;
   max-width: 100vw !important;
   box-sizing: border-box !important;
   ```
4. **Gap zamiast marginów** gdzie to możliwe
5. **!important** tylko gdy style muszą nadpisać inline lub Elementor CSS

### Layout:
1. **Mobile-first** approach
2. **Flexbox** do układania
3. **CSS order** do zmiany kolejności na mobile
4. **Desktop często 25% / 75%** lub podobne proporcje

### WordPress/PHP:
1. **Full name:** `first_name` + `last_name`, fallback do `display_name`
2. **Avatar cascade:**
   - Custom from user meta
   - `auutor.png` w uploads
   - `auutor.png` w uploads-webpc/uploads
   - Gravatar
3. **Query:** `post__not_in` dla featured, `ignore_sticky_posts => true`
4. **Paginacja dynamiczna:** Zawsze pokazuj current page

### JavaScript/Animacje:
1. **Mierzenie wysokości:**
   ```javascript
   requestAnimationFrame(() => {
     el.offsetHeight; // force reflow
     const height = el.scrollHeight + 20; // +buffer
   });
   ```
2. **Smooth animations:**
   ```css
   transition: max-height 0.8s cubic-bezier(0.4, 0, 0.2, 1),
               opacity 0.6s ease-out;
   ```
3. **Swiper gap:** `spaceBetween: 8.3551` (30-32px na mobile)

### Obrazy:
1. **Object-position tylko na desktop** (CSS variable z JavaScript)
2. **Mobile zawsze center center**
3. **Aspect-ratio** zamiast height + width
4. **Struktura:**
   ```html
   <div class="wrapper">
     <div class="image-container">
       <img />
     </div>
   </div>
   ```

---

## 🚨 NAJCZĘSTSZE PROBLEMY

### 1. Overflow poza 100vw
**Prompt:** "dalej overflowuje na prawo za duza sekcja"
**Rozwiązanie:** max-width 100vw + box-sizing border-box

### 2. Powtarzające się posty
**Prompt:** "posty soe potwarzaja to zle maja sie po kolei wysiwetlac"
**Rozwiązanie:** $collected_ids + $rendered_ids arrays

### 3. Inline styles nadpisują CSS
**Prompt:** "dalej te obrazki fatalnie na mobile sie wyswietlaja"
**Rozwiązanie:** data-attribute + JavaScript dla desktop, CSS !important dla mobile

### 4. Max-height źle obliczany
**Prompt:** "max-height jest zle ustalany czasem i teksty nachodza na siebie"
**Rozwiązanie:** requestAnimationFrame + scrollHeight + buffer

### 5. Elementy niewidoczne mimo CSS
**Prompt:** "nie jest ukryty" / "dalej widze za duzo"
**Rozwiązanie:** Sprawdzenie JS, inline styles, specyficzności selektorów

### 6. Paginacja nie działa na desktop
**Prompt:** "dalej nie ma na desktopie paginacji"
**Rozwiązanie:** !important w CSS desktop, transformacja Elementor pagination przez JS

### 7. Breadcrumbs pokazują złe dane
**Prompt:** "breadcrumbs nasz mowi ze cos jest nie tak"
**Rozwiązanie:** Dodanie is_author(), is_home(), sprawdzanie queried_object

---

## 💡 WZORCE KOMUNIKACJI

### Skuteczne:
- ✅ Pokazanie konkretnego HTML z problemu
- ✅ Podanie designu z Figma (React + CSS)
- ✅ Wskazanie pliku i linii: `@file.php (73-74)`
- ✅ "wg designu width: 343px; height: 457px"
- ✅ Pokazanie dwóch stanów: "a jest X, ma być Y"
- ✅ Używanie selektorów CSS: `.kpg-team-slider-text.kpg-team-slider-text-left`

### Mniej precyzyjne:
- ⚠️ "dalej zle" (bez pokazania czym jest "źle")
- ⚠️ "popraw to" (bez wskazania konkretnego elementu)
- ⚠️ Tylko nazwa klasy bez kontekstu

### Wzorcowy prompt:
```
kpg-team-slider-main-image {
    width: 89.5561vw;
przez to proporcje sa tragiczne musimy to poprawic zeby bylo normalne tam zdj duze
wg designu width: 343px;
height: 457px;
aspect-ratio: 343/457;
```
**Dlaczego dobry:**
1. Konkretny selektor
2. Opis problemu ("proporcje tragiczne")
3. Pokazanie źródła prawdy ("wg designu")
4. Konkretne wymiary
5. Wskazanie rozwiązania (aspect-ratio)

---

## 🎯 TOP 10 NAJBARDZIEJ INSTRUKTYWNYCH PROMPTÓW

### 1. Featured post pełny design
```
first featured post of blog should look like this
[+ pełny React component z Figma]
```
**Dlaczego:** Pokazuje kompletny, dokładny design do zaimplementowania

### 2. Desktop layout 25%/75%
```
to jest overflow ta sekcja z blogami zadbaj zeby to bylo 25% 75% z gap 20px prosze na desktopie
[+ pełny HTML kontekst]
```
**Dlaczego:** Precyzyjne proporcje + pokazanie struktury

### 3. Aspect-ratio dla obrazów
```
przez to proporcje sa tragiczne
wg designu width: 343px; height: 457px; aspect-ratio: 343/457;
```
**Dlaczego:** Problem → źródło prawdy → rozwiązanie

### 4. Paginacja - kompletna specyfikacja
```
background: #A3AFB0; width: 1240px; height: 0.5px;
on desktop in blog we need to add this below all blogs list in grid + same pagination as on mobile with 01 02 03 - xx and arrow right
```
**Dlaczego:** Wszystkie wymiary + lokalizacja + referencja do istniejącego stylu

### 5. Kolory i typografia - pełna spec
```
active: color: var(--Gray-90, #404848);
font-family: "DM Mono"; font-size: 16px; font-weight: 300;
line-height: 160%; text-transform: uppercase;
non active color: var(--Gray-50, #A3AFB0);
```
**Dlaczego:** Kompletna specyfikacja dla obu stanów

### 6. Pokazanie problemu z inline styles
```html
<img style="object-position: center calc(50% + 50px);">
dalej te obrazki fatalnie na mobile sie wyswietlaja
```
**Dlaczego:** Pokazuje konkretną przyczynę problemu (inline style)

### 7. Pytanie o dynamiczność
```
@blog-archive.php (543-547) is this correct way to do it? 
what if we are on 4 page will it work? is it dynamic
```
**Dlaczego:** Testuje edge cases i myśli o skalowaniu

### 8. Porównanie "jest vs powinno być"
```
jestem tu: http://.../ 
powinienem byc na home / blog-test 
a jest: <nav>[pokazuje aktualny stan]</nav>
```
**Dlaczego:** Jasne pokazanie oczekiwań vs rzeczywistości

### 9. Ujednolicenie wszystkich tekstów
```
wszystkie teksty w sliderze mialaj oprocz tytulu i stanowiska taki sam font size etc
```
**Dlaczego:** Globalna zasada zamiast pojedynczych zmian

### 10. SVG z dwoma pathami
```
dobra uzztyj po prpstu tego svg zamiast 2 
<svg>...</svg>
```
**Dlaczego:** Decyzja upraszczająca + podanie konkretnego kodu

---

## 🔄 ITERACYJNY PROCES DEBUGOWANIA

### Wzorzec 1: Overflow
```
1. "caly layoutr nie miesci sie w 100vw"
2. [próba naprawy]
3. "dalej overflowuje na prawo"
4. [kolejna próba]
5. "tu ustal max width w vw a nie px z importantem"
```

### Wzorzec 2: Posty się powtarzają
```
1. "posty soe potwarzaja"
2. [pokazanie HTML z problemem]
3. "tak jak tu [przykład prawidłowy z Elementor]"
4. "zle paginacja nie dzial ana kazdej stronie jewst ten sam post powtrzuony"
```

### Wzorzec 3: Elementy niewidoczne/widoczne
```
1. "to nie mialo sie pokazywac pozniej?"
2. "nie jest ukryty"
3. "wczesniej dzialalo teraz nie dziala"
4. "to powinno byc widoczne"
5. "zawsze [widoczne]"
```

---

## 📝 TEMPLATE PROMPTU DO SKOPIOWANIA

```
// ======================================
// WZORZEC DOBREGO PROMPTU
// ======================================

SELEKTOR/LOKALIZACJA:
@plik.php (linijka) lub .css-class

PROBLEM:
[opis co jest nie tak, najlepiej z pokazaniem HTML/CSS]

OCZEKIWANY STAN:
[pokazanie designu z Figma, React component, lub konkretne wartości]

KONTEKST (opcjonalnie):
- wg designu width: Xpx; height: Ypx;
- na mobile/desktop
- tylko dla aktywnego stanu
- dynamiczne z bazy danych

PRZYKŁAD PRAWIDŁOWY (opcjonalnie):
[pokazanie jak to działa gdzie indziej]
```

### Przykład zastosowania:
```
.kpg-team-slider-text.kpg-team-slider-text-right
dalej jest malutkie na mobile

OCZEKIWANY STAN:
font-family: Nohemi;
font-size: 16px (4.1776vw);
line-height: 24px (150%);
letter-spacing: 0.16px;

KONTEKST:
- takie same style jak .kpg-team-slider-text-item
- wszystkie teksty w sliderze (oprócz tytułu i stanowiska) mają mieć jednakową typografię
```

---

## 🎪 NAJCZĘSTSZE TYPY ŻĄDAŃ

### 1. Zmiana jednostek (px → vw)
**Częstotliwość:** ⭐⭐⭐⭐⭐
```
zmien na vw
tu ustal max width w vw a nie px z importantem
```

### 2. Naprawienie overflow/layout
**Częstotliwość:** ⭐⭐⭐⭐⭐
```
caly layoutr nie miesci sie w 100vw wywala poza
dalej overflowuje na prawo za duza sekcja
```

### 3. Dopasowanie do designu z Figma
**Częstotliwość:** ⭐⭐⭐⭐⭐
```
ma wygladac tak
[+ React component + CSS]
```

### 4. Naprawienie kolejności/renderowania
**Częstotliwość:** ⭐⭐⭐⭐
```
posty soe potwarzaja to zle maja sie po kolei wysiwetlac
featured post ma byc na gorze
```

### 5. Responsywność mobile/desktop
**Częstotliwość:** ⭐⭐⭐⭐
```
na desktopie 16px pod tym
na mobile 8px
```

### 6. Animacje i transitions
**Częstotliwość:** ⭐⭐⭐
```
ta animacja jest tragiczna mozemy ja jakos pokazac ladniej
max-height jest zle ustalany czasem i teksty nachodza na siebie
```

### 7. Visibility i display logic
**Częstotliwość:** ⭐⭐⭐
```
nie jest ukryty
to powinno byc widoczne
wczesniej dzialalo teraz nie dziala
```

### 8. Dynamiczne dane z WordPress
**Częstotliwość:** ⭐⭐
```
dynamiczne z profilu wszystko
wyswietl imie i nazwisko w kazdym miesjcu
```

### 9. SVG i ikony
**Częstotliwość:** ⭐⭐
```
dobra uzztyj po prpstu tego svg zamiast 2
<svg>...</svg>
```

### 10. Breadcrumbs i nawigacja
**Częstotliwość:** ⭐⭐
```
breadcrumbs zle dziala jestem tutaj powinienem byc na home / blog-test
```

---

## 🏆 ZŁOTE ZASADY Z SESJI

### 1. Zawsze używaj vw (nigdy px dla layoutu)
**Desktop base:** 1696px  
**Mobile base:** 383px  
**Formula:** `(pixels / base) * 100vw`

### 2. Height tylko dla kontenerów i animacji
```
nie ustalaj height prosze span a, h.. itd tekstowym elementom 
chyba ze to bardzo potrezebne do aniamcji
```

### 3. Box-sizing border-box wszędzie
```css
.element {
  box-sizing: border-box !important;
}
```

### 4. Zapobieganie overflow
```css
.container {
  width: 100% !important;
  max-width: 100vw !important;
  overflow-x: hidden;
}
```

### 5. Full name = first_name + last_name
```php
$full_name = trim($first_name . ' ' . $last_name);
if (empty($full_name)) {
  $full_name = $display_name;
}
```

### 6. Avatar fallback chain
```
1. user_meta: custom_avatar_id/url
2. uploads/auutor.png
3. uploads-webpc/uploads/auutor.png
4. Gravatar
```

### 7. Paginacja zawsze dynamiczna
```
- Pokaż current page nawet jeśli > 3
- Format: 01 02 03 ... current ... max
- Sprawdzaj max_num_pages
```

### 8. Animacje płynne
```css
transition: max-height 0.8s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.6s ease-out;
```
```javascript
const fullHeight = el.scrollHeight + 20; // +buffer
```

### 9. Mobile-first, flexbox order dla reorganizacji
```css
@media (max-width: 767px) {
  .navigation { order: 1; }
  .title { order: 2; }
}
```

### 10. Inline styles = data-attributes + JS
```html
<!-- NIE: -->
<img style="object-position: ...">

<!-- TAK: -->
<img data-object-position="...">
```
```javascript
if (isDesktop) {
  img.style.setProperty('--object-position', value);
}
```

---

## 📖 SŁOWNICZEK PROMPTÓW

**"dalej"** = nadal (problem nie został rozwiązany)  
**"zle"** = niepoprawnie, nie działa  
**"tragiczne"** = bardzo źle, wymaga poprawy  
**"fatalnie"** = bardzo źle  
**"kurwa"** = frustracja, trzeba naprawić natychmiast  
**"prosze"** = prośba o zmianę  
**"tu"** = w tym miejscu  
**"wg designu"** = zgodnie z projektem z Figmy  
**"ma byc"** = oczekiwany stan  
**"powinno byc"** = oczekiwany stan  
**"czemu"** = pytanie o przyczynę  
**"tak jak tu"** = pokazanie przykładu prawidłowego  
**"zawsze"** = w każdym przypadku  

---

## 🔍 JAK UŻYWAĆ TEGO DOKUMENTU

### Dla debugowania:
1. Znajdź podobny problem w sekcji "Najczęstsze problemy"
2. Zobacz wzorcowy prompt
3. Dostosuj do swojej sytuacji

### Dla nowych feature'ów:
1. Zobacz sekcję z podobnym widgetem
2. Użyj struktury promptu template
3. Podaj design z Figma

### Dla optymalizacji:
1. Sprawdź "Złote zasady"
2. Zastosuj best practices
3. Użyj jednostek vw

---

## 📌 QUICK REFERENCE

### Najczęściej używane klasy:
- `.kpg-blog-archive-container`
- `.kpg-featured-post`
- `.kpg-post-list`
- `.kpg-post-list-item`
- `.kpg-post-large`
- `.kpg-blog-pagination`
- `.kpg-team-slider-container`
- `.kpg-team-slider-main-image`
- `.kpg-team-slider-text-left`
- `.kpg-onas-container`
- `.kpg-articles-from-container`

### Najczęściej modyfikowane pliki:
- `blog-archive.php` (widget)
- `blog-framework.css`
- `team-slider.php` (widget)
- `team-slider.css`
- `team-slider.js`
- `post-meta-bar.php`
- `breadcrumbs.php`
- `kpg-blog-framework.php` (mu-plugin)

### Najczęstsze wartości:
- Gap: `32px` (desktop), `16px` (mobile)
- Border-radius: `8px`
- Padding: `16px`
- Font-size body: `16px` (`0.9434vw` desktop, `4.1776vw` mobile)
- Line-height: `150%` lub `24px`
- Transition: `0.3s ease` (hover), `0.8s cubic-bezier(...)` (animacje)

---

## 🎬 KONIEC DOKUMENTU

**Łączna liczba przeanalizowanych linii:** 122,173  
**Łączna liczba wyekstrahowanych promptów:** 72  
**Najdłuższa sesja debugowania:** Overflow (9 iteracji)  
**Najczęstszy problem:** Layout overflow poza 100vw  
**Najczęstsza poprawka:** Zmiana px → vw  

**Utworzony przez:** Cursor AI  
**Data:** 23.12.2025  
**Przeznaczenie:** Referencja dla przyszłych sesji developmentu

---

_Ten dokument to destylacja wiedzy z dwóch intensywnych sesji developmentu. Używaj go jako punktu odniesienia dla przyszłych zadań._
