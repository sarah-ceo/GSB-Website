<?php

require_once './Singleton.php';
require_once './Medecin.class.php';
require_once './DAOMedecin.php';

/**
 * Classe permettant de préparer les données concernant les medecins
 *
 *
 * @author UTILISATEUR
 */
class PresenteurMedecins {

    private $daodoc;
    private $recherche;
    public $code_medecin;
    public $pageCourante;
    public $pageMax;
    public $pageSuivante;
    public $pagePrecedente;
    public $url;

    /**
     * Constructeur appelant le Singleton pour instancier le DAO et récupérant les variables de GET et POST
     */
    function __construct() {
        $this->daodoc = new DAOMedecin(Singleton::getConnection());

        $this->formatRechercheDoc();

        if (isset($_GET['pagedoc'])) {
            $this->pageCourante = htmlspecialchars($_GET['pagedoc']);
        } else {
            $this->pageCourante = 1;
        }

        if (isset($_GET['searchdoc'])) {
            $this->recherche = htmlspecialchars($_GET['searchdoc']);
        }

        if (isset($_GET['codedoc'])) {
            $this->code_medecin = htmlspecialchars($_GET['codedoc']);
        } else {
            $this->code_medecin = "";
        }
    }

    /**
     * Fonction préparant les données des medecins à afficher dans le tableau d'index 
     * en fonction de la recherche et de la pagination
     * @return type
     */
    function prepareData() {

        if ($this->recherche == "") {
            $liste = $this->daodoc->getAllMedecins(10, 10 * ($this->pageCourante - 1));
            $this->pageMax = intval($this->daodoc->getCount() / 10 + 1);
        } else {
            $termes = [];
            $t = explode(":", $this->recherche);
            foreach ($t as $value) {
                if ($value == 'NULL') {
                    $value = NULL;
                }
                array_push($termes, $value);
            }
            $liste = $this->daodoc->getSearchMedecins($termes[0], $termes[1], $termes[2], 10, 10 * ($this->pageCourante - 1));
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
            $this->url .= "searchdoc=" . $this->recherche . "&";
        }

        return $liste;
    }

    /**
     * Fonction récupérant les données d'un médecin par son code, pour sa page de détail par exemple
     * @return type
     */
    function prepareMedecin() {
        $medecin = $this->daodoc->getMedecinByCode($this->code_medecin);
        return $medecin;
    }

    /**
     * Recupération des médicaments que le médecin prescrit
     * @return type
     */
    function prepareListeMedicaments() {
        $listemeds = $this->daodoc->getMedicamentsByMedecin($this->code_medecin, 10, 10 * ($this->pageCourante - 1));
        return $listemeds;
    }

    /**
     * Récupération de la date à laquelle un medecin a décidé de prescrire un médicament
     * @param type $codemed
     * @return type
     */
    function prepareDate($codemed) {
        $date = $this->daodoc->getDateSouscription($codemed, $this->code_medecin);
        return $date;
    }

    /**
     * Création d'un nouveau médecin
     * @param type $nom
     * @param type $prenom
     * @param type $adresse_cabinet
     * @param type $adresse_mail
     * @return type
     */
    function newMedecin($nom, $prenom, $adresse_cabinet, $adresse_mail) {
        $new_id = $this->daodoc->getCreateMedecin($nom, $prenom, $adresse_cabinet, $adresse_mail);
        return $new_id;
    }

    /**
     * Association d'un médecin et d'un médicament
     * @param type $medecin
     * @param type $medicament
     */
    function newMedecinMedicament($medecin, $medicament) {
        $this->daodoc->getAssociateMedecinMedicament($medecin, $medicament);
    }

    /**
     * Suppression d'un medecin
     * @param type $code
     */
    function deleteMedecin($code) {
        $this->daodoc->getDeleteMedecin($code);
    }

    /**
     * Verification de l'association entre medecin et medicament
     * @param type $medecin
     * @param type $medicament
     * @return type
     */
    function CheckAssociationMedecinMedicament($medecin, $medicament) {
        $result = $this->daodoc->getCheckAssociationMedecinMedicament($medecin, $medicament);
        return $result;
    }

    /**
     * Suppression des associations de ce medecin
     * @param type $code
     */
    function DeleteMedecinAssoc($code) {
        $this->daodoc->getDeleteMedecinAssoc($code);
    }

    /**
     * Mise à jour des données d'un médecin
     * @param type $codedoc
     * @param type $nom
     * @param type $prenom
     * @param type $adressecab
     * @param type $adressemail
     */
    function UpdateMedecin($codedoc, $nom, $prenom, $adressecab, $adressemail) {
        $this->daodoc->getUpdateMedecin($codedoc, $nom, $prenom, $adressecab, $adressemail);
    }

    /**
     * Récupération des données de la recherche envoyées via POST pour les formater
     */
    function formatRechercheDoc() {
        if (isset($_POST['nom_medecin']) && isset($_POST['prenom_medecin']) && isset($_POST['adresse'])) {
            $nom = htmlspecialchars($_POST['nom_medecin']);
            if (empty($nom)) {
                $nom = gettype(NULL);
            }
            $prenom = htmlspecialchars($_POST['prenom_medecin']);
            if (empty($prenom)) {
                $prenom = gettype(NULL);
            }
            $adresse = htmlspecialchars($_POST['adresse']);
            if (empty($adresse)) {
                $adresse = gettype(NULL);
            }
            $this->recherche = $nom . ':' . $prenom . ':' . $adresse;
        } else {
            $this->recherche = "";
        }
    }

}
