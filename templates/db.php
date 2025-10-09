<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$rootUser = "root";
$rootPassword = "mon_vrai_mdp";
$appUser = "appuser";
$appPassword = "monmdp";
$sqlFile = "db.sql";

try {
    // 1️⃣ Essayer de se connecter avec appuser
    try {
        $db = new PDO("mysql:host=localhost;dbname=travel_db;charset=utf8", $appUser, $appPassword);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // La base n'existe pas → créer avec root
        $rootPdo = new PDO("mysql:host=localhost;charset=utf8", $rootUser, $rootPassword);
        $rootPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer l'utilisateur si nécessaire
        $rootPdo->exec("CREATE USER IF NOT EXISTS '$appUser'@'localhost' IDENTIFIED WITH mysql_native_password BY '$appPassword';");
        $rootPdo->exec("GRANT ALL PRIVILEGES ON travel_db.* TO '$appUser'@'localhost';");
        $rootPdo->exec("FLUSH PRIVILEGES;");

        // Créer la base de données
        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS travel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        // Lire et exécuter db.sql
        if (!file_exists($sqlFile)) throw new Exception("Fichier $sqlFile introuvable !");
        $sql = file_get_contents($sqlFile);

        $rootPdo->exec("USE travel_db;");
        $rootPdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $rootPdo->exec($sql);
        $rootPdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Reconnecter avec appuser
        $db = new PDO("mysql:host=localhost;dbname=travel_db;charset=utf8", $appUser, $appPassword);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


} catch (PDOException $e) {
    die("Erreur PDO : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
