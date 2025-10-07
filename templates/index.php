<!DOCTYPE html>
<?php session_start();
include("db.php");?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/static/css/styleindex.css">
  <link rel="stylesheet" href="/static/css/header.css">
  <link rel="stylesheet" href="/static/css/footer.css">
  <title>Travel Landing Page</title>
</head>
<body>

<?php include("header.php");?>

  <!-- Hero Section -->
  <section class="hero">
    <h1>Want to visit Morocco ?</h1>
    <button>Give it a try →</button>
  </section>

  <!-- Tours Section -->
  <section class="tours">
    <h2>Best destination</h2>
    <div class="tour-cards">
      <div class="card">
        <img src="https://upload.wikimedia.org/wikipedia/commons/4/49/Marokko0112_%28retouched%29.jpg" alt="">
        <h3>Marrakech</h3>
      </div>
      <div class="card">
        <img src="https://media.beauxarts.com/uploads/2024/07/adobestock_552100961-1300x975.jpg" alt="">
        <h3>Fès</h3>
      </div>
      <div class="card">
        <img src="https://www.2p.ma/images/202310051640Les%20Incontournables%20De%20Casablanca.webp" alt="">
        <h3>Casablanca</h3>
      </div>
      <div class="card">
        <img src="https://www.visitmorocco.com/sites/default/files/styles/thumbnail_events_slider/public/thumbnails/image/chateau-et-port-essaouira.jpg?itok=p7Wl1c0F" alt="">
        <h3>Essaouira</h3>
      </div>
    </div>
  </section>

  <!-- Video Section -->
  <section class="video-section">
    <h2>Latest photos from our visitors</h2>
    <div class="video-thumbnails">
      <img src="https://skyhookcontentful.imgix.net/3ApuFHrbiPIZfnIa7UZrvE/233c1d2c184c5a2b0dfd60af7dcaf2da/woman-7323258_1280.jpg?auto=compress%2Cformat%2Cenhance%2Credeye&crop=faces%2Ccenter&fit=crop&ar=1%3A1&w=576px&ixlib=react-9.10.0" alt="">
      <img src="https://www.traveloffpath.com/wp-content/uploads/2023/12/Tourists-Riding-A-Camel-In-Agadir-A-Coastal-Resort-City-On-The-Atlantic-Coast-Of-Morocco-North-Africa.jpg.webp" alt="">
      <img src="https://www.traveloffpath.com/wp-content/uploads/2023/09/Woman-walking-through-streets-in-Morocco.jpg" alt="">
    </div>
  </section>

  <!-- Footer -->
<?php include("footer.php");?>

</body>
</html>
