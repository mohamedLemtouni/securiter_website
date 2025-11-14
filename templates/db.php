<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$rootUser = "root";
$rootPassword = "mon_vrai_mdp";
$appUser = "appuser";
$appPassword = "monmdp";
$dbFile = "db.sql";      // Création des tables
$dataFile = "data.sql";  // Insertion des 100 entrées

try {
    try {
        // Connexion avec l'utilisateur applicatif
        $db = new PDO("mysql:host=localhost;dbname=travel_db;charset=utf8", $appUser, $appPassword);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Connexion avec root pour initialiser la base
        $rootPdo = new PDO("mysql:host=localhost;charset=utf8", $rootUser, $rootPassword);
        $rootPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Création de l'utilisateur applicatif
        $rootPdo->exec("CREATE USER IF NOT EXISTS '$appUser'@'localhost' IDENTIFIED WITH mysql_native_password BY '$appPassword';");
        $rootPdo->exec("GRANT ALL PRIVILEGES ON travel_db.* TO '$appUser'@'localhost';");
        $rootPdo->exec("FLUSH PRIVILEGES;");

        // Création de la base
        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS travel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        // Exécution du script de création des tables
        if (!file_exists($dbFile)) throw new Exception("Fichier $dbFile introuvable !");
        $sqlDb = file_get_contents($dbFile);
        $rootPdo->exec("USE travel_db;");
        $rootPdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $rootPdo->exec($sqlDb);
        $rootPdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Exécution du script d'insertion des données
        if (!file_exists($dataFile)) throw new Exception("Fichier $dataFile introuvable !");
        $sqlData = file_get_contents($dataFile);
        $rootPdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $rootPdo->exec($sqlData);
        $rootPdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Connexion finale avec l'utilisateur applicatif
        $db = new PDO("mysql:host=localhost;dbname=travel_db;charset=utf8", $appUser, $appPassword);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    die("Erreur PDO : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
