<?php
return [
    'subject' => [
        'admin' => 'Nowe zamówienie - Hussaria Electra',
        'client' => 'Potwierdzenie zamówienia - Hussaria Electra',
    ],
    'content' => [
        'title' => 'Potwierdzenie zamówienia',
        'header' => [
            'hello' => 'Witaj',
            'shop' => 'w stajni Hussaria Electra!',
            'intro' => 'Dziękujemy za wybór ELEKTRYCZNEJ PAKI i wypełnienie formularza. Poniżej znajdziesz szczegóły dotyczące Twojego zamówienia. Wkrótce się z Tobą skontaktujemy, by poprowadzić Cię przez kolejny etap zamówienia.'
        ],
        'summary' => [
            'table' => [
                'header' => [
                    'title' => 'Szczegóły zamówienia',
                    'date' => 'Dnia'
                ],
                'rows' => [
                    'total' => 'Kwota zamówienia',
                    'name' => 'Imię/Nazwa firmy',
                    'lastname' => 'Nazwisko/NIP',
                    'email' => 'E-mail',
                    'phone' => 'Tel',
                    'order' => 'Zamówienie',
                    'extra' => 'Dodatkowo'
                ]
            ]
        ],
        'helper' => [
            'head' => 'Masz pytania? Skontaktuj się z nami',
            'content' => 'Prosimy pamiętać, że wraz z zakupem otrzymujecie Państwo nie tylko najwyższej jakości sprzęt, ale także pełne wsparcie naszego zespołu.',
            'info' => 'Pozostajemy do dyspozycji, służąc pomocą w razie pytań lub potrzeby pomocy technicznej. Jesteśmy dostępni od poniedziałku do piątku w godzinach 7:00 - 15:00'
        ], 
    ],
    'footer' => [
        'link' => 'https://electra.hussaria.pl/',
        'copyright' => '© 2025 electra.hussaria.pl'
    ],
    'notifications' => [
        'success' => [
            'send' => 'Zamówienie zostało pomyślnie wysłane'
        ],
        'errors' => [
            'disabled' => 'Zamówienie drogą mailową jest wyłączone!',
            'validate' => 'Formularz zawiera nieprawidłowe/puste pola',
            'send' => 'Błąd podczas wysyłania formularza',
            'empty' => 'Brak produktów w Koszyku',
            'fields' => [
                'empty' => [    
                    'name' => 'Pole jest wymagane',
                    'lastname' => 'Pole jest wymagane',
                    'email' => 'E-mail jest wymagany',
                    'phone' => 'Numer telefonu jest wymagany',
                    'agree' => 'Należy zaakceptować regulamin',
                    'accept' => 'Należy zaakceptować warunki'
                ],
                'incorect' => [
                    'email' => 'Niepoprawny format adresu e-mail',
                    'phone' => 'Niepoprawny format numeru telefonu [9-15 znaków]',
                    'browser' => 'Nieautoryzowany użytkownik',
                    'timer' => 'Formularz ponownie został wysłany zbyt szybko. Odczekaj parę minuty.',
                    'trying' => 'Zbyt wiele prób wysłania formularza'
                ]
            ]
        ]
    ]
];