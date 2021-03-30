<?php

require_once './Singleton.php';
require_once './Medicament.class.php';
require_once './DAOMedicament.php';
require_once './PresenteurSubstances.class.php';

/**
 * Classe permettant de préparer les données concernant les medicaments
 *
 * @author UTILISATEUR
 */
class PresenteurMedicaments {

    private $daomed;
    private $recherche;
    public $code_medicament;
    public $pageCourante;
    public $pageMax;
    public $pageSuivante;
    public $pagePrecedente;
    public $url;

    /**
     * Constructeur appelant le Singleton pour instancier le DAO et récupérant les variables de GET et POST
     */
    function __construct() {
        $this->daomed = new DAOMedicament(Singleton::getConnection());

        $this->formatRechercheMed();

        if (isset($_GET['pagemed'])) {
            $this->pageCourante = htmlspecialchars($_GET['pagemed']);
        } else {
            $this->pageCourante = 1;
        }

        if (isset($_GET['searchmed'])) {
            $this->recherche = htmlspecialchars($_GET['searchmed']);
        }

        if (isset($_GET['codemed'])) {
            $this->code_medicament = htmlspecialchars($_GET['codemed']);
        } else {
            $this->code_medicament = "";
        }
    }

    /**
     * Fonction préparant les données des médicaments à afficher dans le tableau d'index 
     * en fonction de la recherche et de la pagination
     * @return type
     */
    function prepareData() {

        if ($this->recherche == "") {
            $liste = $this->daomed->getAllMedicaments(10, 10 * ($this->pageCourante - 1));
            $this->pageMax = intval($this->daomed->getCount() / 10 + 1);
        } else {
            $termes = [];
            $t = explode(":", $this->recherche);
            foreach ($t as $value) {
                if ($value == 'NULL') {
                    $value = NULL;
                }
                array_push($termes, $value);
            }
            $liste = $this->daomed->getSearchMedicaments($termes[0], $termes[1], $termes[2], $termes[3], $termes[4], $termes[5], $termes[6], $termes[7], $termes[8], 10, 10 * ($this->pageCourante - 1));
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
            $this->url .= "searchmed=" . $this->recherche . "&";
        }

        return $liste;
    }

    /**
     * Préparation d'un médicament de par son code, pour sa page de detail par exemple
     * @return type
     */
    function prepareMedicament() {
        $medicament = $this->daomed->getMedicamentByCode($this->code_medicament);
        return $medicament;
    }

    /**
     * Recuperer tous les médicaments
     * @return type
     */
    function prepareAllMedicaments() {
        $liste = $this->daomed->getAllMedicamentsNoLimit();
        return $liste;
    }

    /**
     * Recupération des substances contenues dans un médicament
     * @return type
     */
    function prepareListeSubstances() {
        $listesubs = $this->daomed->getSubstancesByMedicament($this->code_medicament, 10, 10 * ($this->pageCourante - 1));
        return $listesubs;
    }

    /**
     * Récupération des médecins qui prescrivent le médicament
     * @return type
     */
    function prepareListeMedecins() {
        $listedocs = $this->daomed->getMedecinsByMedicament($this->code_medicament, 10, 10 * ($this->pageCourante - 1));
        return $listedocs;
    }

    /**
     * Récupération des voies d'administration du médicament
     * @return type
     */
    function prepareListeVA() {
        $listeva = $this->daomed->getVAByMedicament($this->code_medicament);
        return $listeva;
    }

    /**
     * Recuperation de la date de souscription d'un médecin à un médicament
     * @param type $codedoc
     * @return type
     */
    function prepareDate($codedoc) {
        $date = $this->daomed->getDateSouscription($this->code_medicament, $codedoc);
        return $date;
    }

    /**
     * Récupération de toutes les voies d'administration
     * @return type
     */
    function prepareAllVAs() {
        $all_vas = $this->daomed->getAllVAs();
        return $all_vas;
    }

    /**
     * Création d'un nouveau médicament
     * @param type $nom
     * @param type $etat
     * @param type $datemm
     * @param type $surveillance
     * @param type $prescription
     * @param type $prix
     * @param type $dos
     * @return type
     */
    function newMedicament($nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos) {
        $new_id = $this->daomed->getCreateMedicament($nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos);
        return $new_id;
    }

    /**
     * Associer medicament-substance
     * @param type $medicament
     * @param type $substance
     */
    function newMedicamentSubstance($medicament, $substance) {
        $this->daomed->getAssociateMedicamentSubs($medicament, $substance);
    }

    /**
     * Associer médicament-voie d'administration
     * @param type $medicament
     * @param type $va
     */
    function newMedicamentVA($medicament, $va) {
        $this->daomed->getAssociateMedicamentVas($medicament, $va);
    }

    /**
     * Supprimer un médicament
     * @param type $code
     */
    function deleteMedicament($code) {
        $this->daomed->getDeleteMedicament($code);
    }

