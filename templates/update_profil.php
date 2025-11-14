<?php
session_start();
include("db.php");

if (!isset($_SESSION["idcli"])) {
    $redirect = 'connexion.php';
    echo '<!DOCTYPE html><html><head>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          </head><body>
          <script>
          Swal.fire({
              title: "Accès refusé",
              text: "Vous devez vous connecter pour modifier votre profil.",
              icon: "warning",
              confirmButtonText: "Se connecter"
          }).then(() => {
              window.location.href = "'.$redirect.'";
          });
          </script>
          </body></html>';
    exit();
}

$idcli = $_SESSION["idcli"];

// Récupérer les infos actuelles du client
$cmd = $db->prepare("SELECT * FROM CLIENT WHERE ID_CLIENT = :id");
$cmd->execute([":id" => $idcli]);
$client = $cmd->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    $redirect = 'profil.php';
    echo '<!DOCTYPE html><html><head>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          </head><body>
          <script>
          Swal.fire({
              title: "Erreur",
              text: "Profil introuvable.",
              icon: "error",
              confirmButtonText: "OK"
          }).then(() => {
              window.location.href = "'.$redirect.'";
          });
          </script>
          </body></html>';
    exit();
}

// Récupérer les nouvelles valeurs
$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$tel = trim($_POST['tel'] ?? '');

// Gestion de la photo
$photo = $client['PHOTO_PROFILE'];
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['photo']['tmp_name'];
    $fileName = basename($_FILES['photo']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];

    if (in_array($fileExt, $allowed)) {
        $newFileName = 'photos/profilpic/' . uniqid() . '.' . $fileExt;
        if (move_uploaded_file($fileTmp, $newFileName)) {
            $photo = $newFileName;
        }
    }
}

// Mise à jour
$update = $db->prepare("
    UPDATE CLIENT 
    SET PRENOM_CLI = :prenom, NOM_CLI = :nom, EMAIL_CLI = :email, TEL_CLI = :tel, PHOTO_PROFILE = :photo
    WHERE ID_CLIENT = :id
");

try {
    $update->execute([
        ':prenom' => $prenom,
        ':nom' => $nom,
        ':email' => $email,
        ':tel' => $tel,
        ':photo' => $photo,
        ':id' => $idcli
    ]);

    $redirect = 'profil.php';
    $title = 'Succès';
    $text = 'Profil mis à jour avec succès !';
    $icon = 'success';
} catch (PDOException $e) {
    $redirect = 'modifier_profil.php';
    $title = 'Erreur';
    $text = 'Impossible de mettre à jour le profil. Veuillez réessayer.';
    $icon = 'error';
}

// Afficher l'alerte avec HTML complet
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    title: "'.$title.'",
    text: "'.$text.'",
    icon: "'.$icon.'",
    confirmButtonText: "OK"
}).then(() => {
    window.location.href = "'.$redirect.'";
});
</script>
</body>
</html>';
