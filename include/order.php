<?php


session_start();

// =======================================
// SPRAWDZENIE PUSTEGO KOSZYKA ===========
// =======================================
$hasMainProduct = isset($_SESSION['cart']['products']['paka']['addToCart']) && $_SESSION['cart']['products']['paka']['addToCart'] === true;
$hasExtraProducts = false;
if (isset($_SESSION['cart']['products']) && is_array($_SESSION['cart']['products'])) {
    foreach ($_SESSION['cart']['products'] as $key => $product) {
        if ($key !== 'paka') {
            $hasExtraProducts = true;
            break; // Znaleziono dodatkowy produkt
        }
    }
}

if (!$hasMainProduct && !$hasExtraProducts) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Koszyk jest pusty. Nie można złożyć zamówienia.']);
    logError('Próba wysłania pustego koszyka.', 'empty_cart');
    exit;
}
// ========================================
// KONIEC SPRAWDZENIA PUSTEGO KOSZYKA =====
// ========================================

// Szablon mailingu
$templatePath = '../mail/order.html';

// Załaduj funkcje
require_once 'functions.php';


// Sprawdzenie tokena CSRF
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    
    if (!isset($_SESSION['security']['form']['invalid_token'])) {
        $_SESSION['security']['form']['invalid_token'] = 1;
    } else {
        $_SESSION['security']['form']['invalid_token']++;
    }

    echo json_encode(['status' => 'error', 'message' => 'Nieautoryzowany dostęp.']);
    logError('Nieautoryzowany dostęp podczas próby wysłania Formularza. Błędny token !', 'invalid_token');
    exit;
}


// Załaduj PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;


// Załaduj konfiguracje mailingów i klucz szyfrujący
$email_config = require '../config/mailer.php'; // ustawienia SMTP
$admin_config = require '../config/admin.php'; // ustawienia admina i secret_key

// Sprawdź SESSION Settings
if ($_SESSION['cart']['settings']) {
    $lang = $_SESSION['cart']['settings']['lang'];
    $currency = $_SESSION['cart']['settings']['currency'];

    // Pobranie pliku tłumaczeń
    $lang_file = "../mail/lang/$lang.php";

    // Sprawdź, czy plik tłumaczeń istnieje
    if (file_exists($lang_file)) {
        $translate = safeInclude($lang_file);
    } else {
        logError("Brak pliku tłumaczeń w mail: $lang_file . Zatrzymana akcja wysyłki formularza!", '404');
        exit;
    }
} else {
    // Jeśli lang lub currency nie są ustawione w sesji, zatrzymaj dalsze działanie
    logError('Brak _SESSION[Cart] => Lang i Currency, aby wysyłać e-mail [lub ponowna próba wysłania tego samego maila, gdy _SESSION[Cart] został już zresetowany]. Zatrzymana akcja wysyłki formularza!');
    exit;
}

// Pobierz dane z inputów Formularza i wyświetl translate errors przy walidacji
// wyświetl translate errors przy walidacji
// timer => czas blokady ponownego wysłania
// attempts => próby wysłania przed blokadą
$formData = processFormUserData($translate['notifications'], $admin_config['admin']['timer'], $admin_config['admin']['attempts']);

// PHPMailer
$mail = new PHPMailer(true);

