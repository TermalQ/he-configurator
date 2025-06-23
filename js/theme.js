// ====================================================
// ================ THEME MODE ========================
// ====================================================
document.addEventListener('DOMContentLoaded', () => {
    const documentElement = document.documentElement;
    const toggleThemeButton = document.getElementById('toggleTheme');
    const deliveryElement = document.querySelector('.delivery');
    const deliveryContentElement = deliveryElement.querySelector('.delivery__content');

    initializeLocalStorage();

    // Wstaw dane do hidden formularza
    const isUser = localStorage.getItem('HEisUser');
    const isForm = localStorage.getItem('HEisForm');
    const isVisit = localStorage.getItem('HEisVisit');
    document.getElementById('isUser').value = isUser;
    document.getElementById('isForm').value = isForm;
    document.getElementById('isVisit').value = isVisit;
    // =======

    // Set device type attribute
    documentElement.setAttribute("data-device", getDeviceType());

    // Theme toggle
    toggleThemeButton.addEventListener('click', toggleTheme);

    // czy użytkownik wybrał motyw
    const savedTheme = localStorage.getItem('HEisTheme');
    if (savedTheme) {
        documentElement.setAttribute('data-theme', savedTheme);
    }

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Debounce function for scroll
    let debounceTimer;
    function debounce(func, delay) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(func, delay);
    }

    // Scroll for Delivery TOPIC
    function updateStylesOnScroll() {
        if (window.scrollY > 50) {
            deliveryElement.classList.remove('py-3');
            deliveryContentElement.classList.add('d-flex');
        } else {
            deliveryElement.classList.add('py-3');
            deliveryContentElement.classList.remove('d-flex');
        }
    }

    // Add scroll event listener with debounce
    window.addEventListener('scroll', () => debounce(updateStylesOnScroll, 100));


    // ====================================
    // Efect site Blur => Koszyk ON
    // ====================================
    const offcanvasElement = document.getElementById('offcanvasRight');
    const elementsToBlur = ['header', 'main', 'footer'].map(id => document.getElementById(id));
    
    const toggleBlurClass = (action) => {
        elementsToBlur.forEach(element => element.classList[action]('gray-blur'));
    };

    offcanvasElement.addEventListener('show.bs.offcanvas', () => toggleBlurClass('add'));
    offcanvasElement.addEventListener('hidden.bs.offcanvas', () => toggleBlurClass('remove'));

});

// ====================================
// Inicjacja dane w localStorage
// ====================================
function initializeLocalStorage() {
    if (!localStorage.getItem('HEisTheme')) {
        localStorage.setItem('HEisTheme', 'light'); // domyślny motyw
    }
    // Pobierz czas
    const currentTime = new Date().toISOString();

    localStorage.setItem('HEisDevice', getDeviceType());
    localStorage.setItem('HEisVisit', currentTime); // Start wizyty
    localStorage.setItem('HEisUser', 1); // Użytkownik

    if (!localStorage.getItem('HEisForm')) {
        localStorage.setItem('HEisForm', 0); // Formularz
    }
}

// ===========================
// Switch Theme
// ===========================
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('HEisTheme', newTheme); // Theme
    localStorage.setItem('HEisDevice', getDeviceType()); // Device

    // Zmiana logo w zależności od wybranego motywu
    const logo = document.getElementById('logo');
    // const he_icon = document.getElementById('he_icon');
    if (newTheme === 'dark') {
        logo.src = 'img/hussaria_electra_logo_white.png';
        // he_icon.src = 'img/he_icon_white.png';
    } else {
        logo.src = 'img/hussaria_electra_logo_black.png';
        // he_icon.src = 'img/he_icon_black.png';
    }
}

// ===========================
// Check Device
// ===========================
function getDeviceType() {
    const width = window.innerWidth;
    if (width < 768) {
        return "mobile";
    } else if (width < 1024) {
        return "tablet";
    } else {
        return "desktop";
    }
}