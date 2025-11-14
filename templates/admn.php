<?php 
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panneau d'administration</title>
<style>
/* ====== RESET ====== */
* { margin:0; padding:0; box-sizing:border-box; font-family:"Poppins", sans-serif; }
body { display:flex; min-height:100vh; background:#f5f6fa; }

/* ====== SIDEBAR ====== */
.sidebar {
    width:250px; background:#1e1e2d; color:#fff;
    display:flex; flex-direction:column; padding:20px;
}
.sidebar h2 { text-align:center; margin-bottom:40px; font-size:1.5em; letter-spacing:1px; }
.sidebar a { text-decoration:none; color:#c0c0c0; padding:12px 15px; border-radius:10px; transition:.3s; display:block; cursor:pointer;}
.sidebar a:hover, .sidebar a.active { background:#3b3b54; color:#fff; }

/* ====== MAIN CONTENT ====== */
.main-content { flex:1; padding:20px; }

/* ====== HEADER ====== */
.header { background:#fff; padding:15px 25px; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.05); margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;}
.header h1 { font-size:1.5em; color:#333; }
.header button { background:#1e1e2d; color:white; border:none; padding:10px 18px; border-radius:8px; cursor:pointer; transition:.3s; }
.header button:hover { background:#3b3b54; }

/* ====== CARDS ====== */
.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:30px;}
.card { background:#fff; padding:20px; border-radius:15px; box-shadow:0 3px 8px rgba(0,0,0,0.05); }
.card h3 { font-size:1.1em; color:#555; }
.card p { font-size:2em; margin-top:10px; color:#1e1e2d; }

/* ====== TABLE ====== */
.table-section { background:#fff; padding:20px; border-radius:15px; box-shadow:0 3px 8px rgba(0,0,0,0.05); margin-bottom:30px; }
table { width:100%; border-collapse:collapse; }
th, td { text-align:left; padding:12px; border-bottom:1px solid #eee; }
th { background:#f9f9f9; }
tr:hover { background:#f5f5f5; }

/* ====== BUTTONS ====== */
.edit-btn { background:#1e1e2d; color:#fff; padding:6px 12px; border-radius:6px; text-decoration:none; font-size:0.85em; transition:.3s; cursor:pointer; }
.edit-btn:hover { background:#3b3b54; }
.edit-btn.delete { background:#b32d2e; }
.edit-btn.delete:hover { background:#d94546; }

/* ====== TABS ====== */
.tab { display:none; }
.tab.active { display:block; }

form input, form select, form textarea { display:block; margin-bottom:12px; padding:8px; width:300px; border-radius:6px; border:1px solid #ccc; }
form button { padding:8px 16px; border:none; background:#1e1e2d; color:white; border-radius:6px; cursor:pointer; transition:.3s; }
form button:hover { background:#3b3b54; }

@media(max-width:768px){.sidebar{display:none;}.main-content{padding:10px;}}

/* ====== IMAGES ACTIVITÉ ====== */
.activity-images {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.activity-images img {
    width: 140px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.activity-images img:hover {
    transform: scale(1.05);
}


</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a class="<?= (!isset($_GET['tab']) || $_GET['tab']=='dashboard') ? 'active' : '' ?>" onclick="switchTab('dashboard', this)">Dashboard</a>
    <a class="<?= (isset($_GET['tab']) && $_GET['tab']=='users') ? 'active' : '' ?>" onclick="switchTab('users', this)">Utilisateurs</a>
    <a class="<?= (isset($_GET['tab']) && $_GET['tab']=='activities') ? 'active' : '' ?>" onclick="switchTab('activities', this)">Activités & Événements</a>
    <a href="index.php">Main Page</a>
</div>

<div class="main-content">

    <div class="header">
        <h1>Panneau d'administration</h1>
    </div>

<?php
$cmd_cli = $db->prepare("SELECT * FROM CLIENT");
$cmd_cli->execute();
$result_cli = $cmd_cli->fetchAll(PDO::FETCH_ASSOC);
$nb_client = $cmd_cli->rowCount();

$cmd_part = $db->prepare("SELECT * FROM PARTICIPATION");
$cmd_part->execute();
$nb_part = $cmd_part->rowCount();

$cmd_prix = $db->prepare("SELECT SUM(PRIX_TOTAL) AS total_prix FROM PARTICIPATION WHERE STATUT_PART != 'annulee'");
$cmd_prix->execute();
$prix_t = $cmd_prix->fetch(PDO::FETCH_ASSOC)["total_prix"] ?? 0;
$prix_t_formatted = number_format($prix_t, 2, ',', ' ');

$cmd_activ = $db->prepare("SELECT * FROM ACTIVITE_EVENEMENT ORDER BY DATE_DEBUT DESC");
$cmd_activ->execute();
$result_activ = $cmd_activ->fetchAll(PDO::FETCH_ASSOC);
$edit_user = null;

if (isset($_GET['edit_user'])) {
    $id = (int) $_GET['edit_user'];
    $stmt = $db->prepare("SELECT * FROM CLIENT WHERE ID_CLIENT = ?");
    $stmt->execute([$id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}

$edit_activity = null;
if (isset($_GET['edit_activity'])) {
    $id = (int) $_GET['edit_activity'];
    $stmt = $db->prepare("SELECT * FROM ACTIVITE_EVENEMENT WHERE ID_ACT_EV = ?");
    $stmt->execute([$id]);
    $edit_activity = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<div id="dashboard" class="tab <?= (!isset($_GET['tab']) || $_GET['tab']=='dashboard') ? 'active' : '' ?>">
    <div class="cards">
        <div class="card"><h3>Utilisateurs inscrits</h3><p><?= $nb_client ?></p></div>
        <div class="card"><h3>Participations</h3><p><?= $nb_part ?></p></div>
        <div class="card"><h3>Revenus</h3><p><?= $prix_t_formatted ?> MAD</p></div>
    </div>
</div>
<div id="users" class="tab <?= (isset($_GET['tab']) && $_GET['tab']=='users') ? 'active' : '' ?>">
    <div class="table-section">
        <h2>Liste des utilisateurs</h2>
        <table>
            <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($result_cli as $user){ ?>
                <tr>
                    <td><?= $user['ID_CLIENT'] ?></td>
                    <td><?= $user['NOM_CLI']." ".$user['PRENOM_CLI'] ?></td>
                    <td><?= $user['EMAIL_CLI'] ?></td>
                    <td><?= $user['TEL_CLI'] ?></td>
                    <td><?= $user['STATUT_CLI'] ?></td>
                    <td>
                        <a href="admn.php?tab=users&edit_user=<?= $user['ID_CLIENT'] ?>" class="edit-btn">Modifier</a>
                        <a href="admin_traitement.php?value=delete_user&id=<?= $user['ID_CLIENT'] ?>" class="edit-btn delete" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($edit_user): ?>
    <div class="table-section">
        <h2>Modifier Utilisateur</h2>
        <form action="admin_traitement.php?value=update_user" method="POST">
            <input type="hidden" name="ID_CLIENT" value="<?= $edit_user['ID_CLIENT'] ?>">
            <label>Nom :</label><input type="text" name="NOM_CLI" value="<?= htmlspecialchars($edit_user['NOM_CLI']) ?>" required>
            <label>Prénom :</label><input type="text" name="PRENOM_CLI" value="<?= htmlspecialchars($edit_user['PRENOM_CLI']) ?>" required>
            <label>Email :</label><input type="email" name="EMAIL_CLI" value="<?= htmlspecialchars($edit_user['EMAIL_CLI']) ?>" required>
            <label>Téléphone :</label><input type="text" name="TEL_CLI" value="<?= htmlspecialchars($edit_user['TEL_CLI']) ?>">
            <label>Statut :</label>
            <select name="STATUT_CLI">
                <option value="normal" <?= ($edit_user['STATUT_CLI']=='normal')?'selected':'' ?>>Normal</option>
                <option value="admin" <?= ($edit_user['STATUT_CLI']=='admin')?'selected':'' ?>>Admin</option>
                <option value="inactif" <?= ($edit_user['STATUT_CLI']=='inactif')?'selected':'' ?>>Inactif</option>
                <option value="partenaire" <?= ($edit_user['STATUT_CLI']=='partenaire')?'selected':'' ?>>Partenaire</option>
            </select>
            <button type="submit">Enregistrer</button>
        </form>
    </div>
    <?php endif; ?> 
</div>
<div id="activities" class="tab <?= (isset($_GET['tab']) && $_GET['tab']=='activities') ? 'active' : '' ?>">
    <div class="table-section">
        <h2>Liste des activités et événements</h2>
        <a href="admn.php?tab=activities&add_activity=1" class="edit-btn" style="margin-bottom:15px;display:inline-block;">+ Ajouter</a>
        <table>
            <thead>
            <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Type</th>
            <th>Date début</th>
            <th>Prix</th>
            <th>Visibile</th>
            <th>Action</th>
        </tr></thead>
            <tbody>
            <?php foreach($result_activ as $act){ ?>
                <tr>
                    <td><?= $act['ID_ACT_EV'] ?></td>
                    <td><?= htmlspecialchars($act['NOM']) ?></td>
                    <td><?= $act['TYPE_ACT_EVENT'] ?></td>
                    <td><?= $act['DATE_DEBUT'] ?></td>
                    <td><?= $act['PRIX'] ?> MAD</td>
                    <td><?= $act['VISIBLE'] ? 'oui':'non'; ?></td>
                    <td>
                        <a href="admn.php?tab=activities&edit_activity=<?= $act['ID_ACT_EV'] ?>" class="edit-btn">Modifier</a>
                        <a href="admin_traitement.php?value=delete_activity&id=<?= $act['ID_ACT_EV'] ?>" class="edit-btn delete" onclick="return confirm('Supprimer cette activité ?')">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($edit_activity || isset($_GET['add_activity'])): ?>
    <div class="table-section">
        <h2><?= $edit_activity ? 'Modifier une activité / événement' : 'Ajouter une activité / événement' ?></h2>
        <form action="admin_traitement.php?value=save_activity" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ID_ACT_EV" value="<?= $edit_activity['ID_ACT_EV'] ?? '' ?>">
            <label>Nom :</label><input type="text" name="NOM" value="<?= htmlspecialchars($edit_activity['NOM'] ?? '') ?>" required>
            <label>Description courte :</label><input type="text" name="DESCRIPTION_COURTE" value="<?= htmlspecialchars($edit_activity['DESCRIPTION_COURTE'] ?? '') ?>">
            <label>Description complète :</label><textarea name="DESCRIPTION_CMPLT" rows="4"><?= htmlspecialchars($edit_activity['DESCRIPTION_CMPLT'] ?? '') ?></textarea>
            <label>Type :</label>
            <select name="TYPE_ACT_EVENT" required>
                <option value="activite" <?= (isset($edit_activity['TYPE_ACT_EVENT']) && $edit_activity['TYPE_ACT_EVENT']=='activite')?'selected':'' ?>>Activité</option>
                <option value="evenement" <?= (isset($edit_activity['TYPE_ACT_EVENT']) && $edit_activity['TYPE_ACT_EVENT']=='evenement')?'selected':'' ?>>Événement</option>
            </select>
            <label>Date début :</label><input type="datetime-local" name="DATE_DEBUT" value="<?= isset($edit_activity['DATE_DEBUT']) ? date('Y-m-d\TH:i', strtotime($edit_activity['DATE_DEBUT'])) : '' ?>" required>
            <label>Date fin :</label><input type="datetime-local" name="DATE_FIN" value="<?= isset($edit_activity['DATE_FIN']) ? date('Y-m-d\TH:i', strtotime($edit_activity['DATE_FIN'])) : '' ?>">
            <label>Visible :</label>
            <select name="VISIBLE" required>
                <option value="1" <?= (isset($edit_activity['VISIBLE']) && $edit_activity['VISIBLE'] == 1) ? 'selected' : '' ?>>Oui (Visible)</option>
                <option value="0" <?= (isset($edit_activity['VISIBLE']) && $edit_activity['VISIBLE'] == 0) ? 'selected' : '' ?>>Non (Masqué)</option>
            </select>

            <label>Organisateur :</label>
            <select name="ID_CLIENT_ORGANISATEUR">
                <?php foreach($result_cli as $cli){ ?>
                    <option value="<?= $cli['ID_CLIENT'] ?>" <?= (isset($edit_activity['ID_CLIENT_ORGANISATEUR']) && $edit_activity['ID_CLIENT_ORGANISATEUR']==$cli['ID_CLIENT'])?'selected':'' ?>>
                        <?= $cli['NOM_CLI']." ".$cli['PRENOM_CLI'] ?>
                    </option>
                <?php } ?>
            </select>
            <label>Prix :</label><input type="number" step="0.01" name="PRIX" value="<?= htmlspecialchars($edit_activity['PRIX'] ?? '') ?>" required>
            <label>Capacité :</label><input type="number" name="CAPACITE" value="<?= htmlspecialchars($edit_activity['CAPACITE'] ?? '') ?>">
            <label>Difficulté :</label>
            <select name="DIFFICULTE">
                <option value="facile" <?= (isset($edit_activity['DIFFICULTE']) && $edit_activity['DIFFICULTE']=='facile')?'selected':'' ?>>Facile</option>
                <option value="moyen" <?= (isset($edit_activity['DIFFICULTE']) && $edit_activity['DIFFICULTE']=='moyen')?'selected':'' ?>>Moyen</option>
                <option value="difficile" <?= (isset($edit_activity['DIFFICULTE']) && $edit_activity['DIFFICULTE']=='difficile')?'selected':'' ?>>Difficile</option>
            </select>
            <label>Public cible :</label>
            <select name="PUBLIC_CIBLE">
                <option value="tous" <?= (isset($edit_activity['PUBLIC_CIBLE']) && $edit_activity['PUBLIC_CIBLE']=='tous')?'selected':'' ?>>Tous</option>
                <option value="enfant" <?= (isset($edit_activity['PUBLIC_CIBLE']) && $edit_activity['PUBLIC_CIBLE']=='enfant')?'selected':'' ?>>Enfant</option>
                <option value="adulte" <?= (isset($edit_activity['PUBLIC_CIBLE']) && $edit_activity['PUBLIC_CIBLE']=='adulte')?'selected':'' ?>>Adulte</option>
            </select>
            <label>Tags :</label><input type="text" name="TAGS" value="<?= htmlspecialchars($edit_activity['TAGS'] ?? '') ?>">
            <label>Images :</label><input type="file" name="images[]" multiple accept="image/*">
            <button type="submit">Enregistrer</button>
        </form>
        <?php if (!is_null($edit_activity)){?>
        <p style="margin-top: 1%;">Images deja existantes: </p>
        <div class="activity-images">
            <?php $pics = $db->prepare("select * from IMAGE_ACTIVITE where ID_ACT_EV = :id ;"); 
                    $pics->execute(["id" => $edit_activity["ID_ACT_EV"]]);
                    $allpics = $pics->fetchAll(PDO::FETCH_ASSOC);
                    if($allpics){
                        foreach($allpics as $pic){
                            ?> <div style="display: flex;flex-direction: column;align-items: center;">
                            <img src="<?= htmlspecialchars($pic['URL_IMAGE']) ?>" >
                            <a href="./delet_image.php?id_img=<?php echo $pic['ID_IMAGE']?>">
                                <button style="margin-top: 20%;"> supprimer l'image</button>
                            </a></div><?php
                        }
                    
                }}
                    ?>
                    </div>
    </div>
    <?php endif; ?>
</div>

</div>

<script>
// Garder uniquement le switchTab
function switchTab(id, el){
    document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.sidebar a').forEach(a=>a.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    el.classList.add('active');
}
</script>

</body>
</html>