if ($formData) {
    
    // Pobierz dane z SESSION o Koszyku
    $productsCartData = prepareCartSessionData($currency);
    
    if ($productsCartData) {
    
        // Prepare SCALONE dane (Form & Session)
        $orderData = [
            'name'      => $formData['name'],
            'lastname'  => $formData['lastname'],
            'email'     => $formData['email'],
            'phone'     => $formData['phone'],

            'main_product'      => $productsCartData['mainProduct'],
            'extra_products'    => $productsCartData['extraProducts'],
            'total'             => $productsCartData['total'],
            'delivery'          => $productsCartData['delivery_total'],
            
            'date'      => date('Y-m-d'),
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        // logError(print_r($orderData, true), 'danne');

        // Proceed with order processing and email sending
        if ($admin_config['admin']['active'] == 1) {
            
            // Zamień w Szablonie Dane i Tłumaczenia
            $replacedMailTemplate = changeEmailTemplateToReciveData($templatePath, $orderData, $translate, $currency, $admin_config);

            if ($replacedMailTemplate) {      
                
                // Send emails
                $sendToAdmin    = sendEmail($mail, $email_config, $translate['subject']['admin'], $replacedMailTemplate, $admin_config['admin']['mail']);
                $sendToClient   = sendEmail($mail, $email_config, $translate['subject']['client'], $replacedMailTemplate, $formData['email']);
            
                // Determine the response based on the email sending results
                if ($sendToAdmin && $sendToClient) {
                    
                    // Usuń dane z SESSION
                    unset($_SESSION['cart']);

                    $response = ['status' => 'success', 'message' => $translate['notifications']['success']['send']];

                } else {

                    if (!$sendToClient) {
                        logError('Nie udało się wysłać KOPII maila do Kienta', 'form_send');
                    }
                    if (!$sendToAdmin) {
                        logError('Nie udało się wysłać KOPII maila do Admina', 'form_send');
                    }
                    $response = ['status' => 'error', 'message' => $translate['notifications']['errors']['send']];
                }
            }
            
        } else {
            // Email sending wyłaczone
            logError('Wysyłanie zamówienia drogą mailową jest wyłączone w ustawieniach [admin_config]!', 'mailing_config');
            $response = ['status' => 'error', 'message' => $translate['notifications']['errors']['disabled']];
        }

        // Serializacja i szyfrowanie Danych przez zapisem do pliku
        $orderJson = json_encode($orderData, JSON_PRETTY_PRINT);
        $encryptedOrder = encryptData($orderJson, $admin_config['admin']['secret_key']);

        // Zapis do pliku zamówień
        if (file_put_contents($admin_config['admin']['save_to_file'], $encryptedOrder . "\n", FILE_APPEND) === false) {
            logError('Nie udało się zapisać zamówienia do pliku:' . $admin_config['admin']['save_to_file'], 'write_order');
        }

    // Brak zapisanych produktów w Koszyku/Konfiguracji
    } else {
        $response = ['status' => 'error', 'message' => $translate['notifications']['errors']['empty']];
    }
    
} else {
    // Brak wymaganych danych o Kliencie w formularzu
    logError('Nieprawidłowe lub puste dane z inputów formularza. Brak formUserData !', 'form_dane');
    $response = ['status' => 'error', 'message' => $translate['notifications']['errors']['validate']];
}

// Return JSON response
echo json_encode($response);


// =============================================
// FUNKCJE pomocnicze ==========================
// =============================================

// Walidacja pól formularza + timer czasowy przez ponownym wysłaniem formularza + ilość prób wysłania formularza
function processFormUserData($translate, $timer = 120 , $try = 10) {

    // Dane wejściowe z formularza
    $name       = htmlspecialchars($_POST['name']);
    $lastname   = htmlspecialchars($_POST['lastname']);
    $email      = htmlspecialchars($_POST['email']);
    $phone      = htmlspecialchars($_POST['phone']);

    $agree      = isset($_POST['agree']) ? htmlspecialchars($_POST['agree']) : '';
    $accept     = isset($_POST['accept']) ? htmlspecialchars($_POST['accept']) : '';
    // 
    $bot        = htmlspecialchars($_POST['human']);

    // Pobranie danych isUser i isForm
    $isUser     = isset($_POST['isUser']) ? (int) $_POST['isUser'] : false;
    $isVisit    = isset($_POST['isVisit']) ? htmlspecialchars($_POST['isVisit']) : '';
    $isForm     = isset($_POST['isForm']) ? htmlspecialchars($_POST['isForm']) : false;

    // Bot checking
    if (trim($bot) != '') {
        logError('BOT detected. Wysłanie formularza zignorowano.', 'bot_detected');
        echo json_encode(['status' => 'error', 'message' => 'Bot detected']);
        exit;
    }

    // Walidacja inputów
    $errors = [];

    if (empty($name)) {
        $errors['name'] = $translate['errors']['fields']['empty']['name'];
        logError('Pusty [name]. Wysłanie formularza wstrzymano', 'name_incorrect');
    }

    if (empty($lastname)) {
        $errors['lastname'] = $translate['errors']['fields']['empty']['lastname'];
        logError('Pusty [lastname]. Wysłanie formularza wstrzymano', 'lastname_incorrect');
    }

    if (empty($email)) {
        $errors['email'] = $translate['errors']['fields']['empty']['email'];
        logError('Pusty [email]. Wysłanie formularza wstrzymano', 'email_incorrect');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = $translate['errors']['fields']['incorect']['email'];
        logError('Niepoprawny format [e-mail]. Wysłanie formularza wstrzymano', 'email_incorrect');
    }

    if (empty($phone)) {
        $errors['phone'] = $translate['errors']['fields']['empty']['phone'];
        logError('Pusty [phone]. Wysłanie formularza wstrzymano', 'phone_incorrect');
    } elseif (!preg_match('/^\+?[0-9\s\-()]{9,15}$/', $phone)) {
        $errors['phone'] = $translate['errors']['fields']['incorect']['phone'];
        logError('Niepoprawny format [phone]. Wysłanie formularza wstrzymano', 'phone_incorrect');
    }

    if (empty($agree)) {
        $errors['agree'] = $translate['errors']['fields']['empty']['agree'];
        logError('Pusty [agree]. Wysłanie formularza wstrzymano', 'agree_incorrect');
    }

    if (empty($accept)) {
        $errors['accept'] = $translate['errors']['fields']['empty']['accept'];
        logError('Pusty [accept]. Wysłanie formularza wstrzymano', 'accept_incorrect');
    }

    if ($_SESSION['security']['form']['invalid_token'] > $try) {
        logError('Zbyt wiele nieudanych prób z błędnym tokenem CSRF.', 'invalid_token');
        http_response_code(429); // Too Many Requests
        echo json_encode(['status' => 'error', 'message' => 'Zbyt wiele nieudanych prób. Spróbuj ponownie później.']);
        exit;
    }

    // Walidacja isForm był już wysłany => TIMER => LocalStorage
    if ($isForm) {
        try {
            $lastFormSentTime = new DateTime($isForm, new DateTimeZone('UTC'));
            $currentDateTime = new DateTime('now', new DateTimeZone('UTC'));

            $intervalInSeconds = $currentDateTime->getTimestamp() - $lastFormSentTime->getTimestamp();

            if ($intervalInSeconds < $timer) {
                $errors['timer'] = $translate['errors']['fields']['incorect']['timer'];
                logError("Zbyt szybka ponowna próba wysłania [{$intervalInSeconds} s < z {$timer} s]. Wysłanie formularza wstrzymano.", 'user_RE_submit');
            }

            // Zapisz próbę w SESSION
            if (!isset($_SESSION['security']['form']['try'])) {
                $_SESSION['security']['form']['try'] = 1;
            } else {
                $_SESSION['security']['form']['try']++;
            }

        } catch (Exception $e) {
            logError('Błąd parsowania daty isForm: ' . $e->getMessage(), 'date_parse_error');
            $errors['timer'] = $translate['errors']['fields']['incorect']['timer'];
        }
    }

    // Jeśli są błędy, zwróć je
    if (!empty($errors)) {
        logError($translate['errors']['validate'], 'form_incorrect');
        echo json_encode(['status' => 'error', 'message' => $translate['errors']['validate'], 'errors' => $errors]);
        exit;
    }

    // Wszystko jest OK, zwróć dane
    return [
        'name'      => $name,
        'lastname'  => $lastname,
        'email'     => $email,
        'phone'     => $phone,
    ];
}

// Pobieranie zapisanych w SESSION danych
function prepareCartSessionData($currency) {
    // Initialize zmiennych
    $mainProduct = [];
    $extraProducts = [];
    $total = 0;
    $delivery_total = 0;

    
    // Sprawdź SESSION Cart => Products
    if (isset($_SESSION['cart']['products']) && !empty($_SESSION['cart']['products'])) {
        // Iterate over products in the session

        foreach ($_SESSION['cart']['products'] as $key => $product) {

            // ====== 11.04.2025 =========================================
            // Jeśli to produkt ('paka'), sprawdź [addToCart] => true/false
            if ($key === 'paka' && (!isset($product['addToCart']) || $product['addToCart'] !== true)) {
                continue; // pomiń jeśli addToCart nie jest true
            }
            // ============================================================

            // 1. Dodaj Pakę do listy zamówień
            if ($key === 'paka') {
                // PAKA z personalizacją
                $mainProduct = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'promo' => $product['promo'] ?? false,
                    'currency' => $currency,
                    'color' => null,
                    'customization' => [],
                    'flag' => null,
                ];
                $total += $product['price'];
            
                // Dodaj Kolor paki
                if (isset($product['color']) && !empty($product['color'])) {
                    $mainProduct['color'] = [
                        'name' => $product['color']['name'],
                        'hex' => $product['color']['hex'],
                        'ral' => $product['color']['ral'],
                        'price' => $product['color']['price'] ?? 0,
                        'promo' => $product['color']['promo'] ?? false,
                        'currency' => $currency,
                    ];
                    $total += $product['color']['price'] ?? 0;
                }
            
                // Dodaj personalizacje => if exists
                if (isset($product['customization']) && !empty($product['customization'])) {
                    foreach ($product['customization'] as $customization) {
                        $mainProduct['customization'][] = [
                            'title' => $customization['title'] ?? '',
                            'text' => $customization['text'] ?? false,
                            'align' => $customization['align'] ?? false,
                            'font' => $customization['font'] ?? false,
                            'color' => $customization['color'] ?? false,
                            'size' => $customization['size'] ?? false,
                            'format' => $customization['format'] ?? false,
                            'price' => $customization['price'] ?? 0,
                            'promo' => $customization['promo'] ?? false,
                            'currency' => $currency,
                        ];
                        $total += $customization['price'] ?? 0;
                    }
                }
            
                // Add flag details if exists
                if (isset($product['flag']) && !empty($product['flag'])) {
                    $mainProduct['flag'] = [
                        'title'     => $product['flag']['title'],
                        'country'   => $product['flag']['country'],
                        'price'     => $product['flag']['price'] ?? 0,
                        'currency'  => $currency,
                    ];
                    $total += $product['flag']['price'] ?? 0;
                }
            
                // Add delivery cost to total
                if (isset($product['shipping']['price'])) {
                    $delivery_total += $product['shipping']['price'];
                }

            } else {
                // 2. Pozostałe produkty z Quantity
                $quantity = $product['quantity'] ?? 1;
                $extraProducts[] = [
                    'name'      => $product['name'],
                    'color'     => $product['color'],
                    'price'     => $product['price'],
                    'quantity'  => $quantity,
                    'total'     => $product['price'] * $quantity,
                    'currency'  => $currency,
                ];
                $total += $product['price'] * $quantity;
            
                // Add delivery cost to total
                if (isset($product['shipping']['price'])) {
                    $delivery_total += $product['shipping']['price'];
                }
            }

        }
    } else {
        // If there are no default products in the Cart, stop further action
        logError('Brak domyślnych produktów w Koszyku w _SESSION Cart Products do wysyłki e-mail. Zatrzymana akcja wysyłki formularza!');
        exit;
    }

    // Return the prepared data
    return [
        'mainProduct' => $mainProduct,
        'extraProducts' => $extraProducts,
        'total' => $total,
        'delivery_total' => $delivery_total,
    ];
}

