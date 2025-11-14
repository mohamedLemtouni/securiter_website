<?php
session_start();
include("db.php");

if (!isset($_GET["value"])) {
    exit("Paramètre 'value' manquant.");
}

$value = $_GET["value"];

function afficherMessage($message, $lien = "connexion.php", $type = "info") {
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $lien = htmlspecialchars($lien, ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');

    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <title>Message</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f0f0f0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
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
            }).then(() => {
                window.location.href = '$lien';
            });
        </script>
    </body>
    </html>";
    exit;
}

switch ($value) {
    case "conn":
        if (!empty($_POST["email"]) && !empty($_POST["password"])) {
            $email = strtolower(trim($_POST["email"]));
            $password = $_POST["password"];

            $cmd = $db->prepare("SELECT * FROM CLIENT WHERE EMAIL_CLI = :mail");
            $cmd->execute([":mail" => $email]);
            $client = $cmd->fetch(PDO::FETCH_ASSOC);

            if (!$client) {
                afficherMessage("Mail inconnue", "connexion.php", "error");
            }

            if ($client["STATUT_CLI"] === "inactif") {
                afficherMessage("Compte désactivé !", "connexion.php", "warning");
            }

            if (password_verify($password, $client["MDP_CLI"])) {
                $_SESSION["idcli"] = $client["ID_CLIENT"];
                afficherMessage("Connexion réussie !", "index.php", "success");
            } else {
                afficherMessage("Mot de passe incorrect !", "connexion.php", "error");
            }
        } else {
            afficherMessage("Veuillez remplir tous les champs de connexion !", "connexion.php", "warning");
        }
        break;

    case "inscription":
        if (!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["numtel"])) {
            $nom = trim($_POST["nom"]);
            $prenom = trim($_POST["prenom"]);
            $email = strtolower(filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL));
            $tel = trim($_POST["numtel"]);
            $password = $_POST["password"];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $cmd = $db->prepare("SELECT 1 FROM CLIENT WHERE EMAIL_CLI = :mail");
            $cmd->execute([":mail" => $email]);

            if ($cmd->fetch()) {
                afficherMessage("Vous êtes déjà inscrit !", "connexion.php", "info");
            }

            $insert = $db->prepare("INSERT INTO CLIENT (NOM_CLI, PRENOM_CLI, EMAIL_CLI, TEL_CLI, MDP_CLI)
                                    VALUES (:nom, :prenom, :mail, :tel, :pwd)");
            $insert->execute([
                ":nom" => $nom,
                ":prenom" => $prenom,
                ":mail" => $email,
                ":tel" => $tel,
                ":pwd" => $hashedPassword,
            ]);

            afficherMessage("Inscription réussie ! Vous pouvez maintenant vous connecter.", "connexion.php", "success");
        } else {
            afficherMessage("Veuillez remplir tous les champs de l'inscription !", "inscription.php", "warning");
        }
        break;

    default:
        afficherMessage("Action inconnue !", "connexion.php", "error");
}
?>
