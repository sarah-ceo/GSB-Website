<?php

require_once './Singleton.php';
require_once './Substance.class.php';
require_once './DAOSubstance.php';

/**
 * Classe permettant de préparer les données concernant les substances
 *
 * @author UTILISATEUR
 */
class PresenteurSubstances {

    private $daosubs;
    private $recherche;
    public $code_substance;
    public $pageCourante;
    public $pageMax;
    public $pageSuivante;
    public $pagePrecedente;
    public $url;

    /**
     * Constructeur appelant le Singleton pour instancier le DAO et récupérant les variables de GET et POST
     */
    function __construct() {
        $this->daosubs = new DAOSubstance(Singleton::getConnection());

        $this->formatRechercheSubs();

        if (isset($_GET['pagesubs'])) {
            $this->pageCourante = htmlspecialchars($_GET['pagesubs']);
        } else {
            $this->pageCourante = 1;
        }

        if (isset($_GET['searchsubs'])) {
            $this->recherche = htmlspecialchars($_GET['searchsubs']);
        }

        if (isset($_GET['codesubs'])) {
            $this->code_substance = htmlspecialchars($_GET['codesubs']);
        } else {
            $this->code_substance = "";
        }
    }

    /**
     * Fonction préparant les données des substances à afficher dans le tableau d'index 
     * en fonction de la recherche et de la pagination
     * @return type
     */
    function prepareData() {

        if ($this->recherche == "") {
            $liste = $this->daosubs->getAllSubstances(10, 10 * ($this->pageCourante - 1));
            $this->pageMax = intval($this->daosubs->getCount() / 10 + 1);
        } else {
            $termes = [];
            $t = explode(":", $this->recherche);
            foreach ($t as $value) {
                if ($value == 'NULL') {
                    $value = NULL;
                }
                array_push($termes, $value);
            }
            $liste = $this->daosubs->getSearchSubstances($termes[0], $termes[1], $termes[2], 10, 10 * ($this->pageCourante - 1));
            $this->pageMax = intval(count($liste) / 10 + 1);
        }

        if ($this->pageCourante < 1) {
            $this->pageCourante = 1;
        } elseif ($this->pageCourante > $this->pageMax) {
            $this->pageCourante = $this->pageMax;
        }

        if ($this->pageCourante <= 1) {
            $this->pagePrecedente = 1;
            $this->pageSuivante = $this->pageCourante + 1;
        } elseif ($this->pageCourante >= $this->pageMax) {
            $this->pageSuivante = $this->pageMax;
            $this->pagePrecedente = $this->pageCourante - 1;
        } else {
            $this->pagePrecedente = $this->pageCourante - 1;
            $this->pageSuivante = $this->pageCourante + 1;
        }

        $this->url = "index.php?";
        if ($this->recherche != "") {
            $this->url .= "searchsubs=" . $this->recherche . "&";
        }

        return $liste;
    }

    /**
     * Fonction permettant de préparer les données concernant une substance
     * pour sa page de detail par exemple
     * @return type
     */
    function prepareSubstance() {
        $substance = $this->daosubs->getSubstanceByCode($this->code_substance);
        return $substance;
    }

    /**
     * Fonction pour récupérer une substance par son code
     * @param type $code
     * @return type
     */
    function SubstanceByCode($code) {
        $substance = $this->daosubs->getSubstanceByCode($code);
        return $substance;
    }

    /**
     * Fonction pour récupérer toutes les substances
     * @return type
     */
    function prepareAllSubstances() {
        $liste = $this->daosubs->getAllSubstancesNoLimit();
        return $liste;
    }

    /**
     * Fonction pour récuperer les effets secondaires d'une substance
     * @return type
     */
    function prepareListeEffets() {
        $liste_es = $this->daosubs->getEffetsBySubstance($this->code_substance, 10, 10 * ($this->pageCourante - 1));
        return $liste_es;
    }

    /**
     * Fonction retournant les médicaments associés à une substance
     * @return type
     */
    function prepareListeMedicaments() {
        $listemeds = $this->daosubs->getMedicamentsBySubstance($this->code_substance, 10, 10 * ($this->pageCourante - 1));
        return $listemeds;
    }

    /**
     * Fonction pour preparer les codes atc correspondants à une substance
     * @param type $codeatc2
     * @return type
     */
    function prepareATC($codeatc2) {
        $ATC = $this->daosubs->getATCBySubstance($codeatc2);
        return $ATC;
    }

    /**
     * Fonction pour préparer la liste des effets secondaires
     * @return type
     */
    function prepareAllEffets() {
        $all_es = $this->daosubs->getAllEffets();
        return $all_es;
    }

