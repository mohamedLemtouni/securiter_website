  <!-- Navbar -->
    <?php if (!isset($_SESSION["idcli"])) {
      ?>
        <header>
    <div class="logo"><a href="index.php" style="color:white;text-decoration:None">Yalla</a></div>
    <nav>
      <ul>
        <li><a href="test.php">test</a></li>
        <li><a href="#">p2</a></li>
        <li><a href="#">p3</a></li>
        <li><a href="connexion.php">Login</a></li>
      </ul>
    </nav>
  </header>
  <?php }else { 
      $cmd = $db->prepare("select NOM_CLI,PRENOM_CLI from CLIENT where ID_CLIENT = ?");
      $cmd->execute([$_SESSION["idcli"]]);
      $result = $cmd->fetch(PDO::FETCH_ASSOC);
      ?>
    <header>
    <div class="logo"><a href="index.php" style="color:white;text-decoration:None">Yalla</a></div>
    <nav>
      <ul>
        <li><a href="test.php">test</a></li>
        <li><a href="#">p2</a></li>
        <li><a href="#">p3</a></li>
        <li><a href="profil">My Profil</a></li>
        <li><p>Welcome <?php echo $result["NOM_CLI"] . ' ' . $result["PRENOM_CLI"]?></p></li>
      </ul>
    </nav>
  </header>
  <?php }?>

