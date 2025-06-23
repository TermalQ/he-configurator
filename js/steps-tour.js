document.addEventListener('DOMContentLoaded', () => {
    // Jeśli przewodnik już był wyświetlony, nie uruchamiamy skryptu
    if (localStorage.getItem('HEisStepsTourShown')) {
        return;
    }

    // 🔧 Ustawienia zapisu
    let useSessionStorage = true;
    let useLocalStorage = true;

    // Sprawdzenie, czy przewodnik już się wyświetlał
    let sessionSetting = useSessionStorage ? sessionStorage.getItem('HEisStepsTourShown') || 0 : 0;
    let localSetting = useLocalStorage ? localStorage.getItem('HEisStepsTourShown') || 0 : 0;

    let currentStep = 1;
    let steps = [];

    // Tworzenie elementu overlay, jeśli nie istnieje => w CSS
    let overlay = document.querySelector('.overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'overlay';
        document.body.appendChild(overlay);
    }

    // Pobierz język z atrybutu 'data-lang' w tagu <script>
    const scriptElement = document.querySelector('script[src^="js/steps-tour.js"]');
    const language = scriptElement ? scriptElement.getAttribute('data-lang') : 'pl'; // Domyślnie 'pl', jeśli brak
    const margin = scriptElement ? parseInt(scriptElement.getAttribute('data-margin'), 10) : 20; // Domyślny margin od elementu 20


    function loadStepTours() {
        fetch('json/step-tours.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(stepTours => {
                // console.log('Załadowane dane:', stepTours, 'Używany język:', language);
                // Przekształcamy obiekt w tablicę
                const stepToursArray = Object.values(stepTours);
                renderSteps(stepToursArray, language);
                const deviceType = getDeviceType();
                if (deviceType === 'desktop') {
                    startTour(); // Uruchamiamy przewodnik tylko dla desktop
                } else {
                    // Dla innych urządzeń można dostosować wygląd lub zachowanie
                    startMobileTour();
                }
            })
            .catch(error => console.error('Błąd wczytywania JSON:', error));
    }

    function renderSteps(stepTours, lang) {
        stepTours.forEach((step, index) => {
            if (step.active) {
                const div = document.createElement('div');
                div.className = 'tour-step';
                div.dataset.step = index + 1; // Numeracja kroków zaczyna się od 1

                const deviceType = getDeviceType(); // Ustaw <div..data-step-board=""> z JSON
                if (deviceType === 'desktop') {
                    div.dataset.stepBoard = step.board;
                } else {
                    div.dataset.stepBoard = 'bottom';
                }

                div.innerHTML = `
                    <p><strong>${step.header[lang]}</strong></p>
                    <p>${step.text[lang]}</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <span><small>${index + 1} / ${stepTours.length}</small></span>
                        <button class="btn btn-secondary">${step.button[lang]}</button>
                    </div>
                `;
                document.body.appendChild(div);
                steps.push(div);
            }
        });
    }

    function showStep(step) {
        steps.forEach(el => el.style.display = 'none');
        const stepElement = document.querySelector(`.tour-step[data-step="${step}"]`);
        if (stepElement) stepElement.style.display = 'block';
    }

    function positionStep(step) {
        const tourStep = document.querySelector(`.tour-step[data-step="${step}"]`);
        const targetElement = document.querySelector(`.step[data-step="${step}"]`);

        if (!tourStep || !targetElement) return;

        const rect = targetElement.getBoundingClientRect();
        const board = tourStep.dataset.stepBoard; // Pobiera wartość "board"

        // Resetowanie stylów
        tourStep.style.top = 'auto';
        tourStep.style.left = 'auto';
        tourStep.style.right = 'auto';
        tourStep.style.bottom = 'auto';

        switch (board) {
            case 'top':
                tourStep.style.top = `${rect.top + window.scrollY - tourStep.offsetHeight - margin}px`;
                tourStep.style.left = `${rect.left + window.scrollX}px`;
                break;
            case 'bottom':
                tourStep.style.top = `${rect.bottom + window.scrollY + margin}px`;
                tourStep.style.left = `${rect.left + window.scrollX}px`;
                break;
            case 'left':
                tourStep.style.top = `${rect.top + window.scrollY}px`;
                tourStep.style.left = `${rect.left + window.scrollX - tourStep.offsetWidth - margin}px`;
                break;
            case 'right':
                tourStep.style.top = `${rect.top + window.scrollY}px`;
                tourStep.style.left = `${rect.right + window.scrollX + margin}px`;
                break;
            default:
                tourStep.style.top = `${rect.top + window.scrollY + margin}px`;
                tourStep.style.left = `${rect.left + window.scrollX}px`;
        }
    }

    function highlightStep(step) {
        document.querySelectorAll('.step').forEach(el => el.classList.remove('highlight'));
        const stepElement = document.querySelector(`.step[data-step="${step}"]`);
        if (stepElement) stepElement.classList.add('highlight');
    }

    function nextStep() {
        if (currentStep < steps.length) {
            currentStep++;
            updateStep();
        }
    }

    function endTour() {
        steps.forEach(el => el.style.display = 'none');
        overlay.style.display = 'none';
        document.querySelectorAll('.step').forEach(el => el.classList.remove('highlight'));

        // Usuwanie wszystkich elementów .tour-step i nakładki .overlay
        steps.forEach(el => el.remove());
        overlay.remove();

        // 🔥 Zapisz informację, że przewodnik został wyświetlony
        if (useSessionStorage) sessionStorage.setItem('HEisStepsTourShown', true);
        if (useLocalStorage) localStorage.setItem('HEisStepsTourShown', true);
    }

    function startTour() {
        currentStep = 1;
        overlay.style.display = 'block';
        updateStep();
    }

    function startMobileTour() {
        currentStep = 1;
        overlay.style.display = 'block';
        updateMobileStep();
    }

    function updateStep() {
        showStep(currentStep);
        positionStep(currentStep);
        highlightStep(currentStep);
    }

    function updateMobileStep() {
        showStep(currentStep);
        positionMobileStep(currentStep);
        highlightStep(currentStep);
    }

    function positionMobileStep(step) {
        const tourStep = document.querySelector(`.tour-step[data-step="${step}"]`);
        const targetElement = document.querySelector(`.step[data-step="${step}"]`);

        if (!tourStep || !targetElement) return;

        const rect = targetElement.getBoundingClientRect();
        const board = tourStep.dataset.stepBoard; // Pobiera wartość "board"

        // Resetowanie stylów
        tourStep.style.top = 'auto';
        tourStep.style.left = 'auto';
        tourStep.style.right = 'auto';
        tourStep.style.bottom = 'auto';

        // Dla urządzeń mobilnych można dostosować pozycjonowanie
        switch (board) {
            case 'top':
            case 'bottom':
            case 'left':
            case 'right':
                tourStep.style.top = `${rect.bottom + window.scrollY + margin}px`;
                tourStep.style.left = '50%';
                tourStep.style.width = '100%';
                tourStep.style.transform = 'translateX(-50%)';
                break;
            default:
                tourStep.style.top = '50%';
                tourStep.style.left = '50%';
                tourStep.style.transform = 'translate(-50%, -50%)';
        }
    }

    // Delegacja zdarzeń do obsługi dynamicznych przycisków
    document.body.addEventListener('click', (event) => {
        if (event.target.matches('.tour-step button')) {
            // Jeśli kliknięto przycisk, który ma funkcję endTour() na ostatnim kroku
            if (currentStep === steps.length) {
                endTour();
            } else {
                nextStep();
            }
        }
    });

    window.addEventListener('scroll', () => {
        const deviceType = getDeviceType();
        if (deviceType === 'desktop') {
            positionStep(currentStep);
        } else {
            positionMobileStep(currentStep);
        }
    });

    window.addEventListener('resize', () => {
        const deviceType = getDeviceType();
        if (deviceType === 'desktop') {
            positionStep(currentStep);
        } else {
            positionMobileStep(currentStep);
        }
    });

    // 🔥 Jeśli przewodnik był już wyświetlony, nie uruchamiamy go ponownie
    // if ((useSessionStorage && sessionSetting == 0) || (useLocalStorage && localSetting == 0)) {
        loadStepTours();
    // }
});
