// ========================================
// Zabezpiczenie inputów => CrossSiteInjection
// ========================================
function encodeHTML(str) {
    return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
}

// ========================================
// Formatowanie Ceny => PL
// ========================================
function formatNumberWithSpaces(number) {
    return number.toLocaleString('pl-PL', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
      useGrouping: true,
    });
  }

// lub formatowanie Ceny
function formatPricePL(number) {
    return number.toFixed(2) // Dwa miejsca po przecinku
        .replace('.', ',') // Zamiana kropki na przecinek
        .replace(/\B(?=(\d{3})+(?!\d))/g, ' '); // Spacja jako separator tysięcy
}

// ========================================
// Pokaż Errors
// ========================================
function showError(message, typeMessage = 'error-message') {
    // Wyszukaj element <footer>
    let footer = document.querySelector('footer');
    
    // Sprawdzenie, czy sekcja <footer> istnieje
    if (!footer) {
        return;
    }

    // Tworzenie dynamicznego div dla błędu
    let errorDiv = document.createElement('div');
    errorDiv.className = typeMessage;
    errorDiv.textContent = message;

    // Dodanie błędu przed <footer>
    footer.parentNode.insertBefore(errorDiv, footer);

    // Wyświetlenie błędu (animacja fade-in)
    setTimeout(() => {
        errorDiv.style.opacity = '1';
    }, 100);

    // Ukrycie błędu po 4 sekundach (animacja fade-out) i usunięcie z DOM
    setTimeout(() => {
        errorDiv.style.opacity = '0';
        setTimeout(() => {
            errorDiv.remove(); // Usunięcie błędu z DOM po animacji
        }, 500);
    }, 4000);
}


