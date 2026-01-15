# Instrukcja konfiguracji posta blogowego

## Treść posta

Treść posta została przygotowana w pliku `blog-post-content.html`. Skopiuj całą zawartość tego pliku do edytora WordPress (w trybie tekstowym/HTML).

## Konfiguracja widgetu "KPG Blog Content" w Elementorze

### 1. Sekcja "Ważne"

W ustawieniach widgetu:

- **Show Important Section**: Włącz (Yes)
- **Important Position**: Wybierz `after_3` (po sekcji 0.3 "Kim jest sygnalista?")
- **Important Text**: Wklej następujący tekst:

```
Zgłoszenia mogą być anonimowe lub podpisane – w obu przypadkach ustawa wymaga zachowania poufności (art. 8 ustawy). Tożsamość sygnalisty nie może być ujawniona bez jego wyraźnej zgody, chyba że wymagają tego przepisy szczególne.
```

### 2. Sekcja o autorze

- **Show Author Section**: Włącz (Yes) - sekcja zostanie automatycznie wygenerowana na podstawie danych autora posta

### 3. Intro paragraph

- **Show Intro Paragraph**: Włącz (Yes) - pierwszy paragraf zostanie wyświetlony jako intro przed sekcjami

## Struktura posta

1. **Tytuł**: Poufność danych sygnalistów w szkole
2. **Intro**: Paragraf wprowadzający
3. **Sekcja 0.1**: O czym jest ustawa o ochronie sygnalistów?
4. **Sekcja 0.2**: Co może być zgłaszane w szkole?
5. **Sekcja 0.3**: Kim jest sygnalista?
6. **Sekcja "Ważne"**: (wstawiana po 0.3)
7. **Sekcja 0.4**: Jakie dokumenty powinna przygotować szkoła?
8. **Sekcja 0.5**: Jakie kary przewiduje ustawa?
9. **Sekcja 0.6**: Podsumowanie
10. **Sekcja 0.7**: Pomoc prawna w zakresie wdrożenia regulacji dot. ochrony sygnalistów
11. **Sekcja o autorze**: (automatycznie na końcu)

## Uwagi

- Widget automatycznie parsuje treść i tworzy sekcje na podstawie nagłówków `<h2>`
- Numeracja (0.1, 0.2, etc.) jest dodawana automatycznie
- Sekcja "Ważne" jest wstawiana dynamicznie w miejscu określonym w ustawieniach
- Sekcja o autorze jest zawsze na końcu i pobiera dane z profilu autora posta





