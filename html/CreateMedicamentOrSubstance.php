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

require_once './AccessManager.php';
$access = new AccessManager();
if ($access->checkIsVisiteur()){
    header("Location:NoAccess.php");
}

require_once './PresenteurMedicaments.class.php';
require_once './PresenteurSubstances.class.php';

$presenteurmed = new PresenteurMedicaments();
$listemed = $presenteurmed->prepareAllMedicaments();
$allvas = $presenteurmed->prepareAllVAs();

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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <h3>Nouveau médicament:</h3>
            <form id="NouveauMedicament" action="ConfirmationCreation.php" method="POST">
                <input type="hidden" name="entite" value="medicament" required>
                Dénomination : <input type="text" name="denomination_medicament" maxlength="100" placeholder="ex: Doliprane" required><br/>
                Etat de commercialisation : <select name="etat_commercialisation" required>
                    <option value='Commercialisé'>Commercialisé</option>
                    <option value='Non-commercialisé'>Non-commercialisé</option>
                </select><br/>
                Date d'Autorisation de Mise sur le Marché: <input name="damm" type="date" required><br/>
                Niveau de surveillance : <select name="niveau_surveillance" required>
                    <option value='Aucune'>Aucune</option>
                    <option value='Renforcée'>Renforcée</option>
                </select><br/>
                Conditions de prescription : <select name="conditions_prescription" required>
                    <option value='Libre'>Libre</option>
                    <option value='Sur ordonnance'>Sur ordonnance</option>
                </select><br/>
                Prix du médicament : <input type="number" name="prix_medicament" step="0.01" required><br/>
                Dosages : <input type="text" name="dosages" maxlength="100" placeholder="ex: 500mg, 750mg" required><br/>
                Substances actives contenues : <br/>
                <?php foreach ($listesubs as $substance) { ?>
                    <input type="checkbox" name="substances[]" value="<?php echo $substance->code_substance ?>"/><?php echo $substance->DCI ?> <br/>
                <?php } ?>
                Voies d'administration : <br/>
                <?php foreach ($allvas as $va) { ?>
                    <input type="checkbox" name="vas[]" value="<?php echo $va->code_va ?>"/><?php echo $va->denomination_va ?> <br/>
                <?php } ?>
                <button type="submit">Ajouter</button>
                <button type="reset">Reset</button>
            </form>

            <h3>Nouvelle substance active:</h3>
            <form id="NouvelleSubstance" action="ConfirmationCreation.php" method="POST">
                <input type="hidden" name="entite" value="substance">
                Dénomination : <input type="text" name="denomination_substance" maxlength="100" placeholder="ex: Caféine" required><br/>
                Classification ATC: <select name="classification_atc" required>
                    <?php foreach($allatc1 as $atc1) {?>
                    <optgroup label="<?php echo $atc1->code_atc1.': '.$atc1->denomination_atc; ?>">
                        <?php $allatc2 = $presenteursubs->prepareAllATC2($atc1->code_atc1);
                            foreach ($allatc2 as $atc2) {?>
                                <option value='<?php echo $atc2->code_atc2?>'><?php echo $atc2->code_atc2.': '.$atc2->denomination_atc2; ?></option>
                            <?php } ?>
                    </optgroup>
                    <?php } ?>
                </select><br/>
                Effets secondaires : <br/>
                <?php foreach ($alles as $effet) { ?>
                    <input type="checkbox" name="es[]" value="<?php echo $effet->code_es ?>"/><?php echo $effet->denomination_es ?> <br/>
                <?php } ?>
                Informations supplémentaires : <input type="text" name="infos_sup" maxlength="200"><br/>
                <button type="submit">Ajouter</button>
                <button type="reset">Reset</button>
            </form>
        </div>
    </body>
</html>