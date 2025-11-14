<?php
session_start();
include 'db.php';

// Vérification de l'ID d'événement
if (!isset($_GET['val']) || !is_numeric($_GET['val'])) {
    die("<h2>ID d'événement invalide.</h2>");
}
$id = intval($_GET['val']);

// Vérification de la visibilité de l'événement
$test_val = $db->prepare("SELECT VISIBLE FROM ACTIVITE_EVENEMENT WHERE ID_ACT_EV = ?");
$test_val->execute([$id]);
$cmd_tst_val = $test_val->fetch(PDO::FETCH_ASSOC);
if (!$cmd_tst_val["VISIBLE"]) {
    die("<h2>ID d'événement invalide.</h2>");
}

// Récupération des infos de l'événement et organisateur
$sql = "SELECT AE.*, C.NOM_CLI, C.PRENOM_CLI, C.PHOTO_PROFILE, C.EMAIL_CLI
        FROM ACTIVITE_EVENEMENT AE
        JOIN CLIENT C ON AE.ID_CLIENT_ORGANISATEUR = C.ID_CLIENT
        WHERE AE.ID_ACT_EV = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$event) {
    die("<h2>Événement introuvable.</h2>");
}

// Récupération des images
$sqlImg = "SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV = ?";
$stmtImg = $db->prepare($sqlImg);
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

// Vérification si l'événement est déjà en favoris
$favori = false;
if(isset($_SESSION['idcli'])) {
    $checkFav = $db->prepare("SELECT 1 FROM FAVORIS WHERE ID_CLIENT = ? AND ID_ACT_EV = ?");
    $checkFav->execute([$_SESSION['idcli'], $id]);
    $favori = (bool)$checkFav->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['NOM']) ?> - Détails</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/static/css/header.css">
    <link rel="stylesheet" href="/static/css/footer.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<?php include "header.php" ?>
<body class="bg-gray-50 text-gray-800">
<div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg mb-8">

    <!-- Titre et cœur favori -->
    <div class="flex items-center gap-2">
        <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($event['NOM']) ?></h1>
        <i 
            class="fa-regular fa-heart text-3xl cursor-pointer <?= $favori ? 'text-red-500' : 'text-gray-400' ?>" 
            id="heartIcon"
            onclick="toggleFavoris(<?= $event['ID_ACT_EV'] ?>)" style="margin: 0% 0% 1% 1%;">
        </i>
    </div>

    <!-- Slider images -->
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

    <!-- Informations et organisateur -->
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

        <div>
            <h2 class="text-xl font-semibold mb-2">👤 Organisateur</h2>
            <div class="flex items-center space-x-3 flex-col items-start">
                <div class="flex items-center">
                    <img src="<?= htmlspecialchars($event['PHOTO_PROFILE']) ?>" alt="Photo de profil" class="w-16 h-16 rounded-full object-cover">
                    <div class="ml-3">
                        <p class="font-medium"><?= htmlspecialchars($event['PRENOM_CLI'] . ' ' . $event['NOM_CLI']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($event['EMAIL_CLI'] ?? '') ?></p>
                    </div>
                </div>

                <!-- Formulaire de participation -->
                <div class="mt-8 bg-gray-100 p-6 rounded-xl shadow-inner w-full">
                    <form action="participation.php" method="POST" class="space-y-4">
                        <input type="hidden" name="id_event" value="<?= $event['ID_ACT_EV'] ?>">
                        <div>
                            <label for="nb_personne" class="block text-gray-700 font-medium mb-1">Nombre de personnes</label>
                            <input type="number" id="nb_personne" name="nb_personne" min="1" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <?php if ($event['TYPE_ACT_EVENT'] == "activite") { ?>
                            <div>
                                <label for="date_participation" class="block text-gray-700 font-medium mb-1">Date de participation</label>
                                <input type="date" id="date_participation" name="date_participation"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        <?php } ?>
                        <div class="text-right">
                            <button type="submit"
                                    class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                Réserver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Description complète -->
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
        autoplay: { delay: 4000, disableOnInteraction: false },
        pagination: { el: ".swiper-pagination", clickable: true },
        navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    });
</script>

<!-- SweetAlert + toggle favoris -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleFavoris(eventId) {
    const heart = document.getElementById('heartIcon');
    const action = heart.classList.contains('text-red-500') ? 'remove' : 'add';

    fetch('toggle_favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({eventId: eventId, action: action})
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if(action === 'add') {
                heart.classList.remove('text-gray-400');
                heart.classList.add('text-red-500');
            } else {
                heart.classList.remove('text-red-500');
                heart.classList.add('text-gray-400');
            }

            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Erreur', text: data.message });
        }
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur est survenue.' });
        console.error(err);
    });
}
</script>

<?php include "footer.php" ?>
</body>
</html>
