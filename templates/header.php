
<header class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm" style="background-color: #0a2a66;">
  <div class="container position-relative">
    <a class="navbar-brand" href="index.php">
      <img src="./photos/logo_bw.png" alt="Logo" class="d-inline-block align-text-top">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav navbar-nav-center mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="activiter.php">Activities</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="evenement.php">Events</a></li>
      </ul>
    </div>
    <div class="d-flex align-items-center gap-2 ms-auto">
      <?php if (!isset($_SESSION["idcli"])): ?>
        <a href="connexion.php" class="btn" style="background-color: #007FFF; color: #fff;">Login</a>
      <?php else:
        $cmd = $db->prepare("SELECT NOM_CLI, PRENOM_CLI, PHOTO_PROFILE FROM CLIENT WHERE ID_CLIENT = ?");
        $cmd->execute([$_SESSION["idcli"]]);
        $user = $cmd->fetch(PDO::FETCH_ASSOC);
        $photo = !empty($user["PHOTO_PROFILE"]) ? $user["PHOTO_PROFILE"] : './photos/profilpic/profiledefault.jpg';
      ?>
        <div class="dropdown d-flex align-items-center">
          <a class="btn dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false"
             style="background-color: #007FFF; color: #fff;">
            <?= htmlspecialchars($user["PRENOM_CLI"] . ' ' . $user["NOM_CLI"]) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="width:100%;">
            <li><a class="dropdown-item" href="profil.php">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <button class="dropdown-item text-danger" onclick="deconnexion()">Logout</button>
            </li>
          </ul>
        </div>
        <img src="<?= htmlspecialchars($photo) ?>" alt="Profil" class="header-profile-img">

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
          function deconnexion() {
            Swal.fire({
              title: 'Se déconnecter ?',
              text: 'Êtes-vous sûr de vouloir quitter votre session ?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#007FFF',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Oui, déconnectez-moi',
              cancelButtonText: 'Annuler'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = 'logout.php';
              }
            });
          }
        </script>
      <?php endif; ?>
    </div>
  </div>
</header>
