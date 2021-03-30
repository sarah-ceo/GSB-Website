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
    $message="Erreur entite";
}

if ($entite == "medicament") {

    if (isset($_POST["code_medicament"])) {
        $code_medicament = htmlspecialchars($_POST["code_medicament"]);
    } else {
        $message= "Erreur code_medicament";
    }

    if (isset($_POST["denomination_medicament"])) {
        $denomination_medicament = htmlspecialchars($_POST["denomination_medicament"]);
    } else {
        $message= "Erreur denomination_medicament";
    }

    if (isset($_POST["etat_commercialisation"])) {
        $etat_commercialisation = htmlspecialchars($_POST["etat_commercialisation"]);
    } else {
        $message= "Erreur etat_commercialisation";
    }

    if (isset($_POST["damm"])) {
        $damm = htmlspecialchars($_POST["damm"]);
    } else {
        $message= "Erreur damm";
    }

    if (isset($_POST["niveau_surveillance"])) {
        $niveau_surveillance = htmlspecialchars($_POST["niveau_surveillance"]);
    } else {
        $message= "Erreur niveau_surveillance";
    }

    if (isset($_POST["conditions_prescription"])) {
        $conditions_prescription = htmlspecialchars($_POST["conditions_prescription"]);
    } else {
        $message= "Erreur conditions_prescription";
    }

    if (isset($_POST["prix_medicament"])) {
        $prix_medicament = htmlspecialchars($_POST["prix_medicament"]);
    } else {
        $message= "Erreur prix_medicament";
    }

    if (isset($_POST["dosages"])) {
        $dosages = htmlspecialchars($_POST["dosages"]);
    } else {
        $message= "Erreur dosages";
    }

    if (isset($_POST["substances"])) {
        $substances = $_POST["substances"];
    } else {
        $message= "Erreur substances";
    }

    if (isset($_POST["vas"])) {
        $vas = $_POST["vas"];
    } else {
        $message= "Erreur vas";
    }

    $presenteurmed = new PresenteurMedicaments();
    $presenteurmed->deleteMedicamentAssoc($code_medicament);
    $presenteurmed->updateMedicament($code_medicament, $denomination_medicament, $etat_commercialisation, $damm, $niveau_surveillance, $conditions_prescription, $prix_medicament, $dosages);
    
    $presenteurmed->EmailMedecinForMedicament($code_medicament, $denomination_medicament, $etat_commercialisation, $damm, $niveau_surveillance, $conditions_prescription, $prix_medicament, $dosages, $substances, $vas);
    
    foreach ($substances as $subs) {
        $presenteurmed->newMedicamentSubstance($code_medicament, $subs);
    }
    foreach ($vas as $va) {
        $presenteurmed->newMedicamentVA($code_medicament, $va);
    }

    $message= "Medicament modifié, vous allez être redirigé(e) vers la page d'accueil !";
    header('Refresh: 4; URL=http://192.168.100.5/index.php');
} else if ($entite == "substance") {

    if (isset($_POST["code_substance"])) {
        $code_substance = htmlspecialchars($_POST["code_substance"]);
    } else {
        $message= "Erreur code_substance";
    }

    if (isset($_POST["denomination_substance"])) {
        $denomination_substance = htmlspecialchars($_POST["denomination_substance"]);
    } else {
        $message= "Erreur denomination_substance";
    }

    if (isset($_POST["classification_atc"])) {
        $classification_atc = htmlspecialchars($_POST["classification_atc"]);
    } else {
        $message= "Erreur classification_atc";
    }

    if (isset($_POST["es"])) {
        $es = $_POST["es"];
    } else {
        $message= "Erreur es";
    }

    if (isset($_POST["infos_sup"])) {
        $infos_sup = htmlspecialchars($_POST["infos_sup"]);
    } else {
        $message= "Erreur infos_sup";
    }

    $presenteursubs = new PresenteurSubstances();
    $presenteursubs->deleteSubstanceAssoc($code_substance);
    $presenteursubs->UpdateSubstance($code_substance, $denomination_substance, $infos_sup, $classification_atc);
    
    $presenteursubs->EmailsMedecinsForSubstance($code_substance, $denomination_substance, $infos_sup, $classification_atc, $es);

    foreach ($es as $effet) {
        $presenteursubs->newSubstanceES($code_substance, $effet);
    }

    $message= "Substance modifiée, vous allez être redirigé(e) vers la page d'accueil !";
    header('Refresh: 4; URL=http://192.168.100.5/index.php');
    
} else if ($entite == "medecin") {

    if (isset($_POST["code_medecin"])) {
        $code_medecin = htmlspecialchars($_POST["code_medecin"]);
    } else {
        $message= "Erreur code_medecin";
    }
    if (isset($_POST["nom_medecin"])) {
        $nom_medecin = htmlspecialchars($_POST["nom_medecin"]);
    } else {
        $message= "Erreur nom_medecin";
    }
    
    if (isset($_POST["prenom_medecin"])) {
        $prenom_medecin = htmlspecialchars($_POST["prenom_medecin"]);
    } else {
        $message= "Erreur prenom_medecin";
    }
    
    if (isset($_POST["adresse_cabinet"])) {
        $adresse_cabinet = htmlspecialchars($_POST["adresse_cabinet"]);
    } else {
        $message= "Erreur adresse_cabinet";
    }
    
    if (isset($_POST["adresse_mail"])) {
        $adresse_mail = htmlspecialchars($_POST["adresse_mail"]);
    } else {
        $message= "Erreur adresse_mail";
    }
    
    
    if (isset($_POST["medicaments"])) {
        $medicaments = $_POST["medicaments"];
    } else {
        $message= "Erreur medicaments";
    }
    
    $presenteurdoc = new PresenteurMedecins();
    $presenteurdoc->DeleteMedecinAssoc($code_medecin);
    $presenteurdoc->UpdateMedecin($code_medecin, $nom_medecin, $prenom_medecin, $adresse_cabinet, $adresse_mail);

    foreach ($medicaments as $med) {
        $presenteurdoc->newMedecinMedicament($code_medecin, $med);
    }

    $message= "Medecin modifié, vous allez être redirigé(e) vers la page d'accueil !";
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
        <title>Confirmation modification</title>
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
    <div id="ConfirmationModif">
        <h3><?php echo $message;?></h3>
    </div>
</body>
</html>