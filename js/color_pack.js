document.addEventListener('DOMContentLoaded', function() {
    const colorItems = document.querySelectorAll('#packColor li a');
    const frontPanel = document.getElementById('front-panel');
    const leftPanel = document.getElementById('left-panel');
    const selectedColorPack = document.getElementById('selectedColorPack');
    const handles = document.getElementsByClassName('handles'); // Uchwyty boczne

    // Znajdź domyślny kolor
    const defaultColorItem = document.querySelector('#packColor li a[data-default="1"]');

    if (defaultColorItem) {
        const defaultColor = defaultColorItem.getAttribute('data-color');
        // const defaultColor = '#292929';

        // Ustawienie koloru dla domyślnego koloru
        if (selectedColorPack) {
            selectedColorPack.style.backgroundColor = defaultColor;
        }

        if (frontPanel) {
            frontPanel.setAttribute('fill', defaultColor);
        }

        if (leftPanel) {
            leftPanel.setAttribute('fill', defaultColor);
        }

        for (let i = 0; i < handles.length; i++) {
            handles[i].setAttribute('fill', defaultColor);
        }
    }

    // Obsługa kliknięcia na kolory
    colorItems.forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const color = this.getAttribute('data-color');

            if (frontPanel) {
                frontPanel.setAttribute('fill', color);
            }

            if (leftPanel) {
                leftPanel.setAttribute('fill', color);
            }

            for (let i = 0; i < handles.length; i++) {
                handles[i].setAttribute('fill', color);
            }

            if (selectedColorPack) {
                selectedColorPack.style.backgroundColor = color;
            }
        });
    });
});