// Zamień w szablonie mailingu Dane z formularza i session
function changeEmailTemplateToReciveData($templatePath, $orderData, $translate, $currency, $adminConfig) {
    // Sprawdzenie, czy plik szablonu istnieje
    if (!file_exists($templatePath)) {
        logError('Nie znaleziono szablonu email w ' . $templatePath, '404');
        return false;
    }
    
    // Wczytanie zawartości szablonu
    $template = file_get_contents($templatePath);
    
    // Mapa placeholderów do tłumaczeń
    $placeholders = [
        '[lang:content_title]'                          => $translate['content']['title'],
        '[lang:content_header_hello]'                   => $translate['content']['header']['hello'],
        '[lang:content_header_intro]'                   => $translate['content']['header']['intro'],
        '[lang:content_summary_table_header_title]'     => $translate['content']['summary']['table']['header']['title'],
        '[lang:content_summary_table_header_date]'      => $translate['content']['summary']['table']['header']['date'],
        '[lang:content_summary_table_rows_total]'       => $translate['content']['summary']['table']['rows']['total'],
        '[lang:content_summary_table_rows_name]'        => $translate['content']['summary']['table']['rows']['name'],
        '[lang:content_summary_table_rows_lastname]'    => $translate['content']['summary']['table']['rows']['lastname'],
        '[lang:content_summary_table_rows_email]'       => $translate['content']['summary']['table']['rows']['email'],
        '[lang:content_summary_table_rows_phone]'       => $translate['content']['summary']['table']['rows']['phone'],
        '[lang:content_summary_table_rows_order]'       => $translate['content']['summary']['table']['rows']['order'],
        '[lang:content_summary_table_rows_exta]'        => $translate['content']['summary']['table']['rows']['extra'],
        '[lang:content_helper_head]'                    => $translate['content']['helper']['head'],
        '[lang:content_helper_content]'                 => $translate['content']['helper']['content'],
        '[lang:content_helper_info]'                    => $translate['content']['helper']['info'],
        '[lang:footer_link]'                            => $translate['footer']['link'],
        '[lang:footer_copyright]'                       => $translate['footer']['copyright'],
        '[lang:currency]'                               => $currency,
        '[lang:content_header_shop]'                    => $translate['content']['header']['shop'],
        // ===== CONFIG OPTIONS ====
        '[[admin_office_phone]]'                        => $adminConfig['admin']['office_phone'],
        '[[admin_office_email]]'                        => $adminConfig['admin']['office_email']
    ];
    
    // Zamiana zmiennych zamówienia w szablonie
    foreach ($orderData as $key => $value) {
        if ($key === 'main_product') {
            $mainProductHtml = '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family: Montserrat, Arial, sans-serif; font-size: 12px; line-height: 20px; color: #000;">';
        
            // Produkt główny
            $mainProductHtml .= '<tr>';
            $mainProductHtml .= '<td align="left" colspan="2" style="padding: 10px; background-color: #f2f2f2; font-weight: bold;">' 
                . htmlspecialchars($value['name']) . '</td>';
            $mainProductHtml .= '<td align="right" colspan="2" style="padding: 10px; background-color: #f2f2f2;">' 
                . htmlspecialchars($value['price']) . ' ' . htmlspecialchars($value['currency']) . '</td>';
            $mainProductHtml .= '</tr>';

        
            // Kolor
            if (!empty($value['color'])) {
                $color = $value['color'];
                $mainProductHtml .= '<tr style="background-color: ' . htmlspecialchars($color['hex']) . '; color: #FFFFFF">
                    <td style="padding: 8px;">' . htmlspecialchars($color['name']) . '</td>
                    <td style="padding: 8px;">' . htmlspecialchars($color['hex']) . '</td>
                    <td style="padding: 8px;">' . htmlspecialchars($color['ral']) . '</td>
                    <td align="right" style="padding: 8px;">' . htmlspecialchars($color['price']) . ' ' . htmlspecialchars($color['currency']) . '</td>
                </tr>';
            }
        
            // Personalizacja
            if (!empty($value['customization'])) {
                foreach ($value['customization'] as $customization) {
                    // Nagłówek personalizacji
                    $mainProductHtml .= '<tr><td colspan="4" style="padding: 10px; background-color: #f2f2f2; font-weight: bold; font-size: 12px;">' . htmlspecialchars($customization['title']) . '</td></tr>';
            
                    // Tekst personalizacji w ramce
                    $mainProductHtml .= '<tr>';
                    $mainProductHtml .= '<td colspan="4" style="padding: 10px; background-color: #ffffff;">';
                    $mainProductHtml .= '<div style="padding: 8px; border: 1px solid #CCCCCC; font-size: 12px;"><strong>' . htmlspecialchars($customization['text']) . '</strong></div>';
                    $mainProductHtml .= '</td>';
                    $mainProductHtml .= '</tr>';
            
                    // Szczegóły: align, font, size, kolor, cena
                    $mainProductHtml .= '<tr>';
                    $mainProductHtml .= '<td style="padding: 8px; font-size: 10;">' . htmlspecialchars($customization['font']) . '</td>';
                    $mainProductHtml .= '<td style="padding: 8px; font-size: 10px;">' . htmlspecialchars($customization['size']) . '</td>';
                    $mainProductHtml .= '<td style="padding: 8px; font-size: 10px;">' . htmlspecialchars($customization['color']) . '</td>';
                    $mainProductHtml .= '<td align="right" style="padding: 10px; font-size: 12px;">' . htmlspecialchars($customization['price']) . ' ' . htmlspecialchars($customization['currency']) . '</td>';
                    $mainProductHtml .= '</tr>';
                }
            }
            
            
        
            // Flaga
            if (!empty($value['flag'])) {
                $flag = $value['flag'];
                $mainProductHtml .= '<tr>';
                $mainProductHtml .= '<td colspan="5" style="padding: 10px; background-color: #f2f2f2; font-weight: bold;">' . htmlspecialchars($flag['title']) . '</td>';
                $mainProductHtml .= '</tr>';
                $mainProductHtml .= '<tr>';
                $mainProductHtml .= '<td colspan="2" style="padding: 8px;">' . htmlspecialchars($flag['country']) . '</td>';
                $mainProductHtml .= '<td align="right" colspan="2" style="padding: 10px; font-size: 12px;">' . htmlspecialchars($flag['price']) . ' ' . htmlspecialchars($flag['currency']) . '</td>';
                $mainProductHtml .= '</tr>';
            }
        
            $mainProductHtml .= '</table>';
        
            // Zamiana w szablonie
            $template = str_replace('{{_main_product_}}', $mainProductHtml, $template);
        
        } elseif ($key === 'extra_products') {

            $extraProductsHtml = '<table border="0" cellpadding="4" cellspacing="0" style="font-size: 12px; width: 100%;">';

            foreach ($value as $extraProduct) {
                $extraProductsHtml .= '<tr>';

                // Nazwa + kolor
                $extraProductsHtml .= '<td style="padding: 4px 8px;">' 
                    . htmlspecialchars($extraProduct['name']) . ' ' . htmlspecialchars($extraProduct['color']) 
                    . '</td>';

                // Cena jednostkowa
                $extraProductsHtml .= '<td align="center" style="padding: 4px 8px;font-size: 9px;">' 
                    . htmlspecialchars($extraProduct['price']) 
                    . '</td>';

                // Ilość
                $extraProductsHtml .= '<td align="center" style="padding: 4px 8px;font-size: 9px;">' 
                    . htmlspecialchars($extraProduct['quantity']) 
                    . '</td>';

                // Suma
                $extraProductsHtml .= '<td align="right" style="padding: 4px 8px;">' 
                    . htmlspecialchars($extraProduct['total']) . ' ' . htmlspecialchars($extraProduct['currency']) 
                    . '</td>';

                $extraProductsHtml .= '</tr>';
            }

            $extraProductsHtml .= '</table>';


            $template = str_replace('{{_extra_products_}}', $extraProductsHtml, $template);
        
        } else {
            $template = str_replace('{{_' . $key . '_}}', htmlspecialchars($value), $template);
        }
        
    }
    
    // Zamiana placeholderów na tłumaczenia
    $replacedTemplate = strtr($template, $placeholders);
    
    return $replacedTemplate;
}

