<?php
// errors, warning, info => LOGS
function logError($message, $logFile = 'errors') {
    $logDir = __DIR__ . '/../logs';
    $logFile = $logDir .'/'. $logFile .'.log';
    $htaccessFile = $logDir . '/.htaccess'; // Ścieżka do pliku .htaccess

        // Jeśli folder nie istnieje, utwórz go
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
    
            // Jeśli plik .htaccess nie istnieje, utwórz go
            if (!file_exists($htaccessFile)) {
                $htaccessContent = <<<HTACCESS
                    # Apache 2.2
                    <Files "*.log">
                        Order deny,allow
                        Deny from all
                    </Files>
                    
                    # Apache 2.4
                    <Files "*.log">
                        Require all denied
                    </Files>
                    HTACCESS;
                file_put_contents($htaccessFile, $htaccessContent);
            }
        }

    // Format loga: [Data] [IP] Treść błędu
    $logMessage = "[" . date("Y-m-d H:i:s") . "] [" . $_SESSION['ip'] . "] [" . $_SESSION['country'] . "] [" . $_SESSION['city'] . "] " . $message . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Załadowany plik
function safeInclude($filePath) {
    if (file_exists($filePath)) {
        return include $filePath;
    } else {
        logError("Plik nie istnieje: $filePath", '404');
        return null;
    }
}


// =========================================
// Funkcja do wczytywania i dekodowania JSON
// =========================================
function loadJsonFile($file, $default = [], $path = 'json/') {
    $full_path_json = $path . $file;
    if (file_exists($full_path_json)) {
        try {
            $json_data = file_get_contents($full_path_json);
            $data = json_decode($json_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Błąd dekodowania pliku $full_path_json: " . json_last_error_msg());
            }
            return $data;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $default;
        }
    } else {
        return $default;
    }
}

// =========================================
// Funkcja do wczytywania czy są w JSON active
// =========================================
function isAnyJSONpositionActive($positions) {
    foreach ($positions as $panel) {
        foreach ($panel as $position) {
            if (isset($position['active']) && $position['active'] == 1) {
                return true;
            }
        }
    }
    return false;
}

// =========================================
// Funkcja do znalezienia domyślnego koloru paki
// =========================================
function findDefaultColor($data) {
    foreach ($data as $details) {
        foreach ($details['items'] as $item) {
            if (isset($item['default']) && $item['default'] == 1) {
                return $item;
            }
        }
    }
    return false;
}

// ======================================
// Funkcja do aktualizacji plików CSS/JPG
// ======================================
function version($file) {
    if (file_exists($file)) {
        return $file . '?v=5.' . filemtime($file);
    } else {
        logError("Plik nie istnieje: $file", '404');
        return null;
    }
}

// ==============================
// Funkcja do pobrania SVG
// ==============================
function svg_code($svg_file, $path = 'svg/') {
    $full_path = $path . $svg_file;
    if (file_exists($full_path)) {
        echo file_get_contents($full_path);
    } else {
        logError("Plik SVG nie istnieje: $svg_file", '404');
    }
  }

// =============================
// Funkcja do formatowania ceny
// =============================
function formatPrice($price) {
    return number_format($price, 2, ',', ' '); // Formatowanie ceny z dwoma miejscami po przecinku i spacją jako separator tysięcy
}
function formatPriceWithSup($price) {
    $parts = explode('.', number_format($price, 2, '.', ''));
    if (count($parts) === 2) {
        return $parts[0] . ',<sup>' . $parts[1] . '</sup>';
    }
    return $parts[0] . ',<sup>00</sup>';
}

