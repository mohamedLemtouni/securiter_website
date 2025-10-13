<?php 
session_start(); 
include "db.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nos Activités</title>

<link rel="stylesheet" href="/static/css/header.css">
<link rel="stylesheet" href="/static/css/event.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php include "header.php"; ?>

<main class="main-container">
<?php
$cmd = $db->prepare("SELECT * FROM ACTIVITE_EVENEMENT where TYPE_ACT_EVENT = 'evenement' ORDER BY DATE_DEBUT DESC;");
$cmd->execute();
$events = $cmd->fetchAll(PDO::FETCH_ASSOC);

if(count($events) === 0){ 
  echo "<p style='grid-column:1/-1;text-align:center;'>Aucune activité disponible pour le moment.</p>"; 
}
else {
  foreach($events as $even){
    $imgReq = $db->prepare("SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV = ?");
    $imgReq->execute([$even["ID_ACT_EV"]]);
    $images = $imgReq->fetchAll(PDO::FETCH_ASSOC);
?>
  <div class="activity-card">
    <div class="activity-info">
      <h2><?= htmlspecialchars($even["NOM"]) ?></h2>
      <p class="description"><?= htmlspecialchars($even["DESCRIPTION_COURTE"] ?? '') ?></p>
      <p class="price"><?= number_format($even["PRIX"],2,',',' ') ?> MAD</p>
      <form action="reservation.php" method="POST">
        <input type="hidden" name="id_activite" value="<?= $even["ID_ACT_EV"] ?>">
        <button type="submit">Réserver</button>
      </form>
    </div>
    <div class="activity-slider swiper">
      <div class="swiper-wrapper">
        <?php foreach($images as $img){ ?>
          <div class="swiper-slide">
            <img src="<?= htmlspecialchars($img['URL_IMAGE']) ?>" alt="Image <?= htmlspecialchars($even['NOM']) ?>">
          </div>
        <?php } ?>
      </div>
      <!-- Navigation -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
<?php }} ?>
</main>

<?php include "footer.php"; ?>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sliders = document.querySelectorAll('.activity-slider');
  sliders.forEach(slider => {
    new Swiper(slider, {
      loop: true,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      navigation: {
        nextEl: slider.querySelector('.swiper-button-next'),
        prevEl: slider.querySelector('.swiper-button-prev'),
      },
      pagination: {
        el: slider.querySelector('.swiper-pagination'),
        clickable: true,
      },
      effect: 'coverflow',
      coverflowEffect: {
        rotate: 30,
        slideShadows: false,
      },
      grabCursor: true,
    });
  });
});
</script>

</body>
</html>
