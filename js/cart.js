document.addEventListener('DOMContentLoaded', function() {

    // Button 'Zapisz konfigurację' + Waluta
    const saveConfigButton = document.getElementById('save-config');
    const currency = saveConfigButton.dataset.currency;

    // === Generuj KOD HTML paki w Koszyku
    function generatePackBlockInCartHTML(paka) {

        const isMainProductInCart = true; // aby poprawnie podliczać Koszty dostawy w updateCartSummary()

        const html = `
                <div id="mainProductRemove" class="p-2 position-absolute top-0 start-100 translate-middle">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                        </svg>
                    </span>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-2">
                    
                    <div class="product d-flex align-items-center justify-content-start gap-2">
                        <div class="thumbnail p-1">
                            <img src="img/${paka.thumb}" alt="${paka.name}">
                        </div>
                        <div class="product__name">
                            <span id="mainProductName">${paka.name}</span>
                        </div>
                    </div>

                    <div class="Cost">
                        ${paka.promo ? `
                            <del class="Price">
                                ${formatNumberWithSpaces(paka.price)}
                            </del>
                            <span id="mainProductPromo" class="Promo">
                                ${formatNumberWithSpaces(paka.promo)}
                            </span>
                        ` : `
                            <span id="mainProductPrice" class="Price">
                                ${formatNumberWithSpaces(paka.price)}
                            </span>
                        `}
                        <span class="Currency">
                            ${currency}
                        </span>
                    </div>
                </div>

                <div id="cartOptions" class="cartBoxOptions my-2">
                    <small>${paka.color.label}:</small>
                    <div class="boxColor d-flex justify-content-between align-items-center">
                        <div class="color-name d-flex justify-content-between align-items-center gap-2">
                            <span id="mainProductColorMarker"></span>
                            <span id="mainProductColorName">${paka.color.name}</span>
                        </div>
                        <div class="Cost">
                            <span id="mainProductColorPrice" class="Price">${formatNumberWithSpaces(paka.color.price)}</span>
                            <span class="Currency">${currency}</span>
                        </div>
                    </div>
                </div>
            `;
        return { html, isMainProductInCart };
    }

    // === Pobierz data- kolorów z pickera
    function updateColorPackFromPicker(element) {
        if (element) {
            const colorData = {
                name: element.dataset.name,
                price: parseFloat(element.dataset.price),
                code: element.dataset.color,
                ral: element.dataset.colorRal
            };
            updateCartWithColor(colorData);
        }
    }
    
    // ====================================================
    // ======= Add Pack in Cart
    // ====================================================
    saveConfigButton.addEventListener('click', () => {
        
        addPackToCart();
        

        // Dodaj pakę do Koszyka
        function addPackToCart() {
            fetch('include/add-pack-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
                },
                body: JSON.stringify({ action: 'addToCart' })
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Błąd sieci: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    const paka = data.paka; // Zwraca dane o pace z SESSION

                    const { html, isMainProductInCart } = generatePackBlockInCartHTML(paka);

                    const packBoxInCart = document.getElementById('cartBox');
                    if (packBoxInCart) {
                        // 0. Wygeneruj HTML i wstaw do kontenera
                        packBoxInCart.innerHTML = html;
                    } else {
                        console.error('Element cartBox nie został znaleziony.');
                        return;
                    }

                    // I.A. Aktualizuj kolor paki w koszyku (domyślny kolor)
                    const defaultColorOption = document.querySelector('#packColor a[data-default="1"]');
                    if (defaultColorOption) {
                        updateColorPackFromPicker(defaultColorOption);
                    }

                    // I.B. Aktualizuj kolor paki w koszyku (wybrany kolor)
                    const selectedColorOption = document.querySelector('#packColor a.selected');
                    if (selectedColorOption) {
                        updateColorPackFromPicker(selectedColorOption);
                    }

                    // II. Aktualizuj personalizację w koszyku
                    const sections = Array.from(document.querySelectorAll('input[id^="text_"]'))
                        .map(el => el.id.replace('text_', ''));

                    sections.forEach(sectionId => {
                        const sectionData = getTextData(sectionId);
                        if (sectionData) {
                            updateCustomizationCartItem(sectionData, `${sectionId}-item`);
                        }
                    });

                    // III. Dodaj flagę do Koszyka
                    getFlagOptions();

                    // IV. Aktualizujemy sumę cen w koszyku
                    updateCartSummary();

                } else {
                    console.error('Błąd w odpowiedzi serwera:', data.message || 'Brak szczegółów');
                }
            })
            .catch(error => {
                console.error('Błąd podczas dodawania do koszyka:', error);
            });
        }
    });


    // ====================================================
    // ======= Delete Pack from Cart
    // ====================================================
    document.getElementById('cartBox').addEventListener('click', (event) => {
        // Sprawdź, czy zdarzenie pochodzi w elementci z id 'mainProductRemove' (lub potomkach)
        if (event.target && event.target.closest('#mainProductRemove')) {
            removePackFromCart();
        }

        // Usuń pakę z Koszyka
        function removePackFromCart() {
            fetch('include/remove-pack-from-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
                },
                body: JSON.stringify({ action: 'removeFromCart' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
    
                    const cartBox = document.getElementById('cartBox');
                    cartBox.classList.remove('p-2');
                    cartBox.innerHTML = '';
        
                    // Aktualizujemy sumę cen w koszyku
                    updateCartSummary();

                }
            });
        }
    });



    // =======================================
    // 1. Aktualizacja w Koszyku i SESSION koloru Paki
    // =======================================

    // Funkcja aktualizująca koszyk z wybranym kolorem
    function updateCartWithColor(colorData) {
        const colorMarkerElement = document.getElementById('mainProductColorMarker');
        const colorNameElement = document.getElementById('mainProductColorName');
        const colorPriceElement = document.getElementById('mainProductColorPrice');
    
        if (!colorMarkerElement || !colorNameElement || !colorPriceElement) {
            // console.error("Nie znaleziono wymaganych elementów w DOM.");
            return;
        }
    
        colorNameElement.textContent = encodeHTML(colorData.name);
        colorNameElement.setAttribute('data-price', parseFloat(colorData.price));
        colorNameElement.setAttribute('data-color', encodeHTML(colorData.code));
        colorNameElement.setAttribute('data-color-ral', encodeHTML(colorData.ral));
        colorPriceElement.textContent = colorData.price ? formatNumberWithSpaces(colorData.price) : formatNumberWithSpaces(0);
        colorMarkerElement.style.backgroundColor = encodeHTML(colorData.code);
    
        updateCartSummary();
    }
    

    // Obsługa kliknięć na opcje kolorów
    const colorOptions = document.querySelectorAll('#packColor a');
    colorOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();

            // Usuń klasę "selected" z wszystkich opcji
            colorOptions.forEach(opt => opt.classList.remove('selected'));
            // Dodaj klasę "selected" do klikniętej opcji
            this.classList.add('selected');

            
            const colorData = {
                name: encodeHTML(this.dataset.name),
                price: parseFloat(this.dataset.price),
                code: encodeHTML(this.dataset.color),
                ral: encodeHTML(this.dataset.colorRal)
            };  

            // Aktualizacja koszyka
            updateCartWithColor(colorData);

            // Wysłanie żądania AJAX do aktualizacji koloru w sesji
            fetch('include/update-color.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    colorName: colorData.name,
                    colorPrice: colorData.price,
                    colorCode: colorData.code,
                    colorRal: colorData.ral
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // console.log('Kolor został zaktualizowany w sesji:', data.message);
                } else {
                    // console.error('Błąd podczas aktualizacji koloru w sesji:', data.message);
                }
            })
            .catch(error => {
                // console.error('Błąd sieciowy:', error);
            });
        });
    });

    // =======================================
    // 2. Personalizacja do Koszyka => save-config
    // =======================================
    function updateCustomizationCartItem(data, blockId) {
    
        const cartBox   = document.getElementById('cartBox');
        let cartItem    = document.getElementById(blockId);
    
        // Dane w localStorage
        const storedData = JSON.parse(localStorage.getItem('HEisCustomization')) || {};


        // Jeśli 'Text' jest pusty, usuwamy blok i aktualizujemy sumę
        if (!data.Text) {
            if (cartItem) {
                cartBox.removeChild(cartItem);

                // Usuń dane z localStorage dla danego Bloku
                if (storedData && storedData[data.Title]) {
                    delete storedData[data.Title];
                    localStorage.setItem('HEisCustomization', JSON.stringify(storedData));
                    console.log('Dane usunięte z localStorage');
                }
            }

            // Aktualizujemy sumę cen w koszyku
            updateCartSummary();
            
            return;
        }
    
        // Jeśli blok istnieje, aktualizujemy go
        if (cartItem) {
            // Aktualizujemy każdą właściwość (Text, Color, Price, Promo itp.)
            if (data.Title) {
                const titleElement = cartItem.querySelector('.Title');
                if (titleElement) {
                    titleElement.textContent = encodeHTML(data.Title);
                }
            }
    
            if (data.Text) {
                const textElement = cartItem.querySelector('.Text');
                if (textElement) {
                    textElement.textContent = encodeHTML(data.Text);
                }
            }
    
            if (data.Color) {
                const colorElement = cartItem.querySelector('.Color');
                if (colorElement) {
                    colorElement.style.backgroundColor = encodeHTML(data.Color);
                }
            } else {  // przy Grawerowaniu nie ma koloru
                const colorElement = cartItem.querySelector('.Color');
                if (colorElement) {
                    colorElement.remove();
                }
            }
    
            if (data.Price) {
                const priceElement = cartItem.querySelector('.Price');
                if (priceElement) {
                    priceElement.textContent = `${formatNumberWithSpaces(data.Price)}`;
                }
            }
    
            if (data.Promo) {
                const promoElement = cartItem.querySelector('.Promo');
                if (promoElement) {
                    promoElement.textContent = `${formatNumberWithSpaces(data.Promo)}`;
                    promoElement.style.color = 'green';
                }
            }
    
            if (data.Size) {
                const sizeElement = cartItem.querySelector('.Size');
                if (sizeElement) {
                    sizeElement.textContent = encodeHTML(data.Size);
                }
            }
    
            if (data.Format) {
                const formatElement = cartItem.querySelector('.Format');
                if (formatElement) {
                    formatElement.textContent = encodeHTML(data.Format);
                }
            } else {    // po odznaczeniu wszystkich formatów
                const formatElement = cartItem.querySelector('.Format');
                if (formatElement) {
                    formatElement.remove();
                }
            }
    
            if (data.Font) {
                const fontElement = cartItem.querySelector('.Font');
                if (fontElement) {
                    fontElement.textContent = encodeHTML(data.Font);
                }
            }
    
            if (data.Align) {
                const alignElement = cartItem.querySelector('.Align');
                if (alignElement) {
                    alignElement.textContent = encodeHTML(data.Align);
                }
            }
        } else {
            // Jeśli blok nie istnieje, tworzymy nowy
            cartItem = document.createElement('div');
            cartItem.id = blockId;
            cartItem.classList.add('cart-item', 'customization', 'my-3');
    
            // Budujemy HTML nowego elementu, każdy atrybut w osobnym <span>
            let innerHTML = `
                <div class="Title">${data.Title}</div>
                
                        
                <span class="Align">${encodeHTML(data.Align || '')}</span>
                

                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="info d-flex align-items-center justify-content-start gap-2 my-2">
                        ${data.Color
                            ? `<span class="Color" ${data.Color ? `style="background-color: ${data.Color};"` : ''}></span>`
                            : ''
                        }
                        <span class="Text">${encodeHTML(data.Text)}</span>
                    </div>
                    <div class="Cost">
                            ${data.Promo
                                ? `<del class="Price">${formatNumberWithSpaces(data.Price)}</del> <span class="Promo">${formatNumberWithSpaces(data.Promo)}</span> <span class="Currency">${currency}</span>`
                                : `<span class="Price">${formatNumberWithSpaces(data.Price)}</span> <span class="Currency">${currency}</span>`
                            }
                        </span>
                    </div>
                </div>

                <div class="detail d-flex align-items-start justify-content-start gap-2">
                    <div class="d-flex align-items-start justify-content-start gap-2">
                        <span class="Font">${encodeHTML(data.Font || '')}</span>
                    </div>
                    <div class="d-flex align-items-start justify-content-start gap-2">
                        <span class="Size">${encodeHTML(data.Size || '')}</span>
                        <span class="Format">${encodeHTML(data.Format || '')}</span>
                    </div>
                </div>
            `;
            cartItem.innerHTML = innerHTML;
    
            cartBox.appendChild(cartItem);
        }

        const newData = {
            "Title": encodeHTML(data.Title || false),
            "Text": encodeHTML(data.Text || false),
            "Color": data.Color || '',
            "Align": encodeHTML(data.Align || ''),
            "Font": encodeHTML(data.Font || ''),
            "Size": encodeHTML(data.Size || ''),
            "Format": encodeHTML(data.Format || '')
        };

        if (JSON.stringify(storedData[data.Title]) !== JSON.stringify(newData)) {
            storedData[data.Title] = newData;
            localStorage.setItem('HEisCustomization', JSON.stringify(storedData));
        }

        // Zauktualizuj dane w SESSION
        fetch('include/update-customization.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
            },
            body: JSON.stringify({
                Title: data.Title,
                Text: data.Text,
                Color: data.Color,
                Align: data.Align,
                Size: data.Size,
                Font: data.Font,
                Format: data.Format,
                Price: data.Price,
                Promo: data.Promo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Personalizacja została zaktualizowana w sesji:', data.message);
            } else {
                console.error('Błąd podczas aktualizacji personalizacji w sesji:', data.message);
            }
        })
        .catch(error => {
            console.error('Błąd sieciowy:', error);
        });
        
        // Aktualizuj sumę wszystkich Cen w Koszyku
        updateCartSummary()
    }
    
    function getTextData(sectionPrefix) {
        const title     = document.getElementById(`text_${sectionPrefix}`).dataset.name || false;
        const text      = document.getElementById(`text_${sectionPrefix}`).value.trim();
        const color     = document.getElementById(`selectedColorText_${sectionPrefix}`)?.style.backgroundColor || false;
        const align     = document.querySelector(`#textAlign_${sectionPrefix} .tools.active`)?.dataset.alignName || false;
        const size      = document.querySelector(`#textSize_${sectionPrefix} .tools.active`)?.dataset.sizeName || false;
        const formats   = Array.from(document.querySelectorAll(`#textStyle_${sectionPrefix} .tools.active`))
            .map(tool => tool.dataset.styleName)
            .join(', ') || false;
        const font      = document.querySelector(`#fontFamily_${sectionPrefix} canvas.active`)?.dataset.fontName || false;
        const price     = parseFloat(document.getElementById(`text_${sectionPrefix}`).dataset.price) || 0;
        const promo     = parseFloat(document.getElementById(`text_${sectionPrefix}`).dataset.promo) || false;
    
        return {
            Title: title,
            Text: text,
            Color: color,
            Align: align,
            Size: size,
            Format: formats,
            Font: font,
            Price: price,
            Promo: promo
        };
    }
    
    function handleSaveConfig() {
        
        // const sections = ['grawerTopPanel', 'toppanel', 'door', 'side'];
    
        // ✅ Elastyczne i skalowalne => dodasz/usuniesz sekcję, kod się dostosuje.
        const sections = Array.from(document.querySelectorAll('input[id^="text_"]'))
            .map(el => el.id.replace('text_', '')); // Usuwa 'text_' z ID, zostawiając same nazwy sekcji
    
    
        sections.forEach(sectionId => {
            const sectionData = getTextData(sectionId);
            
            if (sectionData) {
                updateCustomizationCartItem(sectionData, `${sectionId}-item`);
            } else {
                showError(`Brak danych dla sekcji: ${sectionId}`);
                console.warn(`Brak danych dla sekcji: ${sectionId}`);
            }
        });
    }
    
    saveConfigButton.addEventListener('click', handleSaveConfig);   // zapisz Personalizację
    

    // ================================
    // 3. Flaga do Koszyka => save-config
    // ================================
    // Funkcja do pobierania danych flagi
    function getFlagData(flagCheckboxId, inputCountryId) {
        const flagCheckbox = document.getElementById(flagCheckboxId);
        const inputCountry = document.getElementById(inputCountryId);

        const title = flagCheckbox.dataset.name || false;
        const price = parseFloat(flagCheckbox.dataset.price) || 0;
        const promo = parseFloat(flagCheckbox.dataset.promo) || false;
        const country = inputCountry.value.trim();

        return {
            FlagTitle: title ? encodeHTML(title) : '',
            FlagPrice: price ?? false,
            FlagPromo: promo ?? false,
            UserCountry: country ? encodeHTML(country) : ''
        };
    }

    function getFlagOptions() {
        const flagCheckbox = document.getElementById('flag');
        const inputCountry = document.getElementById('user_country');

        const flagWarning =  encodeHTML(flagCheckbox.dataset.warning) || false;

        const flagData = getFlagData('flag', 'user_country');
 
        // do SESSION
        let checkin = false;
        if (flagCheckbox.checked) {

            checkin = true;
        }
            
        // Zauktualizuj dane w SESSION
        fetch('include/update-flag.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
            },
            body: JSON.stringify({
                Checked: checkin, // => sesion
                Title: flagData.FlagTitle,
                Country: flagData.UserCountry,
                Price: flagData.FlagPrice,
                Promo: flagData.FlagPromo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Flaga została zaktualizowana w sesji:', data.message);
            } else {
                console.error('Błąd podczas aktualizacji flagi w sesji:', data.message);
            }
        })
        .catch(error => {
            console.error('Błąd sieciowy:', error);
        });


        if (flagCheckbox.checked) {
            if (!inputCountry.value.trim()) {
                // Jeśli pole input jest puste, ustaw kursor i dodaj klasę błędu
                inputCountry.focus();
                inputCountry.classList.add('is-invalid');
                
                flagWarning ? showError(flagWarning, 'warning-message') : false;

            } else {
                // Jeśli pole input jest wypełnione, usuń klasę błędu
                inputCountry.classList.remove('is-invalid');
            }

            const cartBox = document.getElementById('cartBox');

            let flagOptionElement = cartBox.querySelector('#flagOption');

            if (flagData.UserCountry) {
                if (!flagOptionElement) {
                    flagOptionElement = document.createElement('div');
                    flagOptionElement.id = 'flagOption';
                    flagOptionElement.classList.add('cart-item', 'customization', 'my-3');
                    cartBox.appendChild(flagOptionElement);
                }

                flagOptionElement.innerHTML = `
                    <div class="Title">${encodeHTML(flagData.FlagTitle)}</div>

                    <div class="d-flex align-items-center justify-content-between gap-2">

                        <div class="Text">
                            ${encodeHTML(flagData.UserCountry)}
                        </div>
                        <div class="Cost">
                            ${flagData.FlagPromo
                                ? `<del class="Price">${formatNumberWithSpaces(flagData.FlagPrice)}</del> <span class="Promo">${formatNumberWithSpaces(flagData.FlagPromo)}</span> <span class="Currency">${currency}</span>`
                                : `<span class="Price">${formatNumberWithSpaces(flagData.FlagPrice)}</span> <span class="Currency">${currency}</span>`
                            }
                        </div>
                    </div>
                `;

            } else {
                if (flagOptionElement) {
                    flagOptionElement.innerHTML = '';
                }
            }
        } else {
            const cartBox = document.getElementById('cartBox');
            const flagOptionElement = cartBox.querySelector('#flagOption');
            if (flagOptionElement) {
                flagOptionElement.innerHTML = '';
            }
            // Usuń klasę błędu, jeśli checkbox nie jest zaznaczony
            inputCountry.classList.remove('is-invalid');
        }

        // Aktualizuj sumę wszystkich Cen w Koszyku
        // updateCartSummary()
    }

    saveConfigButton.addEventListener('click', getFlagOptions);     // zapisz Flagę


    // ================================
    // 4. Dodawanie pozostałych produktów do Koszyka => add-to-cart
    // ===============================
    
    const colorSpans = document.querySelectorAll('.parameters .values span');
    
    // Podmiana obrazka po kliknięciu w span Kolor
    colorSpans.forEach(span => {
        span.addEventListener('click', function() {
            const itemKey = this.getAttribute('data-item-key');
            const imageSrc = this.getAttribute('data-image-src');
            const image = this.getAttribute('data-image');
            const color = this.getAttribute('data-color');
            const item = document.getElementById('color-image-' + itemKey);
            
            // Usuń klasę "checked" z pozostałych spanów
            const siblings = this.parentNode.children;
            for (let i = 0; i < siblings.length; i++) {
                siblings[i].classList.remove('checked');
            }
            
            // Dodaj klasę "checked" do klikniętego spana
            this.classList.add('checked');
            
            // Zmień obrazek
            item.src = imageSrc;
            
            // Aktualizuj atrybut data-img w przycisku Add to Cart
            const cartButton = document.querySelector('.addToCart[data-key="' + itemKey + '"]');
            if (cartButton) {
                cartButton.setAttribute('data-img', image);
                cartButton.setAttribute('data-color', color);
            }
        });
    });
    // --- Koniec podmiany obrazka


    const cartButtons = document.querySelectorAll('.addToCart');
    const cartItemsElement = document.getElementById('button_cart_total');
    const cartContainer = document.getElementById('cartBoxPromo');

    const transInfo = document.getElementById('featured_info_toCart'); // do pobrania Info z sekcji produktów dodatkowych
    const buttonDeleteText = transInfo.getAttribute('data-delete-button'); // treść na button do usunięcia z Koszyka
    const errorAddToCart = transInfo.getAttribute('data-error-add-message'); // treść przy errors

    // Obiekt koszyka
    const Cart = {
        items: [],

        async addItem(itemKey, image, name, color, price, shipping, shippingContent) {
            try {
                const response = await fetch('include/verify-add-to-cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ key: itemKey, image, name, color, price, shipping, shippingContent })
                });

                if (!response.ok) throw new Error(errorAddToCart);
                const result = await response.json();
                if (result.status !== 'success') throw new Error(errorAddToCart);

                const verifiedItem = result.item;
                const existingItem = this.items.find(item => item.key === verifiedItem.key);

                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    this.items.push({ ...verifiedItem, quantity: 1, shippingContent });
                }

                // Dodaj produkt do sesji
                await fetch('include/update-extra-products.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        action: 'add',
                        key: itemKey,
                        name,
                        color,
                        price,
                        quantity: existingItem ? existingItem.quantity : 1,
                        image,
                        shipping,
                        shippingContent
                    })
                });

                this.updateView();
            } catch (error) {
                showError(error.message);
            }
        },

        async removeItem(itemKey) {
            this.items = this.items.filter(item => item.key !== itemKey);

            // Usuń produkt z sesji
            await fetch('include/update-extra-products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    action: 'remove',
                    key: itemKey
                })
            });

            this.updateView();
        },

        updateView() {
            cartContainer.innerHTML = '';

            let totalPrice = 0;
            let totalShipping = 0;

            this.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                totalPrice += itemTotal;

                const shippingCost = typeof item.shipping === 'number' ? item.shipping : parseFloat(item.shipping.price || 0);
                totalShipping += shippingCost * item.quantity;

                const itemElement = document.createElement('div');
                itemElement.classList.add('cart-items', 'my-1', 'p-2');
                itemElement.innerHTML = `
                    <div class="row d-flex align-items-center">
                        <div class="col-2">
                            <img class="img-fluid" src="img/${item.image}" alt="${item.name}">
                        </div>
                        <div class="col-4">
                            <span class="name">${item.name}</span>
                        </div>
                        <div class="col-3 Cost">
                            <span class="Price">${formatNumberWithSpaces(itemTotal)}</span>
                            <span class="Currency">${currency}</span>
                        </div>
                        <div class="col-1">
                            <span class="quantity">${item.quantity}</span>
                        </div>
                        <div class="col-2 text-end">
                            <span class="removeFromCart" data-key="${item.key}" title="${buttonDeleteText}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                                </svg>
                            </span>
                        </div>
                        ${shippingCost > 0 ? `<div class="text-end"><span class="shipping">${item.shippingContent} ${formatNumberWithSpaces(shippingCost)}</span> <span class="currency">${currency}</span></div>` : ''}
                    </div>
                `;
                cartContainer.appendChild(itemElement);
            });

            // Badge => ilość produktów w Koszyku (dafault = 0)
            cartItemsElement.textContent = this.items.reduce((total, item) => total + item.quantity, 0);

            document.querySelectorAll('.removeFromCart').forEach(button => {
                button.addEventListener('click', function() {
                    Cart.removeItem(this.getAttribute('data-key'));
                    // Aktualizuj sumę wszystkich Cen w Koszyku
                    updateCartSummary();
                });
            });

            // Wywołanie funkcji aktualizującej podsumowanie koszyka
            updateCartSummary();
        }
    };

    // Obsługa przycisków dodawania do koszyka
    cartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemKey = this.getAttribute('data-key');
            const image = this.getAttribute('data-img');
            const name = this.getAttribute('data-name');
            const color = this.getAttribute('data-color');
            const price = parseFloat(this.getAttribute('data-price'));
            const shipping = parseFloat(this.getAttribute('data-shipping'));
            const shippingContent = this.getAttribute('data-shipping-content');

            if (!shippingContent) {
                console.warn('shippingContent is undefined or empty');
            }

            Cart.addItem(itemKey, image, name, color, price, shipping, shippingContent);
            // Aktualizuj sumę wszystkich Cen w Koszyku
            updateCartSummary();
        });
    });


    // ================================
    // Wysyłka formularza
    // ===============================
    function submitOrderForm(cartForm_ID) {
        const form = document.getElementById(cartForm_ID);
        const formData = new FormData(form); // Zbieramy dane z formularza

        const submitButton = form.querySelector('[type="submit"]');
        // submitButton.disabled = true;

        // Wyczyszczenie wcześniejszych błędów i powiadomień
        clearFormMessages(form);

        fetch('include/order.php', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="token"]').getAttribute('content')
            },
            body: formData, // Wysyłamy dane formularza jako FormData
        })
        .then(response => response.json())
        .then(responseData => {
            handleResponse(responseData, form, submitButton);
        })
        .catch(error => {
            handleError(form);
        });
    }

    function clearFormMessages(form) {
        document.getElementById('formMessage').innerHTML = '';
        const inputs = form.querySelectorAll('input, checkbox, textarea, select'); // Wszystkie pola formularza

        // Usuń wcześniejsze klasy 'is-invalid'
        inputs.forEach(input => input.classList.remove('is-invalid'));

        // Usuń poprzednie komunikaty o błędach
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }

    function handleResponse(responseData, form, submitButton) {
        if (responseData.status === 'success') {

            // Jeśli zamówienie zostało pomyślnie wysłane
            document.getElementById('formMessage').innerHTML = '<div class="alert alert-success">'+ responseData.message +'</div>';
            showError(responseData.message, 'success-message')
            form.reset(); // Resetujemy formularz
            form.style.display = 'none'; // Ukrycie formularza po wysłaniu
            // Zaktualizuj HEisForm na bieżącą datę i czas
            localStorage.setItem('HEisForm', new Date().toISOString());
            
        } else {
            // Jeśli wystąpił błąd przy składaniu zamówienia
            submitButton.disabled = false;
            document.getElementById('formMessage').innerHTML = '<div class="alert alert-danger">' + responseData.message + '</div>';

            // Obsługa błędów - dodanie klasy 'is-invalid' do błędnych pól
            if (responseData.errors) {
                for (let field in responseData.errors) {
                    const inputField = form.querySelector(`[name="${field}"]`);
                    if (inputField) {
                        inputField.classList.add('is-invalid');  // Dodajemy klasę is-invalid

                        // Sprawdź, czy już istnieje komunikat błędu dla tego pola
                        if (!inputField.parentElement.querySelector('.invalid-feedback')) {
                            const errorMessage = document.createElement('div');
                            errorMessage.classList.add('invalid-feedback');
                            errorMessage.textContent = responseData.errors[field];
                            inputField.parentElement.appendChild(errorMessage);
                        }
                    }
                }
            }
        }
    }

    function handleError(form) {
        document.getElementById('formMessage').innerHTML = '<div class="alert alert-danger">Wystąpił błąd. Spróbuj ponownie później.</div>';
    }

    // Obsługa kliknięcia przycisku "Zamów"
    document.getElementById('send').addEventListener('click', function(event) {
        event.preventDefault();
        submitOrderForm('cartForm');
    });


// ======================================================================
});

