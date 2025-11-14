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

function generateRandomCode() {
    if (empty($_SESSION['code'])) {
        $_SESSION['code'] = rand(100000, 999999);
    }
}

switch ($value) {
    case "envoi_du_mail":
        if (empty($_POST["mail"])) {
            afficherMessage("Adresse mail manquante.", "motdepasseoublier.php", "warning");
        }

        $mail = strtolower(trim($_POST["mail"]));
        $checkMail = $db->prepare("SELECT ID_CLIENT FROM CLIENT WHERE EMAIL_CLI = :mail");
        $checkMail->execute([":mail" => $mail]);
        $client = $checkMail->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            afficherMessage("Ce mail n'est pas inscrit.", "connexion.php", "error");
        }

        // Génération du code et stockage
        generateRandomCode();
        $_SESSION["mail"] = $mail;

        // En local : afficher le code à l’écran
        $code = $_SESSION['code'];
        afficherMessage("Code de vérification : $code", "motdepasseoublier.php?code_fourni_mail=true&mailrec=" . urlencode($mail), "info");
        break;

    case "code_fourni_mail":
        if (empty($_POST['code_de_mail'])) {
            afficherMessage("Veuillez entrer le code reçu.", "motdepasseoublier.php", "warning");
        }

        if (!isset($_SESSION['code'])) {
            afficherMessage("Aucun code n'a été généré. Recommencez.", "motdepasseoublier.php", "error");
        }

        if ($_SESSION['code'] == $_POST['code_de_mail']) {
            unset($_SESSION['code']);
            $mailrec = isset($_GET["mailrec"]) ? urlencode($_GET["mailrec"]) : "";
            header("Location: motdepasseoublier.php?nvmdp=true&mailrec=$mailrec");
            exit;
        } else {
            afficherMessage("Code invalide.", "motdepasseoublier.php", "error");
        }
        break;

    case "nvmpd":
        if (empty($_SESSION["mail"]) || empty($_POST["newmdp"])) {
            afficherMessage("Informations manquantes.", "motdepasseoublier.php", "warning");
        }

        try {
            $requid = $db->prepare("SELECT ID_CLIENT FROM CLIENT WHERE EMAIL_CLI = :email");
            $requid->execute([":email" => $_SESSION["mail"]]);
            $idcli = $requid->fetch(PDO::FETCH_ASSOC);

            if (!$idcli) {
                afficherMessage("Client introuvable.", "motdepasseoublier.php", "error");
            }

            $hashedPassword = password_hash($_POST["newmdp"], PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE CLIENT SET MDP_CLI = :pwd WHERE ID_CLIENT = :id");
            $update->execute([
                ":pwd" => $hashedPassword,
                ":id" => $idcli["ID_CLIENT"]
            ]);

            unset($_SESSION["mail"]);
            afficherMessage("Mot de passe changé avec succès !", "connexion.php", "success");
        } catch (Exception $e) {
            afficherMessage("Erreur : " . htmlspecialchars($e->getMessage()), "motdepasseoublier.php", "error");
        }
        break;

    default:
        afficherMessage("Action inconnue.", "connexion.php", "error");
}
?>
