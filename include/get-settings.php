<?php
session_start();

// // Sprawdzenie tokena CSRF
// if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
//     http_response_code(403);

//     if (!isset($_SESSION['security']['cart']['invalid_token'])) {
//         $_SESSION['security']['cart']['invalid_token'] = 1;
//     } else {
//         $_SESSION['security']['cart']['invalid_token']++;
//     }

//     echo json_encode(['status' => 'error', 'message' => 'Nieautoryzowany dostęp.']);
//     logError('Nieautoryzowany dostęp podczas pobrania Waluty. Błędny token !', 'invalid_token');
//     exit;
// }

// ===================
$admin_config = require '../config/admin.php'; // ustawienia

// // Blokada timera SESSION
// if (
//     !isset($_SESSION['csrf_token']) ||
//     !isset($_SESSION['csrf_token_created']) ||
//     (time() - $_SESSION['csrf_token_created'] > $admin_config['admin']['token_age'])
// ) {
//     unset($_SESSION['csrf_token']);
//     unset($_SESSION['csrf_token_created']);
//     http_response_code(419); // Authentication Timeout

//     logError('Token wygasł ! Wejścia na stronę => pobranie ustawień sesion[cart][settings] w funkctions.js => updateCartSummary()', 'invalid_token');
//     echo json_encode(['status' => 'error', 'message' => 'Token wygasł. Odśwież stronę.']);
//     exit;
// }

// =======================================
// SESSION => Settings => [Waluta, Delivery Cost]
// =======================================

// Odpowiedź JSON
echo json_encode($_SESSION['cart']['settings']);


?>
