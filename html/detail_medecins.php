<?php
session_start();
if (!$_SESSION["logged"] == "Y") {
    header("Location:login.php");
}
if (isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
}

require_once './AccessManager.php';
require_once './PresenteurMedecins.class.php';

$access = new AccessManager();

$presenteurdoc = new PresenteurMedecins();
$medecin = $presenteurdoc->prepareMedecin();
$listemeds = $presenteurdoc->prepareListeMedicaments();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Detail medecin</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css"/>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <script src="js/bootstrap.js" type="text/javascript"></script>
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
    <div class="containerdetails">
        <div class="row">
            <form class="form-horizontal" id="detailsmedecin-container">
                <h3><?php if (!$access->checkIsChercheur()) : ?><a href="ModifMedecin.php?codedoc=<?php echo $medecin->code_medecin ?>"><button class="btn btn-primary" type="button">Modifier</button></a><?php endif; ?></h3><br/>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="code">Code : </label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" name="code" value="<?php echo $medecin->code_medecin ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="nom">Nom : </label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" name="nom" value="<?php echo $medecin->nom_medecin ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="prenom">Prénom : </label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" name="prenom" value="<?php echo $medecin->prenom_medecin ?> " readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="adressecab">Adresse du cabinet : </label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" name="adressecab" value="<?php echo $medecin->adresse_cabinet ?> " readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="adressemail">Adresse email : </label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" name="adressemail" value="<?php echo $medecin->adresse_mail ?> " readonly>
                    </div>
                </div>
            </form>
        </div>
        <br/>
        <div class="row">
            <div class="medicaments-container">
                <h3>Médicaments associés</h3>
                <table class="table table-striped table-hover table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Dénomination</th>
                            <th>Etat</th>
                            <th>Mise sur le marché</th>
                            <th>Niveau de surveillance</th>
                            <th>Conditions de prescription</th>
                            <th>Prix</th>
                            <th>Date de souscription</th>
                            <?php if (!$access->checkIsVisiteur()) : ?><th><i>Modifier</i></th>
                                <th><i>Supprimer</i></th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listemeds as $medicament) { ?>
                            <tr>
                                <th scope="row"><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->code_medicament ?></a></th>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->denomination_medicament ?></a></td>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->etat_commercialisation ?></a></td>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->DAMM ?></a></td>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->niveau_surveillance ?></a></td>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->conditions_prescription ?></a></td>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->prix_medicament ?></a></td>
                                <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $presenteurdoc->prepareDate($medicament->code_medicament) ?></a></td>
                                <?php if (!$access->checkIsVisiteur()) : ?><td><a href="ModifMedicament.php?codemed=<?php echo $medicament->code_medicament ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                    <td><a href="DeleteMedicament.php?codemed=<?php echo $medicament->code_medicament ?>"><span class="glyphicon glyphicon-trash"></span></a></td><?php endif; ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>