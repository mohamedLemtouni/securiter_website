<?php
session_start();
include "db.php";

// === MISE À JOUR UTILISATEUR ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_GET["value"] == "update_user") {
    $id = intval($_POST["ID_CLIENT"]);
    $nom = trim($_POST["NOM_CLI"]);
    $prenom = trim($_POST["PRENOM_CLI"]);
    $email = trim($_POST["EMAIL_CLI"]);
    $tel = trim($_POST["TEL_CLI"]);
    $statut = $_POST["STATUT_CLI"];

    if (!$id || !$nom || !$prenom || !$email) {
        echo "<script>alert('Champs manquants');history.back();</script>";exit;
    }

    $stmt = $db->prepare("UPDATE CLIENT SET NOM_CLI=?, PRENOM_CLI=?, EMAIL_CLI=?, TEL_CLI=?, STATUT_CLI=? WHERE ID_CLIENT=?");
    $ok = $stmt->execute([$nom,$prenom,$email,$tel,$statut,$id]);
    echo "<script>alert('".($ok?"✅ Utilisateur mis à jour":"❌ Erreur")."');location.href='admn.php';</script>";exit;
}

// === SUPPRESSION UTILISATEUR ===
if ($_GET["value"] == "delete_user" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    // Supprimer ses participations
    $db->prepare("DELETE FROM PARTICIPATION WHERE ID_CLIENT=?")->execute([$id]);
    // Supprimer ses activités et images
    $acts = $db->prepare("SELECT ID_ACT_EV FROM ACTIVITE_EVENEMENT WHERE ID_CLIENT_ORGANISATEUR=?");
    $acts->execute([$id]);
    foreach($acts->fetchAll(PDO::FETCH_ASSOC) as $a){
        $aid = $a["ID_ACT_EV"];
        $imgs = $db->prepare("SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV=?");
        $imgs->execute([$aid]);
        foreach($imgs->fetchAll(PDO::FETCH_ASSOC) as $img){
            if(file_exists($img["URL_IMAGE"])) unlink($img["URL_IMAGE"]);
        }
        $db->prepare("DELETE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV=?")->execute([$aid]);
        $db->prepare("DELETE FROM ACTIVITE_EVENEMENT WHERE ID_ACT_EV=?")->execute([$aid]);
    }
    $ok = $db->prepare("DELETE FROM CLIENT WHERE ID_CLIENT=?")->execute([$id]);
    echo json_encode(["success"=>$ok]);exit;
}

// === AJOUT / MODIF ACTIVITE ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_GET["value"] == "save_activity") {
    $id = intval($_POST["ID_ACT_EV"]);
    $data = [
        $_POST["NOM"], $_POST["DESCRIPTION_COURTE"], $_POST["DESCRIPTION_CMPLT"],
        $_POST["TYPE_ACT_EVENT"], $_POST["DATE_DEBUT"], $_POST["DATE_FIN"],
        $_POST["ID_CLIENT_ORGANISATEUR"], $_POST["PRIX"], $_POST["CAPACITE"],
        $_POST["DIFFICULTE"], $_POST["PUBLIC_CIBLE"], $_POST["TAGS"]
    ];

    if ($id > 0) {
        $sql = "UPDATE ACTIVITE_EVENEMENT SET NOM=?, DESCRIPTION_COURTE=?, DESCRIPTION_CMPLT=?, TYPE_ACT_EVENT=?, DATE_DEBUT=?, DATE_FIN=?, ID_CLIENT_ORGANISATEUR=?, PRIX=?, CAPACITE=?, DIFFICULTE=?, PUBLIC_CIBLE=?, TAGS=? WHERE ID_ACT_EV=?";
        $data[] = $id;
    } else {
        $sql = "INSERT INTO ACTIVITE_EVENEMENT (NOM,DESCRIPTION_COURTE,DESCRIPTION_CMPLT,TYPE_ACT_EVENT,DATE_DEBUT,DATE_FIN,ID_CLIENT_ORGANISATEUR,PRIX,CAPACITE,DIFFICULTE,PUBLIC_CIBLE,TAGS) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    }

    $stmt = $db->prepare($sql);
    $ok = $stmt->execute($data);
    $new_id = $id ?: $db->lastInsertId();

    // Upload images
    if (!empty($_FILES["images"]["name"][0])) {
        foreach ($_FILES["images"]["tmp_name"] as $k => $tmp) {
            if (!$_FILES["images"]["error"][$k]) {
                $dir = "uploads/"; if(!is_dir($dir)) mkdir($dir);
                $path = $dir.basename(time()."_".$_FILES["images"]["name"][$k]);
                move_uploaded_file($tmp, $path);
                $db->prepare("INSERT INTO IMAGE_ACTIVITE (ID_ACT_EV,URL_IMAGE) VALUES (?,?)")->execute([$new_id,$path]);
            }
        }
    }

    echo "<script>alert('Activité enregistrée');location.href='admn.php?tab=activities';</script>";exit;
}

// === SUPPRESSION ACTIVITE ===
if ($_GET["value"] == "delete_activity" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $stmtImgs = $db->prepare("SELECT URL_IMAGE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV=?");
    $stmtImgs->execute([$id]);
    foreach ($stmtImgs->fetchAll(PDO::FETCH_ASSOC) as $img) {
        if (file_exists($img["URL_IMAGE"])) unlink($img["URL_IMAGE"]);
    }
    $db->prepare("DELETE FROM IMAGE_ACTIVITE WHERE ID_ACT_EV=?")->execute([$id]);
    $ok = $db->prepare("DELETE FROM ACTIVITE_EVENEMENT WHERE ID_ACT_EV=?")->execute([$id]);
    echo json_encode(["success"=>$ok]);exit;
}

echo "<script>alert('Accès non autorisé');location.href='admn.php';</script>";
?>
