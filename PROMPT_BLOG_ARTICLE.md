# 📝 Prompt do generowania artykułów blogowych

## 🎯 Instrukcje dla AI

Jesteś ekspertem w pisaniu profesjonalnych artykułów prawnych i edukacyjnych dla bloga o kontroli dotacji oświatowych. Twoim zadaniem jest stworzenie kompletnego artykułu zgodnie z poniższymi wytycznymi.

---

## 📋 Struktura artykułu

### 1. Treść główna

Artykuł powinien być podzielony na **3-6 sekcji** używając nagłówków `<h2>` lub `<h3>`. Każda sekcja powinna zawierać:

- **Nagłówek** (h2 lub h3) - krótki, opisowy tytuł sekcji (5-10 słów)
- **Treść** - 2-4 akapity rozwijające temat sekcji (każdy akapit 3-5 zdań)
- **Formatowanie** - użyj HTML: `<p>`, `<strong>`, `<em>`, `<ul>`, `<ol>`, `<li>`

**Przykład struktury:**
```html
<h2>Tytuł sekcji 1</h2>
<p>Treść pierwszej sekcji z kluczowymi informacjami...</p>
<p>Kolejny akapit rozwijający temat...</p>

<h2>Tytuł sekcji 2</h2>
<p>Treść drugiej sekcji...</p>
<ul>
<li>Punkt pierwszy</li>
<li>Punkt drugi</li>
</ul>
```

### 2. Sekcja "Ważne" (opcjonalna)

**Kiedy używać sekcji "Ważne":**
- Artykuł zawiera ważne informacje prawne wymagające podkreślenia
- Są ostrzeżenia lub kluczowe terminy, które czytelnik musi znać
- Trzeba wyróżnić szczególnie istotne informacje

**Zasady tworzenia sekcji "Ważne":**
- **Długość**: 50-150 słów (1-3 zdania, maksymalnie 2-3 akapity)
- **Styl**: Krótkie, zwięzłe, bezpośrednie informacje
- **Treść**: Tylko najważniejsze informacje - bez zbędnych szczegółów
- **Format**: Czysty tekst bez HTML (HTML zostanie dodany automatycznie)

**Przykład dobrej sekcji "Ważne":**
```
Zgłoszenia mogą być anonimowe lub podpisane – w obu przypadkach ustawa wymaga zachowania poufności (art. 8 ustawy). Tożsamość sygnalisty nie może być ujawniona bez jego wyraźnej zgody, chyba że wymagają tego przepisy szczególne.
```

**Przykład złej sekcji "Ważne" (za długa):**
```
Zgłoszenia mogą być anonimowe lub podpisane. W obu przypadkach ustawa wymaga zachowania poufności zgodnie z art. 8 ustawy. Tożsamość sygnalisty nie może być ujawniona bez jego wyraźnej zgody, chyba że wymagają tego przepisy szczególne. Dodatkowo, szkoła ma obowiązek zapewnić odpowiednie procedury ochrony danych osobowych zgodnie z RODO. Wszystkie zgłoszenia muszą być rejestrowane w specjalnym rejestrze, który podlega kontroli organów nadzorczych...
```

---

## 📝 Szablon prompta

Skopiuj poniższy szablon i wypełnij go:

```
# Artykuł blogowy: [TYTUŁ ARTYKUŁU]

## Temat artykułu:
[Tutaj opisz temat artykułu, cel, grupę docelową - 2-3 zdania]

## Treść główna:

[Wklej tutaj treść artykułu z nagłówkami h2/h3 i akapitami w HTML]

## Sekcja "Ważne" (jeśli potrzebna):

[Tutaj wklej tekst sekcji "Ważne" - tylko tekst, bez HTML, 50-150 słów]

## Pozycja sekcji "Ważne":
[Wybierz jedną z opcji: after_1, after_2, after_3, after_4, after_5, after_6, lub end]
```

---

## 💡 Przykład użycia

```
# Artykuł blogowy: Zgłoszenia sygnalistów w oświacie

## Temat artykułu:
Artykuł wyjaśniający procedurę zgłoszeń sygnalistów w kontekście dotacji oświatowych. 
Grupa docelowa: dyrektorzy szkół, samorządowcy, osoby odpowiedzialne za kontrolę dotacji.

## Treść główna:

<h2>Kto może być sygnalistą?</h2>
<p>Sygnalistą może być każda osoba, która posiada informacje o nieprawidłowościach 
w zakresie dotacji oświatowych. Ustawa o ochronie sygnalistów definiuje sygnalistę 
jako osobę zgłaszającą informacje o naruszeniach prawa.</p>

<p>W kontekście oświaty, sygnalistami mogą być:</p>
<ul>
<li>Pracownicy szkół i placówek oświatowych</li>
<li>Rodzice uczniów</li>
<li>Członkowie społeczności lokalnej</li>
</ul>

<h2>Procedura zgłoszenia</h2>
<p>Zgłoszenie powinno być złożone na piśmie lub elektronicznie do właściwego organu. 
Szkoła ma obowiązek zapewnić odpowiednie kanały komunikacji dla sygnalistów.</p>

<h2>Ochrona sygnalisty</h2>
<p>Ustawa gwarantuje ochronę sygnalisty przed represjami ze strony pracodawcy lub 
innych osób. Ochrona obejmuje zakaz zwolnienia, degradacji oraz innych działań 
dyskryminujących.</p>

## Sekcja "Ważne":

Zgłoszenia mogą być anonimowe lub podpisane – w obu przypadkach ustawa wymaga 
zachowania poufności (art. 8 ustawy). Tożsamość sygnalisty nie może być ujawniona 
bez jego wyraźnej zgody, chyba że wymagają tego przepisy szczególne.

## Pozycja sekcji "Ważne":
after_3
```

