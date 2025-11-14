<!DOCTYPE html>
<?php session_start();
include("db.php"); ?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Azure Travel</title>
  <link rel="icon" sizes="192x192" href="./photos/logo_wbl.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/static/css/header.css">
  <link rel="stylesheet" href="/static/css/footer.css">
  <link rel="stylesheet" href="/static/css/templatemo-style.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://www.travel-magical-morocco.com/wp-content/uploads/2021/11/medina-marrakech-square-morocco-1024x683.jpg') no-repeat center center/cover;
      background-attachment: fixed;
      color: #fff;
      line-height: 1.6;
    }

    /* Hero Section */
    .hero {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      text-align: center;
           padding: 0 20px;
    }
    .hero h1 {
      font-size: 4rem;
      font-weight: 700;
      letter-spacing: 2px;
      margin-bottom: 20px;
    }
    .hero button {
      padding: 0.8rem 2rem;
      border: none;
      background-color: #007bff;
      color: #fff;
      font-weight: 500;
      border-radius: 0.5rem;
      transition: all 0.3s;
    }
    .hero button:hover {
      background-color: #0056b3;
      transform: translateY(-3px);
    }

    /* Tours Section */
    .tours {
      padding: 5rem 2rem;
      background-color: rgba(0,0,0,0.85);
      text-align: center;
    }
    .tours h2 {
      font-size: 2.5rem;
      margin-bottom: 3rem;
    }
    .tour-card img {
      height: 200px;
      object-fit: cover;
    }
    .tour-card {
      border-radius: 0.8rem;
      overflow: hidden;
      background-color: #1a1a1a;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .tour-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 0 20px rgba(0,123,255,0.5);
    }
    .tour-card h3 {
      padding: 1rem;
      font-size: 1.3rem;
      font-weight: 500;
    }

    /* Video Section */
    .video-section {
      padding: 5rem 2rem;
      background-color: rgba(0,0,0,0.9);
      text-align: center;
    }
    .video-section h2 {
      font-size: 2.5rem;
      margin-bottom: 2rem;
    }
    .video-thumbnails img {
      width: 100%;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .video-thumbnails img:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(0,123,255,0.7);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }
      .tour-card img {
        height: 180px;
      }
      .video-section h2 {
        font-size: 2rem;
      }
    }
  </style>
</head>

<?php include("header.php"); ?>
<body>
  <!-- Hero -->
  <section class="hero d-flex flex-column justify-content-center align-items-center text-center">
    <h1>Want to visit Morocco?</h1>
    <a href="#tours" class="btn btn-primary btn-lg mt-3">Give it a try →</a>
  </section>

  <!-- Tours Section -->
  <section id="tours" class="tours container">
    <h2>Best destinations</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="tour-card card text-white">
          <img src="https://upload.wikimedia.org/wikipedia/commons/4/49/Marokko0112_%28retouched%29.jpg" class="card-img-top" alt="Marrakech">
          <h3>Marrakech</h3>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="tour-card card text-white">
          <img src="https://media.beauxarts.com/uploads/2024/07/adobestock_552100961-1300x975.jpg" class="card-img-top" alt="Fès">
          <h3>Fès</h3>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="tour-card card text-white">
          <img src="https://www.2p.ma/images/202310051640Les%20Incontournables%20De%20Casablanca.webp" class="card-img-top" alt="Casablanca">
          <h3>Casablanca</h3>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="tour-card card text-white">
          <img src="https://www.visitmorocco.com/sites/default/files/styles/thumbnail_events_slider/public/thumbnails/image/chateau-et-port-essaouira.jpg?itok=p7Wl1c0F" class="card-img-top" alt="Essaouira">
          <h3>Essaouira</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- Video Section -->
  <section class="video-section container">
    <h2>Latest photos from our visitors</h2>
    <div class="row g-4 video-thumbnails">
      <div class="col-md-4">
        <img src="https://skyhookcontentful.imgix.net/3ApuFHrbiPIZfnIa7UZrvE/233c1d2c184c5a2b0dfd60af7dcaf2da/woman-7323258_1280.jpg?auto=compress%2Cformat%2Cenhance%2Credeye&crop=faces%2Ccenter&fit=crop&ar=1%3A1&w=576px&ixlib=react-9.10.0" alt="Visitor photo" class="img-fluid">
      </div>
      <div class="col-md-4">
        <img src="https://www.traveloffpath.com/wp-content/uploads/2023/12/Tourists-Riding-A-Camel-In-Agadir-A-Coastal-Resort-City-On-The-Atlantic-Coast-Of-Morocco-North-Africa.jpg.webp" alt="Visitor photo" class="img-fluid">
      </div>
      <div class="col-md-4">
        <img src="https://www.traveloffpath.com/wp-content/uploads/2023/09/Woman-walking-through-streets-in-Morocco.jpg" alt="Visitor photo" class="img-fluid">
      </div>
    </div>
  </section>

<?php include("footer.php"); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
