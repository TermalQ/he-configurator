<?php
session_start();

// Sprawdzenie tokena CSRF
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Nieautoryzowany dostęp.']);
    logError('Nieautoryzowany dostęp podczas zapisu Flagi. Błędny token !', 'error');
    exit;
}

// =======================================
// Flaga
// =======================================
// Odczytanie danych JSON z JS [cart.js]
$dataFlag = json_decode(file_get_contents("php://input"), true);

// Jeśli brakuje danych, usuń flagę i zakończ
if (!$dataFlag['Checked'] || empty($dataFlag['Country'])) {
    unset($_SESSION['cart']['products']['paka']['flag']);
    echo json_encode(['status' => 'success', 'message' => $_SESSION]);
    exit;
}

// Dodaj lub aktualizuj flagę w sesji
$_SESSION['cart']['products']['paka']['flag'] = [
    'title' => $dataFlag['Title'],
    'country' => $dataFlag['Country'],
    'price' => $dataFlag['Price'] ?? null,
    'promo' => $dataFlag['Promo'] ?? null,
];

// Odpowiedź JSON
echo json_encode(['status' => 'success', 'message' => $_SESSION]);




?>
