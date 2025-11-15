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
  <link rel="stylesheet" href="/static/css/header.css">  
  <link rel="stylesheet" href="/static/css/card.css">

  <!-- Material Cards CSS -->
  <link rel="stylesheet" href="/static/css/material-cards.css">

  <style>

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
  </style>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

<!-- Grille Material Cards -->
<section class="container">
    <div class="row active-with-click">
        <?php
        $cmd = $db->prepare("SELECT * FROM ACTIVITE_EVENEMENT WHERE TYPE_ACT_EVENT = 'evenement' AND VISIBLE = 1 ORDER BY DATE_DEBUT DESC;");
        $cmd->execute();
        $events = $cmd->fetchAll(PDO::FETCH_ASSOC);

        if(count($events) === 0){
            echo "<p style='width:100%;text-align:center;'>Aucun événement disponible pour le moment.</p>";
        } else {
            $colors = ["Red","Pink","Purple","Deep-Purple","Indigo","Blue","Light-Blue","Cyan","Teal","Green","Light-Green","Lime","Yellow","Amber","Orange","Deep-Orange","Brown","Grey","Blue-Grey"];
            $colorCount = count($colors);
            $i = 0;
            foreach($events as $even){
                $imgReq = $db->prepare("SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV = ?");
                $imgReq->execute([$even["ID_ACT_EV"]]);
                $images = $imgReq->fetchAll(PDO::FETCH_ASSOC);
                $imgUrl = count($images) > 0 ? $images[0]['URL_IMAGE'] : 'img/default.jpg';
                $color = $colors[$i % $colorCount];
                $i++;
        ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <article class="material-card <?= $color ?>">
                <h2>
                    <span><?= htmlspecialchars($even['NOM']) ?></span>
                    <strong>
                        <i class="fa fa-fw fa-star"></i>
                        <?= number_format($even["PRIX"], 2, ',', ' ') ?> MAD
                    </strong>
                </h2>
                <div class="mc-content">
                    <div class="img-container">
                        <img class="img-responsive" src="<?= htmlspecialchars($imgUrl) ?>">
                    </div>
                    <div class="mc-description">
                        <?= htmlspecialchars(substr($even['DESCRIPTION_COURTE'] ?? '', 0, 150)) ?>...
                    </div>
                </div>
                <a class="mc-btn-action">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="mc-footer">
                    <h4>Social</h4>
                    <a href="#" class="fa fa-fw fa-facebook"></a>
                    <a href="#" class="fa fa-fw fa-twitter"></a>
                    <a href="#" class="fa fa-fw fa-linkedin"></a>
                    <a href="#" class="fa fa-fw fa-google-plus"></a>
                </div>
            </article>
        </div>
        <?php
            }
        }
        ?>
    </div>
</section>

<?php include "footer.php"; ?>

<!-- JS -->
<script src="js/bootstrap.bundle.min.js"></script>
<script src="/static/js/material-cards.js"></script>

<script>
$(function() {
    $('.material-card > .mc-btn-action').click(function () {
        var card = $(this).parent('.material-card');
        var icon = $(this).children('i');
        icon.addClass('fa-spin-fast');

        if (card.hasClass('mc-active')) {
            card.removeClass('mc-active');
            window.setTimeout(function() {
                icon.removeClass('fa-arrow-left fa-spin-fast').addClass('fa-bars');
            }, 800);
        } else {
            card.addClass('mc-active');
            window.setTimeout(function() {
                icon.removeClass('fa-bars fa-spin-fast').addClass('fa-arrow-left');
            }, 800);
        }
    });
});

// --- Filtres JS ---
document.addEventListener('DOMContentLoaded', function(){
  const keywordInput = document.getElementById('filter-keyword');
  const tagSelect = document.getElementById('filter-tags');
  const priceInput = document.getElementById('filter-max-price');
  const favoriteCheckbox = document.getElementById('filter-favorites');
  const resetBtn = document.getElementById('filter-reset');
  const activityCards = document.querySelectorAll('.material-card');

  function filterActivities() {
    const keyword = keywordInput.value.trim().toLowerCase();
    const selectedTag = tagSelect.value.toLowerCase();
    const maxPrice = parseFloat(priceInput.value);
    const favoritesOnly = favoriteCheckbox.checked;

    activityCards.forEach(card => {
      const name = card.querySelector('h2 span').textContent.toLowerCase();
      const price = parseFloat(card.querySelector('h2 strong').textContent.replace(/[^0-9,]/g,'').replace(',','.'));
      let visible = true;
      if(keyword && !name.includes(keyword)) visible = false;
      if(!isNaN(maxPrice) && price > maxPrice) visible = false;
      card.style.display = visible ? 'block' : 'none';
    });
  }

  keywordInput.addEventListener('input', filterActivities);
  priceInput.addEventListener('input', filterActivities);
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
