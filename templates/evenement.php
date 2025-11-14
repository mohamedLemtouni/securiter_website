<?php  
session_start();  
include "db.php";  

// Récupérer les favoris de l'utilisateur connecté
$favoritesIds = [];
if (isset($_SESSION['idcli'])) {
    $favStmt = $db->prepare("SELECT ID_ACT_EV FROM FAVORIS WHERE ID_CLIENT = ?");
    $favStmt->execute([$_SESSION['idcli']]);
    $favoritesIds = $favStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Récupération des tags distincts
$tagsStmt = $db->query("SELECT DISTINCT TAGS FROM ACTIVITE_EVENEMENT WHERE TAGS IS NOT NULL AND TAGS <> ''");
$allTags = [];
while($row = $tagsStmt->fetch(PDO::FETCH_ASSOC)) {
    $tagsArray = array_map('trim', explode(',', $row['TAGS']));
    foreach($tagsArray as $tag) {
        if($tag && !in_array(strtolower($tag), $allTags)) {
            $allTags[] = strtolower($tag);
        }
    }
}
sort($allTags); // Tri alphabétique
?>  

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nos Événements</title>

  <!-- CSS -->
  <link rel="stylesheet" href="/static/css/bootstrap.min.css">
  <link rel="stylesheet" href="fontawesome/css/all.min.css">
  <link rel="stylesheet" href="/static/css/templatemo-style.css">
  <link rel="stylesheet" href="/static/css/event.css">   
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
  <link rel="stylesheet" href="/static/css/header.css">
  <style>
    body{
       background: url('https://static.yabiladi.com/files/articles/166268_8e51e2de40912084304420d3b127617a20250514021702_thumb_565.webp') no-repeat center center/cover;
       background-attachment: fixed;
    }
    .filters-container {
      display: flex;
      gap: 10px;
      margin: 20px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .filters-container input, .filters-container select, .filters-container button {
      padding: 8px 12px;
      font-size: 14px;
    }
    .activity-card { display: flex; flex-direction: column; transition: all 0.3s ease; }
    .swiper { width: 100%; height: 230px; border-radius: 10px; overflow: hidden; }
    .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
    figure.tm-video-item { position: relative; overflow: hidden; margin: 0; }
    figure.tm-video-item figcaption {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.6); opacity: 0;
      transition: opacity 0.4s ease; color: #fff;
      text-align: center; display: flex; flex-direction: column;
      justify-content: center; align-items: center;
    }
    figure.tm-video-item:hover figcaption { opacity: 1; }
    figure.tm-video-item h2 { font-size: 1.3rem; margin-bottom: 8px; color: #fff; }
    figure.tm-video-item a { color: #fff; text-decoration: underline; }
  </style>
</head>

<body>
<?php include "header.php"; ?>

<!-- Filtres -->
<div class="filters-container" style="margin-top: 7%;">
  <input type="text" id="filter-keyword" placeholder="Rechercher par mot-clé..." />
  <select id="filter-tags">
      <option value="">Tous les tags</option>
      <?php foreach($allTags as $tag): ?>
          <option value="<?= htmlspecialchars($tag) ?>"><?= ucfirst(htmlspecialchars($tag)) ?></option>
      <?php endforeach; ?>
  </select>
  <input type="number" id="filter-max-price" placeholder="Prix maximum (MAD)" min="0" />
  <label>
    <input type="checkbox" id="filter-favorites"> Favoris uniquement
  </label>
  <button id="filter-reset">Réinitialiser</button>
</div>

<!-- Grille des événements -->
<div class="container">
  <div class="row tm-mb-90 tm-gallery">
    <?php
    $cmd = $db->prepare("SELECT * FROM ACTIVITE_EVENEMENT WHERE TYPE_ACT_EVENT = 'evenement' AND VISIBLE = 1 ORDER BY DATE_DEBUT DESC;");
    $cmd->execute();
    $events = $cmd->fetchAll(PDO::FETCH_ASSOC);

    if(count($events) === 0){
      echo "<p style='width:100%;text-align:center;'>Aucun événement disponible pour le moment.</p>";
    } else {
      foreach($events as $even){
        $imgReq = $db->prepare("SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV = ?");
        $imgReq->execute([$even["ID_ACT_EV"]]);
        $images = $imgReq->fetchAll(PDO::FETCH_ASSOC);
        $tags = strtolower($even['TAGS'] ?? '');
    ?>
      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5 activity-card"
           data-name="<?= htmlspecialchars(strtolower($even['NOM'])) ?>"
           data-price="<?= $even['PRIX'] ?>"
           data-tags="<?= htmlspecialchars($tags) ?>"
           data-favorite="<?= in_array($even['ID_ACT_EV'], $favoritesIds) ? '1' : '0' ?>">

        <figure class="effect-ming tm-video-item">
          <div class="swiper event-swiper">
            <div class="swiper-wrapper">
              <?php 
              if (count($images) > 0) {
                foreach ($images as $img) { ?>
                  <div class="swiper-slide">
                    <img src="<?= htmlspecialchars($img['URL_IMAGE']) ?>" alt="<?= htmlspecialchars($even['NOM']) ?>">
                  </div>
                <?php }
              } else { ?>
                <div class="swiper-slide">
                  <img src="img/default.jpg" alt="Image par défaut">
                </div>
              <?php } ?>
            </div>
            <div class="swiper-pagination"></div>
          </div>

          <figcaption class="d-flex align-items-center justify-content-center">
            <h2><?= htmlspecialchars($even['NOM']) ?></h2>
            <a href="detail_actievent.php?val=<?= $even['ID_ACT_EV'] ?>">Voir plus</a>
          </figcaption>
        </figure>

        <div class="d-flex justify-content-between tm-text-gray mt-2">
          <span class="tm-text-gray-light"><?= date("d M Y", strtotime($even["DATE_DEBUT"])) ?></span>
          <span><?= number_format($even["PRIX"], 2, ',', ' ') ?> MAD</span>
        </div>
      </div>
    <?php
      }
    }
    ?>
  </div>
</div>

<?php include "footer.php"; ?>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){

  // --- Initialisation Swiper ---
  document.querySelectorAll('.event-swiper').forEach(swiperEl => {
    new Swiper(swiperEl, {
      loop: true,
      autoplay: { delay: 3500, disableOnInteraction: false },
      pagination: { el: swiperEl.querySelector('.swiper-pagination'), clickable: true },
      effect: 'slide',
      grabCursor: true,
    });
  });

  // --- Filtres JS ---
  const keywordInput = document.getElementById('filter-keyword');
  const tagSelect = document.getElementById('filter-tags');
  const priceInput = document.getElementById('filter-max-price');
  const favoriteCheckbox = document.getElementById('filter-favorites');
  const resetBtn = document.getElementById('filter-reset');
  const activityCards = document.querySelectorAll('.activity-card');

  function filterActivities() {
    const keyword = keywordInput.value.trim().toLowerCase();
    const selectedTag = tagSelect.value.toLowerCase();
    const maxPrice = parseFloat(priceInput.value);
    const favoritesOnly = favoriteCheckbox.checked;

    activityCards.forEach(card => {
      const name = card.dataset.name;
      const price = parseFloat(card.dataset.price);
      const tags = card.dataset.tags;
      const isFavorite = card.dataset.favorite === "1";
      let visible = true;

      if(keyword && !name.includes(keyword)) visible = false;
      if(selectedTag && (!tags || !tags.split(',').map(t => t.trim()).includes(selectedTag))) visible = false;
      if(!isNaN(maxPrice) && price > maxPrice) visible = false;
      if(favoritesOnly && !isFavorite) visible = false;

      card.style.display = visible ? 'block' : 'none';
    });
  }

  keywordInput.addEventListener('input', filterActivities);
  tagSelect.addEventListener('change', filterActivities);
  priceInput.addEventListener('input', filterActivities);
  favoriteCheckbox.addEventListener('change', filterActivities);
  resetBtn.addEventListener('click', () => {
    keywordInput.value = '';
    tagSelect.value = '';
    priceInput.value = '';
    favoriteCheckbox.checked = false;
    filterActivities();
  });

});
</script>
</body>
</html>
