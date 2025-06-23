<?php
session_start();

// Sprawdzenie tokena CSRF
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    http_response_code(403);

    if (!isset($_SESSION['security']['cart']['invalid_token'])) {
        $_SESSION['security']['cart']['invalid_token'] = 1;
    } else {
        $_SESSION['security']['cart']['invalid_token']++;
    }

    echo json_encode(['status' => 'error', 'message' => 'Nieautoryzowany dostęp.']);
    logError('Nieautoryzowany dostęp podczas zmiany Koloru Paki. Błędny token !', 'invalid_token');
    exit;
}

// =======================================
// Kolor Paki
// =======================================
// Odczytanie danych JSON z JS [cart.js]
$dataColor = json_decode(file_get_contents("php://input"), true);

if (isset($dataColor['colorName']) && isset($dataColor['colorPrice']) && isset($dataColor['colorCode'])) {

    // Sprawdź, czy 'color' istnieje, jeśli nie, zainicjalizuj pustą tablicę
    if (!isset($_SESSION['cart']['products']['paka']['color'])) {
        $_SESSION['cart']['products']['paka']['color'] = [];
    }
    
    // Aktualizuj tylko określone pola => pozostawić [label] z init.php => translate
    $_SESSION['cart']['products']['paka']['color']['name']  = $dataColor['colorName'];
    $_SESSION['cart']['products']['paka']['color']['price'] = $dataColor['colorPrice'];
    $_SESSION['cart']['products']['paka']['color']['ral']   = $dataColor['colorRal'];
    $_SESSION['cart']['products']['paka']['color']['hex']   = $dataColor['colorCode'];

        
    echo json_encode(['status' => 'success', 'message' => $_SESSION]);
} else {
    logError('Nieprawidłowe dane podczas zmiany Koloru Paki', 'invalid_color');
    echo json_encode(['status' => 'error', 'message' => 'Wystąpił błąd podczas zmieny koloru.']);
}



?>
