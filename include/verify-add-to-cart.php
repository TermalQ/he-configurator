<?php
session_start();

if (!isset($_SESSION['lang'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nie wybrano języka.']);
    exit;
}

$lang = $_SESSION['lang'];

// Pobierz dane produktu z żądania POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['key']) || !isset($data['image']) || !isset($data['name']) || !isset($data['price']) || !isset($data['shipping'])) {
    echo json_encode(['status' => 'error', 'message' => 'Niepoprawne dane wejściowe.']);
    exit;
}

$itemKey = $data['key'];
$itemImage = $data['image'];
$itemName = $data['name'];
$itemPrice = $data['price'];
$itemShipping = $data['shipping'];

// Załaduj dane produktów z pliku językowego
$productsFile = '../lang/' . $lang . '.php';
if (!file_exists($productsFile)) {
    echo json_encode(['status' => 'error', 'message' => 'Plik językowy nie istnieje.']);
    exit;
}

$products = include($productsFile);

// Sprawdź, czy dane produktu są poprawne
if (isset($products['products'][$itemKey])) {
    $verifiedItem = $products['products'][$itemKey];

    if ($verifiedItem['name'] === $itemName && 
        $verifiedItem['price'] === $itemPrice && 
        $verifiedItem['shipping']['price'] === $itemShipping) {
        
        // Sprawdź, czy obrazek pasuje do produktu lub któregoś z kolorów
        if ($verifiedItem['image'] === $itemImage) {
            // Obrazek pasuje do produktu
            $verifiedItem['key'] = $itemKey;
            echo json_encode(['status' => 'success', 'item' => $verifiedItem]);
        } elseif (isset($verifiedItem['colors']) && !empty($verifiedItem['colors']['values'])) {
            // Sprawdź, czy obrazek pasuje do któregoś z kolorów
            $colorsImages = array_column($verifiedItem['colors']['values'], 'image');
            if (in_array($itemImage, $colorsImages)) {
                // Obrazek pasuje do któregoś z kolorów
                $verifiedItem['image'] = $itemImage; // Zmień obrazek w $verifiedItem
                $verifiedItem['key'] = $itemKey;
                echo json_encode(['status' => 'success', 'item' => $verifiedItem]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Obrazek nie pasuje do produktu.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Obrazek nie pasuje do produktu.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Niepoprawne dane produktu.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Produkt nie istnieje.']);
}

?>
