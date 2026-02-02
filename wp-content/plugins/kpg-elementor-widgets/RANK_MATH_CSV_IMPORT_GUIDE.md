# Rank Math SEO CSV Import - Instrukcja krok po kroku

## Co zostało przygotowane

Dodano funkcjonalność importu Rank Math SEO z pliku CSV do narzędzia migracji. Teraz możesz zaimportować wszystkie meta tagi Rank Math bezpośrednio z wyeksportowanego pliku CSV.

## Krok 1: Przygotowanie pliku CSV

1. Masz już wyeksportowany plik CSV: `kpgio_rank-math-2026-01-23_21-00-29.csv`
2. **Na live:** Skopiuj ten plik na serwer (możesz użyć FTP/SFTP lub upload przez WordPress)

## Krok 2: Dostęp do narzędzia importu

### Opcja A: Przez WordPress Admin (NAJŁATWIEJSZE)

1. Zaloguj się do WordPress Admin na **nowej stronie** (live)
2. Przejdź do: **Tools → Rank Math Migration**
3. Zobaczysz sekcję **"Method 1: Import from CSV File"**

### Opcja B: Przez WP-CLI (jeśli masz dostęp SSH)

```bash
cd /path/to/wordpress
wp kpg-import-rankmath-csv /path/to/kpgio_rank-math-2026-01-23_21-00-29.csv --old-site-url=https://www.kpgio.pl
```

## Krok 3: Import przez WordPress Admin

1. **W sekcji "Method 1: Import from CSV File":**
   - Kliknij **"Choose File"** i wybierz plik `kpgio_rank-math-2026-01-23_21-00-29.csv`
   - W polu **"Old Site URL"** wpisz: `https://www.kpgio.pl` (lub URL starej strony)
   - Kliknij **"Import from CSV"**

2. **Poczekaj na zakończenie:**
   - Proces może zająć 1-3 minuty w zależności od liczby wpisów
   - Zobaczysz komunikat z wynikami:
     - Liczba zaimportowanych postów
     - Liczba zaimportowanych kategorii/tagów
     - Liczba zaimportowanych autorów
     - Liczba pominiętych elementów (jeśli nie znaleziono w nowej bazie)

## Krok 4: Weryfikacja

1. **Sprawdź kilka postów:**
   - Otwórz edytor posta w WordPress
   - Sprawdź, czy w panelu Rank Math SEO są wypełnione:
     - Title (SEO Title)
     - Description (Meta Description)
     - Focus Keyword
     - Canonical URL
     - Schema Data (jeśli było ustawione)

2. **Sprawdź frontend:**
   - Otwórz kilka postów na stronie
   - Sprawdź źródło strony (Ctrl+U) i poszukaj:
     - `<title>` - powinien zawierać SEO title
     - `<meta name="description">` - powinien zawierać meta description
     - `<link rel="canonical">` - powinien wskazywać na poprawny URL

5. **Canonical:** Jeśli w CSV brakowało kolumny canonical lub miała inną nazwę:
   - Import obsługuje kolumny: `canonical_url`, `Canonical URL`, `Canonical`, `canonical`.
   - Na stronie **Tools → Rank Math Migration** użyj **„Uzupełnij canonical z permalinków”**, aby ustawić canonical na permalink dla postów, które mają SEO title/description, ale nie mają canonical.

## Co jest importowane z CSV

### Dla postów:
- ✅ SEO Title (`seo_title` → `rank_math_title`)
- ✅ SEO Description (`seo_description` → `rank_math_description`)
- ✅ Focus Keyword (`focus_keyword` → `rank_math_focus_keyword`)
- ✅ Robots (`robots` → `rank_math_robots`)
- ✅ Advanced Robots (`advanced_robots` → `rank_math_advanced_robots`)
- ✅ Canonical URL (`canonical_url` / `Canonical URL` / `Canonical` / `canonical` → `rank_math_canonical_url`)
- ✅ SEO Score (`seo_score` → `rank_math_seo_score`)
- ✅ Facebook Title (`social_facebook_title` → `rank_math_facebook_title`)
- ✅ Facebook Description (`social_facebook_description` → `rank_math_facebook_description`)
- ✅ Facebook Image (`social_facebook_thumbnail` → `rank_math_facebook_image`)
- ✅ Twitter Title (`social_twitter_title` → `rank_math_twitter_title`)
- ✅ Twitter Description (`social_twitter_description` → `rank_math_twitter_description`)
- ✅ Twitter Image (`social_twitter_thumbnail` → `rank_math_twitter_image`)
- ✅ Schema Data (`schema_data` → `rank_math_schema_data`)
- ✅ Primary Category (`primary_term` → `rank_math_primary_category`)
- ✅ Pillar Content (`is_pillar_content` → `rank_math_pillar_content`)

