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
if ($access->checkIsChercheur()) {
    header("Location:NoAccess.php");
}

require_once './PresenteurMedecins.class.php';
require_once './PresenteurMedicaments.class.php';


$presenteurdoc = new PresenteurMedecins();
$medecin = $presenteurdoc->prepareMedecin();

$presenteurmed = new PresenteurMedicaments();
$listemed = $presenteurmed->prepareAllMedicaments();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Modification medecin</title>
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
    <div class="containermodif">
        <div class="row">
            <form id="ModifMedecin" action="ConfirmationModification.php" method="POST">
                <h2><u>Modifier le médecin : </u></h2><br/>
                <input type="hidden" name="entite" value="medecin" required>
                <input type="hidden" name="code_medecin" value="<?php echo $medecin->code_medecin; ?>" required>
                <div class="form-group">
                    <label for="nom_medecin">Nom : </label>
                    <input class="form-control" type="text" name="nom_medecin" maxlength="45" value="<?php echo $medecin->nom_medecin; ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom_medecin">Prénom : </label>
                    <input class="form-control" type="text" name="prenom_medecin" maxlength="45" value="<?php echo $medecin->prenom_medecin; ?>" required>
                </div>
                <div class="form-group">
                    <label for="adresse_cabinet">Adresse du cabinet : </label>
                    <input class="form-control" type="text" name="adresse_cabinet" maxlength="100" value="<?php echo $medecin->adresse_cabinet; ?>" required>
                </div>
                <div class="form-group">
                    <label for="adresse_mail">Adresse e-mail : </label>
                    <input class="form-control" type="text" name="adresse_mail" maxlength="45" value="<?php echo $medecin->adresse_mail; ?>" required>
                </div>
                <div class="form-group">
                    <label for="medicaments[]">Medicaments souscrits: </label><br/>
                    <?php foreach ($listemed as $medicament) { ?>
                        <input type="checkbox" name="medicaments[]" value="<?php echo $medicament->code_medicament ?>" <?php if ($presenteurdoc->CheckAssociationMedecinMedicament($medecin->code_medecin, $medicament->code_medicament)) : ?> checked="checked" <?php endif; ?>/><?php echo $medicament->denomination_medicament ?> <br/>
                    <?php } ?>
                </div>
                <button class="btn btn-primary" type="submit">Modifier</button>
                <a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin; ?>"<button class="btn btn-primary" type="button">Annuler</button></a>
            </form>
        </div>
    </div>
</body>
</html>