<!DOCTYPE html>
<html lang="fr">
<?php session_start(); ?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mot de passe oublié</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,800,900" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      background-image: url('./img/bg2.png');
      background-size: cover;
      background-position: center;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }

    .card-reset {
      background: rgba(0, 0, 0, 0.8);
      border: 1px solid #1e90ff;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(30, 144, 255, 0.3);
      width: 100%;
      max-width: 420px;
      padding: 2rem;
    }

    .card-header img {
      height: 50px;
      border: 2px solid #1e90ff;
      border-radius: 8px;
      padding: 4px;
      background: #fff;
    }

    .form-label {
      color: #fff;
      font-weight: 500;
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.9);
      border: 1px solid #1e90ff;
      border-radius: 6px;
    }

    .form-control:focus {
      border-color: #1e90ff;
      box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
    }

    .btn-primary {
      background-color: #1e90ff;
      border: none;
      color: #000;
      font-weight: 600;
    }

    .btn-primary:hover {
      background-color: #000;
      color: #1e90ff;
      border: 1px solid #1e90ff;
      box-shadow: 0 0 15px rgba(30, 144, 255, 0.4);
    }

    h4 {
      color: #1e90ff;
      font-weight: 600;
      text-align: center;
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>

<div class="card card-reset text-light">
  <div class="card-header text-center mb-4">
    <a href="./connexion.php"><img src="./photos/logo_bw.png" alt="Logo"></a>
  </div>
  <div class="card-body">
    <?php if (isset($_GET["nvmdp"]) && $_GET["nvmdp"] === "true") { ?>
      <h4>Nouveau mot de passe</h4>
      <form action="./traitement_mdp_oublie.php?value=nvmpd" method="post">
        <div class="mb-3">
          <label for="newmdp" class="form-label">Nouveau mot de passe</label>
          <input type="password" name="newmdp" id="newmdp"
                 class="form-control"
                 minlength="9"
                 required>
        </div>
        <div class="mb-3">
          <label for="cnfmdp" class="form-label">Confirmer le mot de passe</label>
          <input type="password" name="cnfmdp" id="cnfmdp"
                 class="form-control"
                 minlength="9"
                 required>
        </div>
        <input type="hidden" name="mailrec" value="<?= htmlspecialchars($_GET["mailrec"] ?? '', ENT_QUOTES) ?>">
        <button type="submit" id="btnsubmit" class="btn btn-primary w-100">Changer le mot de passe</button>
      </form>
    <?php } elseif (isset($_GET["code_fourni_mail"]) && $_GET["code_fourni_mail"] === "true") { ?>
      <h4>Entrer le code reçu</h4>
      <form action="./traitement_mdp_oublie.php?value=code_fourni_mail" method="post">
        <div class="mb-3">
          <label for="code_de_mail" class="form-label">Code de vérification</label>
          <input type="text" name="code_de_mail" id="code_de_mail" class="form-control" required>
        </div>
        <input type="hidden" name="mailrec" value="<?= htmlspecialchars($_GET["mailrec"] ?? '', ENT_QUOTES) ?>">
        <button type="submit" id="btnsubmit" class="btn btn-primary w-100">Valider le code</button>
      </form>
    <?php } else { ?>
      <h4>Réinitialiser votre mot de passe</h4>
      <form action="./traitement_mdp_oublie.php?value=envoi_du_mail" method="post">
        <div class="mb-3">
          <label for="mail" class="form-label">Votre email</label>
          <input type="email" name="mail" id="mail" class="form-control" required>
        </div>
        <button type="submit" id="btnsubmit" class="btn btn-primary w-100">Envoyer le code</button>
      </form>
    <?php } ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const newmdp = document.getElementById('newmdp');
  const cnfmdp = document.getElementById('cnfmdp');
  const btnsubmit = document.getElementById('btnsubmit');
  if (newmdp && cnfmdp && btnsubmit) {
    btnsubmit.disabled = true;
    function verifierValeurs() {
      btnsubmit.disabled = !(newmdp.value === cnfmdp.value && newmdp.value.length >= 8);
    }
    newmdp.addEventListener('input', verifierValeurs);
    cnfmdp.addEventListener('input', verifierValeurs);
  }
</script>
</body>
</html>
