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

require_once './PresenteurMedicaments.class.php';
require_once './PresenteurSubstances.class.php';

$presenteurmed = new PresenteurMedicaments();
$listemed = $presenteurmed->prepareAllMedicaments();
$allvas = $presenteurmed->prepareAllVAs();

$presenteursubs = new PresenteurSubstances();
$listesubs = $presenteursubs->prepareAllSubstances();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Nouveau medicament</title>
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
            <form id="NouveauMedicament" action="ConfirmationCreation.php" method="POST">
                <h2><u>Nouveau médicament : </u></h2><br/>
                <input type="hidden" name="entite" value="medicament" required>
                <div class="form-group">
                    <label for="denomination_medicament">Dénomination : </label>
                    <input type="text" class="form-control" name="denomination_medicament" maxlength="100" placeholder="ex: Doliprane" required>
                </div>
                <div class="form-group">
                    <label for="etat_commercialisation" >Etat de commercialisation : </label>
                    <select class="form-control" name="etat_commercialisation" required>
                        <option value='Commercialisé'>Commercialisé</option>
                        <option value='Non-commercialisé'>Non-commercialisé</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="damm">Date d'Autorisation de Mise sur le Marché : </label>
                    <input class="form-control" name="damm" type="date" required>
                </div>
                <div class="form-group">
                    <label for="niveau_surveillance">Niveau de surveillance : </label>
                    <select class="form-control" name="niveau_surveillance" required>
                        <option value='Aucune'>Aucune</option>
                        <option value='Renforcée'>Renforcée</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="conditions_prescription">Conditions de prescription : </label>
                    <select class="form-control" name="conditions_prescription" required>
                        <option value='Libre'>Libre</option>
                        <option value='Sur ordonnance'>Sur ordonnance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="prix_medicament">Prix du médicament : </label>
                    <input class="form-control" type="number" name="prix_medicament" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="dosages">Dosages : </label>
                    <input class="form-control" type="text" name="dosages" maxlength="100" placeholder="ex: 500mg, 750mg" required>
                </div>
                <div class="form-group">
                    <label for="substances[]">Substances actives contenues : </label><br/>
                    <?php foreach ($listesubs as $substance) { ?>
                        <input type="checkbox" name="substances[]" value="<?php echo $substance->code_substance ?>"/><?php echo $substance->DCI ?><br/>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="vas[]">Voies d'administration : </label><br/>
                    <?php foreach ($allvas as $va) { ?>
                        <input type="checkbox" name="vas[]" value="<?php echo $va->code_va ?>"/><?php echo $va->denomination_va ?><br/>
                    <?php } ?>
                </div>
                <button class="btn btn-primary" type="submit">Ajouter</button> 
                <button class="btn btn-primary" type="reset">Réinitialiser le formulaire</button>
            </form>
        </div>
    </div>  
</body>
</html>