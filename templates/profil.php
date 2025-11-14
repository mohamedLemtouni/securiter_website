<?php
session_start();
include("db.php");

// 🔒 Vérifier la connexion utilisateur
if (!isset($_SESSION["idcli"])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script>
          Swal.fire({
              title: 'Accès refusé',
              text: 'Vous devez vous connecter pour accéder à votre profil.',
              icon: 'warning',
              confirmButtonText: 'Se connecter'
          }).then(() => {
              window.location.href = 'connexion.php';
          });
          </script>";
    exit();
}

$idcli = $_SESSION["idcli"];

// 🔍 Récupérer les infos du client
$cmd = $db->prepare("SELECT * FROM CLIENT WHERE ID_CLIENT = :id");
$cmd->execute([":id" => $idcli]);
$client = $cmd->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script>
          Swal.fire({
              title: 'Erreur',
              text: 'Profil introuvable. Veuillez vous reconnecter.',
              icon: 'error',
              confirmButtonText: 'OK'
          }).then(() => {
              window.location.href = 'connexion.php';
          });
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #74ABE2, #5563DE);
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
        }
        .profile-container {
            background: white;
            border-radius: 20px;
            padding: 35px;
            max-width: 500px;
            width: 95%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
        }
        .profile-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #74ABE2;
            margin-bottom: 15px;
        }
        .profile-name {
            font-size: 1.6rem;
            font-weight: 600;
            color: #2C3E50;
        }
        .profile-role {
            font-size: 0.95rem;
            color: #888;
            margin-bottom: 20px;
        }
        .info-box {
            text-align: left;
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px 20px;
            margin-top: 10px;
        }
        .info-item {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .info-item span:first-child {
            font-weight: 500;
            color: #444;
        }
        .btn-group {
            margin-top: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .btn-custom {
            border: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            color: white;
        }
        .btn-participations { background: #2ecc71; }
        .btn-participations:hover { background: #27ae60; }

        .btn-activites { background: #f39c12; }
        .btn-activites:hover { background: #e67e22; }

        .btn-evenements { background: #e67e22; }
        .btn-evenements:hover { background: #d35400; }

        .btn-notifications { background: #9b59b6; }
        .btn-notifications:hover { background: #8e44ad; }

        .btn-modifier { background: #5563DE; }
        .btn-modifier:hover { background: #4451C5; }

        @media (max-width: 500px) {
            .profile-container { padding: 25px; }
            .profile-name { font-size: 1.3rem; }
            .btn-group { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <img src="<?= htmlspecialchars($client['PHOTO_PROFILE']) ?>" alt="Photo de profil" class="profile-img">
    <h2 class="profile-name"><?= htmlspecialchars($client['PRENOM_CLI'] . ' ' . $client['NOM_CLI']) ?></h2>
    <p class="profile-role"><?= ucfirst(htmlspecialchars($client['STATUT_CLI'])) ?></p>

    <div class="info-box">
        <div class="info-item">
            <span>Email :</span>
            <span><?= htmlspecialchars($client['EMAIL_CLI']) ?></span>
        </div>
        <div class="info-item">
            <span>Téléphone :</span>
            <span><?= htmlspecialchars($client['TEL_CLI']) ?: 'Non renseigné' ?></span>
        </div>
    </div>

    <div class="btn-group">
        <button class="btn-custom btn-modifier" onclick="window.location.href='modifier_profil.php'">Modifier Profil</button>
        <button class="btn-custom btn-participations" onclick="window.location.href='participations.php'">Participations</button>
        <button class="btn-custom btn-notifications" onclick="window.location.href='notifications.php'">Notifications</button>
    </div>
     <div class="btn-group">
        <button class="btn-custom" onclick="window.location.href='index.php'">Home</button>
     </div>
</div>

</body>
</html>
