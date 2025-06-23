<?php
session_start();

require_once 'include/functions.php';

$cid = uniqid();

// Pobierz adres IP klienta
$clientIP = $_SERVER['REMOTE_ADDR'];
$accessKey = 'fe78233cc21df3'; // Klucz API z ipinfo.io

// Pobieranie informacji o lokalizacji IP
$response = @file_get_contents("http://ipinfo.io/{$clientIP}?token={$accessKey}");

if ($response === FALSE) {
    // W przypadku błędu pobierania danych (np. z braku połączenia z ipinfo.io)
    $country = '';
    $city = '';
} else {
    // Dekodowanie odpowiedzi
    $details = json_decode($response, true);

    // Sprawdzenie, czy odpowiedź zawiera dane
    if (isset($details['country']) && isset($details['city'])) {
        $country = $details['country'];
        $city = $details['city'];
    } else {
        // Jeżeli brak danych o kraju lub mieście
        $country = '';
        $city = '';
    }
}

$_SESSION['ip'] = $clientIP;
$_SESSION['country'] = $country;
$_SESSION['city'] = $city;
$_SESSION['flagUrl'] = "https://flagcdn.com/16x12/" . strtolower($country) . ".png";

// Generowanie tokena CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_created'] = time();
}



// =========================================
// =========== ZAŁADUJ DANE ================
// =========================================

// Ustawienie języka na podstawie GET lub sesji
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
} else {
    // Domyślny język to polski, jeśli nie ustawiono
    $_SESSION['lang'] = $_SESSION['lang'] ?? 'pl'; 
}

$lang = $_SESSION['lang'];

// Pobranie pliku tłumaczeń
$lang_file = "lang/$lang.php";
$translate = safeInclude($lang_file);

// Pobranie konfiguracji języków => SWITCH
$config_lang_file = 'config/languages.php';
$config_lang = safeInclude($config_lang_file);
$languages = $config_lang['languages'] ?? [];

// Pobranie konfiguracji administratora
$config_file = 'config/admin.php';
$config = safeInclude($config_file);



// =========================================
// ========== JSON Dane ====================
// =========================================

// Step-by-Step Tour [przewodnik] => OFF
$stepTours = loadJsonFile('step-tours.json');

// Wesje plików do Download
$downloads = loadJsonFile('downloads.json');
// Kolory PAKI
$PACKcolors = loadJsonFile('colors.json');
// Kolory tekstu
$font_colors = loadJsonFile('font-colors.json');
// Grawery => położenie i wyrównanie 
$grawer_positions = loadJsonFile('grawer-positions.json');
// Tekst => położenie i wyrównanie 
$text_positions = loadJsonFile('text-positions.json');
// Rozmiar tekstu
$sizes = loadJsonFile('font-sizes.json');
// Formatowanie tekstu
$styles = loadJsonFile('font-styles.json');
// Czcionki
$fonts = loadJsonFile('fonts.json');
// =========================================



// =========================================
// Początkowy Koszyk w SESSION
// =========================================
// Domyślny kolor Paki
$defaultColor = findDefaultColor($PACKcolors) ?? [];

$_SESSION['cart'] = [
    'settings' => [
        'lang' => $lang,
        'currency' => $translate['settings']['currency'],
        // Dodaj info o kosztach dostaw Paki i Produktów
        'delivery' => [
            'pack' => $translate['settings']['delivery']['pack'],
            'products' => $translate['settings']['delivery']['products'],
        ],
    ],
    'products' => [
        'paka' => [
            'addToCart' => false,
            'name' => $translate['package']['name'],
            'price' => $translate['package']['price'],
            'promo' => $translate['package']['promo'],
            'thumb' => $translate['package']['thumb'],
            'delivery' => [
                'price' => $translate['settings']['delivery']['pack']['value'],
                'info' => $translate['settings']['delivery']['pack']['help'],
                'free' => $translate['settings']['delivery']['pack']['free']
            ],
            'color' => !empty($defaultColor) ? [
                'label' => $translate['form']['colors']['title'],
                'name' => $defaultColor['name'][$lang],
                'price' => $defaultColor['price'][$lang],
                'promo' => $defaultColor['promo'][$lang],
                'ral' => $defaultColor['ral'],
                'hex' => $defaultColor['color']
            ] : null
        ]
    ]
];

?>