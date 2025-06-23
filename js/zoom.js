// ==================================
// Zoom CSS Scale for Inputs Text && Click SVG Toogle Zoom v.2
// ==================================
const modelView = document.querySelector('.configurator__model__view');
const initialTransform = getComputedStyle(modelView).transform;
let isZoomedIn = false;

setConfiguratorHeight();


// Funkcja do ustawiania wysokości elementu .configurator__model na podstawie wysokości elementu #svg_paka
function setConfiguratorHeight() {
    const elem = document.querySelector("#svg_paka");
    const configurator = document.querySelector('.configurator__model');
  
    if (elem && configurator) {
      
  
      setTimeout(() => {
        const rect = elem.getBoundingClientRect();
          
        configurator.style.height = `${rect.height + 25}px`;
        
      }, 2100); // 2.1 sekundy
    }
  }
  
  // Funkcja do przełączania powiększenia
  function toggleZoom() {
    const scaleValue = isZoomedIn ? initialTransform : 'scale(.55)';
    const cursorValue = isZoomedIn ? 'zoom-in' : 'zoom-out';
  
    modelView.style.transform = scaleValue;
    modelView.style.cursor = cursorValue;
  
    isZoomedIn = !isZoomedIn;  // Przełączamy stan powiększenia
  
    // Uruchamiamy funkcję setConfiguratorHeight po zmianie powiększenia
    setConfiguratorHeight();
  }

// Funkcja do zmiany skali na 1 po wprowadzeniu tekstu
function handleInputChange() {
    if (!isZoomedIn) {
        modelView.style.transform = 'scale(.55)';
        modelView.style.cursor = 'zoom-out';  // Kursor na zoom-out po powiększeniu
        isZoomedIn = true;
    }
    setConfiguratorHeight();
}

// Przywrócenie pierwotnej transformacji po opuszczeniu pola
function handleInputBlur() {
    if (isZoomedIn) {
        modelView.style.transform = initialTransform;
        modelView.style.cursor = 'zoom-in';  // Kursor na zoom-in
        isZoomedIn = false;
    }
    setConfiguratorHeight();
}

// Nasłuchiwanie na inputy
document.querySelectorAll("input[id^='text_']").forEach(inputField => {
    inputField.addEventListener('input', handleInputChange);  
    inputField.addEventListener('blur', handleInputBlur);
});

// Nasłuchiwanie na kliknięcie na modelView
modelView.addEventListener('click', toggleZoom);