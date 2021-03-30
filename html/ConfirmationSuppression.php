<?php

session_start();
if (!$_SESSION["logged"] == "Y") {
    header("Location:login.php");
}
if (isset($_SESSION["name"])){
    $name = $_SESSION["name"];
}

require_once './PresenteurMedicaments.class.php';
require_once './PresenteurSubstances.class.php';
require_once './PresenteurMedecins.class.php';


if (isset($_POST["entite"])) {
    $entite = htmlspecialchars($_POST["entite"]);
} else {
    $message= "Erreur entite";
}

if ($entite == "medicament") {
    
    if (isset($_POST["code_medicament"])) {
        $code_medicament = htmlspecialchars($_POST["code_medicament"]);
    } else {
        $message= "Erreur code_medicament";
    }
    
    $presenteurmed = new PresenteurMedicaments();
    $presenteurmed->deleteMedicament($code_medicament);
    
    $message= "Medicament supprimé, vous allez être redirigé(e) vers la page d'accueil !";
    header('Refresh: 4; URL=http://192.168.100.5/index.php');
    
} else if ($entite == "substance" ) {
    
    if (isset($_POST["code_substance"])) {
        $code_substance = htmlspecialchars($_POST["code_substance"]);
    } else {
        $message= "Erreur code_substance";
    }
    
    $presenteursubs = new PresenteurSubstances();
    $presenteursubs->deleteSubstance($code_substance);
    
    $message= "Substance supprimée, vous allez être redirigé(e) vers la page d'accueil !";
    header('Refresh: 4; URL=http://192.168.100.5/index.php');
    
} else if ($entite == "medecin" ) {
    
    if (isset($_POST["code_medecin"])) {
        $code_medecin = htmlspecialchars($_POST["code_medecin"]);
    } else {
        $message= "Erreur code_medecin";
    }
    
    $presenteurdoc = new PresenteurMedecins();
    $presenteurdoc->deleteMedecin($code_medecin);
    
    $message= "Medecin supprimé, vous allez être redirigé(e) vers la page d'accueil !";
    header('Refresh: 4; URL=http://192.168.100.5/index.php');
    
} else {
    $message= "Problème d'entité";
}

?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Confirmation suppression</title>
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
    <div id="ConfirmationSuppr">
        <h3><?php echo $message;?></h3>
    </div>
</body>
</html>