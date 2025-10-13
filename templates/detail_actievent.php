<?php
session_start();
include 'db.php';

// Vérification de l'ID passé dans l'URL
if (!isset($_GET['val']) || !is_numeric($_GET['val'])) {
    die("<h2>ID d'événement invalide.</h2>");
}

$id = intval($_GET['val']);

// Requête principale : activité/événement
$sql = "SELECT AE.*, C.NOM_CLI, C.PRENOM_CLI, C.PHOTO_PROFILE
        FROM ACTIVITE_EVENEMENT AE
        JOIN CLIENT C ON AE.ID_CLIENT_ORGANISATEUR = C.ID_CLIENT
        WHERE AE.ID_ACT_EV = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("<h2>Événement introuvable.</h2>");
}

// Récupération des images associées
$sqlImg = "SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV = ?";
$stmtImg = $db->prepare($sqlImg);
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['NOM']) ?> - Détails</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ✅ Style moderne (TailwindCSS CDN pour rapidité) -->
    <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="/static/css/header.css">
  <link rel="stylesheet" href="/static/css/footer.css">
    <!-- ✅ Slider (SwiperJS moderne) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<?php include "header.php"?>
<body class="bg-gray-50 text-gray-800">

    <!-- Conteneur principal -->
    <div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg" style="margin-bottom: 2%;">
        <!-- Titre -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($event['NOM']) ?></h1>

        <!-- Slider -->
        <?php if ($images): ?>
        <div class="swiper mySwiper mb-6 rounded-2xl overflow-hidden">
            <div class="swiper-wrapper">
                <?php foreach ($images as $img): ?>
                    <div class="swiper-slide">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Image activité" class="w-full h-96 object-cover">
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <?php else: ?>
            <p class="text-gray-500 italic mb-6">Aucune image disponible pour cet événement.</p>
        <?php endif; ?>

        <!-- Infos principales -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-2">🗓️ Informations</h2>
                <ul class="space-y-1 text-gray-700">
                    <li><strong>Type :</strong> <?= ucfirst($event['TYPE_ACT_EVENT']) ?></li>
                    <li><strong>Début :</strong> <?= date('d/m/Y H:i', strtotime($event['DATE_DEBUT'])) ?></li>
                    <li><strong>Fin :</strong> <?= $event['DATE_FIN'] ? date('d/m/Y H:i', strtotime($event['DATE_FIN'])) : 'Non spécifiée' ?></li>
                    <li><strong>Prix :</strong> <?= number_format($event['PRIX'], 2, ',', ' ') ?> €</li>
                    <li><strong>Difficulté :</strong> <?= ucfirst($event['DIFFICULTE']) ?></li>
                    <li><strong>Public :</strong> <?= ucfirst($event['PUBLIC_CIBLE']) ?></li>
                </ul>
            </div>

            <!-- Organisateur -->
            <div>
                <h2 class="text-xl font-semibold mb-2">👤 Organisateur</h2>
                <div class="flex items-center space-x-3">
                    <img src="<?= htmlspecialchars($event['PHOTO_PROFILE']) ?>" alt="Photo de profil" class="w-16 h-16 rounded-full object-cover">
                    <div>
                        <p class="font-medium"><?= htmlspecialchars($event['PRENOM_CLI'] . ' ' . $event['NOM_CLI']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($event['EMAIL_CLI'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">📖 Description complète</h2>
            <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($event['DESCRIPTION_CMPLT'])) ?></p>
        </div>

        <!-- Tags -->
        <?php if ($event['TAGS']): ?>
        <div class="mt-6">
            <h3 class="font-semibold mb-1">🏷️ Tags :</h3>
            <?php foreach (explode(',', $event['TAGS']) as $tag): ?>
                <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mr-2"><?= htmlspecialchars(trim($tag)) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>

    <!-- SwiperJS init -->
    <script>
        const swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    </script>
<?php include "footer.php"?>
</body>
</html>
