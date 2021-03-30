<?php
session_start();
if (!$_SESSION["logged"] == "Y") {
    header("Location:login.php");
}
if (isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
}

require_once './AccessManager.php';
require_once './PresenteurMedicaments.class.php';

$access = new AccessManager();

$presenteurmed = new PresenteurMedicaments();
$medicament = $presenteurmed->prepareMedicament();
$listesubs = $presenteurmed->prepareListeSubstances();
$listedocs = $presenteurmed->prepareListeMedecins();
$liste_va = $presenteurmed->prepareListeVA();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Detail medicament</title>
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
            <form class="form-horizontal" id="detailsmedicaments-container">
                <h3><?php if (!$access->checkIsVisiteur()) : ?><a href="ModifMedicament.php?codemed=<?php echo $medicament->code_medicament ?>"><button class="btn btn-primary" type="button">Modifier</button></a><?php endif; ?></h3><br/>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="code">Code : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="code" value="<?php echo $medicament->code_medicament ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="denomination">Dénomination : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="denomination" value="<?php echo $medicament->denomination_medicament ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5"for="etat">Etat de commercialisation : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="etat" value="<?php echo $medicament->etat_commercialisation ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5"for="damm">Date de Mise sur le Marché : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="damm" value="<?php echo $medicament->DAMM ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5"for="surveillance">Niveau de surveillance : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="surveillance" value="<?php echo $medicament->niveau_surveillance ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5"for="prescription">Conditions de prescription : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="prescription" value="<?php echo $medicament->conditions_prescription ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5"for="prix">Prix : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="prix" value="<?php echo $medicament->prix_medicament ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5"for="dosages">Dosages : </label>
                    <div class="col-sm-7">
                    <input class="form-control" type="text" name="dosages" value="<?php echo $medicament->dosages ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-5" for="voies_administration">Voies d'admnistration : </label>
                    <div class="col-sm-7">
                    <?php foreach ($liste_va as $va) { ?>
                        <input class="form-control" type="text" name="voies_administration" value="<?php echo $va->denomination_va ?>" readonly>
                    <?php } ?>
                    </div>
                </div>
            </form>
        </div>
        <br/>
        <div class="row">
            <div class="substances-container">
                <h3>Substances actives associées</h3>
                <table class="table table-striped table-hover table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Dénomination</th>
                            <th>Informations importantes</th>
                            <?php if (!$access->checkIsVisiteur()) : ?><th><i>Modifier</i></th>
                                <th><i>Supprimer</i></th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listesubs as $substance) { ?>
                            <tr>
                                <th scope="row"><a href="detail_substances.php?codesubs=<?php echo $substance->code_substance ?>"><?php echo $substance->code_substance ?></a></th>
                                <td><a href="detail_substances.php?codesubs=<?php echo $substance->code_substance ?>"><?php echo $substance->DCI ?></a></td>
                                <td><a href="detail_substances.php?codesubs=<?php echo $substance->code_substance ?>"><?php echo $substance->infos_sup ?></a></td>
                                <?php if (!$access->checkIsVisiteur()) : ?><td><a href="ModifSubstance.php?codesubs=<?php echo $substance->code_substance ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                    <td><a href="DeleteSubstance.php?codesubs=<?php echo $substance->code_substance ?>"><span class="glyphicon glyphicon-trash"></span></a></td><?php endif; ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="medecins-container">
                <h3>Médecins abonnés</h3> 
                <table class="table table-striped table-hover table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Adresse du cabinet</th>
                            <th>Adresse email</th>
                            <th>Date de souscription</th>
                            <?php if (!$access->checkIsChercheur()) : ?><th><i>Modifier</i></th>
                                <th><i>Supprimer</i></th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listedocs as $medecin) { ?>
                            <tr>
                                <th scope="row"><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->code_medecin ?></a></th>
                                <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->nom_medecin ?></a></td>
                                <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->prenom_medecin ?></a></td>
                                <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->adresse_cabinet ?></a></td>
                                <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->adresse_mail ?></a></td>
                                <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $presenteurmed->prepareDate($medecin->code_medecin) ?></a></td>
                                <?php if (!$access->checkIsChercheur()) : ?><td><a href="ModifMedecin.php?codedoc=<?php echo $medecin->code_medecin ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                    <td><a href="DeleteMedecin.php?codedoc=<?php echo $medecin->code_medecin ?>"><span class="glyphicon glyphicon-trash"></span></a></td><?php endif; ?>
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