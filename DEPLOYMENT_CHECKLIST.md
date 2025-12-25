# Lista plików do wdrożenia na produkcję

## Wszystkie zmienione pliki (34 pliki)

### CSS Files (11)
- `assets/css/_kpg-colors.css`
- `assets/css/articles-from.css`
- `assets/css/blog-content.css`
- `assets/css/comments.css`
- `assets/css/important-section.css`
- `assets/css/onas.css`
- `assets/css/pagination.css`
- `assets/css/post-meta-bar.css`
- `assets/css/spis-responsive.css`
- `assets/css/table-of-contents.css`
- `assets/css/team-slider.css`

### JavaScript Files (9)
- `assets/js/blog-content.js`
- `assets/js/comments.js`
- `assets/js/pagination.js`
- `assets/js/post-meta-bar.js`
- `assets/js/table-of-contents.js`
- `assets/js/team-slider-add-5-slides.js`
- `assets/js/team-slider-create-slides.js`
- `assets/js/team-slider.js`

### PHP Widget Files (12)
- `widgets/articles-from.php`
- `widgets/blog-archive-desktop.php`
- `widgets/blog-archive-mobile.php`
- `widgets/blog-content.php`
- `widgets/blog-sorting.php`
- `widgets/breadcrumbs.php`
- `widgets/comments.php`
- `widgets/important-section.php`
- `widgets/onas.php`
- `widgets/pagination.php`
- `widgets/table-of-contents.php`
- `widgets/team-slider.php`

### PHP Includes & Main (3)
- `includes/elementor-loop-integration.php`
- `includes/empty-comments-template.php`
- `kpg-elementor-widgets.php`

## Główne zmiany w tej sesji

### Team Slider Widget
- ✅ Dodano pole `main_image` dla osobnego obrazka głównego
- ✅ Dodano przycisk "zobacz więcej/mniej" dla mobile (tylko prawa kolumna)
- ✅ Dodano nawigację mobilną jako pierwszy element
- ✅ Naprawiono touch/swipe na sekcji content
- ✅ Zaktualizowano style zgodne z designem mobile i desktop
- ✅ Zaktualizowano style tytułu stanowiska (job_title)

### O Nas Widget
- ✅ Dodano SVG ikony cytatu dla mobile i desktop
- ✅ Dodano wrapper dla avatara i informacji autora
- ✅ Dodano pole `quote_image_mobile` dla obrazka na mobile
- ✅ Zaktualizowano style desktopowe zgodnie z designem
- ✅ Zaktualizowano style autora (avatar, name, title)

## Ostrzeżenia (niekrytyczne)

Wszystkie ostrzeżenia dotyczą nowych właściwości CSS (`leading-trim`, `text-edge`), które są wspierane w nowszych przeglądarkach i są bezpieczne do użycia.

## Instrukcja wdrożenia

1. **Backup**: Zrób backup obecnych plików przed wdrożeniem
2. **Upload**: Prześlij wszystkie 34 pliki z `FILES_TO_DEPLOY.txt` na serwer produkcyjny
3. **Clear Cache**: Wyczyść cache WordPress/Elementor po wdrożeniu
4. **Test**: Przetestuj widgety w edytorze Elementora i na frontendzie

## Szybkie wdrożenie

Możesz użyć skryptu do automatycznego kopiowania:

```bash
# Z listy plików
while IFS= read -r file; do
  if [[ $file != \#* ]] && [[ -n "$file" ]]; then
    echo "Kopiowanie: $file"
    # scp "$file" user@server:/path/to/wordpress/$file
  fi
done < FILES_TO_DEPLOY.txt
```

Lub po prostu skopiuj cały folder:
```bash
# Cały plugin
scp -r wp-content/plugins/kpg-elementor-widgets user@server:/path/to/wordpress/wp-content/plugins/
```
