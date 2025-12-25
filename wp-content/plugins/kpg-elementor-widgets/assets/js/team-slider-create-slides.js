/**
 * KPG Team Slider - Console Script to Create/Duplicate Slides in Elementor
 * 
 * Wklej ten skrypt w konsoli przeglądarki w edytorze Elementora, aby szybko tworzyć/duplikować slajdy
 * 
 * Użycie:
 * 1. Otwórz stronę w edytorze Elementora
 * 2. Wybierz widget "KPG Team Slider"
 * 3. Otwórz konsolę przeglądarki (F12)
 * 4. Wklej ten skrypt
 * 5. Wywołaj funkcję: createTeamSliderSlide() lub duplicateTeamSliderSlide(index)
 */

(function() {
  'use strict';

  /**
   * Znajduje widget Team Slider w Elementorze
   */
  function findTeamSliderWidget() {
    // Sprawdź czy jesteśmy w edytorze Elementora
    if (typeof elementor === 'undefined') {
      console.error('Elementor nie jest dostępny. Upewnij się, że jesteś w edytorze Elementora.');
      return null;
    }

    // W edytorze Elementora użyj elementorFrontend lub elementor.getCurrentElement
    var currentElement = null;
    
    // Metoda 1: elementor.getCurrentElement (jeśli dostępne) - NAJLEPSZA
    if (elementor.getCurrentElement && typeof elementor.getCurrentElement === 'function') {
      currentElement = elementor.getCurrentElement();
      if (currentElement && currentElement.getWidgetType && currentElement.getWidgetType() === 'kpg-team-slider') {
        return currentElement;
      }
    }
    
    // Metoda 2: Przez elementor.elements.models (szukaj w całym dokumencie)
    if (!currentElement && elementor.elements && elementor.elements.models) {
      var allElements = elementor.elements.models;
      for (var i = 0; i < allElements.length; i++) {
        var element = allElements[i];
        if (element.getWidgetType && typeof element.getWidgetType === 'function' && element.getWidgetType() === 'kpg-team-slider') {
          currentElement = element;
          break;
        }
        
        // Rekurencyjnie szukaj w elementach zagnieżdżonych
        if (element.getElements && typeof element.getElements === 'function') {
          var nestedElements = element.getElements();
          if (nestedElements && nestedElements.length) {
            for (var j = 0; j < nestedElements.length; j++) {
              if (nestedElements[j].getWidgetType && typeof nestedElements[j].getWidgetType === 'function' && nestedElements[j].getWidgetType() === 'kpg-team-slider') {
                currentElement = nestedElements[j];
                break;
              }
            }
          }
        }
        if (currentElement) break;
      }
    }
    
    // Metoda 3: Przez elementorFrontend.config (jeśli dostępne)
    if (!currentElement && typeof elementorFrontend !== 'undefined' && elementorFrontend.config) {
      var elements = elementorFrontend.config.elements || {};
      for (var id in elements) {
        if (elements[id].widgetType === 'kpg-team-slider') {
          // Przełącz na ten element
          if (elementor.select && typeof elementor.select === 'function') {
            elementor.select(id);
            if (elementor.getCurrentElement && typeof elementor.getCurrentElement === 'function') {
              currentElement = elementor.getCurrentElement();
              break;
            }
          }
        }
      }
    }

    if (!currentElement) {
      console.error('Nie znaleziono widgeta Team Slider.');
      console.log('Instrukcja:');
      console.log('1. Otwórz edytor Elementora');
      console.log('2. Kliknij na widget "KPG Team Slider" w edytorze');
      console.log('3. Uruchom skrypt ponownie');
      return null;
    }

    return currentElement;
  }

  /**
   * Wypełnia pola repeatera danymi
   * @param {Object} memberData - Dane członka zespołu
   * @param {number} index - Indeks elementu w repeaterze (0-based)
   */
  function fillRepeaterFields(memberData, index) {
    // Znajdź wszystkie elementy repeatera
    var repeaterItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
    if (index >= repeaterItems.length) {
      console.error('Indeks ' + index + ' jest poza zakresem. Dostępne elementy: ' + repeaterItems.length);
      return false;
    }
    
    var repeaterItem = repeaterItems[index];
    if (!repeaterItem) {
      console.error('Nie znaleziono elementu repeatera o indeksie ' + index);
      return false;
    }
    
    // 1. Wypełnij obrazek thumbnail (jeśli URL jest dostępny)
    if (memberData.image && memberData.image.url) {
      var imageInput = repeaterItem.querySelector('input[data-setting="image"]');
      if (imageInput) {
        // Elementor używa specjalnego formatu dla obrazków
        var imageValue = JSON.stringify({
          url: memberData.image.url,
          id: memberData.image.id || '',
          alt: memberData.image.alt || memberData.name || ''
        });
        imageInput.value = imageValue;
        
        // Wywołaj event change
        var event = new Event('change', { bubbles: true });
        imageInput.dispatchEvent(event);
        
        // Zaktualizuj preview (jeśli istnieje)
        var imageControl = imageInput.closest('.elementor-control');
        if (imageControl) {
          var previewArea = imageControl.querySelector('.elementor-control-media__preview');
          if (previewArea && memberData.image.url) {
            previewArea.style.backgroundImage = 'url(' + memberData.image.url + ')';
            previewArea.style.display = 'block';
            var uploadButton = imageControl.querySelector('.elementor-control-media-upload-button');
            if (uploadButton) {
              uploadButton.style.display = 'none';
            }
          }
        }
      }
    }
    
    // 1b. Wypełnij główny obrazek (jeśli URL jest dostępny)
    var mainImageData = memberData.main_image || (memberData.image ? memberData.image : null);
    if (mainImageData && mainImageData.url) {
      var mainImageInput = repeaterItem.querySelector('input[data-setting="main_image"]');
      if (mainImageInput) {
        var mainImageValue = JSON.stringify({
          url: mainImageData.url,
          id: mainImageData.id || '',
          alt: mainImageData.alt || memberData.name || ''
        });
        mainImageInput.value = mainImageValue;
        
        // Wywołaj event change
        var mainImageEvent = new Event('change', { bubbles: true });
        mainImageInput.dispatchEvent(mainImageEvent);
        
        // Zaktualizuj preview (jeśli istnieje)
        setTimeout(function() {
          var mainImageControl = mainImageInput.closest('.elementor-control');
          if (mainImageControl) {
            var mainPreviewArea = mainImageControl.querySelector('.elementor-control-media__preview');
            if (mainPreviewArea && mainImageData.url) {
              mainPreviewArea.style.backgroundImage = 'url(' + mainImageData.url + ')';
              mainPreviewArea.style.display = 'block';
              var mainUploadButton = mainImageControl.querySelector('.elementor-control-media-upload-button');
              if (mainUploadButton) {
                mainUploadButton.style.display = 'none';
              }
            }
          }
        }, 150);
      }
    }
    
    // 2. Wypełnij object_position
    if (memberData.object_position) {
      var objectPositionInput = repeaterItem.querySelector('input[data-setting="object_position"]');
      if (objectPositionInput) {
        objectPositionInput.value = memberData.object_position;
        objectPositionInput.dispatchEvent(new Event('input', { bubbles: true }));
        objectPositionInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    
    // 3. Wypełnij name
    if (memberData.name) {
      var nameInput = repeaterItem.querySelector('input[data-setting="name"]');
      if (nameInput) {
        nameInput.value = memberData.name;
        nameInput.dispatchEvent(new Event('input', { bubbles: true }));
        nameInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    
    // 4. Wypełnij job_title
    if (memberData.job_title) {
      var jobTitleInput = repeaterItem.querySelector('input[data-setting="job_title"]');
      if (jobTitleInput) {
        jobTitleInput.value = memberData.job_title;
        jobTitleInput.dispatchEvent(new Event('input', { bubbles: true }));
        jobTitleInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    
    // 5. Wypełnij intro_text
    if (memberData.intro_text) {
      var introTextInput = repeaterItem.querySelector('textarea[data-setting="intro_text"]');
      if (introTextInput) {
        introTextInput.value = memberData.intro_text;
        introTextInput.dispatchEvent(new Event('input', { bubbles: true }));
        introTextInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    
    // 6. Wypełnij text_left (WYSIWYG)
    if (memberData.text_left) {
      var textLeftTextarea = repeaterItem.querySelector('textarea[name*="text_left"], textarea[id*="text_left"]');
      if (textLeftTextarea) {
        textLeftTextarea.value = memberData.text_left;
        // Dla TinyMCE
        if (window.tinymce && window.tinymce.get(textLeftTextarea.id)) {
          window.tinymce.get(textLeftTextarea.id).setContent(memberData.text_left);
        }
        textLeftTextarea.dispatchEvent(new Event('input', { bubbles: true }));
        textLeftTextarea.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    
    // 7. Wypełnij text_right (WYSIWYG)
    if (memberData.text_right) {
      var textRightTextarea = repeaterItem.querySelector('textarea[name*="text_right"], textarea[id*="text_right"]');
      if (textRightTextarea) {
        textRightTextarea.value = memberData.text_right;
        // Dla TinyMCE
        if (window.tinymce && window.tinymce.get(textRightTextarea.id)) {
          window.tinymce.get(textRightTextarea.id).setContent(memberData.text_right);
        }
        textRightTextarea.dispatchEvent(new Event('input', { bubbles: true }));
        textRightTextarea.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    
    // Zaktualizuj tytuł elementu repeatera
    var titleButton = repeaterItem.querySelector('.elementor-repeater-row-item-title');
    if (titleButton && memberData.name) {
      titleButton.textContent = memberData.name;
    }
    
    return true;
  }

  /**
   * Tworzy nowy slajd w team sliderze (w Elementorze) - używa UI Elementora
   * @param {Object} options - Opcje dla nowego slajdu
   * @param {number} insertAfterIndex - Po którym slajdzie wstawić (opcjonalne, domyślnie na końcu)
   */
  window.createTeamSliderSlide = function(options, insertAfterIndex) {
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
    
    // Domyślne wartości
    var defaults = {
      image: {
        url: 'https://via.placeholder.com/392x520',
        id: '',
        alt: ''
      },
      main_image: {
        url: 'https://via.placeholder.com/534x713',
        id: '',
        alt: ''
      },
      object_position: 'center center',
      name: 'Nowy Członek',
      job_title: 'STANOWISKO',
      intro_text: 'Tekst wprowadzający...',
      text_left: 'Tekst lewej kolumny...',
      text_right: 'Tekst prawej kolumny...'
    };

    // Merge z opcjami użytkownika
    var newMember = JSON.parse(JSON.stringify(defaults));
    if (options) {
      for (var key in options) {
        if (options.hasOwnProperty(key)) {
          newMember[key] = options[key];
        }
      }
    }
    
    // Jeśli użytkownik podał imageUrl jako string, przekonwertuj na obiekt
    if (options && options.imageUrl && typeof options.imageUrl === 'string') {
      newMember.image = {
        url: options.imageUrl,
        id: options.imageId || '',
        alt: options.imageAlt || newMember.name
      };
      delete newMember.imageUrl;
    }
    
    // Znajdź przycisk "Dodaj element" w repeaterze
    var addButton = document.querySelector('.elementor-control-team_members .elementor-repeater-add');
    if (!addButton) {
      console.error('Nie znaleziono przycisku "Dodaj element" w repeaterze Team Members.');
      console.log('Upewnij się, że:');
      console.log('1. Widget "KPG Team Slider" jest wybrany');
      console.log('2. Panel "Team Members" jest otwarty');
      return;
    }
    
    // Policz aktualną liczbę elementów przed dodaniem
    var currentItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
    var currentCount = currentItems.length;
    var newIndex = insertAfterIndex !== undefined ? insertAfterIndex + 1 : currentCount;
    
    // Kliknij przycisk "Dodaj element"
    console.log('Klikam przycisk "Dodaj element"...');
    addButton.click();
    
    // Poczekaj na pojawienie się nowego elementu
    var maxWait = 2000; // 2 sekundy
    var waitTime = 0;
    var checkInterval = 50;
    
    var waitForNewItem = function(callback) {
      var newItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
      if (newItems.length > currentCount) {
        // Nowy element został dodany
        var newItemIndex = newItems.length - 1;
        if (insertAfterIndex !== undefined && insertAfterIndex < currentCount) {
          // Jeśli wstawiamy w środku, znajdź odpowiedni indeks
          newItemIndex = insertAfterIndex + 1;
        }
        callback(newItemIndex);
      } else if (waitTime < maxWait) {
        waitTime += checkInterval;
        setTimeout(waitForNewItem, checkInterval, callback);
      } else {
        console.error('Timeout: Nowy element nie został dodany w ciągu ' + maxWait + 'ms');
      }
    };
    
    waitForNewItem(function(itemIndex) {
      console.log('Nowy element dodany na pozycji ' + itemIndex);
      
      // Wypełnij pola
      setTimeout(function() {
        var success = fillRepeaterFields(newMember, itemIndex);
        if (success) {
          console.log('✓ Utworzono nowy slajd #' + itemIndex + ' w Elementorze');
          console.log('Dane zostały wypełnione. Sprawdź panel Elementora.');
        } else {
          console.error('Nie udało się wypełnić wszystkich pól.');
        }
      }, 100); // Krótkie opóźnienie, aby UI się zaktualizowało
    });
    
    return newIndex;
  };

  /**
   * Parsuje HTML slidera Elementora i wyciąga dane członka zespołu
   * @param {string} htmlString - HTML string z slide'em
   * @returns {Object} - Obiekt z danymi członka zespołu
   */
  function parseElementorSlideHTML(htmlString) {
    // Utwórz tymczasowy element DOM do parsowania
    var tempDiv = document.createElement('div');
    tempDiv.innerHTML = htmlString;
    
    var data = {
      image: {
        url: '',
        id: '',
        alt: ''
      },
      main_image: {
        url: '',
        id: '',
        alt: ''
      },
      object_position: 'center center',
      name: '',
      job_title: '',
      intro_text: '',
      text_left: '',
      text_right: ''
    };
    
    // 1. Znajdź obrazki - główny (większy) i thumbnail (mniejszy)
    // Szukaj w pierwszym kontenerze e-con e-child, który zawiera obrazek
    var firstImageContainer = tempDiv.querySelector('.e-con.e-child .elementor-widget-image img');
    if (firstImageContainer) {
      // To jest główny obrazek (większy)
      data.main_image.url = firstImageContainer.src || firstImageContainer.getAttribute('src') || '';
      var mainImageIdMatch = data.main_image.url.match(/wp-image-(\d+)/);
      if (mainImageIdMatch) {
        data.main_image.id = mainImageIdMatch[1];
      }
      data.main_image.alt = firstImageContainer.alt || data.name || '';
      
      // Dla thumbnail użyj tego samego obrazka (można później zmienić ręcznie)
      data.image.url = data.main_image.url;
      data.image.id = data.main_image.id;
      data.image.alt = data.main_image.alt;
    } else {
      // Fallback: pierwszy obrazek w slide
      var firstImage = tempDiv.querySelector('img');
      if (firstImage) {
        data.main_image.url = firstImage.src || firstImage.getAttribute('src') || '';
        var mainImageIdMatch = data.main_image.url.match(/wp-image-(\d+)/);
        if (mainImageIdMatch) {
          data.main_image.id = mainImageIdMatch[1];
        }
        data.main_image.alt = firstImage.alt || data.name || '';
        
        // Dla thumbnail użyj tego samego obrazka
        data.image.url = data.main_image.url;
        data.image.id = data.main_image.id;
        data.image.alt = data.main_image.alt;
      }
    }
    
    // Szukaj drugiego obrazka (thumbnail) - jeśli istnieje
    var allImages = tempDiv.querySelectorAll('img');
    if (allImages.length > 1) {
      // Drugi obrazek może być thumbnailem (zwykle mniejszy)
      var secondImage = allImages[1];
      var secondImageUrl = secondImage.src || secondImage.getAttribute('src') || '';
      if (secondImageUrl && secondImageUrl !== data.main_image.url) {
        data.image.url = secondImageUrl;
        var thumbnailIdMatch = data.image.url.match(/wp-image-(\d+)/);
        if (thumbnailIdMatch) {
          data.image.id = thumbnailIdMatch[1];
        }
        data.image.alt = secondImage.alt || data.name || '';
      }
    }
    
    // 2. Znajdź nazwę (z elementor-icon-box-title)
    var nameElement = tempDiv.querySelector('.elementor-icon-box-title span, .elementor-icon-box-title');
    if (nameElement) {
      data.name = nameElement.textContent.trim();
    }
    
    // 3. Znajdź tytuł stanowiska (z elementor-icon-box-description)
    var jobTitleElement = tempDiv.querySelector('.elementor-icon-box-description');
    if (jobTitleElement) {
      data.job_title = jobTitleElement.textContent.trim();
    }
    
    // 4. Znajdź tekst wprowadzający (pierwszy text-editor po icon-box, który nie jest ukryty na desktop)
    // Szukaj text-editor, który jest w tym samym kontenerze co icon-box lub następnym
    var iconBoxElement = tempDiv.querySelector('.elementor-icon-box-wrapper');
    if (iconBoxElement) {
      // Znajdź kontener z icon-box
      var iconBoxContainer = iconBoxElement.closest('.elementor-element');
      if (iconBoxContainer) {
        // Znajdź kontener nadrzędny (e-con e-child)
        var parentContainer = iconBoxContainer.closest('.e-con.e-child');
        if (parentContainer) {
          // Szukaj text-editor w tym samym kontenerze, który nie jest ukryty na desktop
          var textEditors = parentContainer.querySelectorAll('.elementor-widget-text-editor:not(.elementor-hidden-desktop):not(.elementor-hidden-laptop):not(.elementor-hidden-tablet)');
          
          // Znajdź pierwszy text-editor który jest po icon-box
          for (var i = 0; i < textEditors.length; i++) {
            var textEditor = textEditors[i];
            // Sprawdź czy text-editor jest w DOM po icon-box
            var textEditorContainer = textEditor.closest('.elementor-element');
            if (textEditorContainer && textEditorContainer !== iconBoxContainer) {
              // Sprawdź kolejność elementów
              var allSiblings = Array.from(parentContainer.querySelectorAll('.elementor-element'));
              var iconBoxIndex = allSiblings.indexOf(iconBoxContainer);
              var textEditorIndex = allSiblings.indexOf(textEditorContainer);
              
              if (textEditorIndex > iconBoxIndex) {
                var text = textEditor.textContent.trim();
                if (text) {
                  data.intro_text = text;
                  break;
                }
              }
            }
          }
          
          // Jeśli nie znaleziono, użyj pierwszego text-editor w tym kontenerze
          if (!data.intro_text && textEditors.length > 0) {
            var firstText = textEditors[0].textContent.trim();
            if (firstText) {
              data.intro_text = firstText;
            }
          }
        }
      }
    }
    
    // Fallback: znajdź pierwszy text-editor który nie jest ukryty na desktop
    if (!data.intro_text) {
      var firstVisibleTextEditor = tempDiv.querySelector('.elementor-widget-text-editor:not(.elementor-hidden-desktop):not(.elementor-hidden-laptop):not(.elementor-hidden-tablet)');
      if (firstVisibleTextEditor) {
        data.intro_text = firstVisibleTextEditor.textContent.trim();
      }
    }
    
    // 5. Znajdź teksty lewej i prawej kolumny
    // Szukaj struktury z dwoma kolumnami - kontener z dwoma zagnieżdżonymi kontenerami
    var textLeftElements = [];
    var textRightElements = [];
    
    // Szukaj kontenera z dwoma kolumnami (e-con.e-parent > e-con.e-child z dwoma e-con.e-child wewnątrz)
    var mainContainers = tempDiv.querySelectorAll('.e-con.e-parent > .e-con.e-child');
    var foundTwoColumnStructure = false;
    
    for (var i = 0; i < mainContainers.length; i++) {
      var mainContainer = mainContainers[i];
      var nestedContainers = mainContainer.querySelectorAll('.e-con.e-child');
      
      if (nestedContainers.length >= 2) {
        // To wygląda na strukturę z dwoma kolumnami
        foundTwoColumnStructure = true;
        
        // Lewa kolumna - pierwszy zagnieżdżony kontener
        var leftContainer = nestedContainers[0];
        var leftTextEditors = leftContainer.querySelectorAll('.elementor-widget-text-editor:not(.elementor-hidden-desktop):not(.elementor-hidden-laptop):not(.elementor-hidden-tablet)');
        leftTextEditors.forEach(function(editor) {
          var text = editor.innerHTML.trim();
          if (text) {
            textLeftElements.push(text);
          }
        });
        
        // Prawa kolumna - drugi zagnieżdżony kontener
        var rightContainer = nestedContainers[1];
        var rightTextEditors = rightContainer.querySelectorAll('.elementor-widget-text-editor:not(.elementor-hidden-desktop):not(.elementor-hidden-laptop):not(.elementor-hidden-tablet)');
        rightTextEditors.forEach(function(editor) {
          var text = editor.innerHTML.trim();
          if (text) {
            textRightElements.push(text);
          }
        });
        
        break;
      }
    }
    
    // Jeśli nie znaleziono struktury dwóch kolumn, znajdź wszystkie text-editor i podziel na pół
    if (!foundTwoColumnStructure || (textLeftElements.length === 0 && textRightElements.length === 0)) {
      // Znajdź kontener z tekstami (zwykle po kontenerze z icon-box)
      var allTextContainers = tempDiv.querySelectorAll('.e-con.e-child');
      var textContainer = null;
      
      // Szukaj kontenera który zawiera text-editor widgets (pomijając kontener z obrazkami i icon-box)
      for (var i = 0; i < allTextContainers.length; i++) {
        var container = allTextContainers[i];
        var hasTextEditors = container.querySelectorAll('.elementor-widget-text-editor:not(.elementor-hidden-desktop)').length > 0;
        var hasIconBox = container.querySelector('.elementor-icon-box-wrapper') !== null;
        var hasImages = container.querySelectorAll('.elementor-widget-image').length > 0;
        
        if (hasTextEditors && !hasIconBox && !hasImages) {
          textContainer = container;
          break;
        }
      }
      
      if (textContainer) {
        var allTextEditors = textContainer.querySelectorAll('.elementor-widget-text-editor:not(.elementor-hidden-desktop):not(.elementor-hidden-laptop):not(.elementor-hidden-tablet)');
        var allTexts = [];
        
        allTextEditors.forEach(function(editor) {
          // Pomiń intro text jeśli już go mamy
          if (data.intro_text && editor.textContent.trim() === data.intro_text.trim()) {
            return;
          }
          
          var text = editor.innerHTML.trim();
          if (text) {
            allTexts.push(text);
          }
        });
        
        // Podziel na pół
        var midPoint = Math.ceil(allTexts.length / 2);
        textLeftElements = allTexts.slice(0, midPoint);
        textRightElements = allTexts.slice(midPoint);
      } else {
        // Ostateczny fallback - wszystkie text-editor w całym slide
        var allTextEditors = tempDiv.querySelectorAll('.elementor-widget-text-editor:not(.elementor-hidden-desktop):not(.elementor-hidden-laptop):not(.elementor-hidden-tablet)');
        var allTexts = [];
        
        allTextEditors.forEach(function(editor, index) {
          // Pomiń intro text
          if (data.intro_text && editor.textContent.trim() === data.intro_text.trim()) {
            return;
          }
          
          var text = editor.innerHTML.trim();
          if (text) {
            allTexts.push(text);
          }
        });
        
        // Podziel na pół
        var midPoint = Math.ceil(allTexts.length / 2);
        textLeftElements = allTexts.slice(0, midPoint);
        textRightElements = allTexts.slice(midPoint);
      }
    }
    
    // Połącz teksty
    data.text_left = textLeftElements.join('\n\n');
    data.text_right = textRightElements.join('\n\n');
    
    return data;
  }

  /**
   * Parsuje wszystkie slide'y z HTML slidera i dodaje je do repeatera
   * @param {string} sliderHTML - HTML string z całym sliderem (wszystkie slide'y)
   */
  window.addAllSlidesFromSliderHTML = function(sliderHTML) {
    if (!sliderHTML || typeof sliderHTML !== 'string') {
      console.error('Musisz podać HTML string z sliderem jako pierwszy argument');
      return;
    }
    
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
    
    // Utwórz tymczasowy element DOM do parsowania
    var tempDiv = document.createElement('div');
    tempDiv.innerHTML = sliderHTML;
    
    // Znajdź wszystkie slide'y
    var slides = tempDiv.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)');
    
    if (slides.length === 0) {
      console.error('Nie znaleziono slide\'ów w HTML');
      return;
    }
    
    console.log('Znaleziono ' + slides.length + ' slide\'ów. Rozpoczynam dodawanie...');
    
    // Funkcja rekurencyjna do dodawania slide'ów jeden po drugim
    var addSlideAtIndex = function(index) {
      if (index >= slides.length) {
        console.log('✓ Wszystkie slide\'y zostały dodane!');
        return;
      }
      
      var slide = slides[index];
      var slideHTML = slide.outerHTML;
      
      console.log('Dodawanie slide\'a #' + (index + 1) + ' z ' + slides.length + '...');
      
      // Parsuj dane z slide'a
      var memberData = parseElementorSlideHTML(slideHTML);
      console.log('Wyciągnięte dane dla slide\'a #' + (index + 1) + ':', memberData);
      
      // Znajdź przycisk "Dodaj element"
      var addButton = document.querySelector('.elementor-control-team_members .elementor-repeater-add');
      if (!addButton) {
        console.error('Nie znaleziono przycisku "Dodaj element". Upewnij się, że panel "Team Members" jest otwarty.');
        return;
      }
      
      // Policz aktualną liczbę elementów przed dodaniem
      var currentItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
      var currentCount = currentItems.length;
      
      // Kliknij przycisk "Dodaj element"
      addButton.click();
      
      // Poczekaj na pojawienie się nowego elementu
      var maxWait = 2000;
      var waitTime = 0;
      var checkInterval = 50;
      
      var waitForNewItem = function(callback) {
        var newItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
        if (newItems.length > currentCount) {
          var newItemIndex = newItems.length - 1;
          callback(newItemIndex);
        } else if (waitTime < maxWait) {
          waitTime += checkInterval;
          setTimeout(waitForNewItem, checkInterval, callback);
        } else {
          console.error('Timeout: Nowy element nie został dodany w ciągu ' + maxWait + 'ms');
          // Spróbuj dodać następny slide mimo wszystko
          setTimeout(function() {
            addSlideAtIndex(index + 1);
          }, 500);
        }
      };
      
      waitForNewItem(function(itemIndex) {
        // Wypełnij pola
        setTimeout(function() {
          var success = fillRepeaterFields(memberData, itemIndex);
          if (success) {
            console.log('✓ Slide #' + (index + 1) + ' dodany na pozycji ' + itemIndex);
            
            // Dodaj następny slide po krótkim opóźnieniu
            setTimeout(function() {
              addSlideAtIndex(index + 1);
            }, 300);
          } else {
            console.error('Nie udało się wypełnić pól dla slide\'a #' + (index + 1));
            // Spróbuj dodać następny slide mimo wszystko
            setTimeout(function() {
              addSlideAtIndex(index + 1);
            }, 500);
          }
        }, 200);
      });
    };
    
    // Rozpocznij dodawanie od pierwszego slide'a
    addSlideAtIndex(0);
  };

  /**
   * Tworzy slajd z HTML stringa (parsuje HTML i dodaje do repeatera)
   * @param {string} htmlString - HTML string z slide'em Elementora
   * @param {number} insertAfterIndex - Po którym slajdzie wstawić (opcjonalne)
   */
  window.createTeamSliderSlideFromHTML = function(htmlString, insertAfterIndex) {
    if (!htmlString || typeof htmlString !== 'string') {
      console.error('Musisz podać HTML string jako pierwszy argument');
      return;
    }
    
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
    
    console.log('Parsowanie HTML...');
    var memberData = parseElementorSlideHTML(htmlString);
    
    console.log('Wyciągnięte dane:', memberData);
    
    // Znajdź przycisk "Dodaj element" w repeaterze
    var addButton = document.querySelector('.elementor-control-team_members .elementor-repeater-add');
    if (!addButton) {
      console.error('Nie znaleziono przycisku "Dodaj element" w repeaterze Team Members.');
      console.log('Upewnij się, że:');
      console.log('1. Widget "KPG Team Slider" jest wybrany');
      console.log('2. Panel "Team Members" jest otwarty');
      return;
    }
    
    // Policz aktualną liczbę elementów przed dodaniem
    var currentItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
    var currentCount = currentItems.length;
    var newIndex = insertAfterIndex !== undefined ? insertAfterIndex + 1 : currentCount;
    
    // Kliknij przycisk "Dodaj element"
    console.log('Klikam przycisk "Dodaj element"...');
    addButton.click();
    
    // Poczekaj na pojawienie się nowego elementu
    var maxWait = 2000; // 2 sekundy
    var waitTime = 0;
    var checkInterval = 50;
    
    var waitForNewItem = function(callback) {
      var newItems = document.querySelectorAll('.elementor-repeater-fields .elementor-repeater-fields');
      if (newItems.length > currentCount) {
        // Nowy element został dodany
        var newItemIndex = newItems.length - 1;
        if (insertAfterIndex !== undefined && insertAfterIndex < currentCount) {
          // Jeśli wstawiamy w środku, znajdź odpowiedni indeks
          newItemIndex = insertAfterIndex + 1;
        }
        callback(newItemIndex);
      } else if (waitTime < maxWait) {
        waitTime += checkInterval;
        setTimeout(waitForNewItem, checkInterval, callback);
      } else {
        console.error('Timeout: Nowy element nie został dodany w ciągu ' + maxWait + 'ms');
      }
    };
    
    waitForNewItem(function(itemIndex) {
      console.log('Nowy element dodany na pozycji ' + itemIndex);
      
      // Wypełnij pola
      setTimeout(function() {
        var success = fillRepeaterFields(memberData, itemIndex);
        if (success) {
          console.log('✓ Utworzono nowy slajd #' + itemIndex + ' z HTML w Elementorze');
          console.log('Dane zostały wypełnione. Sprawdź panel Elementora.');
        } else {
          console.error('Nie udało się wypełnić wszystkich pól.');
        }
      }, 100); // Krótkie opóźnienie, aby UI się zaktualizowało
    });
    
    return newIndex;
  };

  /**
   * Duplikuje istniejący slajd (w Elementorze)
   * @param {number} sourceIndex - Indeks slajdu do skopiowania (0-based)
   * @param {number} insertAfterIndex - Po którym slajdzie wstawić (opcjonalne)
   */
  window.duplicateTeamSliderSlide = function(sourceIndex, insertAfterIndex) {
    var widget = findTeamSliderWidget();
    if (!widget) {
      return;
    }

    var model = widget.getEditModel ? widget.getEditModel() : (widget.getModel ? widget.getModel() : widget);
    if (!model) {
      console.error('Nie można pobrać modelu widgeta');
      return;
    }

    var settings = model.get('settings');
    if (!settings) {
      settings = model;
    }
    
    var teamMembers = [];
    if (settings.get) {
      teamMembers = settings.get('team_members') || [];
    } else if (settings.team_members) {
      teamMembers = settings.team_members || [];
    }
    
    if (teamMembers && teamMembers.toArray) {
      teamMembers = teamMembers.toArray();
    }
    
    if (!Array.isArray(teamMembers)) {
      teamMembers = [];
    }

    if (sourceIndex < 0 || sourceIndex >= teamMembers.length) {
      console.error('Nie znaleziono slajdu o indeksie ' + sourceIndex);
      return;
    }

    // Głęboka kopia źródłowego członka
    var sourceMember = teamMembers[sourceIndex];
    var duplicatedMember = JSON.parse(JSON.stringify(sourceMember));
    
    // Opcjonalnie zmień imię, aby było widoczne, że to kopia
    if (duplicatedMember.name) {
      duplicatedMember.name = duplicatedMember.name + ' (kopia)';
    }

    // Konwersja do formatu funkcji createTeamSliderSlide
    var slideData = {
      image: duplicatedMember.image || {
        url: 'https://via.placeholder.com/392x520',
        id: '',
        alt: ''
      },
      object_position: duplicatedMember.object_position || 'center center',
      name: duplicatedMember.name || 'Nowy Członek',
      job_title: duplicatedMember.job_title || 'STANOWISKO',
      intro_text: duplicatedMember.intro_text || 'Tekst wprowadzający...',
      text_left: duplicatedMember.text_left || 'Tekst lewej kolumny...',
      text_right: duplicatedMember.text_right || 'Tekst prawej kolumny...'
    };

    return createTeamSliderSlide(slideData, insertAfterIndex !== undefined ? insertAfterIndex : sourceIndex);
  };

  console.log('✓ Skrypt do tworzenia slajdów w Elementorze załadowany!');
  console.log('');
  console.log('Użycie:');
  console.log('  1. Wybierz widget "KPG Team Slider" w edytorze Elementora');
  console.log('  2. Upewnij się, że panel "Team Members" jest otwarty');
  console.log('  3. Wywołaj funkcję:');
  console.log('');
  console.log('  // DODAJ WSZYSTKIE SLIDE\'Y Z SLIDERA (NAJLEPSZE!):');
  console.log('  var sliderHTML = `<div class="e-n-carousel">...</div>`; // cały HTML slidera');
  console.log('  addAllSlidesFromSliderHTML(sliderHTML);');
  console.log('');
  console.log('  // Tworzenie pojedynczego slajdu z HTML:');
  console.log('  var html = `<div class="swiper-slide">...</div>`;');
  console.log('  createTeamSliderSlideFromHTML(html, insertAfterIndex);');
  console.log('');
  console.log('  // Tworzenie nowego slajdu z obiektu:');
  console.log('  createTeamSliderSlide({');
  console.log('    image: {url: "https://example.com/image.jpg", id: 123},');
  console.log('    object_position: "center calc(50% + 50px)",');
  console.log('    name: "Jan Kowalski",');
  console.log('    job_title: "ADWOKAT",');
  console.log('    intro_text: "Tekst wprowadzający...",');
  console.log('    text_left: "Tekst lewej kolumny...",');
  console.log('    text_right: "Tekst prawej kolumny..."');
  console.log('  }, insertAfterIndex); // opcjonalnie: po którym slajdzie wstawić');
  console.log('');
  console.log('  // Duplikowanie istniejącego slajdu:');
  console.log('  duplicateTeamSliderSlide(0); // duplikuje pierwszy slajd (indeks 0)');
  console.log('  duplicateTeamSliderSlide(1, 2); // duplikuje drugi slajd i wstawia po trzecim');
})();

