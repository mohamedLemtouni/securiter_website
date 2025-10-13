  <!-- Navbar -->
    <?php if (!isset($_SESSION["idcli"])) {
      ?>
        <header id="hd">
    <div class="logo"><a href="index.php"><img src="./photos/logo_bw.png"></a></div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="activiter.php">Activity</a></li>
        <li><a href="evenement.php">Event</a></li
      </ul>
    </nav>
    <div><a href="connexion.php">Login</a></div>
  </header>
  <?php }else { 
      $cmd = $db->prepare("select NOM_CLI,PRENOM_CLI from CLIENT where ID_CLIENT = ?");
      $cmd->execute([$_SESSION["idcli"]]);
      $result = $cmd->fetch(PDO::FETCH_ASSOC);
      ?>
    <header id="hd">
    <div class="logo"><a href="index.php"><img src="./photos/logo_bw.png"></a></div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="activiter.php">Activity</a></li>
        <li><a href="evenement.php">Event</a></li>
        <li><a href="profil.php"><?php echo $result["NOM_CLI"] . ' ' . $result["PRENOM_CLI"]?></a></li>
      </ul>
    </nav>
  </header>
  <?php }?>

