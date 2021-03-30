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
require_once './PresenteurMedicaments.class.php';
require_once './PresenteurSubstances.class.php';
require_once './PresenteurMedecins.class.php';

$access = new AccessManager();

$presenteurmed = new PresenteurMedicaments();
$listemed = $presenteurmed->prepareData();


$presenteursubs = new PresenteurSubstances();
$listesubs = $presenteursubs->prepareData();
$allatc1 = $presenteursubs->prepareAllATC1();

$presenteurdoc = new PresenteurMedecins();
$listedoc = $presenteurdoc->prepareData();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Index</title>
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
    <div class="container">
        <div class="row" id="row-medicaments">
            <h3>Médicaments <?php if (!$access->checkIsVisiteur()) : ?><a href="CreateMedicament.php"><button type="button">+</button></a><?php endif; ?></h3>
        </div>
        <div class="row">
            <div class="medicaments-container">
                <div class="col-sm-9">
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
                                <?php if (!$access->checkIsVisiteur()) : ?><th><i>Modifier</i></th>
                                    <th><i>Supprimer</i></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listemed as $medicament) { ?>
                                <tr>
                                    <th scope="row"><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->code_medicament ?></a></th>
                                    <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->denomination_medicament ?></a></td>
                                    <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->etat_commercialisation ?></a></td>
                                    <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->DAMM ?></a></td>
                                    <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->niveau_surveillance ?></a></td>
                                    <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->conditions_prescription ?></a></td>
                                    <td><a href="details_medicaments.php?codemed=<?php echo $medicament->code_medicament ?>"><?php echo $medicament->prix_medicament . "€" ?></a></td>
                                    <?php if (!$access->checkIsVisiteur()) : ?><td><a href="ModifMedicament.php?codemed=<?php echo $medicament->code_medicament ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                        <td><a href="DeleteMedicament.php?codemed=<?php echo $medicament->code_medicament ?>"><span class="glyphicon glyphicon-trash"></span></a></td><?php endif; ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="medicaments-pagination" id="medicaments-pagination">
                        <nav aria-label="Navigation medicaments">
                            <ul class="pagination justify-content-end">
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $presenteurmed->url . "pagemed=" . $presenteurmed->pagePrecedente; ?>" tabindex="-1">Précédente</a>
                                </li>
                                <?php for ($page = 1; $page <= $presenteurmed->pageMax; $page++) { ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $presenteurmed->url . "pagemed=" . $page; ?>"><?php echo $page; ?></a></li>
                                    <?php
                                }
                                ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $presenteurmed->url . "pagemed=" . $presenteurmed->pageSuivante; ?>">Suivante</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-sm-3">
                    <form id="RechercheMedicament" action="index.php" method="POST">
                        <h4><u>Recherche avancée</u></h4>
                        <div class="form-group">
                            <label for="nom_medicament">Nom contenant : </label>
                            <input type="text" name="nom_medicament" maxlength="100"><br/>
                        </div>
                        <div class="form-group">
                            <label for="etat">Etat de commercialisation : </label>
                            <select name="etat">
                                <option value=NULL></option>
                                <option value='Commercialisé'>Commercialisé</option>
                                <option value='Non-commercialisé'>Non-commercialisé</option>
                            </select><br/>
                        </div>
                        <div class="form-group">
                            <label for="dateop">Date de Mise sur le Marché: </label><br/>
                            <select name="dateop">
                                <option value=NULL></option>
                                <option value='Inférieur'><</option>
                                <option value='Supérieur'>></option>
                                <option value='Egal'>=</option>
                            </select>
                            <label for="damm">à : </label>
                            <input name="damm" type="date" value=NULL><br/>
                        </div>
                        <div class="form-group">
                            <label for="surveillance">Niveau de surveillance : </label>
                            <select name="surveillance">
                                <option value=NULL></option>
                                <option value='Aucune'>Aucune</option>
                                <option value='Renforcée'>Renforcée</option>
                            </select><br/>
                        </div>
                        <div class="form-group">
                            <label for="conditions">Prescription : </label>
                            <select name="conditions">
                                <option value=NULL></option>
                                <option value='Libre'>Libre</option>
                                <option value='Sur ordonnance'>Sur ordonnance</option>
                            </select><br/>
                        </div>
                        <div class="form-group">
                            <label for="prixop">Prix : </label>
                            <select name="prixop">
                                <option value=NULL></option>
                                <option value='Inférieur'><</option>
                                <option value='Supérieur'>></option>
                                <option value='Egal'>=</option>
                            </select>
                            <label for="prix">à : </label>
                            <input type="number" name="prix" step="0.01" value=NULL><br/>
                        </div>
                        <div class="form-group">
                            <label for="dosages">Dosages : </label>
                            <input type="text" name="dosages" maxlength="100"><br/>
                        </div>
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>



        <div class="row" id="row-substances">
            <h3>Substances <?php if (!$access->checkIsVisiteur()) : ?><a href="CreateSubstance.php"><button type="button">+</button></a><?php endif; ?></h3>
        </div>
        <div class="row">
            <div class="substances-container">
                <div class="col-sm-7">
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
                    <div class="substances-pagination" id="substances-pagination">
                        <nav aria-label="Navigation substances">
                            <ul class="pagination justify-content-end">
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $presenteursubs->url . "pagesubs=" . $presenteursubs->pagePrecedente; ?>#medicaments-pagination" tabindex="-1">Précédente</a>
                                </li>
                                <?php for ($page = 1; $page <= $presenteursubs->pageMax; $page++) { ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $presenteursubs->url . "pagesubs=" . $page; ?>#medicaments-pagination"><?php echo $page; ?></a></li>
                                    <?php
                                }
                                ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $presenteursubs->url . "pagesubs=" . $presenteursubs->pageSuivante; ?>#medicaments-pagination">Suivante</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-sm-5">
                    <form id="RechercheSubstance" action="index.php#medicaments-pagination" method="POST">
                        <h4><u>Recherche avancée</u></h4>
                        <div class="form-group">
                            <label for="nom_substance">Nom contenant: </label>
                            <input type="text" name="nom_substance" maxlength="100"><br/>
                        </div>
                        <div class="form-group">
                            <label for="atc1">Classification ATC (Niveau 1): </label>
                            <select name="atc1">
                                <option value='NULL'></option>
                                <?php foreach ($allatc1 as $atc1) { ?>
                                    <option value='<?php echo $atc1->code_atc1 ?>'><?php echo $atc1->code_atc1 . ': ' . $atc1->denomination_atc; ?></option>
                                <?php } ?>
                            </select><br/>
                        </div>
                        <div class="form-group">
                            <label for="atc2">Classification ATC (Niveau 2): </label>
                            <select name="atc2">
                                <option value='NULL'></option>
                                <?php foreach ($allatc1 as $atc1) { ?>
                                    <optgroup label="<?php echo $atc1->code_atc1 . ': ' . $atc1->denomination_atc; ?>">
                                        <?php
                                        $allatc2 = $presenteursubs->prepareAllATC2($atc1->code_atc1);
                                        foreach ($allatc2 as $atc2) {
                                            ?>
                                            <option value='<?php echo $atc2->code_atc2 ?>'><?php echo $atc2->code_atc2 . ': ' . $atc2->denomination_atc2; ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select><br/>
                        </div>
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="row" id="row-medecins">
            <h3>Médecins <?php if (!$access->checkIsChercheur()) : ?><a href="CreateMedecin.php"><button type="button">+</button></a><?php endif; ?></h3>
        </div>
        <div class="row">
            <div class="medecins-container">
                <div class="col-sm-9">
                    <table class="table table-striped table-hover table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Adresse du cabinet</th>
                                <th>Adresse email</th>
                                <?php if (!$access->checkIsChercheur()) : ?><th><i>Modifier</i></th>
                                    <th><i>Supprimer</i></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listedoc as $medecin) { ?>
                                <tr>
                                    <th scope="row"><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->code_medecin ?></a></th>
                                    <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->nom_medecin ?></a></td>
                                    <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->prenom_medecin ?></a></td>
                                    <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->adresse_cabinet ?></a></td>
                                    <td><a href="detail_medecins.php?codedoc=<?php echo $medecin->code_medecin ?>"><?php echo $medecin->adresse_mail ?></a></td>
                                    <?php if (!$access->checkIsChercheur()) : ?><td><a href="ModifMedecin.php?codedoc=<?php echo $medecin->code_medecin ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                        <td><a href="DeleteMedecin.php?codedoc=<?php echo $medecin->code_medecin ?>"><span class="glyphicon glyphicon-trash"></span></a></td><?php endif; ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="medecins-pagination">
                        <nav aria-label="Navigation medecins">
                            <ul class="pagination justify-content-end">
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $presenteurdoc->url . "pagedoc=" . $presenteurdoc->pagePrecedente; ?>#substances-pagination" tabindex="-1">Précédente</a>
                                </li>
                                <?php for ($page = 1; $page <= $presenteurdoc->pageMax; $page++) { ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $presenteurdoc->url . "pagedoc=" . $page; ?>#substances-pagination"><?php echo $page; ?></a></li>
                                    <?php
                                }
                                ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $presenteurdoc->url . "pagedoc=" . $presenteurdoc->pageSuivante; ?>#substances-pagination">Suivante</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-sm-3">
                    <h4><u>Recherche avancée</u></h4>
                    <form id="RechercheMedecin" action="index.php#substances-pagination" method="POST">
                        <div class="form-group">
                            <label for="nom_medecin">Nom contenant: </label>
                            <input type="text" name="nom_medecin" maxlength="45"><br/>
                        </div>
                        <div class="form-group">
                            <label for="prenom_medecin">Prénom contenant: </label>
                            <input type="text" name="prenom_medecin" maxlength="45"><br/>
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse (du cabinet) contenant: </label>
                            <input type="text" name="adresse" maxlength="100"><br/>
                        </div>
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

