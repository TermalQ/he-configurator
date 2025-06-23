<?php
// ==========================================
// Funkcja do wyświetlania Produktów w zestawie
// ==========================================
function displayIncludePack($items) {
    if ($items) {
        echo '<ul id="gadgets-list" class="gadgets row row-cols-3 row-cols-sm-4 list-unstyled my-1 my-md-3">';
        foreach ($items as $itemKey => $item) {
            if (!empty($item) && $item['active']) {
                echo '<li class="col-6 col-md-3 mb-4" data-id="'. $itemKey .'" data-name="'. $item['name'] .'">';
                
                  echo '<div class="gadget__image py-2 mb-2 text-center rounded">';
                    if (!empty($item['image'])) {
                        $imagePath = 'img/' . $item['image'];
                        $versionedImagePath = version($imagePath);
                        echo '<div class="thumbnail">';
                            echo '<img loading="lazy" decoding="async" fetchPriority="low" class="img-fluid" src="'. htmlspecialchars($versionedImagePath) .'" alt="' . $item['name'] .'">';
                        echo '</div>';
                    }
                  echo '</div>';

                  echo '<div class="gadget__name d-flex align-items-center justify-content-center gap-1">';
                    echo '<span>';
                        echo svg_code('checked.svg');
                    echo '</span>';
                    echo $item['name'];
                  echo '</div>';

                echo '</li>';
            }
        }
        echo '</ul>';
    }
}



