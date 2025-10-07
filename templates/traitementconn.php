<?php
session_destroy();
session_start();
include("db.php");

if (isset($_GET["value"])) {
    $value = $_GET["value"];
} else {
    echo "value pas transmis";
}
function afficherMessage($message, $lien = "connexion.php") {
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <title>Message</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .message-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
            a { color: #007bff; text-decoration: none; font-weight: bold; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class='message-box'>
            <p>$message</p>
            <p><a href='$lien'>Retour</a></p>
        </div>
    </body>
    </html>";
    exit();
}

if ($value == "conn") {
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {
        $cmd = $db->prepare("SELECT * FROM CLIENT WHERE EMAIL_CLI=:mail");
        $cmd->execute([":mail" => $_POST["email"]]);
        $client = $cmd->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            afficherMessage("Ce mail n'est pas inscrit !");
        } elseif ($client["STATUT_CLI"] == "inactif") {
            afficherMessage("Compte désactivé !");
        } elseif (password_verify($_POST["password"], $client["MDP_CLI"])) {
            $_SESSION["idcli"] = $client["ID_CLIENT"];
            afficherMessage("Connexion réussie !", "index.php");
        } else {
            afficherMessage("Mot de passe incorrect !");
        }
    } else {
        afficherMessage("Veuillez remplir tous les champs de connexion !");
    }

} elseif ($value == "inscription") {
    if (!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["numtel"])) {
        $cmd = $db->prepare("SELECT * FROM CLIENT WHERE EMAIL_CLI=:mail");
        $cmd->execute([":mail" => $_POST["email"]]);

        if ($cmd->fetch()) {
            afficherMessage("Vous êtes déjà inscrit !");
        } else {
            $db->prepare("INSERT INTO CLIENT (NOM_CLI, PRENOM_CLI, EMAIL_CLI, TEL_CLI, MDP_CLI)
                          VALUES (:nom, :prenom, :mail, :tel, :pwd)")
               ->execute([
                   ":nom" => $_POST["nom"],
                   ":prenom" => $_POST["prenom"],
                   ":mail" => $_POST["email"],
                   ":tel" => $_POST["numtel"],
                   ":pwd" => password_hash($_POST["password"], PASSWORD_DEFAULT),
               ]);

            afficherMessage("Inscription réussie ! Vous pouvez maintenant vous connecter.");
        }
    } else {
        echo $_POST["nom"] + $_POST["prenom"] + $_POST["email"] + $_POST["password"] + $_POST["numtel"];
        afficherMessage("Veuillez remplir tous les champs de l'inscription !");
    }

} else {
    afficherMessage("Action inconnue !");
}
?>
