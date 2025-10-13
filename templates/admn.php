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
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a class="active" onclick="switchTab('dashboard', this)">Dashboard</a>
    <a onclick="switchTab('users', this)">Utilisateurs</a>
    <a onclick="switchTab('activities', this)">Activités & Événements</a>
    <a href="index.php">Main Page</a>
</div>

<div class="main-content">

    <div class="header">
        <h1>Panneau d'administration</h1>
    </div>

<?php
// ==== DATA ====
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
?>

<!-- ====== TABS ====== -->
<div id="dashboard" class="tab active">
    <div class="cards">
        <div class="card"><h3>Utilisateurs inscrits</h3><p><?= $nb_client ?></p></div>
        <div class="card"><h3>Participations</h3><p><?= $nb_part ?></p></div>
        <div class="card"><h3>Revenus</h3><p><?= $prix_t_formatted ?> MAD</p></div>
    </div>
</div>

<div id="users" class="tab">
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
                        <button class="edit-btn" onclick='editUser(<?= json_encode($user) ?>)'>Modifier</button>
                        <button class="edit-btn delete" onclick='deleteUser(<?= $user["ID_CLIENT"] ?>)'>Supprimer</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="table-section" id="edit-form-section" style="display:none;">
        <h2>Modifier Utilisateur</h2>
        <form action="admin_traitement.php?value=update_user" method="POST">
            <input type="hidden" name="ID_CLIENT" id="ID_CLIENT">
            <label>Nom :</label><input type="text" name="NOM_CLI" id="NOM_CLI" required>
            <label>Prénom :</label><input type="text" name="PRENOM_CLI" id="PRENOM_CLI" required>
            <label>Email :</label><input type="email" name="EMAIL_CLI" id="EMAIL_CLI" required>
            <label>Téléphone :</label><input type="text" name="TEL_CLI" id="TEL_CLI">
            <label>Statut :</label>
            <select name="STATUT_CLI" id="STATUT_CLI">
                <option value="normal">Normal</option><option value="admin">Admin</option>
                <option value="inactif">Inactif</option><option value="partenaire">Partenaire</option>
            </select>
            <button type="submit">Enregistrer</button>
        </form>
    </div>
</div>

<!-- ====== ACTIVITES ====== -->
<div id="activities" class="tab">
    <div class="table-section">
        <h2>Liste des activités et événements</h2>
        <button style="margin-bottom:15px;" onclick="showAddForm()">+ Ajouter</button>
        <table>
            <thead><tr><th>ID</th><th>Nom</th><th>Type</th><th>Date début</th><th>Prix</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($result_activ as $act){ ?>
                <tr>
                    <td><?= $act['ID_ACT_EV'] ?></td>
                    <td><?= htmlspecialchars($act['NOM']) ?></td>
                    <td><?= $act['TYPE_ACT_EVENT'] ?></td>
                    <td><?= $act['DATE_DEBUT'] ?></td>
                    <td><?= $act['PRIX'] ?> MAD</td>
                    <td>
                        <button class="edit-btn" onclick='editActivity(<?= json_encode($act) ?>)'>Modifier</button>
                        <button class="edit-btn delete" onclick='deleteActivity(<?= $act["ID_ACT_EV"] ?>)'>Supprimer</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="table-section" id="activity-form" style="display:none;">
        <h2 id="form-title">Ajouter une activité / événement</h2>
        <form action="admin_traitement.php?value=save_activity" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ID_ACT_EV" id="ID_ACT_EV">
            <label>Nom :</label><input type="text" name="NOM" id="NOM" required>
            <label>Description courte :</label><input type="text" name="DESCRIPTION_COURTE" id="DESCRIPTION_COURTE">
            <label>Description complète :</label><textarea name="DESCRIPTION_CMPLT" id="DESCRIPTION_CMPLT" rows="4"></textarea>
            <label>Type :</label>
            <select name="TYPE_ACT_EVENT" id="TYPE_ACT_EVENT" required>
                <option value="activite">Activité</option><option value="evenement">Événement</option>
            </select>
            <label>Date début :</label><input type="datetime-local" name="DATE_DEBUT" id="DATE_DEBUT" required>
            <label>Date fin :</label><input type="datetime-local" name="DATE_FIN" id="DATE_FIN">
            <label>Organisateur :</label>
            <select name="ID_CLIENT_ORGANISATEUR" id="ID_CLIENT_ORGANISATEUR">
                <?php foreach($result_cli as $cli){ ?>
                    <option value="<?= $cli['ID_CLIENT'] ?>"><?= $cli['NOM_CLI']." ".$cli['PRENOM_CLI'] ?></option>
                <?php } ?>
            </select>
            <label>Prix :</label><input type="number" step="0.01" name="PRIX" id="PRIX" required>
            <label>Capacité :</label><input type="number" name="CAPACITE" id="CAPACITE">
            <label>Difficulté :</label>
            <select name="DIFFICULTE" id="DIFFICULTE">
                <option value="facile">Facile</option><option value="moyen">Moyen</option><option value="difficile">Difficile</option>
            </select>
            <label>Public cible :</label>
            <select name="PUBLIC_CIBLE" id="PUBLIC_CIBLE">
                <option value="tous">Tous</option><option value="enfant">Enfant</option><option value="adulte">Adulte</option>
            </select>
            <label>Tags :</label><input type="text" name="TAGS" id="TAGS">
            <label>Images :</label><input type="file" name="images[]" id="images" multiple accept="image/*">
            <div id="preview-container" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;"></div>
            <button type="submit">Enregistrer</button>
        </form>
    </div>
</div>

</div>

<script>
document.getElementById('images').addEventListener('change', function(e){
    const c=document.getElementById('preview-container'); c.innerHTML='';
    for(const f of e.target.files){
        const i=document.createElement('img'); i.src=URL.createObjectURL(f);
        i.style.width='80px';i.style.height='80px';i.style.objectFit='cover';i.style.borderRadius='8px';
        c.appendChild(i);
    }
});
function switchTab(id,el){document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
document.querySelectorAll('.sidebar a').forEach(a=>a.classList.remove('active'));
document.getElementById(id).classList.add('active');el.classList.add('active');}
function editUser(u){switchTab('users',document.querySelector('.sidebar a[onclick*="users"]'));
document.getElementById('edit-form-section').style.display='block';for(let k in u){if(document.getElementById(k))document.getElementById(k).value=u[k];}}
function showAddForm(){document.getElementById('form-title').textContent="Ajouter une activité / événement";
document.getElementById('activity-form').style.display='block';document.querySelector('#activity-form form').reset();
document.getElementById('ID_ACT_EV').value='';}
function editActivity(a){switchTab('activities',document.querySelector('.sidebar a[onclick*="activities"]'));
document.getElementById('form-title').textContent="Modifier activité / événement";
document.getElementById('activity-form').style.display='block';
for(let k in a){if(document.getElementById(k))document.getElementById(k).value=a[k];}}
function deleteActivity(id){if(confirm("Supprimer cette activité ?"))fetch('admin_traitement.php?value=delete_activity&id='+id)
.then(r=>r.json()).then(d=>{if(d.success){alert('Activité supprimée');location.reload();}else alert('Erreur : '+d.message);});}
function deleteUser(id){if(confirm("Supprimer cet utilisateur ?"))fetch('admin_traitement.php?value=delete_user&id='+id)
.then(r=>r.json()).then(d=>{if(d.success){alert('Utilisateur supprimé');location.reload();}else alert('Erreur : '+d.message);});}
</script>

</body>
</html>
