<?php
// Załaduj konfigurację admina (zawiera secret_key)
$admin_config = require '../config/admin.php';


// Funkcja deszyfrowania
function decryptData($data, $key) {
    $data = base64_decode($data); // Dekodowanie z Base64
    $ivLength = openssl_cipher_iv_length('AES-128-CBC'); // Długość IV
    $iv = substr($data, 0, $ivLength); // Pobierz IV z początku danych
    $encrypted = substr($data, $ivLength); // Pobierz zaszyfrowane dane
    return openssl_decrypt($encrypted, 'AES-128-CBC', $key, 0, $iv); // Odszyfrowanie danych
}

// Wczytaj plik zamówień
if (!file_exists($admin_config['admin']['save_to_file'])) {
    die("Plik zamówień nie istnieje.");
}

$orders = file($admin_config['admin']['save_to_file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (!$orders) {
    die("Brak zamówień do wyświetlenia.");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki - Hussaria Electra</title>
    <link rel="icon" href="../img/hussaria-electra-100x100.png" sizes="32x32">
</head>
<body>
<style type="text/css">
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        color: #000;
        background:#e1e1e1;
    }
    section {
        margin: 20px auto;
        padding: 20px;
        width: 80%;
        background: #fff;
        border-radius: 10px;
    }
    ul {
        margin: 0;
        padding: 0;
        list-style-position: inside;
    }
    li {
        padding: 5px;
        color: #707070;
    }
    .price {
        color: #000;
        padding: 5px;
        background: #f3f3f3;
        border-radius: 5px;
    }
    .prepayment {
        font-size: 1.2rem;
        color: #b11800;
    }
    .total {
        font-weight: bold;
        font-size: 1.5rem;
    }
    h4 {
        margin: .5rem 0;
        padding: .5rem;
        background: #d7def5;
    }
    p {
        padding: 5px;
    }
    .wait,
    .done {
        color: #000000;
        padding: 2px 6px;
        border-radius: 2em;
    }
    .wait {
        background: #f2b017;
    }
    .done {
        background: #b3e9a3;
    }
    .custom {
        position: relative;
        display: inline-block;
        padding: 10px;
        background: #f7f7f7;
        border: 1px dashed #bbbbbb;
    }
    .custom::before {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        left: -5px;
        border-radius: 3px;
        background: #b11800;
    }
    .custom strong {
        color: #d3d3d3;
    }
</style>


<?php

foreach ($orders as $index => $encryptedOrder) {
    // Odszyfruj dane
    $decryptedOrder = decryptData($encryptedOrder, $admin_config['admin']['secret_key']);
    
    if ($decryptedOrder === false) {
        echo "<p><strong>Zamówienie $index:</strong> Nie udało się odszyfrować danych.</p>";
        continue;
    }

    // Dekodowanie JSON
    $orderData = json_decode($decryptedOrder, true);

    if ($orderData === null) {
        echo "<p><strong>Zamówienie $index:</strong> Nie udało się zdekodować JSON.</p>";
        continue;
    }

    // Wyświetlenie zamówienia
    echo "<section>";
    echo "<h4>ID {$index}: <small style='font-size:15px; color:#007683; font-weight:400'>[{$orderData['timestamp']}]</small></h4>";

    echo "<p><strong>Imię/Nazwa firmy:</strong> {$orderData['name']}</p>";
    echo "<p><strong>Nazwisko/NIP:</strong> {$orderData['lastname']}</p>";
    echo "<p><strong>E-mail:</strong> {$orderData['email']}</p>";
    echo "<p><strong>Telefon:</strong> {$orderData['phone']}</p>";

    echo "<p><strong>Zamówienie:</strong></p>";
    $main = $orderData['main_product'];
    echo "<ul>";
    echo "<li><strong>Produkt:</strong> {$main['name']} - {$main['price']} {$main['currency']}</li>";

    // Kolor
    if (!empty($main['color'])) {
        $color = $main['color'];
        echo "<li><strong>Kolor:</strong> {$color['name']} ({$color['hex']} / {$color['ral']}) + {$color['price']} {$color['currency']}</li>";
    }

    // Personalizacja
    if (!empty($main['customization'])) {
        foreach ($main['customization'] as $custom) {
            echo "<li><strong>{$custom['title']}:</strong> {$custom['text']} | {$custom['align']}, {$custom['font']}, {$custom['size']}, {$custom['color']} | +{$custom['price']} {$custom['currency']}</li>";
        }
    }

    // Flaga
    if (!empty($main['flag'])) {
        $flag = $main['flag'];
        echo "<li><strong>{$flag['title']}:</strong> {$flag['country']} + {$flag['price']} {$flag['currency']}</li>";
    }
    echo "</ul>";

    echo "<p><strong>Dodatkowo:</strong></p>";
    echo "<ul>";
    foreach ($orderData['extra_products'] as $extra) {
        $name = htmlspecialchars($extra['name']);
        $color = !empty($extra['color']) ? ' (' . htmlspecialchars($extra['color']) . ')' : '';
        $price = htmlspecialchars($extra['price']);
        $quantity = htmlspecialchars($extra['quantity']);
        $total = htmlspecialchars($extra['total']);
        $currency = htmlspecialchars($extra['currency']);

        echo "<li>{$name}{$color} — {$quantity} szt. x {$price} {$currency} = {$total} {$currency}</li>";
    }
    echo "</ul>";

    echo "<p><strong>Łączna wartość zamówienia:</strong> <span class='total'>{$orderData['total']} zł</span></p>";
    echo "<p><strong>Dostawa:</strong> {$orderData['delivery']} zł</p>";

    echo "<hr>";
    echo "<p><small>Data zamówienia: {$orderData['date']}</small></p>";
    echo "</section>";

    
}
?>


</body>
</html>