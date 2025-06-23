<?php
return [
    'settings' => [
        'theme' => 'Change theme',
        'currency' => 'EUR',
        'delivery' => [
            'pack' => [
                'type' => 'text',
                'value' => 'individual pricing',
                'help' => '',   // extra info np: darmowa dostawa od zamówienia 300zł
                'free' => ''  // darmowy po przekroczeniu wartości zamówienia
            ],
            'products' => [
                'type' => 'text',
                'value' => 'individual pricing',
                'help' => '',
                'free' => ''
            ]
        ],
        'discount' => [
            'name' => 'Discount',
            'value' => 0,
        ],
        'fees' => [
            'name' => 'Fees' ,
            'value' => 0,
        ]
    ],

    'title' => 'Configurator ⚡ Electric Tack Locker 🔋 Hussaria Electra 🚀',
    'header' => 'Order form',
    'helper' => 'Configure your package and submit the form, and we will contact you and guide you through the next stage of the order 😊',
    'form' => [
        'colors' => [
            'title' => 'Color tack locker',
            'fields' => [
                'label' => 'Color',
                'help' => 'Select the color',
                'info' => '',
            ]
        ],
        'marker' => [
            'title' => 'Personalization',
            'fields' => [
                'label' => 'Personalization',
                'help' => 'Select options',
                'info' => 'Available package personalization options',
                'holder' => 'Text',
            ],
            'options' => [
                'grawer' => [
                    'title' =>'Engraving',
                    'price' => [
                        'show' => 0,
                        'value' => 0,
                        'promo' => '',
                    ],
                ],
                'texts' => [
                    'title' =>'Text',
                    'price' => [
                        'show' => 0,
                        'value' => 0,
                        'promo' => '',
                    ],
                ],
            ],
            'tools' => [
                'font-color' => 'Color',
                'font-align' => 'Alignment',
                'font-style' => 'Formatting',
                'font-size' => 'Size',
                'font-family' => 'Font',
                
            ],
            'flag' => [
                'title' => 'Flag',
                'fields' => [
                    'label' => 'Flag',
                    'holder' => 'Enter the country name',
                    'info' => '',
                    'warning' => 'Enter the name of the country (or code: USA, PL, FR, UK, etc.)'
                ],
                'price' => [
                        'show' => 0,
                        'value' => 0,
                        'promo' => '',
                    ],
            ]
        ],
        'info' => [
            'send-pdf' => 'Additional graphic files in PDF format should be sent to',
            'send-mail' => 'biuro@hussaria.pl'
        ],
        'buttons' => [
            'save' => 'Add to cart',
            'cart' => 'Cart',
            'add' => 'Add to cart',
            'view' => 'View cart',
            'submit' => 'Order',
            'delete' => 'Remove'
        ],
        'cart' => [
            'product-image' => 'Image',
            'product-name' => 'Name',
            'product-quantity' => 'Quantity',
            'product-total' => 'Total',
        ],
        'errors' => [
            'add-product' => 'Error: Cannot add product to cart!',
            'del-product' => 'Error: Cannot remove product from cart!',
        ],
        
        // ========================

        'person' => [
            'title' => 'Personal data',
            'fields' => [
                'first_name' => 'First Name / Company',
                'last_name' => 'Last Name / Tax Identification',
                'email' => 'E-mail',
                'phone' => 'Phone',
            ],
        ],
        'order' => [
            'title' => 'Order',
            'accept' => [
                'title' => 'I consent to the processing of personal data entered in the form in order to respond to the submitted inquiry in accordance with',
                'name' => 'the Privacy Policy',
                'link' => 'https://electra.hussaria.pl/en/content/17-privacy-policy',
            ],
            'agree' => [
                'title' => 'I accept',
                'name' => 'statute',
                'link' => 'https://electra.hussaria.pl/pl/content/16-regulamin',
                'shop' => 'of online shop'
            ]
        ],
        'order_summary' => [
            'value' => 'Order value',
            'cost' => 'Delivery costs',
            'amount' => 'Total',
            'back' => 'Return',
            'notification' => 'The product has been added!',
            'confirm' => 'Thank you for ordering!',
        ]
    ],
    
    //===============
    'package' => [
        'active' => 1,
        'name' => 'Electric Tack Locker',
        'desc' => 'Electric tack locker - order a personalized trunk with an electric drive. The perfect solution for storing saddles and equestrian equipment. Equipped with large wheels and an electric drive, it ensures convenience, ease of maneuvering, and eliminates physical effort. An excellent choice for riders and equestrian enthusiasts.',
        'thumb' => 'he_thumb_elektryczna_paka.png',
        'status' => 'In stock',
        'deadline' => 'When the goods are not in stock, the delivery time is extended to 3-4 weeks',
        
        'show_price' => 1,
        'price' =>  8487,
        'promo' => '',
        'shipping' => [
            'active' => 1,
            'free' => 0,
            'price' => 'individual pricing',
            'promo' => '',
            'class' => 'dark',
            'content' => 'Electric Tack Locker',
            'span' => '',
            'info' => 'with individual delivery price',
            'source' => 'Only on electra.hussaria.pl',
        ],
        'include' => [
            'intro_products' => 'When you buy an electric tack locker, you receive it for free',
            'products' => [
                'battery' => [
                    'active' => 1,
                    'name' => 'Battery',
                    'desc' => 'Capacity: 4 Ah',
                    'image' => 'battery.png',
                ],
                'charger' => [
                    'active' => 1,
                    'name' => 'Charger',
                    'desc' => 'To battery',
                    'image' => 'charger.png',
                ],
                'pomp' => [
                    'active' => 1,
                    'name' => 'Electric pump',
                    'desc' => 'with powerbank function',
                    'image' => 'pomp.png',
                ],
                'pharmacy' => [
                    'active' => 1,
                    'name' => 'First aid kit',
                    'desc' => 'Medical kit',
                    'image' => 'pharmacy.png',
                ],
                'lock' => [
                    'active' => 1,
                    'name' => 'Padlock with code',
                    'desc' => 'Protective clasp',
                    'image' => 'lock.png',
                ],
                'powerbank' => [
                    'active' => 1,
                    'name' => 'PowerBank',
                    'desc' => 'With quick charge function',
                    'image' => 'powerbank.png',
                ],
                'cables' => [
                    'active' => 1,
                    'name' => 'Multi charging cables',
                    'desc' => '3in1 Phone Charging Cable Set',
                    'image' => 'cables.png',
                ],
                'gadgets' => [
                    'active' => 1,
                    'name' => 'Set of equestrian gadgets',
                    'desc' => 'A fabric shopping bag, a stylish baseball cap, tactical scissors with a screwdriver set, a thermos mug, a magnetic dry erase board with a set of accessories.',
                    'image' => 'gadgets.png',
                ],
            ],
            

        ]
    ],
    // ==============
    'extra' => 'Additionally in the offer',
    'shipping' => 'individual pricing',
    'shipping_info' => 'Delivery costs',
    'products' => [
        'promo_battery' => [
            'active' => 1,
            'name' => 'Battery',
            'desc' => '',
            'parameters' => [
                'name' => 'Capacity',
                'value' => '4 Ah',
            ],
            'status' => 'In stock',
            'delivery' => 'Immediate shipping possible',
            'image' => 'battery_4ah.png',
            'show_price' => 1,
            'price' => 225,
            'shipping' => [
                'active' => 0,
                'free' => 0,
                'price' => 0,
                'class' => '',
                'content' => 'Delivery from',
            ],
        ],
        'promo_charger' => [
            'active' => 1,
            'name' => 'Battery charger',
            'desc' => '',
            'parameters' => [
                'name' => 'Batteries included',
                'value' => 'No',
            ],
            'status' => 'In stock',
            'delivery' => 'Immediate shipping possible',
            'image' => 'charger_4_5ah.png',
            'show_price' => 1,
            'price' => 130,
            'shipping' => [
                'active' => 0,
                'free' => 0,
                'price' => 0,
                'class' => '',
                'content' => 'Delivery from',
            ],
        ],
        'promo_ramp' => [
            'active' => 1,
            'name' => 'Foldable loading ramp',
            'desc' => '',
            'parameters' => [
                'name' => 'Max load',
                'value' => '270 kg',
            ],
            'status' => 'In stock',
            'delivery' => 'Immediate shipping possible',
            'image' => 'ramp_270kg.png',
            'show_price' => 1,
            'price' => 340,
            'shipping' => [
                'active' => 0,
                'free' => 0,
                'price' => 0,
                'class' => '',
                'content' => 'Delivery from',
            ],
        ],
        'promo_battery5' => [
            'active' => 1,
            'name' => 'Battery',
            'desc' => '',
            'parameters' => [
                'name' => 'Capacity',
                'value' => '5 Ah',
            ],
            'status' => 'In stock',
            'delivery' => 'Immediate shipping possible',
            'image' => 'battery_4ah.png',
            'show_price' => 1,
            'price' => 245,
            'shipping' => [
                'active' => 0,
                'free' => 0,
                'price' => 0,
                'class' => '',
                'content' => 'Delivery from',
            ],
        ],
        'promo_cover' => [
            'active' => 1,
            'name' => 'Cover',
            'desc' => '',
            'colors' => [
                'name' => 'Color',
                'values' => [
                    'czarny' => '#111111',
                ],
            ],
            'colors' => [
                'name' => 'Color',
                'values' => [
                    'black' => [
                        'default' => 1,
                        'title' => 'Black',
                        'image' => 'tack_cover_black.png',
                        'color' => '#2d2a2e',
                    ]
                ],
            ],
            'parameters' => [
                'name' => '',
                'value' => '',
            ],
            'status' => 'In stock',
            'delivery' => 'Immediate shipping possible',
            'image' => 'tack_cover.png',
            'show_price' => 1,
            'price' => 465,
            'shipping' => [
                'active' => 0,
                'free' => 0,
                'price' => 0,
                'class' => '',
                'content' => 'Delivery from',
            ],
        ],
        'promo_cover_trap' => [
            'active' => 1,
            'name' => 'Gangway cover',
            'desc' => '',
            'colors' => [
                'name' => 'Color',
                'values' => [
                    'black' => [
                        'default' => 1,
                        'title' => 'Black',
                        'image' => 'ramp_cover_black.png',
                        'color' => '#2d2a2e',
                    ]
                ],
            ],
            'parameters' => [
                'name' => '',
                'value' => '',
            ],
            'status' => 'In stock',
            'delivery' => 'Immediate shipping possible',
            'image' => 'ramp_cover.png',
            'show_price' => 1,
            'price' => 115,
            'shipping' => [
                'active' => 0,
                'free' => 0,
                'price' => 0,
                'class' => '',
                'content' => 'Delivery from',
            ],
        ],
        'promo_cap' => [
            'active' => 1,
            'name' => 'Baseball cap',
            'desc' => 'easy circumference adjustment',
            'cta' => [
                'color' => '#f78da7',
                'icon' => '',
                'text' => 'Discover what\'s new in the collection',
                'anchor' => 'elite.hussaria.pl',
                'link' => 'https://elite.hussaria.pl/',
            ],
            'colors' => [
                'name' => 'Color',
                'values' => [
                    'black' => [
                        'default' => 1,
                        'title' => 'Black',
                        'image' => 'cap_black.png',
                        'color' => '#2d2a2e',
                    ],
                    'pink' => [
                        'default' => 0,
                        'title' => 'Pink',
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
            'price' => 25,
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
            'name' => 'Thermo cup',
            'desc' => 'with the ability to personalize',
            'parameters' => [
                'name' => 'Capacity',
                'value' => '1000 ml',
            ],
            'status' => 'W magazynie',
            'delivery' => 'Możliwa natychmiastowa wysyłka',
            'image' => 'thermo_cup.png',
            'show_price' => 1,
            'price' => 35,
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