// ==========================================
// Funkcja do wyświetlania Dodatkowych Produktów
// ==========================================
function displayProductCheckbox($items, $currency, $button) {
    echo '<div class="row">';
    foreach ($items as $itemKey => $item) {

        if (!empty($item) && $item['active']) {

            echo '<div class="col-12 col-md-4">';

                echo '<div class="featured m-2 p-2 p-md-4">';

                // Call to Action => if
                if (!empty($item['cta'])) {
                    echo '<div class="cta d-flex gap-2 gap-md-3 justify-content-start align-items-center">';
                    if (!empty($item['cta']['icon'])) {
                        echo '<div style="background:' . $item['cta']['color'] . '" class="icon"><a href="' . $item['cta']['link'] . '"><img src="img/' . $item['cta']['icon'] .' " alt=""></a></div>';
                    }
                            echo '<div class="content">' . $item['cta']['text'] .' <a href="' . $item['cta']['link'] . '">' . $item['cta']['anchor'] .'</a></div>';
                    echo '</div>';
                }
                
                // IMAGE => Color image
                if (!empty($item['colors']['values'])) {
                    // Znajdź domyślny kolor
                    $defaultColor = null;
                    foreach ($item['colors']['values'] as $key => $value) {
                        if ($value['default'] == 1) {
                            $defaultColor = $key;
                            break;
                        }
                    }
                    
                    // Wyświetl domyślne zdjęcie
                    if ($defaultColor) {
                        $imagePath = 'img/' . $item['colors']['values'][$defaultColor]['image'];
                        $versionedImagePath = version($imagePath);
                        
                        echo '<div class="image d-flex justify-content-center align-items-center text-center my-md-3">';
                        echo '<img id="color-image-' . $itemKey . '" loading="lazy" decoding="async" fetchPriority="low" class="img-fluid" src="' . htmlspecialchars($versionedImagePath) . '" alt="' . htmlspecialchars($item['name']) . '">';
                        echo '</div>';
                    }
                } elseif (!empty($item['image'])) {
                    // Pozostałe przypadki bez kolorów
                    $imagePath = 'img/' . $item['image'];
                    $versionedImagePath = version($imagePath);

                    echo '<div class="image d-flex justify-content-center align-items-center text-center my-md-3">';
                    echo '<img loading="lazy" decoding="async" fetchPriority="low" class="img-fluid" src="' . htmlspecialchars($versionedImagePath) . '" alt="' . htmlspecialchars($item['name']) . '">';
                    echo '</div>';
                }

                // Nazwa
                echo '<h6 class="text-center">'. $item['name'].'</h6>';

                // Opis
                if ($item['desc']) {
                    echo '<p class="text-center">'. $item['desc'].'</p>';
                }

                // Kolory
                if (!empty($item['colors']['values'])) {
                    // Znajdź domyślny kolor
                    $defaultColor = null;
                    foreach ($item['colors']['values'] as $key => $value) {
                        if ($value['default'] == 1) {
                            $defaultColor = $key;
                            break;
                        }
                    }
                    
                    echo '<div class="parameters d-flex justify-content-between align-items-center py-2 my-2">';
                    echo '<div class="name">'. $item['colors']['name'] .':</div>';
                    echo '<div class="values d-flex gap-2 justify-content-end align-items-center">';
                    foreach ($item['colors']['values'] as $key => $value) {
                        $imagePath = 'img/' . $value['image'];
                        $versionedImagePath = version($imagePath);
                        
                        // Dodaj klasę "checked" dla domyślnego koloru
                        $checkedClass = ($key == $defaultColor) ? 'checked' : '';
                        
                        echo '<span class="' . $checkedClass . '" data-color="' . $value['title'] . '" data-item-key="' . $itemKey . '" data-image="' . htmlspecialchars($value['image']) . '" data-image-src="' . htmlspecialchars($versionedImagePath) . '" style="background:'. $value['color'] .'" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $value['title'] .'"></span>';
                    }
                    echo '</div>';
                    echo '</div>';
                }


                // Pozostałe parametry
                if (!empty($item['parameters']['value'])) {
                    echo '<div class="parameters d-flex justify-content-between align-items-center py-2 my-2">';
                        echo '<span class="text-center">'. htmlspecialchars($item['parameters']['name']).':</span>';
                        echo '<span class="text-center">'. htmlspecialchars($item['parameters']['value']).'</span>';
                    echo '</div>';
                }

                // Cena
                echo '<div class="d-flex justify-content-between align-items-center">';
                    if ($item['show_price']) {
                        
                            echo '<div id="'. $itemKey .'" class="col-4 col-md-5 product-price">';
                                echo '<div class="block-price text-center p-2">';
                                    echo '<span>'. formatPriceWithSup($item['price']) .'</span> ';
                                    echo '<span>'. htmlspecialchars($currency) .'</span>';
                                echo '</div>';
                            echo '</div>';

                            if ($item['shipping']['active']) {
                            echo '<div id="'. $itemKey .'" class="col-8 col-md-7">';
                                echo '<div class="block-shipping text-end p-2">';
                                    echo '<span>'. htmlspecialchars($item['shipping']['content']) .' '. formatPriceWithSup($item['shipping']['price']) .'</span>';
                                echo '</div>';
                            echo '</div>';
                            }
                        
                    }

                    // Add to Cart Button
                    echo '<div class="d-flex justify-content-start align-items-center gap-4">';
                    if ($item['active']) {
                        // Znajdź domyślny kolor
                        $defaultColor = null;
                        if (!empty($item['colors']['values'])) {
                            foreach ($item['colors']['values'] as $key => $value) {
                                if ($value['default'] == 1) {
                                    $defaultColor = $value['title'];
                                    break;
                                }
                            }
                        }

                        echo '<button type="button" 
                        class="btn btn-warning addToCart"
                        data-button-action="add-to-cart"
                        data-key="' . htmlspecialchars($itemKey) . '"
                        data-img="'. htmlspecialchars($item['image']) .'"
                        data-name="'. htmlspecialchars($item['name']) .'"
                        data-color="'. ($defaultColor ?? '') .'"
                        data-price="'. $item['price'] .'"
                        data-shipping="'. $item['shipping']['price'] .'"
                        data-shipping-content="'. $item['shipping']['content'] .'">';
                        echo svg_code('cart.svg');
                        echo '</button>';
                    } else {
                        echo '<button type="button" class="btn btn-outline-light disabled cart">';
                        echo svg_code('cart.svg');
                        echo '</button>';
                    }
                    echo '</div>';
            
                echo '</div>';

            echo '</div>';
            echo '</div>';

        }
    }
    echo '</div>';
}
?>