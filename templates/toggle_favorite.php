<?php
session_start();
include "db.php";

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['idcli'])) {
    http_response_code(401);
    $response['message'] = 'Veuillez vous connecter pour gérer vos favoris.';
    echo json_encode($response);
    exit;
}

$client_id = $_SESSION['idcli'];

$event_id = isset($_POST['eventId']) ? (int)$_POST['eventId'] : 0;
$action = $_POST['action'] ?? '';

if (!$event_id || !in_array($action, ['add', 'remove'])) {
    http_response_code(400);
    $response['message'] = 'Données manquantes ou action non valide.';
    echo json_encode($response);
    exit;
}

try {
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO FAVORIS (ID_CLIENT, ID_ACT_EV) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE ID_CLIENT = ID_CLIENT");
        if ($stmt->execute([$client_id, $event_id])) {
            $response['success'] = true;
            $response['message'] = 'Ajouté aux favoris !';
        } else {
            $response['message'] = 'Impossible d\'ajouter aux favoris.';
        }
    } else { 
        $stmt = $db->prepare("DELETE FROM FAVORIS WHERE ID_CLIENT = ? AND ID_ACT_EV = ?");
        if ($stmt->execute([$client_id, $event_id])) {
            $response['success'] = true;
            $response['message'] = 'Retiré des favoris !';
        } else {
            $response['message'] = 'Impossible de retirer des favoris.';
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    $response['message'] = 'Erreur de base de données.';
    error_log("Erreur FAVORIS DB: " . $e->getMessage());
}

echo json_encode($response);