// Wysyłanie maila SMPT
function sendEmail($mail, $smtpConfig, $subject, $template, $recipientEmail) {
    try {
        // SMTP Configuration
        if (!$mail->isSMTP()) {
            $mail->isSMTP();
            $mail->Host = $smtpConfig['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtpConfig['smtp']['username'];
            $mail->Password = $smtpConfig['smtp']['password'];
            $mail->SMTPSecure = $smtpConfig['smtp']['encryption'];
            $mail->Port = $smtpConfig['smtp']['port'];
        }

        // Send email
        $mail->setFrom($smtpConfig['smtp']['from_email'], $smtpConfig['smtp']['from_name']);
        $mail->addAddress($recipientEmail);
        $mail->Subject = $subject;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = "base64";
        $mail->isHTML(true);
        $mail->Body = $template;
        $mail->send();
        return true;
    } catch (Exception $e) {
        logError('Błąd przy wysyłaniu e-maili: ' . $e->getMessage(), 'error');
        logError('Nie udało się wysłać wiadomości e-mail. Błąd: ' . $mail->ErrorInfo, 'error_send');
        return false;
    }
}

// Funkcja szyfrowania
function encryptData($data, $key) {
    $iv = random_bytes(openssl_cipher_iv_length('AES-128-CBC')); // Generowanie losowego IV
    $encrypted = openssl_encrypt($data, 'AES-128-CBC', $key, 0, $iv); // Szyfrowanie danych
    return base64_encode($iv . $encrypted); // Dodanie IV do zaszyfrowanych danych
}


