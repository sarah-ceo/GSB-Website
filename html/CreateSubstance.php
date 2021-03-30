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
$listesubs = $presenteursubs->prepareAllSubstances();
$alles = $presenteursubs->prepareAllEffets();
$allatc1 = $presenteursubs->prepareAllATC1();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Nouvelle substance</title>
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
    <div class="containernouveau">
        <div class="row">
            <form id="nouvellesubstance" action="ConfirmationCreation.php" method="POST">
                <h2><u>Nouvelle substance active : </u></h2><br/>
                <input type="hidden" name="entite" value="substance">
                <div class="form-group">
                    <label for="denomination_substance">Dénomination : </label>
                    <input class="form-control" type="text" name="denomination_substance" maxlength="100" placeholder="ex: Caféine" required>
                </div>
                <div class="form-group">
                    <label for="classification_atc">Classification ATC : </label>
                    <select class="form-control" name="classification_atc" required>
                        <?php foreach ($allatc1 as $atc1) { ?>
                        <optgroup label="<?php echo $atc1->code_atc1 . ': ' . $atc1->denomination_atc; ?>">
                            <?php $allatc2 = $presenteursubs->prepareAllATC2($atc1->code_atc1);
                            foreach ($allatc2 as $atc2) { ?>
                                <option value='<?php echo $atc2->code_atc2 ?>'><?php echo $atc2->code_atc2 . ': ' . $atc2->denomination_atc2; ?></option>
                            <?php } ?>
                        </optgroup>
                        <?php } ?>
                    </select><br/>
                </div>
                <div class="form-group">
                    <label for="es[]">Effets secondaires : </label><br/>
                    <?php foreach ($alles as $effet) { ?>
                        <input type="checkbox" name="es[]" value="<?php echo $effet->code_es ?>"/><?php echo $effet->denomination_es ?> <br/>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="infos_sup">Informations supplémentaires : </label>
                    <input class="form-control" type="text" name="infos_sup" maxlength="200">
                </div>
                <button class="btn btn-primary" type="submit">Ajouter</button>
                <button class="btn btn-primary" type="reset">Reinitialiser le formulaire</button>
            </form>
        </div>
    </div>
</body>
</html>