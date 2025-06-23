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
    logError('Nieautoryzowany dostęp podczas usuwania Paki z Koszyka. Błędny token !', 'invalid_token');
    exit;
}


$configuratorData = json_decode(file_get_contents('php://input'), true);

if ($configuratorData['action'] === 'removeFromCart' && $_SESSION['cart']['products']['paka']['addToCart']) {

    $_SESSION['cart']['products']['paka']['addToCart'] = false;
    
    echo json_encode([
        'success' => true
    ]);
    exit;
}

echo json_encode(['success' => false]);
