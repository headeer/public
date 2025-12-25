/**
 * Prosty skrypt do dodania 5 slide'ów do Team Slider w Elementorze
 * 
 * Użycie:
 * 1. Otwórz stronę w edytorze Elementora
 * 2. Wybierz widget "KPG Team Slider"
 * 3. Upewnij się, że panel "Team Members" jest otwarty
 * 4. Otwórz konsolę przeglądarki (F12)
 * 5. Wklej ten skrypt
 * 6. Wywołaj: add5Slides()
 */

(function() {
  'use strict';

  // Dane dla 5 slide'ów
  var slidesData = [
    {
      name: 'Daria Bezwińska',
      job_title: 'Adwokat',
      intro_text: 'Absolwentka kierunku Prawo na Wydziale Prawa, Administracji i Ekonomii Uniwersytetu Wrocławskiego. W 2012 r. ukończyła Studia Podyplomowe – Studia Kształcenia Tłumaczy Języków Romańskich (grupa francuska), a w 2019 r. Studia Podyplomowe Prawo Medyczne i Bioetyka na Uniwersytecie Jagiellońskim. Tytuł adwokata uzyskała w 2016 r.',
      text_left: '<p>Doświadczenie zawodowe zdobywała od 2009 r. w kancelariach adwokackich, radcowskich jak również na stanowisku prawnika in house w firmie leasingowej.</p>\n\n<p>Świadczy bieżące doradztwo prawne dla przedsiębiorców, uczestniczy w negocjacjach kontraktów, sporządza umowy (w tym umowy spółek), opinie prawne i pisma, pomaga odzyskiwać należności.</p>\n\n<p>Specjalizuje się w prawie oświatowym, cywilnym, gospodarczym w tym w prawie handlowym, w prawie umów jak również w prawie administracyjnym i sądowoadministracyjnym.</p>\n\n<p>Świadczy usługi kompleksowej obsługi prawnej placówek oświatowych w szczególności niesamorządowych, poczynając od powoływania placówek, jak również ich bieżącej obsługi i likwidacji.</p>\n\n<p>Udziela wsparcia przy kontrolach organów nadzoru pedagogicznego, jednostek ewidencyjnych, a także reprezentuje placówki w toku postępowania administracyjnego jak również sądowoadministracyjnego w zakresie wykorzystania i wydatkowania dotacji oświatowych.</p>',
      text_right: '<p>Z powodzeniem reprezentuje nauczycieli i placówki, jak również rodziców w toku postępowań dyscyplinarnych przed Komisjami Dyscyplinarnymi.</p>\n\n<p>Od 2019 r. związana jest również z środowiskiem placówek oświatowych przyjaznych edukacji domowej w Polsce.</p>\n\n<p>Prelegent konferencji naukowych i branżowych związanych z oświatą min. prelegentka XII Ogólnopolskiej Konferencji Samorządu i Oświaty EDUKACJA PRZYSZŁOŚCI.</p>',
      image_url: 'https://www.kontroladotacjioswiatowych.pl/wp-content/uploads/image-1-1.png'
    },
    {
      name: 'Konrad Wiśniewski',
      job_title: 'Adwokat',
      intro_text: 'Od 2014 roku jest członkiem Krakowskiej Izby Adwokackiej. W 2017 roku uzyskał wpis na listę adwokatów. Doświadczenie zawodowe zdobywał w krakowskich kancelariach adwokackich świadcząc kompleksową pomoc prawną zarówno na rzecz klientów indywidualnych jak i podmiotów gospodarczych. Pracował również na stanowisku asystenta sędziego.',
      text_left: '<p>Od początku pracy zawodowej zajmował się zagadnieniami związanymi z prawem cywilnym, prawem gospodarczym, w tym prawem spółek handlowych oraz prawem administracyjnym. Specjalizuje się w następujących zagadnieniach:</p>\n\n<p>Audyt prawny nieruchomości</p>\n\n<p>Opiniowanie i negocjowanie umów cesji wierzytelności bankowych objętych zabezpieczeniem hipotecznym, umów o roboty budowlane, umów najmu lokali użytkowych</p>\n\n<p>Obsługa prawna deweloperów w zakresie inwestycji mieszkaniowych oraz usługowych obejmujących: zakupy gruntów, projektowanie, realizację inwestycji budowlanych, sprzedaż oraz wynajem budynków lub powierzchni, administrację i zarządzanie nieruchomościami</p>\n\n<p>Zarządzanie zespołami zajmującymi się procesem przenoszenia wierzytelności hipotecznych na nabywcę</p>\n\n<p>Zarządzanie zespołami zajmującymi się obsługą procesu windykacji wierzytelności hipotecznych</p>',
      text_right: '<p>Sporządzanie, opiniowanie i negocjowanie umów datio in solutum</p>\n\n<p>Procesy cywilne poprzez stadium jurysdykcyjne, a następnie egzekucję z nieruchomości</p>\n\n<p>Przekształcanie nieruchomości rolnych do komercjalizacji.</p>\n\n<p>W zakresie jego specjalizacji znajduje się także pomoc prawna z dziedziny prawa oświatowego.</p>\n\n<p>W ramach współpracy Kancelarii odpowiedzialny jest za współpracę ze szkołami oraz placówkami oświatowymi, świadcząc usługi w zakresie doradztwa, opracowania dokumentów prawnych, a także sporządzania statutów i umów.</p>\n\n<p>Reprezentuje klientów przed sądami powszechnymi oraz organami administracji rządowej i samorządowej i sądami administracyjnymi.</p>',
      image_url: 'https://www.kontroladotacjioswiatowych.pl/wp-content/uploads/image-1-1-1.png'
    },
    {
      name: 'Mateusz Pęczkowski',
      job_title: 'RADCA PRAWNY',
      intro_text: 'Radca Prawny, absolwent Krakowskiej Akademii im. Andrzeja Frycza Modrzewskiego. Aplikację radcowską ukończył przy Okręgowej Izbie Radców Prawnych w Warszawie. Ukończył również studia podyplomowe z prawa pracy na Uniwersytecie Jagiellońskim. Posiada uprawnienia audytora wewnętrznego systemu zarządzania bezpieczeństwem informacji ISO 27001.',
      text_left: '<p>Doświadczenie zawodowe zdobywał w kancelariach w Krakowie, Warszawie, Łodzi i Gdańsku. Od 2017 roku zajmuje się ochroną danych osobowych oraz obsługą prawną przedsiębiorców. Prowadzi także praktykę wspierającą osoby zadłużone.</p>\n\n<p>Prywatnie interesuje się piłką nożną, kibic Interu Mediolan i fan Calcio. Klientom w trudnych momentach często przypomina boiskowe powiedzenie „Con calma si fa tutto."</p>',
      text_right: '<p>W kancelarii zajmuje się w szczególności zagadnieniami związanymi z ochroną danych osobowych, prawem pracy oraz przygotowywaniem i opiniowaniem umów.</p>',
      image_url: 'https://www.kontroladotacjioswiatowych.pl/wp-content/uploads/image-1-2.png'
    },
    {
      name: 'Agnieszka Rutkowska',
      job_title: 'Aplikantka adwokacka',
      intro_text: 'Absolwentka na kierunku Prawo na Uniwersytecie Ekonomicznym w Krakowie. W 2024 roku ukończyła z wyróżnieniem studia magisterskie na kierunku Administracja, specjalność finanse i zamówienia publiczne. Słuchaczka na studiach podyplomowych „Prawo umów w obrocie konsumenckim i profesjonalnym" prowadzonych na Uniwersytecie Jagiellońskim w Krakowie. Przez okres studiów zaangażowana w działalność w samorządzie studenckim na różnych szczeblach, m.in. związana z obszarem dydaktyki i jakości kształcenia.',
      text_left: '<p>W samorządzie studenckim pełniła funkcje zarówno w zarządzie na stanowiskach ds. Dydaktyki i Jakości Kształcenia, ds. Prawnych, jak i sprawowała stanowisko Wiceprzewodniczącej Samorządu. Zasiadała w komisji rewizyjnej, a także komisjach dyscyplinarnych ds. studentów oraz nauczycieli akademickich.</p>\n\n<p>Założycielka studenckiego koła naukowego – Koło Nauk Penalnych „Scientia Nobilitat" działającego przy Uniwersytecie Ekonomicznym w Krakowie. Laureatka nagrody Santander Universidades dla najlepszej studentki w Instytucie Prawa w roku akademickim 2021/2022.</p>\n\n<p>W Kancelarii zajmuje się obsługą prawną placówek oświatowych, w tym uczestniczy w procesie ich zakładania. Uczestniczy w postępowaniach dyscyplinarnych przed Komisjami Dyscyplinarnymi dla Nauczycieli. Udziela wsparcia w zakresie ograniczenia zatrudnienia nauczycieli, urlopów dla poratowania zdrowia, kwalifikacji nauczycieli oraz awansu zawodowego, a także w sprawach z zakresu prawa</p>',
      text_right: '<p>o szkolnictwie wyższym.</p>\n\n<p>Doświadczenie zawodowe zdobywała w krakowskich kancelariach adwokackich, a także podczas praktyk w Biurze Rzecznika Praw Obywatelskich oraz Biurze Zamówień Publicznych Starostwa Powiatowego w Krakowie.</p>',
      image_url: 'https://www.kontroladotacjioswiatowych.pl/wp-content/uploads/image-1-3.png'
    },
    {
      name: 'Aleksandra Nowicka',
      job_title: 'Aplikantka adwokacka',
      intro_text: 'Absolwentka kierunku Prawo oraz kierunku Administracja ze specjalnością w finansach i zamówieniach publicznych na Uniwersytecie Ekonomicznym w Krakowie. Laureatka nagrody dla najlepszej studentki Instytutu Polityk Publicznych i Administracji w roku akademickim 2022/2023.',
      text_left: '<p>W czasie studiów aktywnie angażowała się w działalność samorządu studenckiego – pełniła m.in. funkcję członka zarządu ds. prawnych, przewodniczącej komisji rewizyjnej oraz członka uczelnianej komisji wyborczej.</p>\n\n<p>Założycielka i była przewodnicząca Koła Naukowego Prawa Administracyjnego „Administrare", działającego przy Katedrze Prawa Konstytucyjnego, Administracyjnego oraz Zamówień Publicznych Uniwersytetu Ekonomicznego w Krakowie. Autorka publikacji naukowych z zakresu prawa administracyjnego i publicznego.</p>',
      text_right: '<p>W Kancelarii zajmuje się sporządzaniem i edycją statutów oraz regulaminów różnego rodzaju placówek oświatowych. Udziela wsparcia w zakresie analizy kwalifikacji nauczycieli, systemu edukacji domowej, skreślenia uczniów z listy oraz w sprawach dotacji oświatowych.</p>\n\n<p>Doświadczenie zawodowe zdobywała w krakowskich kancelariach prawnych, działach prawnych spółek oraz podczas staży zagranicznych. Obecnie odbywa aplikację adwokacką przy Okręgowej Radzie Adwokackiej w Krakowie.</p>',
      image_url: 'https://www.kontroladotacjioswiatowych.pl/wp-content/uploads/image-1-4.png'
    }
  ];

  /**
   * Wypełnia pola repeatera danymi
   */
  function fillRepeaterFields(memberData, index) {
    var repeaterItems = document.querySelectorAll('.elementor-repeater-fields');
    if (index >= repeaterItems.length) {
      console.error('Indeks ' + index + ' jest poza zakresem. Dostępne elementy: ' + repeaterItems.length);
      return false;
    }
    
    var repeaterItem = repeaterItems[index];
    if (!repeaterItem) {
      console.error('Nie znaleziono elementu repeatera o indeksie ' + index);
      return false;
    }
    
    console.log('Wypełnianie pól dla slide\'a #' + (index + 1) + ':', memberData.name);
    
    // 1. Wypełnij obrazek thumbnail
    if (memberData.image_url) {
      var imageInput = repeaterItem.querySelector('input[data-setting="image"]');
      if (imageInput) {
        // Spróbuj wyciągnąć ID z URL
        var imageId = '';
        var imageIdMatch = memberData.image_url.match(/wp-image-(\d+)/);
        if (imageIdMatch) {
          imageId = imageIdMatch[1];
        }
        
        var imageValue = JSON.stringify({
          url: memberData.image_url,
          id: imageId,
          alt: memberData.name || ''
        });
        
        imageInput.value = imageValue;
        
        // Wywołaj event change - Elementor nasłuchuje na to
        var changeEvent = new Event('change', { bubbles: true, cancelable: true });
        imageInput.dispatchEvent(changeEvent);
        
        // Zaktualizuj preview
        setTimeout(function() {
          var imageControl = imageInput.closest('.elementor-control');
          if (imageControl) {
            var previewArea = imageControl.querySelector('.elementor-control-media__preview');
            if (previewArea && memberData.image_url) {
              previewArea.style.backgroundImage = 'url(' + memberData.image_url + ')';
              previewArea.style.display = 'block';
              var uploadButton = imageControl.querySelector('.elementor-control-media-upload-button');
              if (uploadButton) {
                uploadButton.style.display = 'none';
              }
            }
          }
        }, 100);
        
        console.log('✓ Obrazek thumbnail ustawiony:', memberData.image_url);
      } else {
        console.warn('Nie znaleziono pola obrazka thumbnail');
      }
    }
    
    // 1b. Wypełnij główny obrazek
    var mainImageUrl = memberData.main_image_url || memberData.image_url;
    if (mainImageUrl) {
      var mainImageInput = repeaterItem.querySelector('input[data-setting="main_image"]');
      if (mainImageInput) {
        // Spróbuj wyciągnąć ID z URL
        var mainImageId = '';
        var mainImageIdMatch = mainImageUrl.match(/wp-image-(\d+)/);
        if (mainImageIdMatch) {
          mainImageId = mainImageIdMatch[1];
        }
        
        var mainImageValue = JSON.stringify({
          url: mainImageUrl,
          id: mainImageId,
          alt: memberData.name || ''
        });
        
        mainImageInput.value = mainImageValue;
        
        // Wywołaj event change
        var mainImageChangeEvent = new Event('change', { bubbles: true, cancelable: true });
        mainImageInput.dispatchEvent(mainImageChangeEvent);
        
        // Zaktualizuj preview
        setTimeout(function() {
          var mainImageControl = mainImageInput.closest('.elementor-control');
          if (mainImageControl) {
            var mainPreviewArea = mainImageControl.querySelector('.elementor-control-media__preview');
            if (mainPreviewArea && mainImageUrl) {
              mainPreviewArea.style.backgroundImage = 'url(' + mainImageUrl + ')';
              mainPreviewArea.style.display = 'block';
              var mainUploadButton = mainImageControl.querySelector('.elementor-control-media-upload-button');
              if (mainUploadButton) {
                mainUploadButton.style.display = 'none';
              }
            }
          }
        }, 150);
        
        console.log('✓ Główny obrazek ustawiony:', mainImageUrl);
      } else {
        console.warn('Nie znaleziono pola głównego obrazka');
      }
    }
    
    // 2. Wypełnij object_position
    var objectPositionInput = repeaterItem.querySelector('input[data-setting="object_position"]');
    if (objectPositionInput) {
      objectPositionInput.value = memberData.object_position || 'center center';
      objectPositionInput.dispatchEvent(new Event('input', { bubbles: true }));
      objectPositionInput.dispatchEvent(new Event('change', { bubbles: true }));
      console.log('✓ Object position ustawiony:', objectPositionInput.value);
    }
    
    // 3. Wypełnij name
    if (memberData.name) {
      var nameInput = repeaterItem.querySelector('input[data-setting="name"]');
      if (nameInput) {
        nameInput.value = memberData.name;
        nameInput.dispatchEvent(new Event('input', { bubbles: true }));
        nameInput.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('✓ Name ustawiony:', memberData.name);
      } else {
        console.warn('Nie znaleziono pola name');
      }
    }
    
    // 4. Wypełnij job_title
    if (memberData.job_title) {
      var jobTitleInput = repeaterItem.querySelector('input[data-setting="job_title"]');
      if (jobTitleInput) {
        jobTitleInput.value = memberData.job_title;
        jobTitleInput.dispatchEvent(new Event('input', { bubbles: true }));
        jobTitleInput.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('✓ Job title ustawiony:', memberData.job_title);
      } else {
        console.warn('Nie znaleziono pola job_title');
      }
    }
    
    // 5. Wypełnij intro_text
    if (memberData.intro_text) {
      var introTextInput = repeaterItem.querySelector('textarea[data-setting="intro_text"]');
      if (introTextInput) {
        introTextInput.value = memberData.intro_text;
        introTextInput.dispatchEvent(new Event('input', { bubbles: true }));
        introTextInput.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('✓ Intro text ustawiony');
      } else {
        console.warn('Nie znaleziono pola intro_text');
      }
    }
    
    // 6. Wypełnij text_left (WYSIWYG)
    if (memberData.text_left) {
      // Znajdź textarea dla text_left - szukaj w kontrolce text_left
      var textLeftControl = repeaterItem.querySelector('.elementor-control-text_left');
      if (textLeftControl) {
        var textLeftTextarea = textLeftControl.querySelector('textarea.elementor-wp-editor');
        if (textLeftTextarea && textLeftTextarea.id) {
          // Ustaw wartość w textarea
          textLeftTextarea.value = memberData.text_left;
          
          // Dla TinyMCE - poczekaj aż będzie gotowy
          var setTinyMCEContent = function() {
            if (window.tinymce) {
              var editor = window.tinymce.get(textLeftTextarea.id);
              if (editor) {
                editor.setContent(memberData.text_left);
                editor.save();
                // Wywołaj event na textarea
                textLeftTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                textLeftTextarea.dispatchEvent(new Event('change', { bubbles: true }));
                console.log('✓ Text left ustawiony (TinyMCE)');
                return true;
              }
            }
            return false;
          };
          
          if (!setTinyMCEContent()) {
            // Poczekaj na inicjalizację TinyMCE
            var waitCount = 0;
            var waitInterval = setInterval(function() {
              waitCount++;
              if (setTinyMCEContent() || waitCount > 20) {
                clearInterval(waitInterval);
                if (waitCount > 20) {
                  console.warn('Timeout czekania na TinyMCE dla text_left');
                }
              }
            }, 100);
          }
        } else {
          console.warn('Nie znaleziono textarea dla text_left');
        }
      } else {
        console.warn('Nie znaleziono kontrolki text_left');
      }
    }
    
    // 7. Wypełnij text_right (WYSIWYG)
    if (memberData.text_right) {
      // Znajdź textarea dla text_right - szukaj w kontrolce text_right
      var textRightControl = repeaterItem.querySelector('.elementor-control-text_right');
      if (textRightControl) {
        var textRightTextarea = textRightControl.querySelector('textarea.elementor-wp-editor');
        if (textRightTextarea && textRightTextarea.id) {
          // Ustaw wartość w textarea
          textRightTextarea.value = memberData.text_right;
          
          // Dla TinyMCE - poczekaj aż będzie gotowy
          var setTinyMCEContent = function() {
            if (window.tinymce) {
              var editor = window.tinymce.get(textRightTextarea.id);
              if (editor) {
                editor.setContent(memberData.text_right);
                editor.save();
                // Wywołaj event na textarea
                textRightTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                textRightTextarea.dispatchEvent(new Event('change', { bubbles: true }));
                console.log('✓ Text right ustawiony (TinyMCE)');
                return true;
              }
            }
            return false;
          };
          
          if (!setTinyMCEContent()) {
            // Poczekaj na inicjalizację TinyMCE
            var waitCount = 0;
            var waitInterval = setInterval(function() {
              waitCount++;
              if (setTinyMCEContent() || waitCount > 20) {
                clearInterval(waitInterval);
                if (waitCount > 20) {
                  console.warn('Timeout czekania na TinyMCE dla text_right');
                }
              }
            }, 100);
          }
        } else {
          console.warn('Nie znaleziono textarea dla text_right');
        }
      } else {
        console.warn('Nie znaleziono kontrolki text_right');
      }
    }
    
    // Zaktualizuj tytuł elementu repeatera
    setTimeout(function() {
      var titleButton = repeaterItem.querySelector('.elementor-repeater-row-item-title');
      if (titleButton && memberData.name) {
        titleButton.textContent = memberData.name;
      }
    }, 200);
    
    return true;
  }

  /**
   * Dodaje wszystkie 5 slide'ów do repeatera
   */
  window.add5Slides = function() {
    // Sprawdź czy jesteśmy w edytorze Elementora
    if (typeof elementor === 'undefined') {
      console.error('Elementor nie jest dostępny. Upewnij się, że jesteś w edytorze Elementora.');
      return;
    }
    
    // Sprawdź czy widget jest wybrany
    var currentElement = elementor.getCurrentElement ? elementor.getCurrentElement() : null;
    if (!currentElement || (currentElement.getWidgetType && currentElement.getWidgetType() !== 'kpg-team-slider')) {
      console.error('Wybierz widget "KPG Team Slider" w edytorze Elementora przed uruchomieniem skryptu.');
      return;
    }
    
    // Znajdź przycisk "Dodaj element" - spróbuj różnych selektorów
    var addButton = document.querySelector('.elementor-control-team_members .elementor-repeater-add') ||
                    document.querySelector('[data-setting="team_members"] .elementor-repeater-add') ||
                    document.querySelector('.elementor-control-repeater .elementor-repeater-add');
    
    if (!addButton) {
      console.error('Nie znaleziono przycisku "Dodaj element". Upewnij się, że panel "Team Members" jest otwarty.');
      console.log('Szukam przycisku w DOM...');
      var allButtons = document.querySelectorAll('.elementor-repeater-add');
      console.log('Znalezione przyciski repeater:', allButtons.length);
      return;
    }
    
    console.log('Rozpoczynam dodawanie 5 slide\'ów...');
    
    // Funkcja do znajdowania elementów repeatera - spróbuj różnych selektorów
    function getRepeaterItems() {
      // Główny selektor - elementy repeatera
      var items = document.querySelectorAll('.elementor-repeater-fields-wrapper .elementor-repeater-fields[role="listitem"]');
      if (items.length > 0) {
        return items;
      }
      // Fallback
      items = document.querySelectorAll('.elementor-repeater-fields');
      if (items.length > 0) {
        return items;
      }
      return document.querySelectorAll('[role="listitem"].elementor-repeater-fields');
    }
    
    // Funkcja rekurencyjna do dodawania slide'ów
    var addSlideAtIndex = function(index) {
      if (index >= slidesData.length) {
        console.log('✓ Wszystkie 5 slide\'ów zostały dodane!');
        return;
      }
      
      var slideData = slidesData[index];
      console.log('Dodawanie slide\'a #' + (index + 1) + ': ' + slideData.name);
      
      // Policz aktualną liczbę elementów przed dodaniem
      var currentItems = getRepeaterItems();
      var currentCount = currentItems.length;
      console.log('Aktualna liczba elementów przed dodaniem: ' + currentCount);
      
      // Użyj MutationObserver do wykrycia nowego elementu
      var repeaterContainer = document.querySelector('.elementor-control-team_members .elementor-repeater-fields') ||
                              document.querySelector('[data-setting="team_members"] .elementor-repeater-fields');
      
      if (!repeaterContainer) {
        console.error('Nie znaleziono kontenera repeatera');
        setTimeout(function() {
          addSlideAtIndex(index + 1);
        }, 500);
        return;
      }
      
      var observer = null;
      var itemAdded = false;
      
      // Obserwuj zmiany w DOM
      observer = new MutationObserver(function(mutations) {
        if (itemAdded) return;
        
        var newItems = getRepeaterItems();
        if (newItems.length > currentCount) {
          itemAdded = true;
          if (observer) {
            observer.disconnect();
          }
          
          var newItemIndex = newItems.length - 1;
          console.log('Nowy element wykryty na pozycji: ' + newItemIndex);
          
          // Wypełnij pola po krótkim opóźnieniu
          setTimeout(function() {
            var success = fillRepeaterFields(slideData, newItemIndex);
            if (success) {
              console.log('✓ Slide #' + (index + 1) + ' (' + slideData.name + ') dodany na pozycji ' + newItemIndex);
              
              // Dodaj następny slide po opóźnieniu
              setTimeout(function() {
                addSlideAtIndex(index + 1);
              }, 500);
            } else {
              console.error('Nie udało się wypełnić pól dla slide\'a #' + (index + 1));
              setTimeout(function() {
                addSlideAtIndex(index + 1);
              }, 500);
            }
          }, 300);
        }
      });
      
      // Rozpocznij obserwację
      observer.observe(repeaterContainer, {
        childList: true,
        subtree: true
      });
      
      // Kliknij przycisk "Dodaj element"
      console.log('Klikam przycisk "Dodaj element"...');
      addButton.click();
      
      // Timeout fallback - jeśli MutationObserver nie zadziała
      setTimeout(function() {
        if (!itemAdded && observer) {
          observer.disconnect();
          console.warn('Timeout: Sprawdzam czy element został dodany...');
          
          var newItems = getRepeaterItems();
          if (newItems.length > currentCount) {
            var newItemIndex = newItems.length - 1;
            console.log('Element znaleziony (fallback) na pozycji: ' + newItemIndex);
            
            setTimeout(function() {
              var success = fillRepeaterFields(slideData, newItemIndex);
              if (success) {
                console.log('✓ Slide #' + (index + 1) + ' (' + slideData.name + ') dodany na pozycji ' + newItemIndex);
                setTimeout(function() {
                  addSlideAtIndex(index + 1);
                }, 500);
              } else {
                console.error('Nie udało się wypełnić pól dla slide\'a #' + (index + 1));
                setTimeout(function() {
                  addSlideAtIndex(index + 1);
                }, 500);
              }
            }, 300);
          } else {
            console.error('Timeout: Nowy element nie został dodany dla slide\'a #' + (index + 1));
            setTimeout(function() {
              addSlideAtIndex(index + 1);
            }, 500);
          }
        }
      }, 3000); // 3 sekundy timeout
    };
    
    // Rozpocznij dodawanie od pierwszego slide'a
    addSlideAtIndex(0);
  };

  console.log('✓ Skrypt załadowany!');
  console.log('Wywołaj: add5Slides()');
})();

