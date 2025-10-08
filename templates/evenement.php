<?php 
session_start(); 
include "db.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/static/css/header.css">
    <link rel="stylesheet" href="/static/css/footer.css">
    <link rel="stylesheet" href="/static/css/event.css">
    <title>Événements</title>
</head>
<body>
    <?php include("header.php")?>

    <main class="event-container">
        <?php 
        if(!isset($_GET["town"])){
            $cmd = $db->prepare("SELECT * FROM ACTIVITE_EVENEMENT WHERE TYPE = 'evenement';");
            $cmd->execute();
            $nb_ligne = $cmd->rowCount();   

            if($nb_ligne == 0){ ?>
                <div class="no-event">
                    <p>Aucun événement disponible pour le moment.</p>
                </div>
            <?php 
            } else {
                while($even = $cmd->fetch(PDO::FETCH_ASSOC)){ ?>
                    <div class="event-card">
                        <h2 class="event-title"><?php echo htmlspecialchars($even["NOM"]); ?></h2>
                        <p class="event-description"><?php echo htmlspecialchars($even["DESCRIPTION"]); ?></p>
                        <button class="event-button">En savoir plus</button>
                    </div>
                <?php 
                }
            }
        }
        ?>
    </main>

    <?php include("footer.php")?>
</body>
</html>