// =======================================
// ==== Aktualizacja KOSZYKA =============
// =======================================
async function updateCartSummary() {
    let totalRegularPrice = 0; // Regularna suma zamówienia (Price)
    let totalDiscountedPrice = 0; // Suma po promocji (Promo)
    // Koszty dostawy
    let deliveryCost = null;
    let deliveryNotes = []; // Notaki do dostawy ["Koszt dostawy: 20 zł", "Darmowa dostawa powyżej 300 zł"];

    let hasPromo = false; // Flaga do sprawdzania, czy istnieje promocja


    // Pobieranie wszystkich pozycji w koszyku
    document.querySelectorAll('.shoppingCart .Cost').forEach(item => {
        let priceElement = item.querySelector(".Price");
        let promoElement = item.querySelector(".Promo");

        let price = priceElement ? parseFloat(priceElement.textContent.replace(/\s/g, '').replace(',', '.')) : 0;
        let promoPrice = promoElement ? parseFloat(promoElement.textContent.replace(/\s/g, '').replace(',', '.')) : null;

        totalRegularPrice += price;
        if (promoPrice !== null) {
            totalDiscountedPrice += promoPrice;
            hasPromo = true;
        } else {
            totalDiscountedPrice += price;
        }
    });


    try {
        // Pobierz aktualną [Waluta, Delivery Cost] z sesji
        const response = await fetch('include/get-settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
            }
        });
        const settings = await response.json();
        const currency = settings['currency'];
        // Pobieranie kosztów dostawy + type z SESSION
        const deliveryPack      = settings['delivery']['pack'];
        const deliveryProducts  = settings['delivery']['products'];

        // Sprawdzenie obecności "Paki" w koszyku [!!!!] => jeśli jest <span> to koszty dostawy zamówienia 0
        // // ===============================================
        const mainProductElement = document.querySelector('#mainProductPrice');
        const isMainProductInCart = mainProductElement !== null;
        // console.log(isMainProductInCart);
        // ===================

        if (deliveryPack['type'] === 'numeric' && deliveryProducts['type'] === 'numeric') {

            if (isMainProductInCart) {
                // Jeśli jest Paka w koszyku to (delivery pack[value] ~ 0) Koszt dostawy
                deliveryCost = deliveryPack['value'];

            } else {
                // Sprawdzenie i wybranie max Kosztów dostawy [Paki lub Produktów]
                deliveryCost = Math.max(deliveryPack['value'], deliveryProducts['value']);
            }

            // Sprawdź, czy zamówienie przekroczyło próg darmowej dostawy (cene lub nieskończoność)
            const freeThresholdPack = deliveryPack['free'] ? parseFloat(deliveryPack['free']) : Infinity;
            const freeThresholdProducts = deliveryProducts['free'] ? parseFloat(deliveryProducts['free']) : Infinity;

            const freeThreshold = Math.min(freeThresholdPack, freeThresholdProducts);
            if (totalDiscountedPrice >= freeThreshold) {
                deliveryCost = 0;
            }

            deliveryNotes.push({
                type: 'pack',
                help: deliveryPack['help']
            });
            deliveryNotes.push({
                type: 'products',
                help: deliveryProducts['help']
            });

        } else if (deliveryPack['type'] === 'numeric') {

            deliveryCost = deliveryPack['value'];

            // Sprawdź, czy zamówienie przekroczyło próg darmowej dostawy (cena lub nieskończoność)
            const freeThreshold = deliveryPack['free'] ? parseFloat(deliveryPack['free']) : Infinity;
            if (totalDiscountedPrice >= freeThreshold) {
                deliveryCost = 0;
            }

            deliveryNotes.push({
                type: 'pack',
                help: deliveryPack['help']
            });
            deliveryNotes.push({
                type: 'products',
                note: deliveryProducts['value'],
                help: deliveryProducts['help']
            });

        } else if (deliveryProducts['type'] === 'numeric') {

            deliveryCost = deliveryProducts['value'];

            // Sprawdź, czy zamówienie przekroczyło próg darmowej dostawy (cena lub nieskończoność)
            const freeThreshold = deliveryProducts['free'] ? parseFloat(deliveryProducts['free']) : Infinity;
            if (totalDiscountedPrice >= freeThreshold) {
                deliveryCost = 0;
            }

            deliveryNotes.push({
                type: 'pack',
                note: deliveryPack['value'],
                help: deliveryPack['help']
            });
            deliveryNotes.push({
                type: 'products',
                note: deliveryProducts['value'],
                help: deliveryProducts['help']
            });

        } else {

            deliveryNotes.push({
                type: 'pack',
                note: deliveryPack['value'],
                help: deliveryPack['help']
            });
            deliveryNotes.push({
                type: 'products',
                note: deliveryProducts['value'],
                help: deliveryProducts['help']
            });
        }
        
        // Darmowa dostawa od zamówienia powyżej 300 zł, inaczej 20 zł‚
        // deliveryCost = totalDiscountedPrice > 300 ? 0 : 30;


        // Aktualizacja wartości zamówienia
        const cartValueElement = document.getElementById("cartValue");
        if (cartValueElement) {
            if (hasPromo) {
                cartValueElement.innerHTML = `<del>${formatNumberWithSpaces(totalRegularPrice)}</del> <span>${formatNumberWithSpaces(totalDiscountedPrice)}</span> <span class="Currency">${currency}</span>`;
            } else {
                cartValueElement.innerHTML = `${formatNumberWithSpaces(totalRegularPrice)} <span class="Currency">${currency}</span>`;
            }
        }

        // Aktualizacja Kosztów dostawy
        const cartDeliveryElement = document.getElementById("cartDelivery");
        const cartDeliveryInfo = document.getElementById("cartDeliveryInfo");

        if (cartDeliveryElement && cartDeliveryInfo) {
            if (deliveryCost !== null) {

                if (totalDiscountedPrice > 0) { // pusty koszyk => zerujemu koszty dostawy
                    cartDeliveryElement.textContent = `${formatNumberWithSpaces(deliveryCost)} ${currency}`;
                } else {
                    cartDeliveryElement.textContent = '';
                }

                if (deliveryNotes.length > 0) {

                    cartDeliveryInfo.innerHTML = '';

                    deliveryNotes.forEach(note => {
                        if (note.help) {
                            cartDeliveryInfo.innerHTML += `<li>${note.help}</li>`;
                        }
                    });
                }
            } else {

                if (deliveryNotes.length > 0) {
                    cartDeliveryElement.innerHTML = '';
                    cartDeliveryInfo.innerHTML = '';
                
                    let existingNotes = []; // aby zapobiec powtarzaniu tekstu o dostawie
                    
                    deliveryNotes.forEach(note => {
                        if (!existingNotes.includes(note.note)) {
                            cartDeliveryElement.innerHTML += `${note.note}`;
                            existingNotes.push(note.note);
                        }
                        if (note.help) {
                            cartDeliveryInfo.innerHTML += `<li>${note.help}</li>`;
                        }
                    });
                }
            }
            
        }


        // Obliczenie wartości końcowej (z promocją)
        let totalAmount = totalDiscountedPrice;
        if (deliveryCost !== null) {
            totalAmount += deliveryCost;
        }
        const cartTotalElement = document.getElementById("cartTotal");
        if (cartTotalElement) {
            if (totalDiscountedPrice > 0) { // pusty koszyk => zerujemu [razem]
                cartTotalElement.textContent = `${formatNumberWithSpaces(totalAmount)} ${currency}`;
            } else {
                cartTotalElement.textContent = '';
            }
        }
    } catch (error) {
        console.error("Błąd podczas pobierania Waluty lub Kosztów dostawy get-settings:", error);
    }
}