### Dla kategorii/tagów:
- ✅ SEO Title
- ✅ SEO Description
- ✅ Robots
- ✅ Canonical URL

### Dla autorów:
- ✅ SEO Title
- ✅ SEO Description
- ✅ Robots
- ✅ Canonical URL

## Automatyczna zamiana URL-i

Jeśli podasz **Old Site URL** (np. `https://www.kpgio.pl`), wszystkie URL-e w meta tagach zostaną automatycznie zamienione na URL nowej strony. To dotyczy:
- Canonical URLs
- Obrazków w meta tagach (Facebook, Twitter)
- Wszystkich innych URL-i w danych

## Co jeśli post/term/user nie został znaleziony?

Jeśli post, kategoria lub autor nie został znaleziony w nowej bazie (np. slug się zmienił), zostanie pominięty. W raporcie zobaczysz:
- "Post not found (skipped): nazwa-sluga"

W takim przypadku możesz:
1. Sprawdzić, czy post istnieje w nowej bazie
2. Jeśli slug się zmienił, możesz ręcznie zaktualizować meta tagi w edytorze WordPress

## Rozwiązywanie problemów

### "CSV file not found"
- Upewnij się, że plik został poprawnie wgrany
- Sprawdź uprawnienia do pliku (powinien być czytelny)

### "Rank Math SEO plugin is not active"
- Zainstaluj i aktywuj plugin Rank Math SEO przed importem

### "Post not found (skipped)"
- Post o danym slug nie istnieje w nowej bazie
- Sprawdź, czy post został poprawnie zmigrowany
- Możesz ręcznie zaktualizować meta tagi w edytorze

### Import trwa długo
- Dla 90+ postów import może zająć 2-5 minut
- To normalne - nie przerywaj procesu

## Co skopiować na live?

### Pliki do skopiowania:

1. **Plugin z aktualizacją:**
   ```
   wp-content/plugins/kpg-elementor-widgets/includes/rank-math-migration.php
   ```
   (Ten plik już jest w pluginie, więc jeśli masz najnowszą wersję, nie musisz nic kopiować)

2. **Plik CSV:**
   ```
   kpgio_rank-math-2026-01-23_21-00-29.csv
   ```
   Skopiuj ten plik na serwer (możesz wgrać przez FTP lub upload przez WordPress)

### Jak wgrać plik CSV przez WordPress:

1. Zaloguj się do WordPress Admin
2. Przejdź do **Media → Add New**
3. Wgraj plik CSV (choć WordPress może nie pokazać go w bibliotece, to nie problem)
4. Albo użyj FTP/SFTP i wgraj plik do katalogu `wp-content/uploads/`

### Alternatywnie - użyj WP-CLI:

Jeśli masz dostęp SSH, możesz wgrać plik przez SCP:

```bash
scp kpgio_rank-math-2026-01-23_21-00-29.csv user@server:/path/to/wordpress/
```

A potem uruchomić import:

```bash
ssh user@server
cd /path/to/wordpress
wp kpg-import-rankmath-csv kpgio_rank-math-2026-01-23_21-00-29.csv --old-site-url=https://www.kpgio.pl
```

## Podsumowanie - szybki start

1. ✅ **Masz plik CSV** - `kpgio_rank-math-2026-01-23_21-00-29.csv`
2. ✅ **Wgraj plik na serwer** (FTP lub przez WordPress Media)
3. ✅ **Zaloguj się do WordPress Admin** na nowej stronie
4. ✅ **Przejdź do Tools → Rank Math Migration**
5. ✅ **Wybierz plik CSV** i wpisz Old Site URL: `https://www.kpgio.pl`
6. ✅ **Kliknij "Import from CSV"**
7. ✅ **Poczekaj na zakończenie** (1-3 minuty)
8. ✅ **Sprawdź wyniki** i zweryfikuj kilka postów

Gotowe! 🎉
