<?php
require_once 'include/init.php';
require_once 'include/products.php';
?>
<!DOCTYPE html>
<html lang="pl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translate['title'] ?></title>
    <link rel="icon" href="img/hussaria-electra-100x100.png" sizes="32x32">
    <meta name="description" content="<?php echo $translate['package']['desc'] ?>">
    <meta name="keywords" content="hussaria electra, elektryczna paka, paka z napędem elektrycznym">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <meta property="og:title" content="<?php echo $translate['title'] ?>">
    <meta property="og:description" content="<?php echo $translate['package']['desc'] ?>">
    <meta property="og:image" content="https://electra.hussaria.pl/configurator/img/elektryczna_paka.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="500">
    <meta property="og:image:alt" content="><?php echo $translate['title'] ?>">
    <meta property="og:url" content="https://electra.hussaria.pl/configurator/">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Fonts USED -->
    <link rel="stylesheet" href="https://use.typekit.net/vfx4swx.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo version('css/style.css') ?>">
</head>
<body>
    <header id="header" class="container-fluid fixed-top py-1">
        <div class="row">
            <!-- LOGO -->
            <div class="col-12 order-sm-1 col-md-3 order-md-1 d-flex align-items-center justify-content-between justify-content-md-start gap-1">
                <div class="logo-wrapper">
                    <a href="./"><img id="logo" class="logo" src="<?php echo version('img/hussaria_electra_logo_black.png') ?>" alt="Hussaria Electra"></a>
                </div>
                <div class="info px-2">
                    <div class="contact d-flex align-items-center justify-content-start gap-1">
                        <?php 
                            echo svg_code('phone.svg');
                            echo "<span>" . $config['admin']['office_phone'] ."</span>";
                        ?>
                    </div>
                </div>
            </div>
            <!-- end -->

            <!-- Delivery -->
            <div class="col-12 order-sm-3 col-md-6 order-md-2 d-flex align-items-center justify-content-start delivery position-relative my-1 ps-md-4 py-3">
                <div class="d-flex align-items-center justify-content-start gap-3">
                    <div class="delivery__content align-items-center justify-content-between gap-2">
                        <div class="delivery__content__headline"><?php echo $translate['package']['shipping']['content'] ?> <span><?php echo $translate['package']['shipping']['span'] ?></span></div>
                        <div class="delivery__content__subheadline text-center px-2"><?php echo $translate['package']['shipping']['info'] ?></div>
                    </div>
                </div>
            </div>
            <!-- end -->

            <!-- Navigation -->
            <div class="col-12 order-sm-2 col-md-3 order-md-3 d-flex align-items-center justify-content-around justify-content-md-end gap-3">

                <!-- Cart -->
                <div class="cart_button">
                    <button type="button" id="cart" class="btn btn-warning position-relative step" data-step="4" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" title="<?php echo $translate['form']['buttons']['view'] ?>">
                        <span>
                            <?php echo svg_code('cart.svg')?>
                            <sup id="button_cart_total" class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger"></sup>
                        </span>
                    </button>
                </div>
                <!-- end -->

                <!-- Languages -->
                 <nav>
                    <ul class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                        <?php foreach ($languages as $code => $langData): ?>
                            <li class="nav-item<?= ($lang == $code) ? ' active' : '' ?>">
                                <?php if ($lang != $code): ?>
                                    <a class="nav-link" href="?lang=<?= $code ?>">
                                <?php endif; ?>
                                    <img src="img/<?= $langData['img'] ?>" alt="<?= $langData['name'] ?>"  data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= $langData['name'] ?>">
                                <?php if ($lang != $code): ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        <li class="mx-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Zmień motyw">
                                
                            </li>
                    </ul>
                 </nav>
                
                <button id="toggleTheme" type="button" class="btn p-0 border-0 bg-transparent" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="<?php echo $translate['settings']['theme'] ?>">
                    <img src="<?php echo version('img/brightness.png') ?>" alt="<?php echo $translate['settings']['theme'] ?>">
                </button>
                <!-- end -->
            </div>
            <!-- end -->            
        </div>
    </header>

    <main id="main" class="container-fluid">
        <div class="row configurator position-relative">
            <!-- MODEL -->
            <div class="col-12 configurator__model">
                <div id="paka" class="configurator__model__view text-center">
                    <?php updateSVGcodeWithTEXTitems(__DIR__ . '/svg/paka.svg', __DIR__ . '/json/text-positions.json', __DIR__ . '/json/grawer-positions.json', __DIR__ . '/json/flag-positions.json'); ?>
                </div>
            </div>

            <!-- CONFIGURATOR -->
            <div class="col-6 col-md-4 col-lg-3 configurator__panel position-absolute z-1 top-0 start-0">
                <div class="configurator__panel__options p-1 p-md-3 my-1 my-md-3 step" data-step="1">
                    <!-- COLORS PACK -->
                    <section class="pack-colors d-flex align-items-center justify-content-between">
                        <div class="options_label">
                            <div class="d-flex align-items-center justify-content-start gap-3">
                                <div class="icons">
                                    <?php echo svg_code('palette.svg') ?>
                                </div>
                                <div class="option">
                                    <h4 class="fs-6"><?php echo $translate['form']['colors']['fields']['label'] ?>:</h4>
                                    <span class="help"><?php echo $translate['form']['colors']['fields']['help'] ?></span>
                                </div>
                            </div>
                        </div>

                        <?php
                            $defaultColor = null;
                            // Znajdź domyślny kolor z wszystkich kategorii
                            foreach ($PACKcolors as $categoryKey => $category) {
                                if (!empty($category['items'])) {
                                    foreach ($category['items'] as $color) {
                                        if (!empty($color['default']) && !empty($color['active'])) {
                                            $defaultColor = $color;
                                            break 2; // Wyjdź z obu pętli
                                        }
                                    }
                                }
                            }
                        ?>

                        <div class="dropdown">
                            <button class="btn btn-colors d-flex align-items-center dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <span id="selectedColorPack" class="color-box me-1"
                                    <?php if ($defaultColor): ?>
                                        style="background-color: <?= htmlspecialchars($defaultColor['color'], ENT_QUOTES, 'UTF-8'); ?>;"
                                    <?php endif; ?>
                                ></span>
                            </button>

                            <ul class="colors dropdown-menu" id="packColor" data-option-type="select">
                                <?php
                                foreach ($PACKcolors as $categoryKey => $category) {
                                    if (!empty($category['items'])) {
                                        echo '<li class="dropdown-header">' . htmlspecialchars($category['category'][$lang], ENT_QUOTES, 'UTF-8') . '</li>';
                                        echo '<li>';
                                        echo '<div class="d-flex flex-wrap gap-2 px-3">';
                                        foreach ($category['items'] as $color) {
                                            $defaultAttr = isset($color['default']) && $color['default'] ? 1 : 0;
                                            if($color['active']) {
                                                echo '<div style="background-color: ' . htmlspecialchars($color['color'], ENT_QUOTES, 'UTF-8') . ';" class="picker">';
                                                echo '<a href="#" class="dropdown-item color-txt"
                                                        data-category="' . htmlspecialchars($categoryKey, ENT_QUOTES, 'UTF-8') . '"
                                                        data-default="' . $defaultAttr . '"
                                                        data-color="' . htmlspecialchars($color['color'], ENT_QUOTES, 'UTF-8') . '"
                                                        data-name="' . htmlspecialchars($color['name'][$lang], ENT_QUOTES, 'UTF-8') . '"
                                                        data-color-ral="' . htmlspecialchars($color['ral'], ENT_QUOTES, 'UTF-8') . '"
                                                        data-price="' . htmlspecialchars($color['price'][$lang], ENT_QUOTES, 'UTF-8') . '"
                                                        data-promo="' . htmlspecialchars($color['promo'][$lang], ENT_QUOTES, 'UTF-8') . '"
                                                        title="' . htmlspecialchars($color['name'][$lang], ENT_QUOTES, 'UTF-8') . '"></a>';
                                                echo '</div>';
                                            }
                                        }
                                        echo '</div>';
                                        echo '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </section>
                    <!-- end -->
                </div>

                <div class="configurator__panel__options p-1 p-md-3 my-1 my-md-3 step" data-step="2">
                    <!-- TEXT CUSTOMIZATION -->
                        <section class="customization">
                            <div class="options_label">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-1">
                                    <div class="d-flex align-items-center justify-content-start gap-3">
                                        <div class="icons">
                                            <?php echo svg_code('person.svg') ?>
                                        </div>
                                        <div class="option">
                                            <h4 class="fs-6"><?php echo $translate['form']['marker']['fields']['label'] ?>:</h4>
                                        </div>
                                    </div>
                                </div>       
                            </div>


                            <!-- Text Personalization -->
                            <?php if ($text_positions && isAnyJSONpositionActive($text_positions)): ?>
                                <div class="accordion my-2" id="textAccordion">
                                <?php foreach ($text_positions as $key => $value): ?>
                                    <?php if ($value[0]['active'] == 1): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading_<?php echo $key; ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?php echo $key; ?>" aria-expanded="false" aria-controls="collapse_<?php echo $key; ?>">
                                                    <?php echo htmlspecialchars($value[0]['title'][$lang]); ?>
                                                </button>
                                            </h2>
                                            <div id="collapse_<?php echo $key; ?>" class="accordion-collapse collapse" data-bs-parent="#textAccordion">
                                                <div class="accordion-body">
                                                    <!-- Text Input -->
                                                    <div class="form-group my-2 settings">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <?php foreach ($value as $item) {
                                                                    echo svg_code($item['icon']);
                                                                } ?>
                                                            </span>
                                                            <input type="text" 
                                                                class="form-control"
                                                                id="text_<?php echo $key; ?>" 
                                                                data-option-type="text"
                                                                data-name="<?php echo $translate['form']['marker']['options']['texts']['title'] . ': ' . htmlspecialchars($value[0]['title'][$lang]); ?>"
                                                                data-price="<?php echo htmlspecialchars($translate['form']['marker']['options']['texts']['price']['value']); ?>"
                                                                data-promo="<?php echo htmlspecialchars($translate['form']['marker']['options']['texts']['price']['promo']); ?>"
                                                                placeholder="<?php echo htmlspecialchars($translate['form']['marker']['fields']['holder']); ?>..." 
                                                                maxlength="20">
                                                        </div>
                                                    </div>

                                                    <!-- Text Color -->
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between settings py-1">
                                                        <div class="fs-6"><?php echo $translate['form']['marker']['tools']['font-color']; ?>:</div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-colors d-flex align-items-center dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <span id="selectedColorText_<?php echo $key; ?>" class="color-box me-2" style="background-color:<?php echo htmlspecialchars($value[0]['start_color']); ?>"></span>
                                                            </button>
                                                            <ul class="dropdown-menu colors" id="textColor_<?php echo $key; ?>">
                                                                <?php if ($value[0]['default_colors'] == 1): ?>
                                                                    <?php foreach ($font_colors as $category): ?>
                                                                        <li class="dropdown-header"><?php echo htmlspecialchars($category['category'][$lang]); ?></li>
                                                                        <li>
                                                                            <div class="d-flex flex-wrap gap-2 px-3">
                                                                                <?php foreach ($category['colors'] as $color): ?>
                                                                                    <div style="background-color: <?php echo htmlspecialchars($color); ?>;" class="picker">
                                                                                        <a class="dropdown-item color-txt" data-color="<?php echo htmlspecialchars($color); ?>" href="#" title="<?php echo htmlspecialchars($color); ?>"></a>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <!-- Text Align -->
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between settings py-1">
                                                        <div class="fs-6"><?php echo $translate['form']['marker']['tools']['font-align']; ?>:</div>
                                                        <ul class="d-flex tools justify-content-start align-items-center gap-1" id="textAlign_<?php echo $key; ?>">
                                                            <?php foreach ($value as $item): ?>
                                                                <?php foreach ($item['align'] as $alignName => $alignData): ?>
                                                                    <?php if ($alignData['active'] == 1): ?>
                                                                    <li><button type="button"
                                                                        class="tools <?= $alignData['current'] ? 'active' : '' ?>"
                                                                        id="buttonText_<?php echo $key . '_' . $alignName; ?>"
                                                                        data-x="<?php echo htmlspecialchars($alignData['x']); ?>"
                                                                        data-y="<?php echo htmlspecialchars($alignData['y']); ?>"
                                                                        data-anchor="<?php echo htmlspecialchars($alignData['anchor']); ?>"
                                                                        data-transform="<?php echo htmlspecialchars($alignData['transform']); ?>"
                                                                        <?php if ($alignData['current']): ?>
                                                                            data-default="true"
                                                                        <?php endif; ?>
                                                                        data-align-name="<?php echo $translate['form']['marker']['tools']['font-align']; ?>: <?php echo htmlspecialchars($alignData['name'][$lang]); ?>"
                                                                        title="<?php echo htmlspecialchars($alignData['name'][$lang]); ?>">
                                                                        <?php echo svg_code($alignData['svg']); ?>
                                                                    </button></li>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>

                                                    <!-- Text Size -->
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between settings py-1">
                                                        <div class="fs-6"><?php echo $translate['form']['marker']['tools']['font-size']; ?>:</div>
                                                        <ul class="d-flex tools justify-content-start align-items-center gap-1" id="textSize_<?php echo $key; ?>">
                                                            <?php foreach ($sizes as $size): ?>
                                                                <?php if ($size['active'] == 1): ?>
                                                                <li><button type="button"
                                                                    class="tools <?= $size['current'] ? 'active' : '' ?>"
                                                                    data-size-name="<?php echo $translate['form']['marker']['tools']['font-size']; ?>: <?php echo htmlspecialchars($size['name'][$lang]); ?>"
                                                                    data-size="<?php echo htmlspecialchars($size['size']); ?>"
                                                                    title="<?php echo htmlspecialchars($size['name'][$lang]); ?>">
                                                                    <?php echo svg_code($size['svg']); ?>
                                                                </button></li>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>

                                                    <!-- Text Style -->
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between settings py-1">
                                                        <div class="fs-6"><?php echo $translate['form']['marker']['tools']['font-style']; ?>:</div>
                                                        <ul class="d-flex tools justify-content-start align-items-center gap-1" id="textStyle_<?php echo $key; ?>">
                                                            <?php foreach ($styles as $style): ?>
                                                                <li><button type="button" class="tools"
                                                                    data-style-name="<?php echo $translate['form']['marker']['tools']['font-style']; ?>: <?php echo htmlspecialchars($style['name'][$lang]); ?>"
                                                                    data-style="<?php echo htmlspecialchars($style['style']); ?>"
                                                                    title="<?php echo htmlspecialchars($style['name'][$lang]); ?>">
                                                                    <?php echo svg_code($style['svg']); ?>
                                                                </button></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>

                                                    <!-- Text Fonts -->
                                                    <div class="d-flex flex-wrap align-items-start justify-content-start flex-column settings p-1">
                                                        <div class="fs-6"><?php echo $translate['form']['marker']['tools']['font-family']; ?>:</div>
                                                        <ul class="scrollable-list rounded" id="fontFamily_<?php echo $key; ?>">
                                                            <?php foreach ($fonts as $index => $font): ?>
                                                                <li><canvas id="canvasfont_<?php echo $index.'_'. $key ?>"
                                                                    data-font="1.25em <?php echo htmlspecialchars($font['fontFamily']) ?>, sans-serif"
                                                                    data-family="<?php echo htmlspecialchars($font['fontFamily']) ?>"
                                                                    data-name="<?php echo htmlspecialchars($font["name"]) ?>"
                                                                    data-font-name="<?php echo $translate['form']['marker']['tools']['font-family']; ?>: <?php echo htmlspecialchars($font["name"]) ?>"
                                                                    data-rating="<?php echo htmlspecialchars($font["rating"]) ?>"
                                                                    data-badge="<?php echo htmlspecialchars($font["badge"]) ?>">
                                                                </canvas></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>

                                                </div> <!-- End of accordion-body -->
                                            </div> <!-- End of accordion-collapse -->
                                        </div> <!-- End of accordion-item -->
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </div> <!-- End of accordion -->

                            <?php endif; ?>
                            <!-- end text_position -->
                           
                        </section>
                    <!-- end -->
                </div>

                <div class="configurator__panel__options p-1 p-md-3 my-1 my-md-3">
                    <!-- FLAGS -->
                    <section class="flags d-flex flex-wrap align-items-center justify-content-between">
                            <div class="options_label">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="icons">
                                        <?php echo svg_code('flag.svg') ?>
                                    </div>
                                    <div class="option">
                                        <h4 class="fs-6"> <?php echo $translate['form']['marker']['flag']['fields']['label'] ?>:</h4>
                                    </div>
                                </div>                                
                            </div>

                            <div class="input-group w-50 d-none" id="flagInputGroup">
                                <!-- <span class="input-group-text">
                                    <?php echo '<img src="'. $_SESSION['flagUrl'] .'" alt="'. $_SESSION['country'] .'">' ?>
                                </span> -->
                                <input type="text"
                                class="form-control"
                                id="user_country"
                                data-option-type="text"
                                data-name="<?php echo $translate['form']['marker']['flag']['fields']['holder'] ?>"
                                placeholder="<?php echo $translate['form']['marker']['flag']['fields']['holder'] ?>..." value="" maxlength="10">
                            </div>

                            <div class="checkbox">
                                <input type="checkbox" id="flag" class="d-none"
                                data-option-type="checkbox"
                                data-name="<?php echo $translate['form']['marker']['flag']['title'] ?>"
                                data-price="<?php echo $translate['form']['marker']['flag']['price']['value'] ?>"
                                data-promo="<?php echo $translate['form']['marker']['flag']['price']['promo'] ?>"
                                data-warning="<?php echo $translate['form']['marker']['flag']['fields']['warning'] ?>"
                                data-country="<?php echo $_SESSION['country'] ?>"
                                >
                                <label for="flag" class="switch"><span></span></label>   
                            </div>
                        </section>
                    <!-- end -->
                </div>

                <!-- SEND PDF -->
                <div class="options_label p-1 p-md-3 sendFiles">
                    <div class="d-flex align-items-center justify-content-start gap-3">
                        <div class="icons">
                            <?php echo svg_code('images.svg') ?>
                        </div>
                        <div class="option">
                            <div class="info"><?php echo $translate['form']['info']['send-pdf'] ?>: <span id="contact_Email"><?php echo $translate['form']['info']['send-mail'] ?></span></div>
                        </div>
                    </div>                                
                </div>
                
                <!-- SAVE BUTTON -->
                <div class="my-2">
                    <button type="button"
                    id="save-config"
                    class="btn btn-warning step"
                    data-step="3" 
                    data-currency="<?php echo $translate['settings']['currency'] ?>"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><?php echo $translate['form']['buttons']['save'] ?></button>
                </div>
                <!-- end -->
            </div>

            <!-- HELPER -->
            <div class="d-none d-md-block col col-md-4 col-lg-3 configurator__helper position-absolute top-0 end-0 me-0 me-md-3 mt-0 mt-md-5 p-2 p-md-4">
                <?php echo $translate['helper'] ?>
            </div>


        </div>

        <section class="container-fluid py-2 py-md-5">
            <div class="container">
                <h4><?php echo $translate['package']['include']['intro_products'] ?>:</h4>
                <?php displayIncludePack($translate['package']['include']['products']); ?>
            </div>
        </section>

    </main>

    <footer id="footer">
        <section class="container-fluid py-3 py-md-5">
            <div class="container">
                <h4><?php echo $translate['extra'] ?>:</h4>
                <div id="featured_info_toCart" class="d-flex justify-content-center align-items-center gap-3" data-error-add-message="<?php echo $translate['form']['errors']['add-product'] ?>" data-error-delete-message="<?php echo $translate['form']['errors']['del-product'] ?>" data-delete-button="<?php echo $translate['form']['buttons']['delete'] ?>" data-total-cart-message="<?php echo $translate['form']['cart']['product-total'] ?>" data-quantity-cart-message="<?php echo $translate['form']['cart']['product-quantity'] ?>" data-delivery="<?php echo $translate['form']['cart']['product-quantity'] ?>">
                    <?php displayProductCheckbox($translate['products'], $translate['settings']['currency'], $translate['form']['buttons']['add']) ;?>
                </div>
            </div>
        </section>
        <div class="copyright text-center py-3">
            © Copyright 2025 | <?php echo $translate['package']['name'] ?>
        </div>
    </footer>


    <!-- SIDEBAR && CART -->
    <section class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <small id="offcanvasRightLabel"><?php echo $translate['header'] ?></small>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!-- BODY -->
            <div class="shoppingCart">
                <h4 class="fs-6"><?php echo $translate['form']['buttons']['cart'] ?></h4>
                
                <!-- PACK & COLOR -->
                <div id="cartBox" class="cartBox my-1 my-md-2 p-2 pt-3 position-relative"></div>

                <!-- PRODUCTS -->
                <div id="cartBoxPromo" class="cartBoxPromo"></div>
                
            </div>

            <!-- Podsumowanie Koszyka -->
            <div class="cartBoxTotal p-2">
                <div class="cartBoxTotal__value d-flex justify-content-between align-items-center gap-2">
                    <span><?php echo $translate['form']['order_summary']['value'] ?>:</span>
                    <span id="cartValue"></span>
                </div>
                <div class="cartBoxTotal__delivery d-flex justify-content-between align-items-center gap-2">
                    <span><?php echo $translate['form']['order_summary']['cost'] ?>:</span>
                    <span id="cartDelivery"></span>
                </div>
                <div class="cartBoxTotal__delivery__info">
                    <ul id="cartDeliveryInfo" class="text-end"></ul>
                </div>
                <div class="cartBoxTotal__amount d-flex justify-content-end align-items-center gap-2">
                    <span><?php echo $translate['form']['order_summary']['amount'] ?>:</span>
                    <span id="cartTotal"></span>
                </div>
            </div>

            <!-- FORMULARZ -->
            <div id="formMessage"></div>
            <form id="cartForm">
                <div id="cartBoxForm" class="cartBoxForm my-1 p-2">
                    <h4 class="fs-6"><?php echo $translate['form']['person']['title'] ?></h4>
                
                    <div class="form-group">
                        <label for="name"><?php echo $translate['form']['person']['fields']['first_name'] ?>:</label>
                        <input type="text" aria-label="<?php echo $translate['form']['person']['fields']['first_name'] ?>" id="name" class="form-control" name="name" autocomplete="off" required>
                    </div> 
                    <div class="form-group">
                        <label for="lastname"><?php echo $translate['form']['person']['fields']['last_name'] ?>:</label>
                        <input type="text" aria-label="<?php echo $translate['form']['person']['fields']['last_name'] ?>" id="lastname" class="form-control" name="lastname" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><?php echo $translate['form']['person']['fields']['email'] ?>:</label>
                        <input type="text" aria-label="<?php echo $translate['form']['person']['fields']['email'] ?>" id="email" class="form-control" name="email" autocomplete="off" minlength="3" maxlength="64" required>
                    </div>
                    <div class="form-group">
                        <label for="phone"><?php echo $translate['form']['person']['fields']['phone'] ?>:</label>
                        <input type="text" aria-label="<?php echo $translate['form']['person']['fields']['phone'] ?>" id="phone" class="form-control" name="phone" autocomplete="off" required>
                    </div>

                    <div class="form-check">     
                        <input class="form-check-input" type="checkbox" id="accept" name="accept">
                        <label for="accept"><sup>*</sup><?php echo $translate['form']['order']['accept']['title'] ?> <a href="<?php echo $translate['form']['order']['accept']['link'] ?>"><?php echo $translate['form']['order']['accept']['name'] ?></a>.</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree" name="agree">
                        <label for="agree"><sup>*</sup><?php echo $translate['form']['order']['agree']['title'] ?> <a href="<?php echo $translate['form']['order']['agree']['link'] ?>"><?php echo $translate['form']['order']['agree']['name'] ?></a> <?php echo $translate['form']['order']['agree']['shop'] ?>.</label>
                    </div>
                </div>
                                        
                <div class="submit py-1 text-center">
                    <!-- Inputs -->
                    <input type="hidden" name="human" id="human" value="">
                    <input type="hidden" name="isUser" id="isUser" value="">
                    <input type="hidden" name="isForm" id="isForm" value="">
                    <input type="hidden" name="isVisit" id="isVisit" value="">
                    <!-- end -->
                    <button class="btn send" id="send" type="submit"><?php echo $translate['form']['buttons']['submit'] ?></button>
                </div>                
            </form>
        </div>
    </section>
        
        
    <!-- Theme Settings -->
    <script src="<?php echo version('js/theme.js'); ?>"></script>
    <!-- Helper Functions -->
    <script src="<?php echo version('js/functions.js'); ?>"></script>
    <!-- Corol Pack -->
    <script src="<?php echo version('js/color_pack.js'); ?>"></script>
    <!-- Text Format -->
    <script src="<?php echo version('js/personal_panel.js'); ?>"></script>
    <!-- Text Font -->
    <script src="<?php echo version('js/canvas.js'); ?>"></script>
    <!-- Flags -->
    <script src="<?php echo version('js/flags.js'); ?>"></script>
    <!-- Cart -->
    <script src="<?php echo version('js/cart.js'); ?>"></script>
    <!-- Zoom -->
    <script src="<?php echo version('js/zoom.js'); ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