---

## ✅ Wytyczne stylistyczne

### Treść główna:
- **Ton**: Profesjonalny, ale przystępny
- **Język**: Polski, poprawny gramatycznie
- **Długość**: 800-1500 słów
- **Nagłówki**: Krótkie, konkretne (5-10 słów)
- **Akapity**: 3-5 zdań każdy

### Sekcja "Ważne":
- **Długość**: 50-150 słów (1-3 zdania)
- **Styl**: Krótkie, zwięzłe, bezpośrednie
- **Treść**: Tylko najważniejsze informacje
- **Format**: Czysty tekst bez HTML

### Formatowanie HTML:
- `<h2>` - główne sekcje
- `<h3>` - podsekcje (opcjonalnie)
- `<p>` - akapity
- `<strong>` - ważne informacje
- `<em>` - nacisk
- `<ul>`, `<ol>`, `<li>` - listy
- `<a href="...">` - linki (jeśli potrzebne)

---

## 🎯 Pozycje sekcji "Ważne"

Wybierz jedną z poniższych pozycji:

- **`after_1`** - Po pierwszej sekcji (0.1)
- **`after_2`** - Po drugiej sekcji (0.2)
- **`after_3`** - Po trzeciej sekcji (0.3) ⭐ **Najczęściej używane**
- **`after_4`** - Po czwartej sekcji (0.4)
- **`after_5`** - Po piątej sekcji (0.5)
- **`after_6`** - Po szóstej sekcji (0.6)
- **`end`** - Na końcu artykułu (przed sekcją o autorze)

**Rekomendacja**: Używaj `after_3` jeśli artykuł ma 4-6 sekcji, lub `end` jeśli sekcja "Ważne" podsumowuje cały artykuł.

---

## 📋 Checklist przed wysłaniem

- [ ] Artykuł ma 3-6 sekcji z nagłówkami `<h2>` lub `<h3>`
- [ ] Każda sekcja ma 2-4 akapity treści
- [ ] Sekcja "Ważne" jest krótka i zwięzła (50-150 słów) - jeśli jest potrzebna
- [ ] Sekcja "Ważne" zawiera tylko najważniejsze informacje
- [ ] Wszystkie HTML tagi są poprawnie zamknięte
- [ ] Tekst jest poprawny gramatycznie
- [ ] Treść jest zgodna z tematem
- [ ] Pozycja sekcji "Ważne" jest wybrana (jeśli sekcja jest używana)

---

## 🚀 Szybki szablon do kopiowania

```
# Artykuł blogowy: 

## Temat artykułu:


## Treść główna:



## Sekcja "Ważne" (jeśli potrzebna):


## Pozycja sekcji "Ważne":
[after_1 / after_2 / after_3 / after_4 / after_5 / after_6 / end]
```

---

## 📌 Ważne uwagi

1. **Sekcja "Ważne" jest opcjonalna** - nie każdy artykuł musi ją mieć
2. **Domyślnie sekcja "Ważne" jest wyłączona** - trzeba ją włączyć w ustawieniach widgetu w Elementorze
3. **Tekst sekcji "Ważne" powinien być krótki** - maksymalnie 150 słów
4. **Pozycja sekcji "Ważne" jest ważna** - wybierz miejsce, gdzie informacja będzie najbardziej widoczna
5. **HTML w sekcji "Ważne"** - nie używaj HTML, tylko czysty tekst (HTML zostanie dodany automatycznie)

---

## 🔧 Jak włączyć sekcję "Ważne" w Elementorze

Po wygenerowaniu artykułu:

1. Otwórz artykuł w edytorze Elementora
2. Znajdź widget **"KPG Blog Content"**
3. W panelu ustawień:
   - **Show Important Section**: Włącz (Yes)
   - **Important Position**: Wybierz pozycję (np. `after_3`)
   - **Important Text**: Wklej tekst sekcji "Ważne" z prompta
4. Zapisz zmiany

---

_Utworzono: 2025-01-27_
_Status: Aktualny - zgodny z aktualną implementacją widgetu_
