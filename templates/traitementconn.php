<?php
session_start();
include("db.php");

if (isset($_GET["value"])) {
    $value = $_GET["value"];
} else {
    echo "value pas transmis";
}

function afficherMessage($message, $lien = "connexion.php", $type = "info") {
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <title>Message</title>
        <!-- Import SweetAlert2 -->
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                title: 'Information',
                text: '$message',
                icon: '$type',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '$lien';
                }
            });
        </script>
    </body>
    </html>";
    exit();
}

if ($value == "conn") {
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {

        $email = strtolower(trim($_POST["email"])); // nettoyer l'email
        $password = $_POST["password"];

        $cmd = $db->prepare("SELECT * FROM CLIENT WHERE EMAIL_CLI=:mail");
        $cmd->execute([":mail" => $email]);
        $client = $cmd->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            afficherMessage("Ce mail n'est pas inscrit !", "connexion.php", "error");
        } elseif ($client["STATUT_CLI"] == "inactif") {
            afficherMessage("Compte désactivé !", "connexion.php", "warning");
        } elseif (password_verify($password, $client["MDP_CLI"])) {
            $_SESSION["idcli"] = $client["ID_CLIENT"];
            afficherMessage("Connexion réussie !", "index.php", "success");
        } else {
            afficherMessage("Mot de passe incorrect !", "connexion.php", "error");
        }

    } else {
        afficherMessage("Veuillez remplir tous les champs de connexion !", "connexion.php", "warning");
    }

} elseif ($value == "inscription") {
    if (!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["numtel"])) {

        $nom = trim($_POST["nom"]);
        $prenom = trim($_POST["prenom"]);
        $email = strtolower(trim($_POST["email"]));
        $tel = trim($_POST["numtel"]);
        $password = $_POST["password"];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $cmd = $db->prepare("SELECT * FROM CLIENT WHERE EMAIL_CLI=:mail");
        $cmd->execute([":mail" => $email]);

        if ($cmd->fetch()) {
            afficherMessage("Vous êtes déjà inscrit !", "connexion.php", "info");
        } else {
            $db->prepare("INSERT INTO CLIENT (NOM_CLI, PRENOM_CLI, EMAIL_CLI, TEL_CLI, MDP_CLI)
                          VALUES (:nom, :prenom, :mail, :tel, :pwd)")
               ->execute([
                   ":nom" => $nom,
                   ":prenom" => $prenom,
                   ":mail" => $email,
                   ":tel" => $tel,
                   ":pwd" => $hashedPassword,
               ]);

            afficherMessage("Inscription réussie ! Vous pouvez maintenant vous connecter.", "connexion.php", "success");
        }

    } else {
        afficherMessage("Veuillez remplir tous les champs de l'inscription !", "inscription.php", "warning");
    }

} else {
    afficherMessage("Action inconnue !", "connexion.php", "error");
}
?>
