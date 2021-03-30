<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
if (!$_SESSION["logged"] == "Y") {
    header("Location:login.php");
}
if (isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
}

require_once './AccessManager.php';
$access = new AccessManager();
if ($access->checkIsVisiteur()) {
    header("Location:NoAccess.php");
}

require_once './PresenteurSubstances.class.php';

$presenteursubs = new PresenteurSubstances();
$substance = $presenteursubs->prepareSubstance();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Suppression substance</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css"/>
        <script src="js/bootstrap.js" type="text/javascript"></script>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <nav class="navbar navbar-dark bg-primary navbar-fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo-gsb.png" width="40" height="40" alt="GSB">
            </a>
            <ul class="nav navbar-nav">
                <li id="navmeds"><a href="index.php">Accueil - Médicaments</a></li>
                <li id="navsubs"><a href="index.php#medicaments-pagination">Substances actives</a></li>
                <li id="navdocs"><a href="index.php#substances-pagination">Medecins</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <p class="navbar-text">Connecté(e): <?php echo $name ?></p>
                </li>
                <li><a href="login.php?logout">Se déconnecter</a></li>
            </ul>
        </div>
    </nav>
</head>
<body>
    <div class="containersuppr">
        <form id="DeleteSubstance" action="ConfirmationSuppression.php" method="POST">
            <h2>Voulez-vous vraiment supprimer la substance: <?php echo $substance->DCI; ?> ?</h2><br/>
            <input type="hidden" name="entite" value="substance" required>
            <input type="hidden" name="code_substance" value="<?php echo $substance->code_substance; ?>" required>
            <button class="btn btn-primary" type="submit">Confirmer</button>
            <a href="detail_substances.php?codesubs=<?php echo $substance->code_substance; ?>"<button class="btn btn-primary" type="button">Annuler</button></a>
        </form>
    </div>
</body>
</html>