// =============================
// Funkcja do aktualizowania SVG pliku => dodanie <text id=preView_panel> Grawer, Flag
// na podstawie JSON [text position]
// =============================
function updateSVGcodeWithTEXTitems($svgFile, $textJsonFile, $grawerJsonFile, $flagJsonFile) {

    if (!file_exists($textJsonFile)) {
        logError("Plik nie istnieje: $textJsonFile | Nie można ustalić odpowiednich pozycji dla TEXT na SVG", '404');
        return;
    }

    if (!file_exists($grawerJsonFile)) {
        logError("Plik nie istnieje: $grawerJsonFile | Nie można ustalić odpowiednich pozycji dla GRAWER ZONE i TEXT na SVG", '404');
        return;
    }

    if (!file_exists($flagJsonFile)) {
        logError("Plik nie istnieje: $flagJsonFile | Nie można ustalić odpowiednich pozycji dla FLAG ZONE na SVG", '404');
        return;
    }

    if (!file_exists($svgFile)) {
        logError("Plik SVG nie istnieje: $svgFile | Nie można dodać bloków tekstowych, grawerów i flag", '404');
        return;
    }

    // Sprawdzenie dat modyfikacji
    $textJsonModified = filemtime($textJsonFile);
    $grawerJsonModified = filemtime($grawerJsonFile);
    $flagJsonModified = filemtime($flagJsonFile);
    $svgModified = filemtime($svgFile);

    // Jeśli którykolwiek JSON jest nowszy od SVG, aktualizujemy
    if ($textJsonModified > $svgModified || $grawerJsonModified > $svgModified || $flagJsonModified > $svgModified) {
        $textJsonData = json_decode(file_get_contents($textJsonFile), true) ?? [];
        $grawerJsonData = json_decode(file_get_contents($grawerJsonFile), true) ?? [];
        $flagJsonData = json_decode(file_get_contents($flagJsonFile), true) ?? [];

        $svgContent = file_get_contents($svgFile);
        if ($svgContent === false) {
            logError("Błąd wczytywania pliku SVG: $svgFile", 'critical');
            return;
        }

        // Usuwanie starych elementów
        $svgContent = preg_replace([
            '/<text id="previewText_[^"]*?".*?<\/text>\s*/si',
            '/<rect id="preview_[^"]*?"[^>]*?><\/rect>\s*/si', // Usuwa elementy <rect> z id zaczynającym się od "preview_"
            '/<rect id="preview_[^"]*?"[^>]*?\/>\s*/si' // Usuwa samozamykające się elementy <rect> z id zaczynającym się od "preview_"
        ], '', $svgContent);



        $newElements = "";

        // ✅ Dodawanie <rect> i <text> dla GRAWER `grawer-positions.json`
        foreach ($grawerJsonData as $grawerName => $grawerBlocks) {
            foreach ($grawerBlocks as $block) {
                if (!isset($block['align']) || !is_array($block['align'])) continue;
        
                $startColor = $block['start_color'] ?? '#000000'; // Domyślny kolor tekstu
                $rectId = 'preview_' . $grawerName;
        
                // Get the first active alignment or the first one if none are active
                $align = null;
                foreach ($block['align'] as $alignKey => $alignData) {
                    if (isset($alignData['active']) && $alignData['active'] == 1) {
                        $align = $alignData;
                        break;
                    }
                }
                if (!$align) {
                    $align = reset($block['align']);
                }
        
                $x = $align['x'] ?? 0;
                $y = $align['y'] ?? 0;
                $width = $align['width'] ?? 18;
                $height = $align['height'] ?? 3.5;
                $transform = $align['transform'] ?? '';
                $fill_color = $align['fill'] ?? $startColor; // Fill rect bierze z JSON lub start_color
        
                // Dodanie <rect>
                $newElements .= sprintf(
                    "\t<rect id=\"%s\" x=\"%s\" y=\"%s\" width=\"%s\" height=\"%s\" fill=\"%s\" transform=\"%s\" style=\"visibility: hidden;\" />\n",
                    htmlspecialchars($rectId, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($x, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($y, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($width, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($height, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($fill_color, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($transform, ENT_QUOTES, 'UTF-8')
                );
        
                // Sprawdzenie, czy w JSON jest info o tekście
                if (isset($align['text'])) {
                    $textId = 'previewText_' . $grawerName;
                    $textX = $align['text']['x'] ?? 0;
                    $textY = $align['text']['y'] ?? 0;
                    $textAnchor = $align['text']['anchor'] ?? 'start';
                    $textTransform = $align['text']['transform'] ?? '';
        
                    // Dodanie <text> z kolorem z start_color
                    $newElements .= sprintf(
                        "\t<text id=\"%s\" x=\"%s\" y=\"%s\" font-size=\"12\" fill=\"%s\" text-anchor=\"%s\" transform=\"%s\"></text>\n",
                        htmlspecialchars($textId, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($textX, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($textY, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($startColor, ENT_QUOTES, 'UTF-8'), // Kolor tekstu = start_color
                        htmlspecialchars($textAnchor, ENT_QUOTES, 'UTF-8'), // Anchor
                        htmlspecialchars($textTransform, ENT_QUOTES, 'UTF-8')
                    );
                }
            }
        }
        

        
        
        // ✅ Dodawanie <text> dla NAPISÓW `text-positions.json`
        foreach ($textJsonData as $blockName => $blockData) {
            $textId = 'previewText_' . $blockName;

            if (!isset($blockData[0]['align'])) continue;

            $align = $blockData[0]['align']['left'] ?? reset($blockData[0]['align']);
            $x = $align['x'] ?? 0;
            $y = $align['y'] ?? 0;
            $anchor = $align['anchor'] ?? 'start';
            $transform = $align['transform'] ?? '';
            $fill_color = $blockData[0]['start_color'] ?? "#FFFFFF";

            $newElements .= sprintf(
                "\t<text id=\"%s\" x=\"%s\" y=\"%s\" font-size=\"25\" fill=\"%s\" text-anchor=\"%s\" transform=\"%s\"></text>\n",
                htmlspecialchars($textId, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($x, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($y, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($fill_color, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($anchor, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($transform, ENT_QUOTES, 'UTF-8')
            );
        }

        
        

        // ✅ Dodawanie <rect> dla `flag_position.json`
        foreach ($flagJsonData as $flagName => $flagBlocks) {
            foreach ($flagBlocks as $block) {
                if (!isset($block['align']) || !is_array($block['align'])) continue;
        
                foreach ($block['align'] as $alignKey => $align) {
                    $rectId = 'preview_' . $flagName . '_' . $alignKey;
                    $x = $align['x'] ?? 0;
                    $y = $align['y'] ?? 0;
                    $width = $align['width'] ?? 30;
                    $height = $align['height'] ?? 20;

                    $transform = $align['transform'] ?? '';
                    $fill_color = $block['start_color'] ?? '#FFFFFF';
        
                    $newElements .= sprintf(
                        "\t<rect id=\"%s\" x=\"%s\" y=\"%s\" width=\"%s\" height=\"%s\" fill=\"%s\" stroke=\"#FFFFFF\" stroke-width=\"0.15\" transform=\"%s\" style=\"visibility: hidden;\" />\n",
                        htmlspecialchars($rectId, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($x, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($y, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($width, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($height, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($fill_color, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($transform, ENT_QUOTES, 'UTF-8')
                    );
                }
            }
        }
        
        // Dodanie nowych elementów do SVG
        $svgContent = str_replace('</svg>', $newElements . '</svg>', $svgContent);
        file_put_contents($svgFile, $svgContent);
    }

    echo file_get_contents($svgFile);
}


?>