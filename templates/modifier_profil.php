<?php
session_start();
include("db.php");

// 🔒 Vérifier la connexion utilisateur
if (!isset($_SESSION["idcli"])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script>
          Swal.fire({
              title: 'Accès refusé',
              text: 'Vous devez vous connecter pour accéder à cette page.',
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

// 💡 Valeurs sécurisées avec fallback
$photo = !empty($client['PHOTO_PROFILE']) ? $client['PHOTO_PROFILE'] : './photos/profilpic/profiledefault.jpg';
$prenom = htmlspecialchars($client['PRENOM_CLI']);
$nom = htmlspecialchars($client['NOM_CLI']);
$email = htmlspecialchars($client['EMAIL_CLI']);
$tel = htmlspecialchars($client['TEL_CLI'] ?: '');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8; /* fond clair */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .edit-container {
            background: white;
            border-radius: 12px;
            padding: 40px 30px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
        }

        .edit-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #1A3D7C; /* bleu azure foncé */
        }

        .profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1A3D7C; /* accent bleu */
            display: block;
            margin: 0 auto 20px auto;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px 12px;
        }

        .btn-save {
            background-color: #1A3D7C;
            color: white;
            font-weight: 500;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            transition: 0.3s;
        }
        .btn-save:hover {
            background-color: #142d5c;
        }

        @media (max-width: 500px) {
            .edit-container { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Modifier votre profil</h2>
    <img src="<?= $photo ?>" alt="Photo Profil" class="profile-img">

    <form action="update_profil.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $prenom ?>" required>
        </div>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= $nom ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" required>
        </div>
        <div class="mb-3">
            <label for="tel" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="tel" name="tel" value="<?= $tel ?>">
        </div>
        <div class="mb-4">
            <label for="photo" class="form-label">Photo de profil</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
        </div>

        <button type="submit" class="btn btn-save">Enregistrer les modifications</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
