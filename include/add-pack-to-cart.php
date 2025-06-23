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
    logError('Nieautoryzowany dostęp podczas dodania Paki do Koszyka. Błędny token !', 'invalid_token');
    exit;
}


$configuratorData = json_decode(file_get_contents('php://input'), true);

if ($configuratorData['action'] === 'addToCart' && !$_SESSION['cart']['products']['paka']['addToCart']) {

    $_SESSION['cart']['products']['paka']['addToCart'] = true;
    

    $paka = $_SESSION['cart']['products']['paka'];

    echo json_encode([
        'success' => true,
        'paka' => $paka,
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Paka już została dodana do koszyka.']);
