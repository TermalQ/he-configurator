document.addEventListener("DOMContentLoaded", function () {

    // === Pola tekstowe (inputy)
    const textInputs = document.querySelectorAll('.form-control[id^="text_"]');
    textInputs.forEach(input => {
        input.addEventListener("input", function () {
            const targetId = this.id.replace("text_", "previewText_");
            const svgText = document.getElementById(targetId);
            if (svgText) {
                svgText.textContent = encodeHTML(this.value.trim());
            }
        });
    });

    // =======================
    // === Pokaż/Ukryj Grawer Zonę i Flagę
    // =======================
    const inputFieldGrawer = document.getElementById('text_grawerTopPanel');
    const grawerZone = document.getElementById('preview_grawerTopPanel');
    const flagZoneGrawer = document.getElementById('preview_flagTopPanel_grawer');
    const flagZoneWithoutGrawer = document.getElementById('preview_flagTopPanel_left');
    const flagCheckbox = document.getElementById('flag'); // checkbox form

    const textTopPanel = document.getElementById('previewText_toppanel');
    const buttonTextTopPanel = document.getElementById('buttonText_toppanel_left');

    const initialTextTopPanelX = textTopPanel ? parseFloat(textTopPanel.getAttribute('x')) || 0 : 0;
    const initialButtonX = buttonTextTopPanel ? parseFloat(buttonTextTopPanel.getAttribute('data-x')) || 0 : 0;

    // Funkcja aktualizująca widoczność elementów i pozycję tekstu + buttona
    function updateVisibility() {
        if (!textTopPanel) return;

        const flagChecked = flagCheckbox?.checked || false;
        let shift = flagChecked ? 35 : 0;
        const currentAnchor = textTopPanel.getAttribute('text-anchor') || 'start';

        if (!inputFieldGrawer) {
            if (currentAnchor === 'start') {
                textTopPanel.setAttribute('x', initialTextTopPanelX + shift);
            }

            if (buttonTextTopPanel) {
                buttonTextTopPanel.setAttribute('data-x', initialButtonX + shift);
            }

            if (flagZoneGrawer) flagZoneGrawer.style.visibility = 'hidden';
            if (flagZoneWithoutGrawer) flagZoneWithoutGrawer.style.visibility = flagChecked ? 'visible' : 'hidden';
            return;
        }

        const hasText = encodeHTML(inputFieldGrawer.value.trim() !== '');
        if (grawerZone) grawerZone.style.visibility = hasText ? 'visible' : 'hidden';

        if (hasText) shift += 20;

        if (currentAnchor === 'start') {
            textTopPanel.setAttribute('x', initialTextTopPanelX + shift);
        }

        if (buttonTextTopPanel) {
            buttonTextTopPanel.setAttribute('data-x', initialButtonX + shift);
        }

        if (flagZoneGrawer) flagZoneGrawer.style.visibility = hasText && flagChecked ? 'visible' : 'hidden';
        if (flagZoneWithoutGrawer) flagZoneWithoutGrawer.style.visibility = !hasText && flagChecked ? 'visible' : 'hidden';
    }

    if (inputFieldGrawer) inputFieldGrawer.addEventListener('input', updateVisibility);
    if (flagCheckbox) flagCheckbox.addEventListener('change', updateVisibility);


    // Kolor tekstu w SVG
    const colorPickers = document.querySelectorAll('.colors .color-txt');
    colorPickers.forEach(colorPicker => {
        colorPicker.addEventListener("click", function (event) {
            event.preventDefault();
            const selectedColor = this.getAttribute("data-color");
            const section = this.closest('.accordion-item');
            if (!section) return;

            const inputField = section.querySelector('.form-control[id^="text_"]');
            if (!inputField) return;

            const targetId = inputField.id.replace("text_", "previewText_");
            const svgText = document.getElementById(targetId);
            if (svgText) {
                svgText.setAttribute("fill", selectedColor);
            }

            const colorPreview = section.querySelector(`#selectedColorText_${inputField.id.replace("text_", "")}`);
            if (colorPreview) {
                colorPreview.style.backgroundColor = selectedColor;
            }
        });
    });

    // === Zmiana czcionki
    const fontFamilyButtons = document.querySelectorAll('.tools[data-font-family]');
    fontFamilyButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Dezaktywuj wszystkie inne przyciski czcionki
            fontFamilyButtons.forEach(b => b.classList.remove('active'));

            const fontFamily = this.getAttribute('data-font-family');
            const section = this.closest('.accordion-item');
            if (!section) return;

            const inputField = section.querySelector('.form-control[id^="text_"]');
            if (!inputField) return;

            const targetId = inputField.id.replace("text_", "previewText_");
            const svgText = document.getElementById(targetId);
            if (svgText) {
                svgText.style.fontFamily = fontFamily;
            }

            // Dodaj klasę .active do klikniętego przycisku
            this.classList.add('active');
        });
    });

    // === Zmiana rozmiaru tekstu
    const textSizeButtons = document.querySelectorAll('.tools[data-size]');
    textSizeButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Dezaktywuj wszystkie inne przyciski rozmiaru
            textSizeButtons.forEach(b => b.classList.remove('active'));

            const textSize = this.getAttribute('data-size');
            const section = this.closest('.accordion-item');
            if (!section) return;

            const inputField = section.querySelector('.form-control[id^="text_"]');
            if (!inputField) return;

            const targetId = inputField.id.replace("text_", "previewText_");
            const svgText = document.getElementById(targetId);
            if (svgText) {
                svgText.style.fontSize = textSize;
            }

            // Dodaj klasę .active do klikniętego przycisku
            this.classList.add('active');
        });
    });

    // === Zmiana wyrównania tekstu z uwzględnieniem x, y, i text-anchor
    const textAlignButtons = document.querySelectorAll('.tools[data-align-name]');
    textAlignButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Dezaktywuj wszystkie inne przyciski wyrównania
            textAlignButtons.forEach(b => b.classList.remove('active'));

            const alignName = this.getAttribute('data-align-name');
            const x = this.getAttribute('data-x');
            const y = this.getAttribute('data-y');
            const anchor = this.getAttribute('data-anchor');
            const transform = this.getAttribute('data-transform');


            const section = this.closest('.accordion-item');
            if (!section) return;

            const inputField = section.querySelector('.form-control[id^="text_"]');
            if (!inputField) return;

            const targetId = inputField.id.replace("text_", "previewText_");
            const svgText = document.getElementById(targetId);
            if (svgText) {
                // Ustawienie wartości text-anchor
                svgText.setAttribute('text-anchor', anchor);
                // Ustawienie wartości x, y i transform
                svgText.setAttribute('x', x);
                svgText.setAttribute('y', y);
                svgText.setAttribute('transform', transform);
            }

            // Dodaj klasę .active do klikniętego przycisku
            this.classList.add('active');
        });
    });

    // === Zmiana stylu tekstu (np. podkreślenie, przekreślenie)
    const textStyleButtons = document.querySelectorAll('.tools[data-style]');
    textStyleButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Zmiana aktywnej klasy .active tylko na przyciskach stylu tekstu
            this.classList.toggle('active');

            const textStyle = this.getAttribute('data-style');
            const section = this.closest('.accordion-item');
            if (!section) return;

            const inputField = section.querySelector('.form-control[id^="text_"]');
            if (!inputField) return;

            const targetId = inputField.id.replace("text_", "previewText_");
            const svgText = document.getElementById(targetId);
            if (svgText) {
                // Zmiana stylu na podstawie klikniętego przycisku
                if (textStyle === 'bold') {
                    svgText.style.fontWeight = svgText.style.fontWeight === 'bold' ? 'normal' : 'bold';
                } else if (textStyle === 'italic') {
                    svgText.style.fontStyle = svgText.style.fontStyle === 'italic' ? 'normal' : 'italic';
                } else if (textStyle === 'underline') {
                    svgText.style.textDecoration = svgText.style.textDecoration.includes('underline') ? '' : 'underline';
                } else if (textStyle === 'line-through') {
                    svgText.style.textDecoration = svgText.style.textDecoration.includes('line-through') ? '' : 'line-through';
                }
            }
        });
    });

});
