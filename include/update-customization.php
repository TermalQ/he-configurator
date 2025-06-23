<?php
session_start();

// Sprawdzenie tokena CSRF
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Nieautoryzowany dostęp.']);
    logError('Nieautoryzowany dostęp podczas zapisu Koloru Paki. Błędny token !', 'invalid_token');
    exit;
}

// =======================================
// Personalizacja
// =======================================
// Odczytanie danych JSON z JS [cart.js]
$dataCustomization = json_decode(file_get_contents("php://input"), true);

if (isset($dataCustomization['Title'])) {
    // Sprawdź, czy personalizacja już istnieje w sesji
    if (!isset($_SESSION['cart']['products']['paka']['customization'])) {
        $_SESSION['cart']['products']['paka']['customization'] = [];
    }

    // Jeżeli nie ma tekstu, usuń personalizację z sesji
    if (!isset($dataCustomization['Text'])) {
        if (isset($_SESSION['cart']['products']['paka']['customization'][$dataCustomization['Title']])) {
            unset($_SESSION['cart']['products']['paka']['customization'][$dataCustomization['Title']]);
            
            // Sprawdź, czy tablica personalizacji jest pusta i usuń ją, jeśli tak
            if (empty($_SESSION['cart']['products']['paka']['customization'])) {
                unset($_SESSION['cart']['products']['paka']['customization']);
            }
        }
    } else {
        // Dodaj personalizację do sesji
        $_SESSION['cart']['products']['paka']['customization'][$dataCustomization['Title']] = [
            'title' => $dataCustomization['Title'],
            'text' => $dataCustomization['Text'] ?? false,
            'align' => $dataCustomization['Align'] ?? false,
            'color' => $dataCustomization['Color'] ?? null,
            'size' => $dataCustomization['Size'] ?? null,
            'format' => $dataCustomization['Format'] ?? null,
            'font' => $dataCustomization['Font'] ?? null,
            'price' => $dataCustomization['Price'] ?? null,
            'promo' => $dataCustomization['Promo'] ?? null,
        ];
    }

    // Pobierz wartość personalizacji z sesji
    $customizationValue = $_SESSION['cart']['products']['paka']['customization'][$dataCustomization['Title']] ?? '';

    // Wyślij odpowiedź w formacie JSON, zawierając tylko niezbędne dane
    echo json_encode(['status' => 'success', 'message' => $customizationValue]);
} else {
    logError('Nieprawidłowe dane podczas zapisu Personalizacji Paki', 'error');
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowe dane.']);
}




?>
