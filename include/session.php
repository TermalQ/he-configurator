<?php

// Ograniczenie do metody POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError("Nieprawidłowa metoda żądania.");
    exit;
}

// Sprawdzenie tokena CSRF
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
    logError("Nieautoryzowany dostęp - błędny token CSRF.");
    exit;
}

// Odczytanie danych JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    logError("Nieprawidłowe dane JSON.");
    exit;
}

// Lista dozwolonych kluczy
$allowedKeys = ['color', 'glitter', 'text', 'place', 'align', 'fontColor', 'fontFamily', 'fontSize'];

// Walidacja danych
$cleanConfig = [];
foreach ($data as $key => $value) {
    if (in_array($key, $allowedKeys)) {
        if ($key === 'color' || $key === 'fontColor') {
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                $cleanConfig[$key] = $value;
            } else {
                logError("Nieprawidłowy format koloru dla klucza: $key - wartość: $value");
            }
        } elseif ($key === 'glitter') {
            $cleanConfig[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        } elseif ($key === 'fontSize') {
            $size = filter_var($value, FILTER_VALIDATE_INT, ["options" => ["min_range" => 8, "max_range" => 72]]);
            if ($size !== false) {
                $cleanConfig[$key] = $size;
            } else {
                logError("Nieprawidłowy rozmiar czcionki: $value");
            }
        } else {
            $cleanConfig[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
}

// Jeśli nie ma poprawnych danych, logujemy błąd
if (empty($cleanConfig)) {
    logError("Brak poprawnych danych do zapisania w sesji.");
    exit;
}

// Zapisanie konfiguracji w sesji
$_SESSION['config'] = $cleanConfig;
?>