    /**
     * Fonction pour préparer la liste des atc1
     * @return type
     */
    function prepareAllATC1() {
        $allatc1 = $this->daosubs->getAllATC1();
        return $allatc1;
    }

    /**
     * Fonction pour préparer la liste des atc2
     * @param type $atc1
     * @return type
     */
    function prepareAllATC2($atc1) {
        $atc2 = $this->daosubs->getATC2ByATC1($atc1);
        return $atc2;
    }

    /**
     * Fonction pour créer une nouvelle substance
     * @param type $DCI
     * @param type $infos_sup
     * @param type $code_atc2
     * @return type
     */
    function newSubstance($DCI, $infos_sup, $code_atc2) {
        $new_id = $this->daosubs->getCreateSubstance($DCI, $infos_sup, $code_atc2);
        return $new_id;
    }

    /**
     * Fonction pour ajouter un effet secondaire à la substance
     * @param type $substance
     * @param type $effet
     */
    function newSubstanceES($substance, $effet) {
        $this->daosubs->getAssociateSubstanceES($substance, $effet);
    }

    /**
     * Fonction pour la suppression d'une substance
     * @param type $code
     */
    function deleteSubstance($code) {
        $this->daosubs->getDeleteSubstance($code);
    }

    /**
     * Fonction vérifiant les associations d'une substance
     * @param type $substance
     * @param type $effet
     * @return type
     */
    function checkAssociationSubstanceES($substance, $effet) {
        $result = $this->daosubs->getCheckAssociationSubstanceES($substance, $effet);
        return $result;
    }

    /**
     * Fonction pour la suppression des associations d'une substance
     * @param type $code
     */
    function deleteSubstanceAssoc($code) {
        $this->daosubs->getDeleteSubstanceAssoc($code);
    }

    /**
     * Fonction pour la mise à jour d'une substance
     * @param type $code
     * @param type $DCI
     * @param type $infos_sup
     * @param type $code_atc2
     */
    function UpdateSubstance($code, $DCI, $infos_sup, $code_atc2) {
        $this->daosubs->getUpdateSubstance($code, $DCI, $infos_sup, $code_atc2);
    }

    /**
     * Fonction envoyant un email via sendmail aux médecins prescrivant la substance
     * @param type $code_substance
     * @param type $DCI
     * @param type $infos_sup
     * @param type $code_atc2
     * @param type $listeeffets
     */
    function EmailsMedecinsForSubstance($code_substance, $DCI, $infos_sup, $code_atc2, $listeeffets) {
        $subject = "Mise à jour de la substance active : " . $DCI;
        $meds = "";
        $medicamentsconcernes = $this->daosubs->getMedicamentsBySubstance($code_substance);
        foreach ($medicamentsconcernes as $med) {
            $meds = $meds . $med->denomination_medicament . " / ";
        }

        $premessage = "GSB vous informe qu'une mise à jour a été effectuée sur la substance active : " . $DCI
                . "\n Cette substance est contenue dans les médicaments suivants : " . $meds
                . "\n Voici ses nouvelles informations :"
                . "\n Dénomination : " . $DCI
                . "\n Classification ATC: " . $this->daosubs->getATCBySubstance($code_atc2)
                . "\n Informations supplémentaires : " . $infos_sup;

        $es = "\n Effets secondaires liés à la substance : ";

        foreach ($listeeffets as $effet) {
            $esobjet = $this->daosubs->getEffetbyCode($effet);
            $es = $es . $esobjet->denomination_es . " / ";
        }

        $signature = "\ GSB vous remercie pour la confiance que vous accordez à ses produits."
                . "\n Cordialement,"
                . "Les Laboratoires Galaxy-Swiss Bourdin";

        $message = $premessage . $es . $signature;

        $emails = $this->daosubs->getEmailsMedecinsForSubstance($code_substance);

        foreach ($emails as $addr_mail) {
            mail($addr_mail, $subject, $message);
        }
    }

    /**
     * Fonction permettant de formater les données reçues en POST pour la recherche
     */
    function formatRechercheSubs() {
        if (isset($_POST['nom_substance']) && isset($_POST['atc2']) && isset($_POST['atc1'])) {
            $nom = htmlspecialchars($_POST['nom_substance']);
            if (empty($nom)) {
                $nom = gettype(NULL);
            }
            $atc2 = htmlspecialchars($_POST['atc2']);
            $atc1 = htmlspecialchars($_POST['atc1']);
            $this->recherche = $nom . ':' . $atc2 . ':' . $atc1;
        } else {
            $this->recherche = "";
        }
    }

}
