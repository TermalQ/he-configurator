document.addEventListener("DOMContentLoaded", function () {
    // === Wszystkie elementy canvas z czcionkami
    const FONT_FAMILY_SELECTOR = 'ul.scrollable-list[id^="fontFamily_"]';

    const FONT_BUTTON_SELECTOR = 'li canvas';
    const INPUT_FIELD_SELECTOR = '.form-control[id^="text_"]';
    const SVG_TEXT_ID_PREFIX = "previewText_";

    // Funkcja do zmiany czcionki
    function changeFontFamily(button, selectedFontFamily) {
        const section = button.closest('.accordion-body');
        if (!section) return;

        const inputField = section.querySelector(INPUT_FIELD_SELECTOR);
        if (!inputField) return;

        const targetId = inputField.id.replace("text_", SVG_TEXT_ID_PREFIX);
        const svgText = document.getElementById(targetId);

        if (svgText) {
            svgText.setAttribute("font-family", selectedFontFamily);
        }
    }

    // Funkcja do ustawiania klasy aktywnej
    function setActiveFontButton(fontButtons, activeButton) {
        fontButtons.forEach(btn => btn.classList.remove('active'));
        activeButton.classList.add('active');
    }

    // Główna logika ustawienie czcionki na SVG <text>
    const fontFamilyLists = document.querySelectorAll(FONT_FAMILY_SELECTOR);

    fontFamilyLists.forEach(list => {
        const fontButtons = list.querySelectorAll(FONT_BUTTON_SELECTOR);

        fontButtons.forEach(button => {
            button.addEventListener("click", function () {
                const selectedFontFamily = this.getAttribute("data-family");
                changeFontFamily(this, selectedFontFamily);
                setActiveFontButton(fontButtons, this);
            });
        });
    });


    // Funkcja do aktualizacji canvasa
    function updateCanvas(canvasEl, font, text, name, rating, badge, colorText = false, backgroundColor = false) {
        const canvas = canvasEl; 
        const container = canvas.parentNode;
        const ctx = canvas.getContext("2d");

        // Ustaw szerokość i wysokość canvasu na 100% szerokości kontenera
        canvas.width = container.clientWidth;
        canvas.height = 45;

        const width = canvas.width;
        const height = canvas.height;

        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = backgroundColor ? backgroundColor : "#F0F0F0";
        ctx.fillRect(0, 0, width, height);

        // Rysowanie kółek rankingowych
        for (let i = 0; i < 5; i++) {
            ctx.beginPath();
            ctx.arc(width - (6 * (i + 1)), 10, 2, 0, 2 * Math.PI);
            ctx.strokeStyle = "#d03622"; // Kolor obrysu
            ctx.stroke();
        }

        // Wypełnianie kółek na podstawie oceny
        for (let i = 0; i < rating; i++) {
            ctx.beginPath();
            ctx.arc(width - (6 * (i + 1)), 10, 2, 0, 2 * Math.PI);
            ctx.fillStyle = "#d03622"; // Kolor wypełnienia
            ctx.fill();
        }

        // Rysowanie nazwy
        ctx.font = "10px Montserrat, sans-serif";
        ctx.fillStyle = "#000000";
        ctx.textAlign = "left";
        ctx.fillText(name, 5, 10);

        // Rysowanie odznaki
        ctx.font = "10px Montserrat, sans-serif";
        ctx.fillStyle = "#d03622";
        ctx.fontVariantCaps = "small-caps";
        ctx.letterSpacing = "5px";
        ctx.textAlign = "left";
        ctx.fillText(badge, 15, height / 2);

        ctx.font = font;
        ctx.fillStyle = colorText ? colorText : "#000000";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        const displayText = text.trim() ? text : `${name}`;
        ctx.fillText(displayText, width / 2, height / 2);
    }

    // Funkcja do zaktualizowania wszystkich canvasów w danym panelu
    function updateAllCanvases() {
        const panels = document.querySelectorAll('.accordion-collapse.collapse'); // Wyszukaj panele

        panels.forEach(panel => {
           
            const inputField = panel.querySelector('.form-control[id^="text_"]');
            if (!inputField) return;

            const inputText = encodeHTML(inputField.value); // Pobranie tekstu z inputa
            const textColor = panel.querySelector('.color-txt.selected')?.getAttribute('data-color') || '#000000'; // Pobranie koloru tekstu

            panel.querySelectorAll('canvas').forEach(canvasEl => {
                const font = canvasEl.getAttribute('data-font');
                const name = canvasEl.getAttribute('data-name');
                const family = canvasEl.getAttribute('data-family');
                const rating = parseInt(canvasEl.getAttribute('data-rating'), 10); // Rating jest liczbą
                const badge = canvasEl.getAttribute('data-badge');
                updateCanvas(canvasEl, font, inputText, name, rating, badge, textColor);

                // Dodaj obsługę zdarzenia click na canvas
                canvasEl.addEventListener('click', () => {
                    panel.querySelectorAll('canvas').forEach(c => c.classList.remove('selected')); // Usuń klasę 'selected' z innych
                    canvasEl.classList.add('selected'); // Dodaj do klikniętego canvasu

                    const previewText = panel.querySelector(`#${inputField.id.replace("text_", "previewText_")}`);
                    if (previewText) {
                        previewText.setAttribute("font-family", family); // Zaktualizuj czcionkę w SVG
                    }
                });

                // Ustawienie koloru tekstu w SVG
                const previewText = panel.querySelector(`#${inputField.id.replace("text_", "previewText_")}`);
                if (previewText) {
                    previewText.setAttribute("fill", textColor);
                }
            });
        });
    }

    // Obsługa zmiany tekstu w inputach
    document.querySelectorAll('.form-control[id^="text_"]').forEach(inputField => {
        inputField.addEventListener("input", updateAllCanvases);
    });

    // Obsługa kliknięć na opcje koloru
    document.querySelectorAll('.color-txt').forEach(colorOption => {
        colorOption.addEventListener('click', event => {
            event.preventDefault();
            const panel = colorOption.closest('.accordion-body'); // Zmienione na akordeon
            if (!panel) return;

            panel.querySelectorAll('.color-txt').forEach(c => c.classList.remove('selected')); // Usuń zaznaczenie z innych opcji
            colorOption.classList.add('selected'); // Zaznacz kliknięty kolor
            updateAllCanvases();
        });
    });

    

    // Wywołanie funkcji przy kliknięciu w accordeon button
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', () => {
            setTimeout(updateAllCanvases, 100) // Odczekaj na animację
        })
    })
});