    /**
     * Fonction vérifiant si un médicament et une substance sont liés
     * @param type $medicament
     * @param type $substance
     * @return type
     */
    function getCheckAssociationMedicamentSubs($medicament, $substance) {
        $result = $this->daomed->getCheckAssociationMedicamentSubs($medicament, $substance);
        return $result;
    }

    /**
     * Fonction verifiant si un médicament est disponible pour une certaine voies d'administration
     * @param type $medicament
     * @param type $va
     * @return type
     */
    function CheckAssociationMedicamentVA($medicament, $va) {
        $result = $this->daomed->getCheckAssociationMedicamentVA($medicament, $va);
        return $result;
    }

    /**
     * Suppression du médicament
     * @param type $code
     */
    function deleteMedicamentAssoc($code) {
        $this->daomed->getDeleteMedicamentAssoc($code);
    }

    /**
     * Mise à jour du médicament
     * @param type $code
     * @param type $nom
     * @param type $etat
     * @param type $datemm
     * @param type $surveillance
     * @param type $prescription
     * @param type $prix
     * @param type $dos
     */
    function updateMedicament($code, $nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos) {
        $this->daomed->getUpdateMedicament($code, $nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos);
    }

    /**
     * Envoi d'un email à tous les medecins prescrivant ce médicament
     * @param type $code_medicament
     * @param type $nom
     * @param type $etat
     * @param type $datemm
     * @param type $surveillance
     * @param type $prescription
     * @param type $prix
     * @param type $dos
     * @param type $listesubs
     * @param type $listevas
     */
    function EmailMedecinForMedicament($code_medicament, $nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos, $listesubs, $listevas) {
        $subject = "Mise à jour du médicament : " . $nom;

        $premessage = "GSB vous informe qu'une mise à jour a été effectuée sur le médicament " . $nom . " que vous prescrivez."
                . "\n Voici ses nouvelles informations:"
                . "\n Dénomination : " . $nom
                . "\n Etat de commercialisation: " . $etat
                . "\n Date de mise sur le marché : " . $datemm
                . "\n Etat de surveillance : " . $surveillance
                . "\n Conditions de prescription : " . $prescription
                . "\n Prix : " . $prix
                . "\n Dosages disponibles : " . $dos;

        $vas = "\n Voies d'administration disponibles : ";
        $substances = "\n Substances actives contenues: ";

        foreach ($listevas as $va) {
            $vaobjet = $this->daomed->getVAbyCode($va);
            $vas = $vas . $vaobjet->denomination_va . " / ";
        }

        foreach ($listesubs as $sub) {
            $presenteursub = new PresenteurSubstances();
            $substanceobjet = $presenteursub->SubstanceByCode($sub);
            $substances = $substances . $substanceobjet->DCI . " / ";
        }

        $signature = "\ GSB vous remercie pour la confiance que vous accordez à ses produits."
                . "\n Cordialement,"
                . "Les Laboratoires Galaxy-Swiss Bourdin";

        $message = $premessage . $vas . $substances . $signature;

        $emails = $this->daomed->getEmailsMedecinsForMedicament($code_medicament);

        foreach ($emails as $addr_mail) {
            mail($addr_mail, $subject, $message);
        }
    }

    /**
     * Fonction pour préparer la recherche de par ce qui a été envoyé par POST
     */
    function formatRechercheMed() {
        if (isset($_POST['nom_medicament']) && isset($_POST['etat']) && isset($_POST['dateop']) && isset($_POST['damm']) && isset($_POST['surveillance']) && isset($_POST['conditions']) && isset($_POST['prixop']) && isset($_POST['prix']) && isset($_POST['dosages'])) {
            $nom = htmlspecialchars($_POST['nom_medicament']);
            if (empty($nom)) {
                $nom = gettype(NULL);
            }
            $etat = htmlspecialchars($_POST['etat']);
            $dateop = htmlspecialchars($_POST['dateop']);
            $damm = htmlspecialchars($_POST['damm']);
            if (empty($damm)) {
                $damm = gettype(NULL);
            }
            $surveillance = htmlspecialchars($_POST['surveillance']);
            $conditions = htmlspecialchars($_POST['conditions']);
            $prixop = htmlspecialchars($_POST['prixop']);
            $prix = htmlspecialchars($_POST['prix']);
            if (empty($prix)) {
                $prix = gettype(NULL);
            }
            $dosages = htmlspecialchars($_POST['dosages']);
            if (empty($dosages)) {
                $dosages = gettype(NULL);
            }
            $this->recherche = $nom . ':' . $etat . ':' . $dateop . ':' . $damm . ':' . $surveillance . ':' . $conditions . ':' . $prixop . ':' . $prix . ':' . $dosages;
        } else {
            $this->recherche = "";
        }
    }

}
