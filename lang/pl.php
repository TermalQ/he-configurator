<?php
return [
    'settings' => [
        'theme' => 'Zmień motyw',
        'currency' => 'zł',
        'delivery' => [
            'pack' => [
                'type' => 'numeric',
                'value' => 0,
                'help' => '',   // extra info np: darmowa dostawa od zamówienia 300zł
                'free' => '',  // darmowy po przekroczeniu wartości zamówienia
            ],
            'products' => [
                'type' => 'numeric',
                'value' => 30,
                'help' => '',
                'free' => '',
            ]
        ],
        'discount' => [
            'name' => 'Rabat',
            'value' => 0,
        ],
        'fees' => [
            'name' => 'Opłaty' ,
            'value' => 0,
        ]
    ],

    'title' => 'Konfigurator ⚡ Elektryczna Paka 🔋 Hussaria Electra 🚀',
    'header' => 'Formularz zamówienia',
    'helper' => 'Skonfiguruj swoją pakę i wyślij formularz, a my skontaktujemy się z Tobą i poprowadzimy Cię przez kolejny etap zamówienia 😊',
    'form' => [
        'colors' => [
            'title' => 'Kolor paki',
            'fields' => [
                'label' => 'Kolor paki',
                'help' => 'Wybierz kolor',
                'info' => 'Dostępna paleta kolorów do zmiany koloru paki',
            ],
        ],
        'marker' => [
            'title' => 'Personalizacja',
            'fields' => [
                'label' => 'Personalizacja',
                'help' => 'Wybierz opcje',
                'info' => 'Dostępne opcje personalizacji paki',
                'holder' => 'Tekst'
            ],
            'options' => [
                'grawer' => [
                    'title' =>'Grawerowanie',
                    'price' => [
                        'show' => 0,
                        'value' => 0,
                        'promo' => '',
                    ],
                ],
                'texts' => [
                    'title' =>'Tekst',
                    'price' => [
                        'show' => 0,
                        'value' => 0,
                        'promo' => '',
                    ],
                ],
            ],
            'tools' => [
                'font-color' => 'Kolor',
                'font-align' => 'Wyrównanie',
                'font-style' => 'Formatowanie',
                'font-size' => 'Rozmiar',
                'font-family' => 'Czcionka',
                
            ],
            'flag' => [
                'title' => 'Flaga',
                'fields' => [
                    'label' => 'Flaga',
                    'holder' => 'Wpisz nazwę kraju',
                    'info' => '',
                    'warning' => 'Wpisz nazwę kraju (lub kod: USA, PL, FR, UK, itd.)'
                ],
                'price' => [
                        'show' => 0,
                        'value' => 0,
                        'promo' => ''
                    ],
            ]
        ],
        'info' => [
            'send-pdf' => 'Dodatkowe pliki graficzne w formacie PDF wyślij na adres',
            'send-mail' => 'biuro@hussaria.pl'
        ],
        'buttons' => [
            'save' => 'Dodaj do koszyka',
            'cart' => 'Koszyk',
            'add' => 'Dodaj do koszyka',
            'view' => 'Zobacz koszyk',
            'submit' => 'Zamawiam',
            'delete' => 'Usuń'
        ],
        'cart' => [
            'product-image' => 'Zdjęcie',
            'product-name' => 'Nazwa',
            'product-quantity' => 'Ilość',
            'product-total' => 'Suma',
        ],
        'errors' => [
            'add-product' => 'Błąd: Nie można dodać produktu do koszyka!',
            'del-product' => 'Błąd: Nie można usunąć produktu z koszyka!',
        ],
        
        // ========================

        'person' => [
            'title' => 'Dane personalne',
            'fields' => [
                'first_name' => 'Imię / Nazwa firmy',
                'last_name' => 'Nazwisko / Numer NIP',
                'email' => 'E-mail',
                'phone' => 'Telefon',
            ],
        ],
        'order' => [
            'title' => 'Zamówienie',
            'accept' => [
                'title' => 'Wyrażam zgodę na przetwarzanie danych osobowych wpisanych w formularzu w celu udzielenia odpowiedzi na przesłane zapytanie zgodnie z',
                'name' => 'Polityką Prywatności',
                'link' => 'https://electra.hussaria.pl/content/17-polityka-prywatnosci',
            ],
            'agree' => [
                'title' => 'Akceptuję',
                'name' => 'regulamin',
                'link' => 'https://electra.hussaria.pl/pl/content/16-regulamin',
                'shop' => 'sklepu internetowego'
            ]
        ],
        'order_summary' => [
            'value' => 'Wartość zamówienia',
            'cost' => 'Koszt dostawy',
            'amount' => 'Razem',
            'back' => 'Powrót',
            'notification' => 'Produkt został dodany!',
            'confirm' => 'Dziękujemy za zamówienie!',
        ]
    ],
    
    //===============
    'package' => [
        'active' => 1,
        'name' => 'Elektryczna paka',
        'desc' => 'Elektryczna paka jeździecka - zamów personalizowaną pakę z napędem elektrycznym. Idealne rozwiązanie do przechowywania siodeł i sprzętu jeździeckiego. Wyposażona w duże koła i napęd elektryczny, zapewnia wygodę, łatwość manewrowania i eliminuje wysiłek fizyczny. Doskonały wybór dla zawodników i pasjonatów jeździectwa.',
        'keywords' => 'hussaria electra, elektryczna paka, paka z napędem elektrycznym',
        'thumb' => 'he_thumb_elektryczna_paka.png',
        'status' => 'W magazynie',
        'deadline' => 'Gdy towaru nie ma na magazynie, termin realizacji wydłuża się do 3-4 tygodni',
        
        'show_price' => 1,
        'price' => 35547,
        'promo' => '', // Cena promocyjna
        'shipping' => [
            'active' => 1,
            'content' => 'Elektryczna paka',
            'span' => 'z darmową dostawą',
            'info' => 'na terenie całej Polski'
        ],
        'include' => [
            'intro_products' => 'Kupując elektryczną pakę otrzymujesz gratis',
            'products' => [
                'battery' => [
                    'active' => 1,
                    'name' => 'Akumulator',
                    'desc' => 'Pojemność: 4 Ah',
                    'image' => 'battery.png',
                ],
                'charger' => [
                    'active' => 1,
                    'name' => 'Ładowarka',
                    'desc' => 'Do akumulatora',
                    'image' => 'charger.png',
                ],
                'pomp' => [
                    'active' => 1,
                    'name' => 'Pompka elektryczna',
                    'desc' => 'z funkcją Powerbanku',
                    'image' => 'pomp.png',
                ],
                'pharmacy' => [
                    'active' => 1,
                    'name' => 'Apteczka',
                    'desc' => 'Przybornik medyczny',
                    'image' => 'pharmacy.png',
                ],
                'lock' => [
                    'active' => 1,
                    'name' => 'Kłódka na szyfr',
                    'desc' => 'Ochronne zapięcie',
                    'image' => 'lock.png',
                ],
                'powerbank' => [
                    'active' => 1,
                    'name' => 'PowerBank',
                    'desc' => 'Z funkcją szybkiego ładowania',
                    'image' => 'powerbank.png',
                ],
                'cables' => [
                    'active' => 1,
                    'name' => 'Zestaw kabli',
                    'desc' => 'Zestaw kabli do ładowania telefonu 3w1',
                    'image' => 'cables.png',
                ],
                'gadgets' => [
                    'active' => 1,
                    'name' => 'Zestaw gadżetów',
                    'desc' => 'Materiałowa torba zakupowa, stylowa czapka z daszkiem, nożyczki taktyczne z zestawem śrubokrętów, kubek termiczny, magnetyczna tablica suchościeralna z kompletem akcesoriów',
                    'image' => 'gadgets.png',
                ],
            ],
            

        ]
    ],
    // ==============
    'extra' => 'Dodatkowo w ofercie',
    'products' => [
        'promo_battery' => [
            'active' => 1,
            'name' => 'Akumulator',
            'desc' => '',
            'parameters' => [
                'name' => 'Pojemność',
                'value' => '4 Ah',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'battery_4ah.png',
            'show_price' => 1,
            'price' => 950,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_charger' => [
            'active' => 1,
            'name' => 'Ładowarka do akumulatora',
            'desc' => '',
            'parameters' => [
                'name' => 'Baterie w zestawie',
                'value' => 'Nie',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'charger_4_5ah.png',
            'show_price' => 1,
            'price' => 550,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_ramp' => [
            'active' => 1,
            'name' => 'Składany trap załadunkowy',
            'desc' => '',
            'parameters' => [
                'name' => 'Maks. obciążenie',
                'value' => '270 kg',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'ramp_270kg.png',
            'show_price' => 1,
            'price' => 1490,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_battery5' => [
            'active' => 1,
            'name' => 'Akumulator',
            'desc' => '',
            'parameters' => [
                'name' => 'Pojemność',
                'value' => '5 Ah',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'battery_4ah.png',
            'show_price' => 1,
            'price' => 1050,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_cover' => [
            'active' => 1,
            'name' => 'Pokrowiec na pakę',
            'desc' => '',
            'colors' => [
                'name' => 'Kolor',
                'values' => [
                    'black' => [
                        'default' => 1,
                        'title' => 'Czarny',
                        'image' => 'tack_cover_black.png',
                        'color' => '#2d2a2e',
                    ]
                ],
            ],
            'parameters' => [
                'name' => '',
                'value' => '',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'tack_cover.png',
            'show_price' => 1,
            'price' => 1990,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_cover_trap' => [
            'active' => 1,
            'name' => 'Pokrowiec na trap',
            'desc' => '',
            'colors' => [
                'name' => 'Kolor',
                'values' => [
                    'black' => [
                        'default' => 1,
                        'title' => 'czarny',
                        'image' => 'ramp_cover_black.png',
                        'color' => '#2d2a2e',
                    ]
                ],
            ],
            'parameters' => [
                'name' => '',
                'value' => '',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'ramp_cover.png',
            'show_price' => 1,
            'price' => 480,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_cap' => [
            'active' => 1,
            'name' => 'Czapka z daszkiem',
            'desc' => 'łatwa regulacja obwodu',
            'cta' => [
                'color' => '#f78da7',
                'icon' => '',
                'text' => 'Odkryj nowości z kolekcji',
                'anchor' => 'elite.hussaria.pl',
                'link' => 'https://elite.hussaria.pl/',
            ],
            'colors' => [
                'name' => 'Kolor',
                'values' => [
                    'black' => [
                        'default' => 1,
                        'title' => 'Czarna',
                        'image' => 'cap_black.png',
                        'color' => '#2d2a2e',
                    ],
                    'pink' => [
                        'default' => 0,
                        'title' => 'Różowa',
                        'image' => 'cap_pink.png',
                        'color' => '#ffc0cb',
                    ]
                ],
            ],
            'parameters' => [
                'name' => '',
                'value' => '',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'cap.png',
            'show_price' => 1,
            'price' => 99,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
        'promo_thermo_cup' => [
            'active' => 1,
            'name' => 'Kubek termiczny',
            'desc' => 'z możliwością personalizacji',
            'parameters' => [
                'name' => 'Pojemność',
                'value' => '1000 ml',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'thermo_cup.png',
            'show_price' => 1,
            'price' => 149,
            'shipping' => [
                'active' => 0,
                'free' => 1,
                'price' => 0,
                'class' => '',
                'content' => 'Dodstawa od',
            ],
        ],
    ],
];