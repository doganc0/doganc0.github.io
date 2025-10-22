<?php
require_once __DIR__ . '/../bootstrap.php';

$db = Database::getInstance();
$settings = (new Settings($db))->getSettings();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magnus Sigorta | Akıllı Teklif Formu</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="app">
        <header class="app__header">
            <div class="logo">
                <?php if (!empty($settings['site_logo'])): ?>
                    <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Magnus Sigorta">
                <?php else: ?>
                    <span class="logo__fallback">Magnus Sigorta</span>
                <?php endif; ?>
            </div>
            <div class="header__contact">
                <span class="header__label">Çağrı Merkezi</span>
                <a href="tel:<?= htmlspecialchars($settings['site_phone']) ?>" class="header__phone"><?= htmlspecialchars($settings['site_phone']) ?></a>
            </div>
        </header>

        <main class="app__content">
            <section id="product-selection" class="card-grid">
                <h1 class="section__title">Magnus Sigorta ile Avantajlı Teklifinizi Hemen Alın</h1>
                <p class="section__subtitle">İhtiyacınıza uygun sigorta ürününü seçin, birkaç adımda uzmanlarımızdan teklifinizi alın.</p>
                <div id="productCards" class="card-grid__items"></div>
            </section>

            <section id="form-section" class="form-wizard" hidden>
                <div class="form-wizard__hero">
                    <div class="form-wizard__avatar">
                        <?php if (!empty($settings['form_avatar'])): ?>
                            <img src="<?= htmlspecialchars($settings['form_avatar']) ?>" alt="Magnus Danışmanı">
                        <?php else: ?>
                            <div class="avatar__placeholder">İ</div>
                        <?php endif; ?>
                    </div>
                    <div class="form-wizard__intro">
                        <h2>Merhaba ben İlknur!</h2>
                        <p>Hemen birkaç kısa soruyla size en uygun sigorta teklifini hazırlayalım.</p>
                    </div>
                </div>

                <div class="stepper" id="stepper"></div>

                <form id="dynamicForm" novalidate>
                    <div id="stepContainer" class="step-container"></div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary" id="btnPrev">Geri</button>
                        <button type="button" class="btn btn-primary" id="btnNext">İleri</button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <template id="productCardTemplate">
        <article class="card">
            <div class="card__icon"></div>
            <h3 class="card__title"></h3>
            <p class="card__description"></p>
        </article>
    </template>

    <script>
        window.MagnusSettings = {
            redirectUrl: "<?= htmlspecialchars($settings['redirect_url'] ?: 'https://magnussigorta.com') ?>",
            successPhone: "<?= htmlspecialchars($settings['site_phone']) ?>"
        };
    </script>
    <script src="assets/js/app.js"></script>
</body>
</html>
