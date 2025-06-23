<?php
session_start();

// Sprawdzenie tokena CSRF
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Nieautoryzowany dostęp.']);
    logError('Nieautoryzowany dostęp podczas aktualizacji Dodatkowych produktów. Błędny token !', 'error');
    exit;
}

// =======================================
// Dodatkowe Produkty w Koszyku
// =======================================
// Odczytanie danych JSON z JS [cart.js]
$dataProducts = json_decode(file_get_contents("php://input"), true);

$action = $dataProducts['action'];
$itemKey = $dataProducts['key'];

if ($action === 'add') {
    $item = [
        'name' => $dataProducts['name'],
        'color' => $dataProducts['color'] ?? false,
        'price' => $dataProducts['price'],
        'quantity' => $dataProducts['quantity'],
        'image' => $dataProducts['image'],
        'shipping' => $dataProducts['shipping'],
        'shippingContent' => $dataProducts['shippingContent']
    ];

    if (!isset($_SESSION['cart']['products'][$itemKey])) {
        $_SESSION['cart']['products'][$itemKey] = $item;
    } else {
        $_SESSION['cart']['products'][$itemKey]['quantity'] += 1;
    }

    echo json_encode(['status' => 'success']);

} elseif ($action === 'remove') {

    if (isset($_SESSION['cart']['products'][$itemKey])) {
        unset($_SESSION['cart']['products'][$itemKey]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found in session']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

?>
